"""Prueba directa de las acciones de la tarea (toolsMANAPI /bandeja/*) con un caso real; limpia todo al final."""
import json, sys, urllib.request, urllib.parse
import os
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
import paridad as p
API = 'http://localhost:8290/tools/man/'
def post(ruta, cuerpo):
    req = urllib.request.Request(API + ruta, json.dumps(cuerpo).encode(), {'Content-Type': 'application/json', 'Accept': 'application/json'})
    try:
        return json.loads(urllib.request.urlopen(req, timeout=120).read())
    except urllib.error.HTTPError as e:
        return {'_http': e.code, 'cuerpo': e.read().decode()[:300]}
get, put, bpost = p.bonita_admin()
NICK = 'mig.man.a@trazalog.test'
uid = get('/API/identity/user?p=0&c=1&f=userName%%3D%s' % urllib.parse.quote(NICK))[0]['id']
eq = '130'
ul_antes = p.sql("SELECT CAST(ultima_lectura AS CHAR), CAST(fecha_ultimalectura AS CHAR) FROM equipos WHERE id_equipo=%s" % eq)[0]
r = post('ot', {'ot': {'id_tarea': '246', 'fecha_program': '2026-11-01', 'fecha_entrega': '2026-11-05', 'descripcion': 'MIG acciones bandeja',
                       'id_usuario': '213', 'id_sucursal': '0', 'id_proveedor': '0', 'id_equipo': eq, 'id_empresa': '1'},
                'herramientas': [], 'insumos': [], 'session': '', 'emprId': '1'})
print('alta', r)
o, caso = r['id'], r['caseId']
def tareas():
    return get('/API/bpm/humanTask?p=0&c=10&f=caseId%%3D%s' % caso)
def estado():
    return p.sql("SELECT tipo, id_solicitud, estado, CAST(fecha_inicio AS CHAR), latitud, longitud, id_usuario_a, CAST(fecha_terminada AS CHAR), justificacion "
                 "FROM orden_trabajo WHERE id_orden=%s" % o)
def base(t, **x):
    d = {'usrNick': NICK, 'id_empresa': '1', 'session': '', 'id_tarea': t}
    d.update(x); return d
