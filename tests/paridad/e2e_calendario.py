#!/usr/bin/env python3
"""
Prueba de punta a punta del Plan de Mantenimiento (Calendario) con la Bonita real de DEV: programar órdenes de cada tipo (por la
pantalla de MAN, es decir PHP + toolsMANAPI + MANDataService), series, rechazos, deshacer ante una falla de Bonita, y la ventana de
la orden (asignar, reasignar, responsable). Asset no se compara (su login a Bonita con el usuario de prueba no funciona, T28):
las filas esperadas son las que arma Calendario::guardar_agregar de asset (datos2 y setHerramInsPorTarea). Limpia todo al final.

Corre en LOCAL con la VPN y con el MI local desplegado (puerto 8290). Variables: MAN_ENV_FILE, MAN_MYCNF.
Uso: python3 tests/paridad/e2e_calendario.py
"""
import calendar
import datetime
import json
import os
import subprocess
import sys
import time
import urllib.error
import urllib.parse
import urllib.request

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
import paridad as P

API = 'http://localhost:8290/tools/man/'
NICK = os.environ.get('MAN_TEST_NICK', 'mig.man.a@trazalog.test')
EMP = 1
EQ = 130
PREF = 'MIG cal'
FMT = '%Y-%m-%d %H:%M:%S'


def api(ruta, cuerpo):
    req = urllib.request.Request(API + ruta, json.dumps(cuerpo).encode(), {'Content-Type': 'application/json', 'Accept': 'application/json'})
    try:
        return json.loads(urllib.request.urlopen(req, timeout=300).read())
    except urllib.error.HTTPError as e:
        try:
            return json.loads(e.read().decode())
        except ValueError:
            return {'status': False, 'msj': 'HTTP %d' % e.code}


def ins(q):
    return P.sql(q + '; SELECT LAST_INSERT_ID()')[0][0]


def sumar_meses(d, m):
    k = d.month - 1 + m
    y, mes = d.year + k // 12, k % 12 + 1
    return d.replace(year=y, month=mes, day=min(d.day, calendar.monthrange(y, mes)[1]))


def serie(ini, cada_dias=0, cada_meses=0, meses=1):
    """Fechas esperadas por calendario (E45)."""
    lim = sumar_meses(ini, meses)
    out, n = [], 0
    while True:
        f = ini + datetime.timedelta(days=cada_dias * n) if cada_dias else sumar_meses(ini, cada_meses * n)
        if f > lim:
            return out
        out.append(f.strftime(FMT))
        n += 1


def ots(tipo, origen):
    return P.sql("SELECT id_orden, CAST(fecha_program AS CHAR), estado, IFNULL(case_id, 0), nro, id_tarea, descripcion, duracion, id_usuario, id_usuario_e, id_sucursal, "
                 "id_proveedor, id_solicitud, id_tareapadre, id_equipo, id_empresa, lectura_programada, lectura_ejecutada, cliId, CAST(fecha AS CHAR), tipo "
                 "FROM orden_trabajo WHERE tipo=%s AND id_solicitud=%s ORDER BY fecha_program, id_orden" % (tipo, origen))


