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

Uso: python3 tests/paridad/paridad.py [<pantalla>...]   (sin argumentos: todas; ver PANTALLAS)
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


# ------------------------------------------------------------------ ABM simple (descripción + estado)
def paridad_abm(asset, man, emp_asset, nombre, tabla, pk):
    """ABM con la forma de Área: index, Obtener_x, Guardar_x, Modificar_x y Eliminar_x (baja lógica AN).
    nombre: el del controller en minúscula (area, proceso). tabla y pk: los de assetv2."""
    Nom = nombre.capitalize()
    pa = ASSET + nombre + '/'
    pm = MAN + Nom + '/'

    comparar(nombre + ' · listado', celdas_tabla(asset.get(pa + 'index/Add-Edit-Del')),
             celdas_tabla(man.get(pm + 'index/Add-Edit-Del')))

    propio = sql("SELECT %s FROM %s WHERE estado='AC' AND id_empresa=%d LIMIT 1" % (pk, tabla, emp_asset))
    if propio:
        comparar(nombre + ' · obtener (propio)', jload(asset.post(pa + 'Obtener_' + nombre, {pk: propio[0][0]})),
                 jload(man.post(pm + 'Obtener_' + nombre, {pk: propio[0][0]})))

    # Alta: cada lado crea el suyo; se compara respuesta y fila resultante (salvo id y descripción).
    ra = jload(asset.post(pa + 'Guardar_' + nombre, {'descripcion': PREFIJO + ' A', 'id_empresa': emp_asset}))
    rm = jload(man.post(pm + 'Guardar_' + nombre, {'descripcion': PREFIJO + ' M', 'id_empresa': emp_asset}))
    comparar(nombre + ' · alta (respuesta)', ra, rm)
    fila = "SELECT %s, id_empresa, estado FROM %s WHERE descripcion='%%s' ORDER BY %s DESC LIMIT 1" % (pk, tabla, pk)
    fa, fm = sql(fila % (PREFIJO + ' A')), sql(fila % (PREFIJO + ' M'))
    comparar(nombre + ' · alta (fila en la base)', [r[1:] for r in fa], [r[1:] for r in fm])
    ida, idm = fa[0][0], fm[0][0]

    ra = jload(asset.post(pa + 'Modificar_' + nombre, {pk: ida, 'descripcion': PREFIJO + ' A2', 'id_empresa': emp_asset}))
    rm = jload(man.post(pm + 'Modificar_' + nombre, {pk: idm, 'descripcion': PREFIJO + ' M2', 'id_empresa': emp_asset}))
    comparar(nombre + ' · modificación (respuesta)', ra, rm)
    fila = "SELECT descripcion, id_empresa, estado FROM %s WHERE %s=%%s" % (tabla, pk)
    comparar(nombre + ' · modificación (fila en la base)', [r[1:] for r in sql(fila % ida)], [r[1:] for r in sql(fila % idm)])

    ra = jload(asset.post(pa + 'Eliminar_' + nombre, {pk: ida}))
    rm = jload(man.post(pm + 'Eliminar_' + nombre, {pk: idm}))
    comparar(nombre + ' · baja (respuesta)', ra, rm)
    comparar(nombre + ' · baja (fila en la base)', [r[1:] for r in sql(fila % ida)], [r[1:] for r in sql(fila % idm)])

    # Aislamiento: un registro de otra empresa. MAN no lo ve, no lo modifica y no lo da de baja.
    ajeno = sql("SELECT %s FROM %s WHERE id_empresa<>%d LIMIT 1" % (pk, tabla, emp_asset))
    if ajeno:
        comparar(nombre + ' · aislamiento: MAN no ve registros de otra empresa', None,
                 jload(man.post(pm + 'Obtener_' + nombre, {pk: ajeno[0][0]})))
        antes = sql(fila % ajeno[0][0])
        man.post(pm + 'Modificar_' + nombre, {pk: ajeno[0][0], 'descripcion': PREFIJO + ' HACK'})
        man.post(pm + 'Eliminar_' + nombre, {pk: ajeno[0][0]})
        comparar(nombre + ' · aislamiento: MAN no modifica ni da de baja registros de otra empresa', antes,
                 sql(fila % ajeno[0][0]))

    sql("DELETE FROM %s WHERE descripcion LIKE '%s%%'" % (tabla, PREFIJO))


