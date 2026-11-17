"""Inicio de OT para cada tipo de origen (2 solicitud, 3 preventivo, 4 backlog con su solicitud): estados y restauración."""
import json, sys, urllib.request
import os
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
import paridad as p
def post(ruta, cuerpo):
    req = urllib.request.Request('http://localhost:8290/tools/man/' + ruta, json.dumps(cuerpo).encode(), {'Content-Type': 'application/json', 'Accept': 'application/json'})
    try: return json.loads(urllib.request.urlopen(req, timeout=60).read())
    except urllib.error.HTTPError as e: return {'_http': e.code, 'cuerpo': e.read().decode()[:200]}
sr = p.sql("SELECT id_solicitud, estado FROM solicitud_reparacion WHERE id_empresa=1 LIMIT 1")[0]
pv = p.sql("SELECT prevId, estadoprev FROM preventivo WHERE id_empresa=1 LIMIT 1")[0]
bk = p.sql("SELECT backId, estado, IFNULL(sore_id, 'NULL') FROM tbl_back WHERE id_empresa=1 LIMIT 1")[0]
p.sql("UPDATE tbl_back SET sore_id=%s WHERE backId=%s" % (sr[0], bk[0]))
bk = bk + [sr[1]]
print('antes', sr, pv, bk)
ids = []
p.sql("UPDATE preventivo SET estadoprev='S' WHERE prevId=%s" % pv[0]); p.sql("UPDATE tbl_back SET estado='S' WHERE backId=%s" % bk[0])
try:
    for tipo, sol in (('2', sr[0]), ('3', pv[0]), ('4', bk[0])):
        if tipo == '4': p.sql("UPDATE solicitud_reparacion SET estado='S' WHERE id_solicitud=%s" % sr[0])
        p.sql("INSERT INTO orden_trabajo (tipo, id_solicitud, id_empresa, estado, id_equipo, descripcion) VALUES ('%s', %s, 1, 'PL', 130, 'MIG inicio tipo')" % (tipo, sol))
        o = p.sql("SELECT MAX(id_orden) FROM orden_trabajo WHERE descripcion='MIG inicio tipo'")[0][0]; ids.append(o)
        print('tipo', tipo, post('bandeja/ot/inicio', {'id_empresa': '1', 'id_ot': o, 'lat': '1', 'lon': '2', 'fecha': '2026-10-29 10:00:00'}),
              p.sql("SELECT estado, CAST(fecha_inicio AS CHAR) FROM orden_trabajo WHERE id_orden=%s" % o))
    print('después', p.sql("SELECT estado FROM solicitud_reparacion WHERE id_solicitud=%s" % sr[0]), p.sql("SELECT estadoprev FROM preventivo WHERE prevId=%s" % pv[0]),
          p.sql("SELECT estado FROM tbl_back WHERE backId=%s" % bk[0]), p.sql("SELECT estado FROM solicitud_reparacion WHERE id_solicitud=%s" % sr[0]))
finally:
    p.sql("DELETE FROM orden_trabajo WHERE descripcion='MIG inicio tipo'")
    p.sql("UPDATE solicitud_reparacion SET estado='%s' WHERE id_solicitud=%s" % (sr[1], sr[0]))
    p.sql("UPDATE preventivo SET estadoprev='%s' WHERE prevId=%s" % (pv[1], pv[0]))
    p.sql("UPDATE tbl_back SET estado='%s' WHERE backId=%s" % (bk[1], bk[0]))
    p.sql("UPDATE tbl_back SET sore_id=%s WHERE backId=%s" % (bk[2], bk[0]))
    print('restaurado', p.sql("SELECT estado FROM solicitud_reparacion WHERE id_solicitud=%s" % sr[0]))
