#!/usr/bin/env python3
"""
Prueba de paridad asset vs MAN (nivel 1 de doctest/MIGRACION-ASSETPLANNER.md §3.4).

La misma operación se hace en asset y en MAN, con el mismo usuario y la misma empresa, contra la
misma base (assetv2 de DEV). Consultas: se compara la respuesta normalizada. Altas, cambios y bajas:
se compara la respuesta y el estado que queda en la base. Los datos de prueba llevan el prefijo
"MIG paridad" y se borran al terminar.

Corre en LOCAL con la VPN (necesita asset, tools/DNATO y la MariaDB de DEV). No va en GitHub Actions.

Variables de entorno:
  MAN_ENV_FILE    archivo .env con DOCTEST_LOCAL_MAN_USER / DOCTEST_LOCAL_MAN_PASS
  MAN_MYCNF       archivo de opciones de mysql (host/usuario/clave de assetv2), para leer y limpiar
  ASSET_URL       default http://traz-comp.local/traz-prod-assetplanner/index.php/
  TOOLS_URL       default http://traz-comp.local/traz-v3/traz-tools/
  DNATO_URL       default http://traz-comp.local/traz-comp-dnato/

Uso: python3 tests/paridad/paridad.py <pantalla> [<pantalla>...]   (hoy: area)
"""
import html
import http.cookiejar
import json
import os
import re
import subprocess
import sys
import urllib.parse
import urllib.request

ASSET = os.environ.get('ASSET_URL', 'http://traz-comp.local/traz-prod-assetplanner/index.php/')
TOOLS = os.environ.get('TOOLS_URL', 'http://traz-comp.local/traz-v3/traz-tools/')
DNATO = os.environ.get('DNATO_URL', 'http://traz-comp.local/traz-comp-dnato/')
MAN = TOOLS + 'traz-tools-man/'
PREFIJO = 'MIG paridad'

fallas = []


def leer_env(ruta):
    vals = {}
    with open(ruta, encoding='utf-8') as f:
        for linea in f:
            m = re.match(r'^([A-Z0-9_]+)=(.*)$', linea.strip())
            if m:
                vals[m.group(1)] = m.group(2)
    return vals


class Sesion:
    def __init__(self):
        self.op = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(http.cookiejar.CookieJar()))

    def get(self, url):
        return self.op.open(url, timeout=60).read().decode('utf-8', 'replace')

    def post(self, url, datos):
        cuerpo = urllib.parse.urlencode(datos).encode()
        return self.op.open(url, cuerpo, timeout=60).read().decode('utf-8', 'replace')


def sesion_asset(usr, pas):
    s = Sesion()
    s.get(ASSET.replace('index.php/', ''))
    if s.post(ASSET + 'login/sessionStart_', {'usr': usr, 'pas': pas}).strip() != '1':
        sys.exit('No se pudo iniciar sesión en asset')
    return s


def sesion_tools(usr, pas):
    s = Sesion()
    s.get(DNATO + 'main/login')
    s.post(DNATO + 'main/login/', {'email': usr, 'password': pas})
    if 'TRAZALOG | TOOLS' not in s.get(TOOLS):
        sys.exit('No se pudo iniciar sesión en tools')
    return s


def sql(consulta):
    r = subprocess.run(['mysql', '--defaults-file=' + os.environ['MAN_MYCNF'], '-N', '-B', '-e', consulta],
                       capture_output=True, text=True, check=True)
    return [linea.split('\t') for linea in r.stdout.strip().split('\n') if linea]


def celdas_tabla(pagina):
    """Filas de la tabla principal de una pantalla, como listas de textos de celda (sin acciones)."""
    filas = []
    for tr in re.findall(r'<tr[^>]*>(.*?)</tr>', pagina, re.S):
        tds = re.findall(r'<td[^>]*>(.*?)</td>', tr, re.S)
        if tds:
            filas.append([html.unescape(re.sub(r'<[^>]+>', '', t)).strip() for t in tds])
    return sorted(filas)


def comparar(nombre, a, m, nota=''):
    ok = a == m
    print(('OK   ' if ok else 'FALLA') + ' | ' + nombre + (' — ' + nota if nota else ''))
    if not ok:
        print('       asset: ' + json.dumps(a, ensure_ascii=False)[:400])
        print('       MAN  : ' + json.dumps(m, ensure_ascii=False)[:400])
        fallas.append(nombre)


def jload(texto):
    try:
        return json.loads(texto)
    except ValueError:
        return {'_no_json': texto.strip()[:200]}


