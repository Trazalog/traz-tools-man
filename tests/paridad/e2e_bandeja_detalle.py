"""Prueba directa del detalle de la tarea (toolsMANAPI /bandeja/tarea/detalle, linea, comentarios, comentar) con un caso real; limpia todo al final."""
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
r = post('ot', {'ot': {'id_tarea': '246', 'fecha_program': '2026-11-01', 'fecha_entrega': '2026-11-05', 'descripcion': 'MIG detalle bandeja',
                       'id_usuario': '213', 'id_sucursal': '0', 'id_proveedor': '0', 'id_equipo': eq, 'id_empresa': '1'},
                'herramientas': [], 'insumos': [], 'session': '', 'emprId': '1'})
print('alta', r)
o, caso = r['id'], r['caseId']
def base(t, **x):
    d = {'usrNick': NICK, 'id_empresa': '1', 'session': '', 'id_tarea': t}
    d.update(x); return d
try:
    ts = get('/API/bpm/humanTask?p=0&c=10&f=caseId%%3D%s' % caso)
    t = ts[0]['id']; print('tarea', ts[0]['name'])
    d = post('bandeja/tarea/detalle', base(t)); print('detalle', json.dumps(d)[:600])
    print('detalle otra empresa', post('bandeja/tarea/detalle', base(t, id_empresa='2')))
    print('linea', json.dumps(post('bandeja/tarea/linea', base(t)))[:400])
    print('comentar', post('bandeja/tarea/comentar', base(t, contenido='Prueba "comillas" y <b>html</b>\ncon salto \\ barra')))
    c = post('bandeja/tarea/comentarios', base(t)); print('comentarios', json.dumps(c)[:700])
    print('comentar otra empresa', post('bandeja/tarea/comentar', base(t, id_empresa='2', contenido='no')))
finally:
    p.sql("DELETE FROM case_empresa WHERE case_id=%s" % caso)
    p.sql("DELETE FROM orden_trabajo WHERE id_orden=%s" % o)
    for ruta in ('/API/bpm/case/%s' % caso, '/API/bpm/archivedCase/%s' % caso):
        get(ruta, 'DELETE')
    print('limpio; caso', caso)