def main():
    env = P.leer_env(os.environ['MAN_ENV_FILE'])
    man = P.sesion_tools(env['DOCTEST_LOCAL_MAN_USER'], env['DOCTEST_LOCAL_MAN_PASS'])
    pm = P.MAN + 'Calendario/'
    bonita, put, bpost = P.bonita_admin()
    uid = bonita('/API/identity/user?p=0&c=1&f=userName%%3D%s' % urllib.parse.quote(NICK))[0]['id']
    usr = P.sql("SELECT usrId FROM sisusers WHERE usrNick='%s'" % NICK)[0][0]
    hoy = datetime.date.today().strftime('%Y-%m-%d')
    per = dict((d, i) for i, d in P.sql("SELECT idperiodo, descripcion FROM periodo WHERE id_empresa=%d" % EMP))
    tarea = P.sql("SELECT id_tarea, descripcion FROM tareas WHERE id_empresa=%d AND estado='AC' ORDER BY id_tarea LIMIT 1" % EMP)[0]
    ul0 = P.sql("SELECT CAST(ultima_lectura AS CHAR), CAST(fecha_ultimalectura AS CHAR) FROM equipos WHERE id_equipo=%d" % EQ)[0]
    ids = {'prev': [], 'pred': [], 'back': [], 'sol': []}
    casos = []

    def tareas_caso(c):
        return [t['name'] for t in bonita('/API/bpm/humanTask?p=0&c=20&f=caseId%%3D%s' % c)]

    def programar(**d):
        base = {'tipo': 3, 'id_origen': 0, 'fecha_progr': '03-11-2026', 'hora_progr': '08:00', 'evento': 1, 'cant_meses': 0, 'horas': 0}
        base.update(d)
        return P.jload(man.post(pm + 'programar', base))

    try:
        # ---------------------------------------------------------------- planes de prueba (empresa 1, equipo 130)
        fix = lambda t, k, q: ids[k].append(ins(q))
        prev = ins("INSERT INTO preventivo (id_equipo,id_tarea,perido,cantidad,ultimo,id_componente,critico1,horash,estadoprev,prev_duracion,id_unidad,prev_canth,prev_adjunto,id_empresa,lectura_base) "
                   "VALUES (%d,%s,'%s',3,'2026-10-30',0,NULL,1,'C',60,1,1,'mig_cal_prev.pdf',%d,NULL)" % (EQ, tarea[0], per['Diario'], EMP))
        ids['prev'].append(prev)
        P.sql("INSERT INTO tbl_preventivoherramientas (prevId,herrId,cantidad,id_empresa) VALUES (%s,1,2,%d),(%s,2,3,%d)" % (prev, EMP, prev, EMP))
        P.sql("INSERT INTO tbl_preventivoinsumos (prevId,artId,cantidad,id_empresa) VALUES (%s,5,7,%d)" % (prev, EMP))
        cont = ins("INSERT INTO preventivo (id_equipo,id_tarea,perido,cantidad,ultimo,id_componente,critico1,horash,estadoprev,prev_duracion,id_unidad,prev_canth,id_empresa,lectura_base) "
                   "VALUES (%d,%s,'%s',20,'2026-10-30',0,0,1,'C',30,1,1,%d,3)" % (EQ, tarea[0], per.get('Ciclos', per.get('Horas')), EMP))
        ids['prev'].append(cont)
        cero = ins("INSERT INTO preventivo (id_equipo,id_tarea,perido,cantidad,ultimo,id_componente,critico1,horash,estadoprev,prev_duracion,id_unidad,prev_canth,id_empresa,lectura_base) "
                   "VALUES (%d,%s,'%s',0,'2026-10-30',0,NULL,1,'C',30,1,1,%d,NULL)" % (EQ, tarea[0], per['Diario'], EMP))
        ids['prev'].append(cero)
        pred = ins("INSERT INTO predictivo (id_equipo,tarea_descrip,fecha,periodo,cantidad,horash,estado,pred_duracion,id_unidad,pred_canth,id_empresa,pred_adjunto) "
                   "VALUES (%d,'%s','2026-10-30','%s',2,1,'C',90,1,1,%d,'mig_cal_pred.pdf')" % (EQ, tarea[0], per['Mensual'], EMP))
        ids['pred'].append(pred)
        P.sql("INSERT INTO tbl_predictivoherramientas (predId,herrId,cantidad,id_empresa) VALUES (%s,1,1,%d)" % (pred, EMP))
        back = ins("INSERT INTO tbl_back (id_equipo,id_tarea,fecha,horash,estado,back_duracion,id_unidad,back_canth,id_empresa,sore_id,tarea_opcional) "
                   "VALUES (%d,247,'2026-10-30',1,'C',45,1,1,%d,NULL,'%s tarea \"opcional\"')" % (EQ, EMP, PREF))
        ids['back'].append(back)
        P.sql("INSERT INTO tbl_backlogherramientas (backId,herrId,cantidad,id_empresa) VALUES (%s,2,2,%d)" % (back, EMP))
        P.sql("INSERT INTO tbl_backloginsumos (backId,artId,cantidad,id_empresa) VALUES (%s,5,3,%d)" % (back, EMP))

        # ---------------------------------------------------------------- 1. preventivo, una orden
        r = programar(id_origen=prev)
        P.comparar('programar · preventivo una orden (respuesta)', True, r.get('status'))
        f = ots(3, prev)
        casos += [x[3] for x in f if x[3] != '0']
        P.comparar('programar · preventivo una orden (fila como la de asset)',
                   [[tarea[1] if False else f[0][0], '2026-11-03 08:00:00', 'PL', f[0][3], '1', str(tarea[0]), tarea[1], '60', usr, '1', '1', '0', str(prev), str(prev), str(EQ), str(EMP), '0', '0', '1', hoy, '3']],
                   [f[0]] if f else [])
        P.comparar('programar · preventivo: caso en Bonita, registrado en case_empresa, tarea "Asignar Recursos y Tareas"',
                   (True, True, ['Asignar Recursos y Tareas']),
                   (bool(f) and f[0][3] != '0', bool(f) and bool(P.sql("SELECT 1 FROM case_empresa WHERE case_id=%s AND empr_id=%d" % (f[0][3], EMP))),
                    tareas_caso(f[0][3]) if f else []))
        oid = f[0][0] if f else 0
        P.comparar('programar · preventivo: herramientas, insumos y adjunto copiados',
                   ([['1', '2'], ['2', '3']], [['5', '7']], [['assets/filespreventivos/mig_cal_prev.pdf']]),
                   (P.sql("SELECT herrId, cantidad FROM tbl_otherramientas WHERE otId=%s ORDER BY herrId" % oid), P.sql("SELECT artId, cantidad FROM tbl_otinsumos WHERE otId=%s" % oid),
                    P.sql("SELECT ot_adjunto FROM tbl_otadjuntos WHERE otId=%s" % oid)))
        P.comparar('programar · preventivo una orden: el plan pasa a PL y no cambia su última fecha', [['PL', '2026-10-30']],
                   P.sql("SELECT estadoprev, CAST(ultimo AS CHAR) FROM preventivo WHERE prevId=%s" % prev))
        r = programar(id_origen=prev)
        P.comparar('programar · preventivo ya planificado: se rechaza', False, r.get('status'))
        P.comparar('programar · preventivo ya planificado: no crea otra orden', 1, len(ots(3, prev)))
        limpiar_origen(bonita, 3, prev, 'C', '2026-10-30')

        # ---------------------------------------------------------------- 2. preventivo, serie de 2 meses
        r = programar(id_origen=prev, evento=2, cant_meses=2)
        esp = serie(datetime.datetime(2026, 11, 3, 8, 0), cada_dias=3, meses=2)
        f = ots(3, prev)
        casos += [x[3] for x in f if x[3] != '0']
        P.comparar('programar · preventivo serie 2 meses (respuesta)', True, r.get('status'))
        P.comparar('programar · preventivo serie: fechas (Diario cada 3 días, 2 meses)', esp, [x[1] for x in f])
        P.comparar('programar · preventivo serie: todas con su caso, estado PL y marca confirmada', (True, {'PL'}, {'1'}),
                   (all(x[3] != '0' for x in f) and len(set(x[3] for x in f)) == len(f), set(x[2] for x in f), set(x[4] for x in f)))
        P.comparar('programar · preventivo serie: copias por orden', (2 * len(f), len(f), len(f)),
                   (int(P.sql("SELECT COUNT(*) FROM tbl_otherramientas h JOIN orden_trabajo o ON o.id_orden=h.otId WHERE o.tipo=3 AND o.id_solicitud=%s" % prev)[0][0]),
                    int(P.sql("SELECT COUNT(*) FROM tbl_otinsumos h JOIN orden_trabajo o ON o.id_orden=h.otId WHERE o.tipo=3 AND o.id_solicitud=%s" % prev)[0][0]),
                    int(P.sql("SELECT COUNT(*) FROM tbl_otadjuntos h JOIN orden_trabajo o ON o.id_orden=h.otId WHERE o.tipo=3 AND o.id_solicitud=%s" % prev)[0][0])))
        P.comparar('programar · preventivo serie: la nueva fecha base es la fecha límite (2 meses)', [['PL', '2027-01-03']],
                   P.sql("SELECT estadoprev, CAST(ultimo AS CHAR) FROM preventivo WHERE prevId=%s" % prev))
        limpiar_origen(bonita, 3, prev, 'C', '2026-10-30')

        # ---------------------------------------------------------------- 3. preventivo por contador
        P.sql("UPDATE equipos SET ultima_lectura=12.5 WHERE id_equipo=%d" % EQ)
        P.comparar('programar · contador en serie: se rechaza', False, programar(id_origen=cont, evento=2, cant_meses=3).get('status'))
        P.comparar('programar · plan por tiempo marcado como contador: se rechaza', False, programar(id_origen=prev, horas=1).get('status'))
        r = programar(id_origen=cont, horas=1)
        f = ots(3, cont)
        casos += [x[3] for x in f if x[3] != '0']
        P.comparar('programar · contador (respuesta y lectura de la orden = última lectura del equipo)', (True, '12.5'), (r.get('status'), f[0][17] if f else None))
        limpiar_origen(bonita, 3, cont, 'C', '2026-10-30')
        P.comparar('programar · frecuencia 0 en serie: se rechaza', False, programar(id_origen=cero, evento=2, cant_meses=3).get('status'))

        # ---------------------------------------------------------------- 4. predictivo, serie de 12 meses (Mensual cada 2)
        r = programar(tipo=5, id_origen=pred, evento=2, cant_meses=12, fecha_progr='30-11-2026', hora_progr='09:00')
        f = ots(5, pred)
        casos += [x[3] for x in f if x[3] != '0']
        P.comparar('programar · predictivo serie (7 órdenes por calendario, el 30 de cada dos meses)',
                   serie(datetime.datetime(2026, 11, 30, 9, 0), cada_meses=2, meses=12), [x[1] for x in f])
        P.comparar('programar · predictivo: duración, tarea, adjunto y estado del plan', (['90'] * len(f), [str(tarea[0])] * len(f), 'PL', len(f)),
                   ([x[7] for x in f], [x[5] for x in f], P.sql("SELECT estado FROM predictivo WHERE predId=%s" % pred)[0][0],
                    int(P.sql("SELECT COUNT(*) FROM tbl_otadjuntos h JOIN orden_trabajo o ON o.id_orden=h.otId WHERE o.tipo=5 AND o.id_solicitud=%s AND h.ot_adjunto='assets/filespredictivos/mig_cal_pred.pdf'" % pred)[0][0])))
        limpiar_origen(bonita, 5, pred, 'C')

        # ---------------------------------------------------------------- 5. backlog sin solicitud
        r = programar(tipo=4, id_origen=back, fecha_progr='06-11-2026', hora_progr='11:00')
        f = ots(4, back)
        casos += [x[3] for x in f if x[3] != '0']
        P.comparar('programar · backlog sin solicitud (descripción = tarea opcional, duración, caso nuevo)',
                   (True, PREF + ' tarea "opcional"', '45', '247', True), (r.get('status'), f[0][6] if f else None, f[0][7] if f else None, f[0][5] if f else None, bool(f) and f[0][3] != '0'))
        limpiar_origen(bonita, 4, back, 'C')

        # ---------------------------------------------------------------- 6. rechazos
        for nombre, d in (('cantidad de meses 0', dict(id_origen=prev, evento=2, cant_meses=0)), ('cantidad de meses 37', dict(id_origen=prev, evento=2, cant_meses=37)),
                          ('cantidad de meses abc', dict(id_origen=prev, evento=2, cant_meses='abc')), ('fecha inválida', dict(id_origen=prev, fecha_progr='31-02-2026')),
                          ('hora inválida', dict(id_origen=prev, hora_progr='25:99')), ('tipo inválido', dict(id_origen=prev, tipo=9)),
                          ('serie de un backlog', dict(tipo=4, id_origen=back, evento=2, cant_meses=3)), ('origen inexistente', dict(id_origen=99999999))):
            P.comparar('programar · rechazo: %s' % nombre, False, programar(**d).get('status'))
        P.comparar('programar · los rechazos no crearon nada', (0, 0), (len(ots(3, prev)), len(ots(4, back))))
        base = {'id_empresa': '6', 'emprId': '1', 'usrId': usr, 'usrNick': NICK, 'session': '', 'tipo': '3', 'id_origen': str(prev), 'fecha': '2026-11-03 08:00:00', 'evento': '1', 'cant_meses': '0', 'horas': '0'}
        P.comparar('programar · aislamiento: un plan de la empresa 1 pedido como empresa 6', False, api('plan/programar', base).get('status'))
        P.comparar('programar · aislamiento: nada creado', 0, len(ots(3, prev)))

        # ---------------------------------------------------------------- 7. una falla a mitad de la serie: se deshace todo
        # Se fuerza con una fila de case_empresa puesta de antemano para el próximo caso de Bonita: la primera orden de la serie no puede
        # registrar su caso (el número ya existe), las demás sí lanzan el suyo, y el motor tiene que borrar los casos ya lanzados.
        r = programar(id_origen=prev)
        f = ots(3, prev)
        proximo = int(f[0][3]) + 1 if f else 0
        casos += [x[3] for x in f if x[3] != '0']
        limpiar_origen(bonita, 3, prev, 'C', '2026-10-30')
        P.sql("INSERT INTO case_empresa (case_id, empr_id) VALUES (%d, %d)" % (proximo, EMP))
        casos.append(str(proximo))
        r = programar(id_origen=prev, evento=2, cant_meses=1)
        P.comparar('programar · falla a mitad de una serie: error', False, r.get('status'))
        time.sleep(2)
        P.comparar('programar · falla a mitad de una serie: no queda ninguna orden, el plan sigue pendiente y no sobran marcas',
                   (0, 'C', '2026-10-30', 0), (len(ots(3, prev)), P.sql("SELECT estadoprev FROM preventivo WHERE prevId=%s" % prev)[0][0],
                                               P.sql("SELECT CAST(ultimo AS CHAR) FROM preventivo WHERE prevId=%s" % prev)[0][0],
                                               int(P.sql("SELECT COUNT(*) FROM orden_trabajo WHERE nro LIKE 'P%%' AND id_empresa=%d" % EMP)[0][0])))
        vivos = [c['id'] for c in bonita('/API/bpm/case?p=0&c=30&o=id%20DESC') if int(c['id']) >= proximo]
        P.comparar('programar · falla a mitad de una serie: no quedan casos de Bonita sin su orden', [], vivos)
        P.sql("DELETE FROM case_empresa WHERE case_id=%d" % proximo)
        # Los casos que el motor deshizo quedan registrados en core.case_empresa de PostgreSQL (H11): se limpian acá
        subprocess.run(['bash', '-c', 'set -a; . %s; set +a; psql -At -c "delete from core.case_empresa where case_id >= %d"' % (os.environ['MAN_PGENV'], proximo)], capture_output=True)

        # ---------------------------------------------------------------- 8. mover y estirar desde el calendario (PHP)
        o = ins("INSERT INTO orden_trabajo (tipo,id_solicitud,id_empresa,estado,id_equipo,descripcion,fecha_program,duracion,nro) VALUES ('1',0,%d,'PL',%d,'%s ot','2026-11-10 08:00:00',60,'1')" % (EMP, EQ, PREF))
        P.comparar('calendario · mover', (True, [['2026-11-11 09:30:00', '60']]),
                   (P.jload(man.post(pm + 'updateDiaProg', {'id': o, 'prog': '2026-11-11 09:30:00'})), P.sql("SELECT CAST(fecha_program AS CHAR), duracion FROM orden_trabajo WHERE id_orden=%s" % o)))
        P.comparar('calendario · estirar 30 y acortar 10', (True, True, [['2026-11-11 09:30:00', '80']]),
                   (P.jload(man.post(pm + 'updateDuracion', {'id': o, 'duracion': 30})), P.jload(man.post(pm + 'updateDuracion', {'id': o, 'duracion': -10})),
                    P.sql("SELECT CAST(fecha_program AS CHAR), duracion FROM orden_trabajo WHERE id_orden=%s" % o)))
        P.sql("DELETE FROM orden_trabajo WHERE id_orden=%s" % o)

        # ---------------------------------------------------------------- 9. correctivo: solicitud urgente con su caso real
        sol = crear_solicitud(bonita, put, bpost, uid, urgente=True, ids=ids, casos=casos)
        r = programar(tipo=2, id_origen=sol[0], fecha_progr='04-11-2026', hora_progr='09:00')
        f = ots(2, sol[0])
        P.comparar('programar · correctivo (orden con el caso de la solicitud, 60 minutos, tarea 0, descripción = causa)',
                   (True, sol[1], '60', '0', PREF + ' solicitud'), (r.get('status'), f[0][3] if f else None, f[0][7] if f else None, f[0][5] if f else None, f[0][6] if f else None))
        time.sleep(3)
        P.comparar('programar · correctivo: Bonita pasó a "Asignar Recursos y Tareas Urgente" y la solicitud quedó PL', (['Asignar Recursos y Tareas Urgente'], 'PL'),
                   (tareas_caso(sol[1]), P.sql("SELECT estado FROM solicitud_reparacion WHERE id_solicitud=%s" % sol[0])[0][0]))
        P.comparar('programar · correctivo ya planificado: se rechaza', False, programar(tipo=2, id_origen=sol[0]).get('status'))
        # La ventana de la orden: pestañas de asignación, asignar, reasignar
        oc = f[0][0] if f else 0
        v = man.get(pm + 'verEjecutarOT/%s/' % oc)
        P.comparar('ventana · orden con la tarea pendiente: pestañas, botón y detalle', (True, True, True, 'Correctivo' in v or 'Solicitud de servicio' in v),
                   ('id="ejecutar_ot"' in v, 'Agregar Tareas' in v, PREF + ' solicitud' in v, 'Correctivo' in v or 'Solicitud de servicio' in v))
        P.comparar('ventana · de otra empresa no se muestra', True, 'La orden no existe' in man.get(pm + 'verEjecutarOT/%s/' % P.sql("SELECT id_orden FROM orden_trabajo WHERE id_empresa<>%d LIMIT 1" % EMP)[0][0]))
        po = P.MAN + 'Otrabajo/'
        P.comparar('ventana · asignar con una orden de otra empresa: se rechaza', False,
                   P.jload(man.post(po + 'EjecutarOT', {'ot': P.sql("SELECT id_orden FROM orden_trabajo WHERE id_empresa<>%d LIMIT 1" % EMP)[0][0], 'responsable': usr, 'tareastd': 247, 'tareaOpcional': ''})).get('status'))
        r = P.jload(man.post(po + 'EjecutarOT', {'ot': oc, 'responsable': usr, 'tareastd': 247, 'tareaOpcional': 'Revisar "sello"\nurgente', 'latitud': '-31.5', 'longitud': '-68.5'}))
        P.comparar('ventana · asignar (respuesta)', True, r.get('status'))
        P.comparar('ventana · asignar: orden AS con tarea, responsable, ubicación y la solicitud AS', ([['AS', '247', 'Revisar "sello"\\nurgente', usr, '-31.5', '-68.5']], 'AS'),
                   (P.sql("SELECT estado, id_tarea, descripcion, id_usuario_a, latitud, longitud FROM orden_trabajo WHERE id_orden=%s" % oc),
                    P.sql("SELECT estado FROM solicitud_reparacion WHERE id_solicitud=%s" % sol[0])[0][0]))
        time.sleep(2)
        t = [x for x in bonita('/API/bpm/humanTask?p=0&c=20&f=caseId%%3D%s' % sol[1])]
        P.comparar('ventana · asignar: "Ejecutar OT" quedó asignada al responsable', [('Ejecutar OT', True)], [(x['name'], x.get('assigned_id') == uid) for x in t])
        P.comparar('ventana · asignar de nuevo: se rechaza (la tarea ya no está)', False, P.jload(man.post(po + 'EjecutarOT', {'ot': oc, 'responsable': usr, 'tareastd': 247, 'tareaOpcional': ''})).get('status'))
        P.comparar('ventana · reasignar (al mismo usuario y a otro de la empresa)', (True, True),
                   (P.jload(man.post(po + 'reasignaOt', {'ot': oc, 'responsable': usr})).get('status'), P.jload(man.post(po + 'reasignaOt', {'ot': oc, 'responsable': 148})).get('status')))
        P.comparar('ventana · reasignar: responsable en la orden', [['148']], P.sql("SELECT id_usuario_a FROM orden_trabajo WHERE id_orden=%s" % oc))
        P.comparar('ventana · reasignar a un usuario de otra empresa: se rechaza', False, P.jload(man.post(po + 'reasignaOt', {'ot': oc, 'responsable': 1})).get('status'))
        P.comparar('ventana · responsable al cerrar (propio y ajeno)', (True, False, [[usr]]),
                   (P.jload(man.post(po + 'updateResponsable', {'idOt': oc, 'id_usuario_a': usr})), P.jload(man.post(po + 'updateResponsable', {'idOt': oc, 'id_usuario_a': 1})),
                    P.sql("SELECT id_usuario_a FROM orden_trabajo WHERE id_orden=%s" % oc)))

        # ---------------------------------------------------------------- 10. backlog que viene de una solicitud
        sol2 = crear_solicitud(bonita, put, bpost, uid, urgente=False, ids=ids, casos=casos)
        b2 = P.sql("SELECT backId FROM tbl_back WHERE sore_id=%s" % sol2[0])[0][0]
        ids['back'].append(b2)
        P.sql("UPDATE tbl_back SET estado='S', id_tarea=247, back_duracion=45 WHERE backId=%s" % b2)
        r = programar(tipo=4, id_origen=b2, fecha_progr='05-11-2026', hora_progr='10:00')
        f = ots(4, b2)
        P.comparar('programar · backlog con solicitud (orden con el caso de la solicitud)', (True, sol2[1], '45'), (r.get('status'), f[0][3] if f else None, f[0][7] if f else None))
        time.sleep(3)
        P.comparar('programar · backlog con solicitud: "Planificar Backlog" cerrada, backlog y solicitud PL', (['Asignar Recursos y Tareas'], 'PL', 'PL'),
                   (tareas_caso(sol2[1]), P.sql("SELECT estado FROM tbl_back WHERE backId=%s" % b2)[0][0], P.sql("SELECT estado FROM solicitud_reparacion WHERE id_solicitud=%s" % sol2[0])[0][0]))
        ob = f[0][0] if f else 0
        r = P.jload(man.post(po + 'EjecutarOT', {'ot': ob, 'responsable': usr, 'tareastd': 247, 'tareaOpcional': ''}))
        P.comparar('ventana · asignar backlog con solicitud: orden, backlog y solicitud AS', (True, 'AS', 'AS', 'AS'),
                   (r.get('status'), P.sql("SELECT estado FROM orden_trabajo WHERE id_orden=%s" % ob)[0][0], P.sql("SELECT estado FROM tbl_back WHERE backId=%s" % b2)[0][0],
                    P.sql("SELECT estado FROM solicitud_reparacion WHERE id_solicitud=%s" % sol2[0])[0][0]))
    finally:
        limpiar_todo(bonita, ids, casos, ul0)
    print('\n%s' % ('E2E CALENDARIO OK' if not P.fallas else 'E2E CALENDARIO CON FALLAS: ' + ', '.join(P.fallas)))
    sys.exit(1 if P.fallas else 0)