try:
    ts = tareas(); print('tareas', [(t['name'], t.get('assigned_id')) for t in ts]); print('OT', estado())
    put('/API/bpm/humanTask/%s' % ts[0]['id'], {'assigned_id': uid})
    print('completa', ts[0]['name'], repr(bpost('/API/bpm/userTask/%s/execution' % ts[0]['id'], {}))[:40])
    ts = tareas(); print('tareas', [(t['name'], t.get('assigned_id')) for t in ts])
    tid = ts[0]['id']
    print('tomar', post('bandeja/tarea/asignar', base(tid, accion='tomar')), [t.get('assigned_id') for t in tareas()], 'uid', uid)
    print('soltar', post('bandeja/tarea/asignar', base(tid, accion='soltar')), [t.get('assigned_id') for t in tareas()])
    print('otra empresa', post('bandeja/tarea/asignar', dict(base(tid, accion='tomar'), id_empresa='2')))
    print('inicio otra empresa', post('bandeja/ot/inicio', {'id_empresa': '2', 'id_ot': o, 'lat': '1', 'lon': '2', 'fecha': '2026-10-29 10:00:00'}))
    print('inicio', post('bandeja/ot/inicio', {'id_empresa': '1', 'id_ot': o, 'lat': '-31.5', 'lon': '-68.5', 'fecha': '2026-10-29 10:00:00'}), estado())
    print('tomar', post('bandeja/tarea/asignar', base(tid, accion='tomar')))
    print('ejecutar', post('bandeja/tarea/ejecutar', base(tid, fecha='2026-10-29 11:00:00', usr_id='213')), estado())
    ts = tareas(); print('tareas luego', [(t['name'], t.get('assigned_id')) for t in ts])
    # Confecciona informe servicio: se completa por Bonita y se carga el informe
    p.sql("INSERT INTO orden_servicio (lectura, fecha, comprobante, id_equipo, id_contratista, id_solicitudreparacion, valesid, estado, id_ordenherraminetas, "
          "id_orden_insumo, id_ot, id_empresa, fechahorainicio, fechahorafin, horometroinicio, horometrofin) VALUES (0, '2026-10-28', 'MIG acciones', %s, 0, 0, 0, 'C', "
          "0, 0, %s, 1, '2026-10-28 08:00:00', '2026-10-28 10:00:00', 100, 123.4)" % (eq, o))
    for paso in range(4):
        ts = tareas(); nombres = [t['name'] for t in ts]
        if 'Verifica Informe de Servicio' in nombres or not ts: break
        t = ts[0]; put('/API/bpm/humanTask/%s' % t['id'], {'assigned_id': uid})
        print('  completa', t['name'], repr(bpost('/API/bpm/userTask/%s/execution' % t['id'], {}))[:60])
    ts = tareas(); print('tareas', [(t['name'], t.get('assigned_id')) for t in ts])
    v = [t for t in ts if t['name'] == 'Verifica Informe de Servicio'][0]['id']
    print('tomar', post('bandeja/tarea/asignar', base(v, accion='tomar')))
    print('verificar NO', post('bandeja/tarea/verificar', base(v, opcion='false', justificacion='Falta detalle MIG')), estado())
    ts = tareas(); print('tareas luego del rechazo', [(t['name'], t.get('assigned_id')) for t in ts])
    for paso in range(4):
        ts = tareas(); nombres = [t['name'] for t in ts]
        if 'Verifica Informe de Servicio' in nombres or not ts: break
        t = ts[0]; put('/API/bpm/humanTask/%s' % t['id'], {'assigned_id': uid})
        print('  completa', t['name'], repr(bpost('/API/bpm/userTask/%s/execution' % t['id'], {}))[:60])
    v = [t for t in tareas() if t['name'] == 'Verifica Informe de Servicio'][0]['id']
    put('/API/bpm/humanTask/%s' % v, {'assigned_id': uid})
    print('verificar SI', post('bandeja/tarea/verificar', base(v, opcion='true', justificacion='')), estado())
    print('informe', p.sql("SELECT estado FROM orden_servicio WHERE id_ot=%s" % o))
    print('lectura', p.sql("SELECT id_equipo, lectura, CAST(fecha AS CHAR), usrId, observacion, operario_nom, turno, estado FROM historial_lecturas WHERE observacion LIKE '%%| OT: %s'" % o))
    ts = tareas(); print('tareas final', [(t['name'], t.get('assigned_id')) for t in ts])
    c = [t for t in ts if t['name'] == 'Presta conformidad']
    if c:
        print('tomar', post('bandeja/tarea/asignar', base(c[0]['id'], accion='tomar')))
        print('conformidad', post('bandeja/tarea/conformidad', base(c[0]['id'], opcion='true')), estado())
        print('tareas tras conformidad', [(t['name'], t.get('assigned_id')) for t in tareas()])
finally:
    p.sql("DELETE FROM historial_lecturas WHERE observacion LIKE '%%| OT: %s'" % o)
    p.sql("UPDATE equipos SET ultima_lectura=%s, fecha_ultimalectura=%s WHERE id_equipo=%s" % (ul_antes[0], "'%s'" % ul_antes[1] if ul_antes[1] != 'NULL' else 'NULL', eq))
    p.sql("DELETE FROM orden_servicio WHERE id_ot=%s AND comprobante='MIG acciones'" % o)
    p.sql("DELETE FROM case_empresa WHERE case_id=%s" % caso)
    p.sql("DELETE FROM orden_trabajo WHERE id_orden=%s" % o)
    for ruta in ('/API/bpm/case/%s' % caso, '/API/bpm/archivedCase/%s' % caso):
        get(ruta, 'DELETE')
    print('limpio; caso', caso)