# ------------------------------------------------------------------------------------- parámetros
def sin_prueba(lista):
    """Saca de una respuesta de lista las filas de prueba del otro lado, para comparar el resto.
    false (lo que devuelven asset y MAN cuando el equipo queda sin filas) cuenta como lista vacía: el
    orden de la prueba hace que a un lado le queden filas del otro y al otro no."""
    if lista is False:
        return []
    if not isinstance(lista, list):
        return lista
    return sorted([f for f in lista if not str(f.get('paramdescrip', '')).startswith(PREFIJO)],
                  key=lambda f: json.dumps(f, sort_keys=True))


def paridad_parametro(asset, man, emp_asset):
    pa = ASSET + 'Parametro/'
    pm = MAN + 'Parametro/'

    comparar('parametro · equipos de la empresa', jload(asset.get(pa + 'getequipo')), jload(man.get(pm + 'getequipo')))
    comparar('parametro · catálogo de parámetros', jload(asset.get(pa + 'traerparametro')),
             jload(man.get(pm + 'traerparametro')))

    # Alta de parámetro en el catálogo: cada lado crea el suyo.
    ra = jload(asset.post(pa + 'guardar', {'data[paramdescrip]': PREFIJO + ' A'}))
    rm = jload(man.post(pm + 'guardar', {'data[paramdescrip]': PREFIJO + ' M'}))
    comparar('parametro · alta en catálogo (tipo de respuesta)', type(ra).__name__, type(rm).__name__)
    fila = "SELECT paramId, id_empresa, estado, min FROM parametros WHERE paramdescrip='%s'"
    fa, fm = sql(fila % (PREFIJO + ' A')), sql(fila % (PREFIJO + ' M'))
    comparar('parametro · alta en catálogo (fila en la base)', [r[1:] for r in fa], [r[1:] for r in fm])
    comparar('parametro · alta en catálogo (devuelve el id creado)', [str(ra), str(rm)], [fa[0][0], fm[0][0]])
    pid_a, pid_m = fa[0][0], fm[0][0]

    equipo = sql("SELECT id_equipo FROM equipos WHERE estado='AC' AND id_empresa=%d ORDER BY id_equipo LIMIT 1" % emp_asset)[0][0]

    # Asociar al equipo: cada lado asocia su parámetro. La respuesta es la lista del equipo.
    ra = jload(asset.post(pa + 'guardar_todo', {'data[id_equipo]': equipo, 'data[id_parametro]': pid_a,
                                               'data[maximo]': '10', 'data[minimo]': '2.5'}))
    rm = jload(man.post(pm + 'guardar_todo', {'data[id_equipo]': equipo, 'data[id_parametro]': pid_m,
                                             'data[maximo]': '10', 'data[minimo]': '2.5'}))
    comparar('parametro · asociar (lista sin filas de prueba)', sin_prueba(ra), sin_prueba(rm))
    fila = "SELECT id_equipo, CAST(maximo AS CHAR), CAST(minimo AS CHAR), id_empresa FROM setupparam WHERE id_parametro=%s"
    comparar('parametro · asociar (fila en la base)', sql(fila % pid_a), sql(fila % pid_m))
    propia = lambda lista, pid: [f for f in (lista if isinstance(lista, list) else []) if f.get('id_parametro') == pid]
    comparar('parametro · asociar (la lista trae la fila nueva)',
             [{k: v for k, v in f.items() if k != 'id_parametro' and k != 'paramdescrip'} for f in propia(ra, pid_a)],
             [{k: v for k, v in f.items() if k != 'id_parametro' and k != 'paramdescrip'} for f in propia(rm, pid_m)])

    comparar('parametro · listar parámetros del equipo', sin_prueba(jload(asset.post(pa + 'getparametros', {'id_equipo': equipo}))),
             sin_prueba(jload(man.post(pm + 'getparametros', {'id_equipo': equipo}))))

    # Traer para editar: la vista manda fila (parent) y el array de ids (comp).
    ea = jload(asset.get(pa + 'editar?' + urllib.parse.urlencode({'e': equipo, 'parent': 0, 'comp[0]': pid_a})))
    em = jload(man.get(pm + 'editar?' + urllib.parse.urlencode({'e': equipo, 'parent': 0, 'comp[0]': pid_m})))
    sin_ids = lambda r: [{k: v for k, v in f.items() if k not in ('id_parametro', 'paramdescrip')} for f in r.get('datos', [])] if isinstance(r, dict) else r
    comparar('parametro · traer para editar', sin_ids(ea), sin_ids(em))

    ra = jload(asset.post(pa + 'guardarmodif', {'datos[id_equipo]': equipo, 'datos[id_parametro]': pid_a,
                                               'datos[maximo]': '20', 'datos[minimo]': '1'}))
    rm = jload(man.post(pm + 'guardarmodif', {'datos[id_equipo]': equipo, 'datos[id_parametro]': pid_m,
                                             'datos[maximo]': '20', 'datos[minimo]': '1'}))
    comparar('parametro · modificar (lista sin filas de prueba)', sin_prueba(ra), sin_prueba(rm))
    comparar('parametro · modificar (fila en la base)', sql(fila % pid_a), sql(fila % pid_m))

    ra = jload(asset.post(pa + 'eliminar', {'id_equipoElim': equipo, 'id_parametroElim': pid_a}))
    rm = jload(man.post(pm + 'eliminar', {'id_equipoElim': equipo, 'id_parametroElim': pid_m}))
    comparar('parametro · quitar asociación (lista sin filas de prueba)', sin_prueba(ra), sin_prueba(rm))
    comparar('parametro · quitar asociación (fila en la base)', sql(fila % pid_a), sql(fila % pid_m))

    # Aislamiento: equipo de otra empresa con parámetros asociados.
    ajeno = sql("SELECT s.id_equipo, s.id_parametro FROM setupparam s JOIN equipos e ON e.id_equipo=s.id_equipo "
                "WHERE e.id_empresa<>%d LIMIT 1" % emp_asset)
    if ajeno:
        eq, par = ajeno[0]
        comparar('parametro · aislamiento: MAN no lista parámetros de equipos ajenos', {'_no_json': 'nada'},
                 jload(man.post(pm + 'getparametros', {'id_equipo': eq})))
        antes = sql("SELECT CAST(maximo AS CHAR), CAST(minimo AS CHAR) FROM setupparam WHERE id_equipo=%s AND id_parametro=%s" % (eq, par))
        man.post(pm + 'guardarmodif', {'datos[id_equipo]': eq, 'datos[id_parametro]': par, 'datos[maximo]': '999', 'datos[minimo]': '999'})
        man.post(pm + 'eliminar', {'id_equipoElim': eq, 'id_parametroElim': par})
        comparar('parametro · aislamiento: MAN no modifica ni quita asociaciones ajenas', antes,
                 sql("SELECT CAST(maximo AS CHAR), CAST(minimo AS CHAR) FROM setupparam WHERE id_equipo=%s AND id_parametro=%s" % (eq, par)))
        man.post(pm + 'guardar_todo', {'data[id_equipo]': eq, 'data[id_parametro]': pid_m, 'data[maximo]': '1', 'data[minimo]': '0'})
        comparar('parametro · aislamiento: MAN no asocia parámetros a equipos ajenos', [],
                 sql("SELECT id_equipo FROM setupparam WHERE id_parametro=%s" % pid_m))

    sql("DELETE FROM setupparam WHERE id_parametro IN (%s, %s)" % (pid_a, pid_m))
    sql("DELETE FROM parametros WHERE paramdescrip LIKE '%s%%'" % PREFIJO)


PANTALLAS = {
    'area': lambda a, m, e: paridad_abm(a, m, e, 'area', 'area', 'id_area'),
    'proceso': lambda a, m, e: paridad_abm(a, m, e, 'proceso', 'proceso', 'id_proceso'),
    'parametro': paridad_parametro,
}

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
