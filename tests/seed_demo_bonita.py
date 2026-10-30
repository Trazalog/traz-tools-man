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


def borrar(bonita):
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


if __name__ == '__main__':
    main()
