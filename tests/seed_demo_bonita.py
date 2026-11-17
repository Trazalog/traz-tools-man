#!/usr/bin/env python3
"""
Solicitudes de ejemplo con su caso real en la Bonita de DEV, para ver la Bandeja y las solicitudes urgentes del Calendario.
Crea tres solicitudes de la empresa 1 con causa "DEMO MAN ...": una en "Análisis de Solicitud de Servicio", una urgente (queda en
"Planificar Solicitud") y una no urgente (queda en "Editar Backlog"). Con --borrar borra las solicitudes, sus backlogs y sus casos.
Uso: MAN_ENV_FILE=... MAN_MYCNF=... python3 tests/seed_demo_bonita.py [--borrar]   (necesita el MI local en el puerto 8290)
"""
import json
import os
import subprocess
import sys
import time
import urllib.error
import urllib.parse
import urllib.request

sys.path.insert(0, os.path.join(os.path.dirname(os.path.abspath(__file__)), 'paridad'))
import paridad as P

API = 'http://localhost:8290/tools/man/'
NICK = os.environ.get('MAN_TEST_NICK', 'mig.man.a@trazalog.test')
EMP = 1


def api(ruta, d):
    req = urllib.request.Request(API + ruta, json.dumps(d).encode(), {'Content-Type': 'application/json', 'Accept': 'application/json'})
    try:
        return json.loads(urllib.request.urlopen(req, timeout=200).read())
    except urllib.error.HTTPError as e:
        return {'status': False, 'msj': e.read().decode()[:200]}


def borrar_etapas(bonita):
    filas = P.sql("SELECT id_orden, IFNULL(case_id, 0) FROM orden_trabajo WHERE descripcion LIKE 'ETAPA DEMO MAN%%' AND id_empresa=%d" % EMP)
    if not filas:
        return
    pg = os.path.join(os.path.dirname(os.environ['MAN_MYCNF']), 'pg.env')
    for o, c in filas:
        if c != '0':
            for ruta in ('/API/bpm/case/%s' % c, '/API/bpm/archivedCase/%s' % c):
                bonita(ruta, 'DELETE')
            P.sql("DELETE FROM case_empresa WHERE case_id=%s" % c)
            subprocess.run(['bash', '-c', 'set -a; . %s; set +a; psql -At -c "delete from core.case_empresa where case_id=%s"' % (pg, c)], capture_output=True)
        P.sql("DELETE FROM orden_servicio WHERE id_ot=%s" % o)
        P.sql("DELETE FROM orden_trabajo WHERE id_orden=%s" % o)
    print('ordenes por etapas borradas: %d' % len(filas))


def etapas(bonita, put, bpost, uid, usr, eq):
    """Cuatro órdenes con su caso, cada una detenida en una tarea distinta: Asignar, Ejecutar OT, Confecciona informe y Verifica informe."""
    def alta(texto):
        r = api('ot', {'ot': {'id_tarea': '246', 'fecha_program': '2026-11-02', 'fecha_entrega': '2026-11-06', 'descripcion': texto, 'id_usuario': usr, 'id_sucursal': '0',
                              'id_proveedor': '0', 'id_equipo': str(eq), 'id_empresa': str(EMP)}, 'herramientas': [], 'insumos': [], 'session': '', 'emprId': '1'})
        return r['id'], r['caseId']

    def pendiente(caso):
        t = bonita('/API/bpm/humanTask?p=0&c=10&f=caseId%%3D%s' % caso)
        return t[0] if t else None

    def avanzar(caso, hasta):
        for _ in range(6):
            t = pendiente(caso)
            if t is None or t['name'] == hasta:
                return t
            put('/API/bpm/humanTask/%s' % t['id'], {'assigned_id': uid})
            bpost('/API/bpm/userTask/%s/execution' % t['id'], {})
            time.sleep(2)
        return pendiente(caso)

    o1, c1 = alta('ETAPA DEMO MAN asignar responsable')
    o2, c2 = alta('ETAPA DEMO MAN ejecutar orden')
    avanzar(c2, 'Ejecutar OT')
    o3, c3 = alta('ETAPA DEMO MAN confeccionar informe')
    avanzar(c3, 'Confecciona informe servicio')
    o4, c4 = alta('ETAPA DEMO MAN verificar informe')
    P.sql("INSERT INTO orden_servicio (lectura, fecha, comprobante, id_equipo, id_contratista, id_solicitudreparacion, valesid, estado, id_ordenherraminetas, id_orden_insumo, id_ot, id_empresa, "
          "fechahorainicio, fechahorafin, horometroinicio, horometrofin) VALUES (0,'2026-10-28','ETAPA DEMO MAN',%s,0,0,0,'C',0,0,%s,%d,'2026-10-28 08:00:00','2026-10-28 10:00:00',100,123.4)" % (eq, o4, EMP))
    avanzar(c4, 'Verifica Informe de Servicio')
    print('ordenes por etapas creadas: %s' % [o1, o2, o3, o4])