def crear_solicitud(bonita, put, bpost, uid, urgente, ids, casos):
    """Solicitud nueva con su caso; decide la urgencia por la bandeja de MAN. Devuelve (id, caso). Si no es urgente, cierra "Editar Backlog"."""
    r = api('solicitud', {'solicitud': {'f_solicitado': time.strftime(FMT), 'usrId': P.sql("SELECT usrId FROM sisusers WHERE usrNick='%s'" % NICK)[0][0], 'solicitante': 'MIG',
                                        'causa': PREF + ' solicitud', 'id_equipo': str(EQ), 'id_empresa': str(EMP)}, 'session': '', 'emprId': '1'})
    sol, caso = r['id'], r['caseId']
    ids['sol'].append(sol)
    casos.append(caso)
    tid = bonita('/API/bpm/humanTask?p=0&c=10&f=caseId%%3D%s' % caso)[0]['id']
    base = {'usrNick': NICK, 'id_empresa': str(EMP), 'session': '', 'id_tarea': tid}
    api('bandeja/tarea/asignar', dict(base, accion='tomar'))
    api('bandeja/tarea/urgencia', dict(base, opcion='true' if urgente else 'false'))
    for _ in range(10):
        time.sleep(2)
        t = bonita('/API/bpm/humanTask?p=0&c=10&f=caseId%%3D%s' % caso)
        if t:
            break
    if not urgente:
        put('/API/bpm/humanTask/%s' % t[0]['id'], {'assigned_id': uid})
        bpost('/API/bpm/userTask/%s/execution' % t[0]['id'], {})
        for _ in range(10):
            time.sleep(2)
            t = bonita('/API/bpm/humanTask?p=0&c=10&f=caseId%%3D%s' % caso)
            if t and t[0]['name'] == 'Planificar Backlog':
                break
    return sol, caso