# --------------------------------------------------------------------------------------------- área
def paridad_area(asset, man, emp_asset):
    pa = 'area/'
    pm = MAN + 'Area/'

    comparar('area · listado', celdas_tabla(asset.get(ASSET + pa + 'index/Add-Edit-Del')),
             celdas_tabla(man.get(pm + 'index/Add-Edit-Del')))

    id_existente = sql("SELECT id_area FROM area WHERE estado='AC' AND id_empresa=%d LIMIT 1" % emp_asset)[0][0]
    comparar('area · obtener (propia)', jload(asset.post(ASSET + pa + 'Obtener_area', {'id_area': id_existente})),
             jload(man.post(pm + 'Obtener_area', {'id_area': id_existente})))

    # Alta: cada lado crea la suya; se compara respuesta y fila resultante (salvo id y descripción).
    ra = jload(asset.post(ASSET + pa + 'Guardar_area', {'descripcion': PREFIJO + ' A', 'id_empresa': emp_asset}))
    rm = jload(man.post(pm + 'Guardar_area', {'descripcion': PREFIJO + ' M', 'id_empresa': emp_asset}))
    comparar('area · alta (respuesta)', ra, rm)
    fa = sql("SELECT id_area, id_empresa, estado FROM area WHERE descripcion='%s A' ORDER BY id_area DESC LIMIT 1" % PREFIJO)
    fm = sql("SELECT id_area, id_empresa, estado FROM area WHERE descripcion='%s M' ORDER BY id_area DESC LIMIT 1" % PREFIJO)
    comparar('area · alta (fila en la base)', [r[1:] for r in fa], [r[1:] for r in fm])
    ida, idm = fa[0][0], fm[0][0]

    ra = jload(asset.post(ASSET + pa + 'Modificar_area', {'id_area': ida, 'descripcion': PREFIJO + ' A2', 'id_empresa': emp_asset}))
    rm = jload(man.post(pm + 'Modificar_area', {'id_area': idm, 'descripcion': PREFIJO + ' M2', 'id_empresa': emp_asset}))
    comparar('area · modificación (respuesta)', ra, rm)
    comparar('area · modificación (fila en la base)',
             [r[1:] for r in sql("SELECT descripcion, id_empresa, estado FROM area WHERE id_area=%s" % ida)],
             [r[1:] for r in sql("SELECT descripcion, id_empresa, estado FROM area WHERE id_area=%s" % idm)])

    ra = jload(asset.post(ASSET + pa + 'Eliminar_area', {'id_area': ida}))
    rm = jload(man.post(pm + 'Eliminar_area', {'id_area': idm}))
    comparar('area · baja (respuesta)', ra, rm)
    comparar('area · baja (fila en la base)',
             sql("SELECT id_empresa, estado FROM area WHERE id_area=%s" % ida),
             sql("SELECT id_empresa, estado FROM area WHERE id_area=%s" % idm))

    # Aislamiento: un área de otra empresa. asset la devuelve (bug T1 heredado); MAN no debe.
    ajena = sql("SELECT id_area FROM area WHERE id_empresa<>%d LIMIT 1" % emp_asset)
    if ajena:
        rm = jload(man.post(pm + 'Obtener_area', {'id_area': ajena[0][0]}))
        comparar('area · aislamiento: MAN no ve áreas de otra empresa', None, rm,
                 'diferencia buscada con asset, que sí la devuelve')
        antes = sql("SELECT descripcion, estado FROM area WHERE id_area=%s" % ajena[0][0])
        man.post(pm + 'Modificar_area', {'id_area': ajena[0][0], 'descripcion': PREFIJO + ' HACK'})
        man.post(pm + 'Eliminar_area', {'id_area': ajena[0][0]})
        comparar('area · aislamiento: MAN no modifica ni da de baja áreas de otra empresa', antes,
                 sql("SELECT descripcion, estado FROM area WHERE id_area=%s" % ajena[0][0]))

    sql("DELETE FROM area WHERE descripcion LIKE '%s%%'" % PREFIJO)


PANTALLAS = {'area': paridad_area}

if __name__ == '__main__':
    env = leer_env(os.environ['MAN_ENV_FILE'])
    usr, pas = env['DOCTEST_LOCAL_MAN_USER'], env['DOCTEST_LOCAL_MAN_PASS']
    asset, man = sesion_asset(usr, pas), sesion_tools(usr, pas)
    emp = int(sql("SELECT u.empresaid FROM usuarioasempresa u JOIN sisusers s ON s.usrId=u.usrId "
                  "WHERE s.usrNick='%s' AND u.tipo=1 LIMIT 1" % usr)[0][0])
    for p in (sys.argv[1:] or PANTALLAS.keys()):
        PANTALLAS[p](asset, man, emp)
    print('\n%s' % ('PARIDAD OK' if not fallas else 'PARIDAD CON FALLAS: ' + ', '.join(fallas)))
    sys.exit(1 if fallas else 0)
