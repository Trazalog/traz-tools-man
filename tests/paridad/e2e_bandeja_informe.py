"""Prueba directa del guardado del informe de servicio (toolsMANAPI /bandeja/tarea/informe) con un caso real: alta, reemplazo al editar y rechazo
de otra empresa; limpia todo al final."""
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
r = post('ot', {'ot': {'id_tarea': '246', 'fecha_program': '2026-11-01', 'fecha_entrega': '2026-11-05', 'descripcion': 'MIG informe bandeja',
                       'id_usuario': '213', 'id_sucursal': '0', 'id_proveedor': '0', 'id_equipo': eq, 'id_empresa': '1'},
                'herramientas': [], 'insumos': [], 'session': '', 'emprId': '1'})
print('alta', r)
o, caso = r['id'], r['caseId']
def tareas():
    return get('/API/bpm/humanTask?p=0&c=10&f=caseId%%3D%s' % caso)
def base(t, **x):
    d = {'usrNick': NICK, 'id_empresa': '1', 'session': '', 'id_tarea': t}
    d.update(x); return d
def estado():
    return {'informes': p.sql("SELECT id_orden, id_equipo, estado, CAST(fechahorainicio AS CHAR), CAST(fechahorafin AS CHAR), horometroinicio, horometrofin, id_solicitudreparacion FROM orden_servicio WHERE id_ot=%s" % o),
            'tareas': p.sql("SELECT HEX(d.id_tarea) FROM deta_ordenservicio d JOIN orden_servicio s ON s.id_orden=d.id_ordenservicio WHERE s.id_ot=%s ORDER BY d.id_detasercicio" % o),
            'operarios': p.sql("SELECT a.usrId FROM asignausuario a JOIN orden_servicio s ON s.id_orden=a.id_orden WHERE s.id_ot=%s ORDER BY a.id" % o),
            'evidencias': p.sql("SELECT a.nombre, LENGTH(a.valor_base64) FROM orden_servicio_adjuntos a JOIN orden_servicio s ON s.id_orden=a.id_orden WHERE s.id_ot=%s" % o)}
def informe(t, **x):
    d = base(t, id_solicitudreparacion='0', fecha_inicio='2026-10-29 08:00:00', fecha_fin='2026-10-29 10:30:00', horometro_inicio='100', horometro_fin='123.5',
             tareas=[{'tarea': 'Cambio de "filtro" ñandú'}, {'tarea': 'Limpieza'}], operarios=[{'usrId': '213'}], evidencias=[{'valor_base64': 'QUJDREVGRw==', 'nombre': 'foto.png'}])
    d.update(x); return post('bandeja/tarea/informe', d)
try:
    ts = tareas(); put('/API/bpm/humanTask/%s' % ts[0]['id'], {'assigned_id': uid}); bpost('/API/bpm/userTask/%s/execution' % ts[0]['id'], {})
    tid = tareas()[0]['id']; print('tarea', tareas()[0]['name'])
    print('tomar', post('bandeja/tarea/asignar', base(tid, accion='tomar')))
    print('ejecutar', post('bandeja/tarea/ejecutar', base(tid, fecha='2026-10-29 11:00:00', usr_id='213')))
    ts = tareas(); print('tareas', [(t['name'], t.get('assigned_id')) for t in ts])
    c = [t for t in ts if t['name'] == 'Confecciona informe servicio'][0]['id']
    print('informe otra empresa', informe(c, id_empresa='2'))
    print('sin informe tras rechazo', estado()['informes'])
    print('informe', informe(c)); e = estado(); print(json.dumps(e, ensure_ascii=False))
    ts = tareas(); print('tareas luego', [(t['name'], t.get('assigned_id')) for t in ts])
    v = [t for t in ts if t['name'] == 'Verifica Informe de Servicio'][0]['id']
    print('tomar', post('bandeja/tarea/asignar', base(v, accion='tomar')))
    print('verificar NO', post('bandeja/tarea/verificar', base(v, opcion='false', justificacion='Falta detalle MIG')))
    c = [t for t in tareas() if t['name'] == 'Confecciona informe servicio'][0]['id']
    print('informe editado', informe(c, tareas=[{'tarea': 'Solo una'}], operarios=[], evidencias=[], horometro_fin='130'))
    e = estado(); print(json.dumps(e, ensure_ascii=False))
    print('tareas final', [(t['name'], t.get('assigned_id')) for t in tareas()])
finally:
    p.sql("DELETE FROM orden_servicio_adjuntos WHERE id_orden IN (SELECT id_orden FROM orden_servicio WHERE id_ot=%s)" % o)
    p.sql("DELETE FROM deta_ordenservicio WHERE id_ordenservicio IN (SELECT id_orden FROM orden_servicio WHERE id_ot=%s)" % o)
    p.sql("DELETE FROM asignausuario WHERE id_orden IN (SELECT id_orden FROM orden_servicio WHERE id_ot=%s)" % o)
    p.sql("DELETE FROM orden_servicio WHERE id_ot=%s" % o)
    p.sql("DELETE FROM case_empresa WHERE case_id=%s" % caso)
    p.sql("DELETE FROM orden_trabajo WHERE id_orden=%s" % o)
    for ruta in ('/API/bpm/case/%s' % caso, '/API/bpm/archivedCase/%s' % caso):
        get(ruta, 'DELETE')
    print('limpio; caso', caso)