def limpiar_origen(bonita, tipo, origen, estado, ultimo=None):
    """Borra las órdenes del origen con sus casos, copias y registros, y vuelve el plan a su estado."""
    filas = P.sql("SELECT id_orden, IFNULL(case_id, 0) FROM orden_trabajo WHERE tipo=%s AND id_solicitud=%s AND id_empresa=%d" % (tipo, origen, EMP))
    for _, c in filas:
        if c != '0':
            bonita('/API/bpm/case/%s' % c, 'DELETE')
            bonita('/API/bpm/archivedCase/%s' % c, 'DELETE')
            P.sql("DELETE FROM case_empresa WHERE case_id=%s" % c)
            subprocess.run(['bash', '-c', 'set -a; . %s; set +a; psql -At -c "delete from core.case_empresa where case_id=%s"' % (os.environ['MAN_PGENV'], c)], capture_output=True)
    if filas:
        lista = ','.join(f[0] for f in filas)
        for t in ('tbl_otherramientas', 'tbl_otinsumos', 'tbl_otadjuntos'):
            P.sql("DELETE FROM %s WHERE otId IN (%s)" % (t, lista))
        P.sql("DELETE FROM orden_trabajo WHERE id_orden IN (%s)" % lista)
    tabla = {3: ('preventivo', 'estadoprev', 'prevId'), 5: ('predictivo', 'estado', 'predId'), 4: ('tbl_back', 'estado', 'backId')}[tipo]
    P.sql("UPDATE %s SET %s='%s' WHERE %s=%s" % (tabla[0], tabla[1], estado, tabla[2], origen))
    if ultimo and tipo == 3:
        P.sql("UPDATE preventivo SET ultimo='%s' WHERE prevId=%s" % (ultimo, origen))