def borrar(bonita):
    borrar_etapas(bonita)
    sols = [r[0] for r in P.sql("SELECT id_solicitud FROM solicitud_reparacion WHERE causa LIKE 'DEMO MAN%%' AND id_empresa=%d" % EMP)]
    if not sols:
        return
    lista = ','.join(sols)
    casos = [r[0] for r in P.sql("SELECT case_id FROM solicitud_reparacion WHERE id_solicitud IN (%s) AND case_id IS NOT NULL" % lista)]
    for c in casos:
        for ruta in ('/API/bpm/case/%s' % c, '/API/bpm/archivedCase/%s' % c):
            bonita(ruta, 'DELETE')
    if casos:
        P.sql("DELETE FROM case_empresa WHERE case_id IN (%s)" % ','.join(casos))
        pg = os.path.join(os.path.dirname(os.environ['MAN_MYCNF']), 'pg.env')
        subprocess.run(['bash', '-c', 'set -a; . %s; set +a; psql -At -c "delete from core.case_empresa where case_id in (%s)"' % (pg, ','.join(casos))], capture_output=True)
    P.sql("DELETE FROM tbl_back WHERE sore_id IN (%s)" % lista)
    P.sql("DELETE FROM solicitud_reparacion WHERE id_solicitud IN (%s)" % lista)
    print('solicitudes de ejemplo borradas: %d' % len(sols))


def main():
    bonita, put, bpost = P.bonita_admin()
    borrar(bonita)
    if '--borrar' in sys.argv:
        return
    usr = P.sql("SELECT usrId FROM sisusers WHERE usrNick='%s'" % NICK)[0][0]
    eq = P.sql("SELECT id_equipo FROM equipos WHERE id_empresa=%d AND estado='AC' ORDER BY id_equipo LIMIT 1" % EMP)[0][0]
    uid = bonita('/API/identity/user?p=0&c=1&f=userName%%3D%s' % urllib.parse.quote(NICK))[0]['id']
    nuevas = []
    for causa in ('DEMO MAN falla en bomba de agua', 'DEMO MAN perdida de aceite urgente', 'DEMO MAN ruido en el motor'):
        r = api('solicitud', {'solicitud': {'f_solicitado': time.strftime('%Y-%m-%d %H:%M:%S'), 'usrId': usr, 'solicitante': 'MIG', 'causa': causa, 'id_equipo': str(eq),
                                            'id_empresa': str(EMP)}, 'session': '', 'emprId': '1'})
        nuevas.append((r['id'], r['caseId']))
    for (sol, caso), urgente in ((nuevas[1], 'true'), (nuevas[2], 'false')):
        tid = bonita('/API/bpm/humanTask?p=0&c=10&f=caseId%%3D%s' % caso)[0]['id']
        base = {'usrNick': NICK, 'id_empresa': str(EMP), 'session': '', 'id_tarea': tid}
        api('bandeja/tarea/asignar', dict(base, accion='tomar'))
        api('bandeja/tarea/urgencia', dict(base, opcion=urgente))
    print('solicitudes de ejemplo creadas: %s' % [n[0] for n in nuevas])
    etapas(bonita, put, bpost, uid, usr, eq)


if __name__ == '__main__':
    main()