def limpiar_todo(bonita, ids, casos, ul0):
    """Deja la base como estaba: órdenes, casos, planes y solicitudes de la prueba."""
    for tipo, k in ((3, 'prev'), (5, 'pred'), (4, 'back')):
        for o in ids[k]:
            try:
                limpiar_origen(bonita, tipo, o, 'C')
            except Exception as e:  # sigue limpiando
                print('     | no se pudo limpiar', tipo, o, e)
    P.sql("DELETE FROM orden_trabajo WHERE descripcion LIKE '%s%%' AND id_empresa=%d" % (PREF, EMP))
    for c in casos:
        for ruta in ('/API/bpm/case/%s' % c, '/API/bpm/archivedCase/%s' % c):
            bonita(ruta, 'DELETE')
        P.sql("DELETE FROM case_empresa WHERE case_id=%s" % c)
        subprocess.run(['bash', '-c', 'set -a; . %s; set +a; psql -At -c "delete from core.case_empresa where case_id=%s"' % (os.environ['MAN_PGENV'], c)], capture_output=True)
    for k, t, pk in (('prev', 'preventivo', 'prevId'), ('pred', 'predictivo', 'predId'), ('back', 'tbl_back', 'backId')):
        for o in ids[k]:
            for det in {'prev': ('tbl_preventivoherramientas', 'tbl_preventivoinsumos'), 'pred': ('tbl_predictivoherramientas', 'tbl_predictivoinsumos'),
                        'back': ('tbl_backlogherramientas', 'tbl_backloginsumos')}[k]:
                P.sql("DELETE FROM %s WHERE %s=%s" % (det, pk, o))
            P.sql("DELETE FROM %s WHERE %s=%s" % (t, pk, o))
    if ids['sol']:
        P.sql("DELETE FROM orden_trabajo WHERE tipo=2 AND id_solicitud IN (%s) AND id_empresa=%d" % (','.join(ids['sol']), EMP))
        P.sql("DELETE FROM solicitud_reparacion WHERE id_solicitud IN (%s)" % ','.join(ids['sol']))
    P.sql("UPDATE equipos SET ultima_lectura=%s, fecha_ultimalectura=%s WHERE id_equipo=%d" % (ul0[0], "'%s'" % ul0[1] if ul0[1] != 'NULL' else 'NULL', EQ))
    print('     | limpio: órdenes, planes, solicitudes y casos de la prueba borrados (quedan sus filas de core.case_empresa si PostgreSQL no estuvo disponible)')


if __name__ == '__main__':
    os.environ.setdefault('MAN_PGENV', os.path.join(os.path.dirname(os.environ.get('MAN_MYCNF', '/tmp/x')), 'pg.env'))
    main()
