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
import urllib.error
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

    def abrir(self, req, cuerpo=None):
        """Devuelve el cuerpo de la respuesta; ante un error HTTP, '<HTTP nnn>' (para poder compararlo)."""
        try:
            return self.op.open(req, cuerpo, timeout=60).read().decode('utf-8', 'replace')
        except urllib.error.HTTPError as e:
            return '<HTTP %d>' % e.code

    def get(self, url):
        return self.abrir(url)

    def post(self, url, datos):
        return self.abrir(url, urllib.parse.urlencode(datos).encode())

    def post_archivo(self, url, datos, campo, nombre, contenido, otros=()):
        """POST multipart/form-data con un archivo (como un FormData del navegador). otros: más
        archivos, como tuplas (campo, nombre, contenido)."""
        limite = '----paridad' + os.urandom(8).hex()
        partes = []
        for k, v in datos.items():
            partes.append(('--%s\r\nContent-Disposition: form-data; name="%s"\r\n\r\n%s\r\n' % (limite, k, v)).encode())
        for c, n, b in ((campo, nombre, contenido),) + tuple(otros):
            partes.append(('--%s\r\nContent-Disposition: form-data; name="%s"; filename="%s"\r\n'
                           'Content-Type: application/pdf\r\n\r\n' % (limite, c, n)).encode() + b + b'\r\n')
        partes.append(('--%s--\r\n' % limite).encode())
        req = urllib.request.Request(url, b''.join(partes), {'Content-Type': 'multipart/form-data; boundary=' + limite})
        return self.abrir(req)


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
                       capture_output=True, text=True, errors='replace', check=True)
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


# ------------------------------------------------------------------------------------ componentes
PDF_PRUEBA = b'%PDF-1.4\n1 0 obj<<>>endobj\ntrailer<<>>\n%%EOF\n'


def paridad_componente(asset, man, emp_asset):
    pa = ASSET + 'Componente/'
    pm = MAN + 'Componente/'
    norm_pdf = lambda t: re.sub(r'\d+_\d*_[\d-]+', 'ID_EMP_FECHA', t)

    comparar('componente · catálogo (pantalla)', celdas_tabla(asset.get(pa + 'index/Add-Edit-Del')),
             celdas_tabla(man.get(pm + 'index/Add-Edit-Del')))
    comparar('componente · asignados a equipos (pantalla)', celdas_tabla(asset.get(pa + 'asigna/Add-Edit-Del')),
             celdas_tabla(man.get(pm + 'asigna/Add-Edit-Del')))
    for accion in ('traerequipo', 'getmarca', 'getcomponente', 'getsistema'):
        comparar('componente · ' + accion, jload(asset.get(pa + accion)), jload(man.get(pm + accion)))

    marca = sql("SELECT marcaid FROM marcasequipos WHERE id_empresa=%d AND estado='AC' ORDER BY marcaid LIMIT 1" % emp_asset)[0][0]
    sistema = sql("SELECT sistemaid FROM sistema WHERE id_empresa=%d AND estado='AC' ORDER BY sistemaid LIMIT 1" % emp_asset)[0][0]
    equipo = sql("SELECT id_equipo FROM equipos WHERE estado='AC' AND id_empresa=%d ORDER BY id_equipo LIMIT 1" % emp_asset)[0][0]

    # Alta en el catálogo con archivo (FormData). Asset deja la empresa vacía en el nombre del archivo; MAN la pone.
    ra = jload(asset.post_archivo(pa + 'agregarComponente', {'descrip1': PREFIJO + ' A', 'info': 'info', 'ma': marca},
                                  'inputPDF', 'prueba.pdf', PDF_PRUEBA))
    rm = jload(man.post_archivo(pm + 'agregarComponente', {'descrip1': PREFIJO + ' M', 'info': 'info', 'ma': marca},
                                'inputPDF', 'prueba.pdf', PDF_PRUEBA))
    comparar('componente · alta con archivo (respuesta)', ra, rm)
    fila = "SELECT id_componente, id_equipo, informacion, marcaid, estado, id_empresa, pdf FROM componentes WHERE descripcion='%s'"
    fa, fm = sql(fila % (PREFIJO + ' A')), sql(fila % (PREFIJO + ' M'))
    comparar('componente · alta con archivo (fila en la base)', [r[1:6] for r in fa], [r[1:6] for r in fm])
    cid_a, cid_m = fa[0][0], fm[0][0]
    comparar('componente · alta con archivo (nombre del archivo, sin id/empresa/fecha)',
             norm_pdf(fa[0][6]), norm_pdf(fm[0][6]), 'MAN agrega la empresa al nombre')
    carpeta = os.environ.get('ASSET_FILES_DIR', '/mnt/win/dev/git/traz-prod-assetplanner/assets/files/equipos/')
    comparar('componente · alta con archivo (el archivo quedó en la carpeta de asset)',
             [True, True], [os.path.exists(carpeta + fa[0][6]), os.path.exists(carpeta + fm[0][6])])

    ra = jload(asset.post(pa + 'editarComponente', {'idComponenteE': cid_a, 'descripcionE': PREFIJO + ' A2', 'informacionE': 'info2', 'marcaE': marca}))
    rm = jload(man.post(pm + 'editarComponente', {'idComponenteE': cid_m, 'descripcionE': PREFIJO + ' M2', 'informacionE': 'info2', 'marcaE': marca}))
    comparar('componente · editar sin archivo (respuesta)', ra, rm)
    fila_id = "SELECT informacion, marcaid, estado, id_empresa FROM componentes WHERE id_componente=%s"
    comparar('componente · editar sin archivo (fila en la base)', sql(fila_id % cid_a), sql(fila_id % cid_m))

    # Asignar al equipo (la vista manda arrays desde 1). La respuesta de asset trae un dump de depuración; MAN no.
    asset.post(pa + 'guardar_componente', {'idequipo': equipo, 'comp[1]': cid_a, 'codigo[1]': 'MIGA', 'sistemaid[1]': sistema, 'x': 1, 'ge': ''})
    man.post(pm + 'guardar_componente', {'idequipo': equipo, 'comp[1]': cid_m, 'codigo[1]': 'MIGM', 'sistemaid[1]': sistema, 'x': 1, 'ge': ''})
    asoc = "SELECT idcomponenteequipo, id_equipo, estado, sistemaid, id_empresa FROM componenteequipo WHERE id_componente=%s"
    aa, am = sql(asoc % cid_a), sql(asoc % cid_m)
    comparar('componente · asignar a equipo (fila en la base)', [r[1:] for r in aa], [r[1:] for r in am])
    sin_comp_prueba = lambda l: sorted([f for f in (l if isinstance(l, list) else []) if f.get('id_componente') not in (cid_a, cid_m)],
                                       key=lambda f: json.dumps(f, sort_keys=True))
    comparar('componente · componentes del equipo', sin_comp_prueba(jload(asset.post(pa + 'getcompo', {'idequipo': equipo}))),
             sin_comp_prueba(jload(man.post(pm + 'getcompo', {'idequipo': equipo}))))
    quitar = lambda d, ks: {k: v for k, v in d.items() if k not in ks} if isinstance(d, dict) else d
    comparar('componente · traer asignación para editar',
             quitar(jload(asset.post(pa + 'getEditar', {'idCompEq': aa[0][0]})), ('idcomponenteequipo', 'id_componente', 'codigo')),
             quitar(jload(man.post(pm + 'getEditar', {'idCompEq': am[0][0]})), ('idcomponenteequipo', 'id_componente', 'codigo')))
    ra = jload(asset.post(pa + 'editarCompEq', {'idCompEq': aa[0][0], 'data[id_equipo]': equipo, 'data[id_componente]': cid_a, 'data[codigo]': 'MIG2'}))
    rm = jload(man.post(pm + 'editarCompEq', {'idCompEq': am[0][0], 'data[id_equipo]': equipo, 'data[id_componente]': cid_m, 'data[codigo]': 'MIG2'}))
    comparar('componente · editar asignación (respuesta)', ra, rm)
    asoc2 = "SELECT id_equipo, codigo, estado FROM componenteequipo WHERE idcomponenteequipo=%s"
    comparar('componente · editar asignación (fila en la base)', sql(asoc2 % aa[0][0]), sql(asoc2 % am[0][0]))
    comparar('componente · quitar asignación (respuesta)', asset.post(pa + 'baja_comp', {'idequipo': equipo, 'datos': cid_a}).strip(),
             man.post(pm + 'baja_comp', {'idequipo': equipo, 'datos': cid_m}).strip())
    comparar('componente · quitar asignación (fila en la base)', sql(asoc2 % aa[0][0]), sql(asoc2 % am[0][0]))

    comparar('componente · baja del catálogo (respuesta)', jload(asset.post(pa + 'bajaComponente', {'idComponente': cid_a})),
             jload(man.post(pm + 'bajaComponente', {'idComponente': cid_m})))
    comparar('componente · baja del catálogo (fila en la base)', sql(fila_id % cid_a), sql(fila_id % cid_m))

    # Alta desde la asignación, con el PDF en base64 (agregar_componente).
    import base64
    b64 = base64.b64encode(PDF_PRUEBA).decode()
    ra = jload(asset.post(pa + 'agregar_componente', {'parametros[descripcion]': PREFIJO + ' B64A', 'parametros[informacion]': 'i',
                                                     'parametros[marcaid]': marca, 'parametros[pdf]': b64}))
    rm = jload(man.post(pm + 'agregar_componente', {'parametros[descripcion]': PREFIJO + ' B64M', 'parametros[informacion]': 'i',
                                                   'parametros[marcaid]': marca, 'parametros[pdf]': b64}))
    comparar('componente · alta con PDF en base64 (respuesta)', ra, rm)
    fa, fm = sql(fila % (PREFIJO + ' B64A')), sql(fila % (PREFIJO + ' B64M'))
    comparar('componente · alta con PDF en base64 (fila en la base)', [r[1:6] for r in fa], [r[1:6] for r in fm])

    # Aislamiento con datos de otra empresa.
    ajena = sql("SELECT ce.idcomponenteequipo, ce.id_equipo, ce.id_componente FROM componenteequipo ce "
                "WHERE ce.id_empresa<>%d AND ce.estado='AC' LIMIT 1" % emp_asset)
    if ajena:
        ice, eq, comp = ajena[0]
        comparar('componente · aislamiento: MAN no trae asignaciones ajenas', [], jload(man.post(pm + 'getEditar', {'idCompEq': ice})))
        antes = sql(asoc2 % ice)
        man.post(pm + 'editarCompEq', {'idCompEq': ice, 'data[id_equipo]': eq, 'data[id_componente]': comp, 'data[codigo]': 'HACK'})
        man.post(pm + 'baja_comp', {'idequipo': eq, 'datos': comp})
        comparar('componente · aislamiento: MAN no edita ni quita asignaciones ajenas', antes, sql(asoc2 % ice))
        compajeno = sql("SELECT id_componente FROM componentes WHERE id_empresa<>%d AND estado='AC' LIMIT 1" % emp_asset)[0][0]
        antes = sql(fila_id % compajeno)
        man.post(pm + 'bajaComponente', {'idComponente': compajeno})
        man.post(pm + 'editarComponente', {'idComponenteE': compajeno, 'descripcionE': 'HACK', 'informacionE': 'HACK', 'marcaE': marca})
        comparar('componente · aislamiento: MAN no edita ni da de baja componentes ajenos', antes, sql(fila_id % compajeno))
        man.post(pm + 'guardar_componente', {'idequipo': eq, 'comp[1]': cid_m, 'codigo[1]': 'HACK', 'sistemaid[1]': sistema, 'x': 1})
        comparar('componente · aislamiento: MAN no asigna a equipos ajenos', [], sql("SELECT id_equipo FROM componenteequipo WHERE id_componente=%s AND id_equipo=%s" % (cid_m, eq)))

    for f in sql("SELECT pdf FROM componentes WHERE descripcion LIKE '%s%%' AND pdf IS NOT NULL" % PREFIJO):
        ruta = carpeta + os.path.basename(f[0])
        if os.path.exists(ruta):
            os.remove(ruta)
    sql("DELETE FROM componenteequipo WHERE id_componente IN (SELECT id_componente FROM componentes WHERE descripcion LIKE '%s%%')" % PREFIJO)
    sql("DELETE FROM componentes WHERE descripcion LIKE '%s%%'" % PREFIJO)


def sin_claves(d, claves):
    """Copia de una fila (o lista de filas) sin las claves que cambian de un lado al otro (ids, código)."""
    if isinstance(d, list):
        return sorted([sin_claves(x, claves) for x in d], key=lambda f: json.dumps(f, sort_keys=True))
    if isinstance(d, dict):
        return {k: v for k, v in d.items() if k not in claves}
    return d


def paridad_equipo(asset, man, emp_asset):
    pa = ASSET + 'Equipo/'
    pm = MAN + 'Equipo/'
    carpeta = os.environ.get('ASSET_EQUIPOS_DIR', '/mnt/win/dev/git/traz-prod-assetplanner/assets/filesequipos/')
    limpiar_equipo(carpeta)
    ids = ('id_equipo', 'codigo', 'deeq', 'descripcion')
    es_prueba = lambda f: any(str(v).startswith(PREFIJO) for v in f.values()) if isinstance(f, dict) else False
    lista = lambda t: sin_claves([f for f in t if not es_prueba(f)], ()) if isinstance(t, list) else t

    for accion in ('getarea', 'getproceso', 'getcriti', 'getetapa', 'getgrupo', 'getmarca', 'getcliente', 'getcontra'):
        comparar('equipo · ' + accion, lista(jload(asset.get(pa + accion))), lista(jload(man.get(pm + accion))))

    # Altas rápidas desde el formulario. La respuesta es el id nuevo.
    altas = {}
    for accion, campos, tabla, pk, col in (
            ('agregar_area', {'descripcion': '%s'}, 'area', 'id_area', 'descripcion'),
            ('agregar_proceso', {'descripcion': '%s'}, 'proceso', 'id_proceso', 'descripcion'),
            ('agregar_criti', {'descripcion': '%s'}, 'criticidad', 'id_criti', 'descripcion'),
            ('agregar_etapa', {'descripcion': '%s'}, 'sector', 'id_sector', 'descripcion'),
            ('agregar_grupo', {'descripcion': '%s'}, 'grupo', 'id_grupo', 'descripcion'),
            ('agregar_marca', {'marcadescrip': '%s', 'estado': 'AC'}, 'marcasequipos', 'marcaid', 'marcadescrip'),
            ('agregar_cliente', {'cliName': 'N', 'cliLastName': 'L', 'cliDni': '123456789', 'cliAddress': 'D', 'cliPhone': 'T',
                                 'cliEmail': 'e@x', 'cliRazonSocial': '%s', 'estado': 'AC'}, 'admcustomers', 'cliId', 'cliRazonSocial')):
        par = lambda lado: {'parametros[%s]' % k: (v % (PREFIJO + ' ' + lado) if '%s' in v else v) for k, v in campos.items()}
        ra, rm = asset.post(pa + accion, par('A')).strip(), man.post(pm + accion, par('M')).strip()
        comparar('equipo · %s (devuelve un id)' % accion, ra.isdigit() and int(ra) > 0, rm.isdigit() and int(rm) > 0)
        fila = "SELECT * FROM %s WHERE %s=%%s" % (tabla, pk)
        fa, fm = sql(fila % (ra or 0)), sql(fila % (rm or 0))
        nombre = lambda filas: [[c for c in f if not c.startswith(PREFIJO) and c not in (ra, rm)] for f in filas]
        comparar('equipo · %s (fila en la base)' % accion, nombre(fa), nombre(fm))
        altas[accion] = (ra, rm)

    unidad = str(emp_asset)
    contratista = sql("SELECT id_contratista FROM contratistas WHERE id_empresa=%d ORDER BY id_contratista LIMIT 1" % emp_asset)[0][0]

    def form(lado, codigo):
        i = 0 if lado == 'A' else 1
        return {'codigo': codigo, 'descripcion': PREFIJO + ' ' + lado, 'fecha_ingreso': '2026-01-15', 'fecha_garantia': '',
                'marca': altas['agregar_marca'][i], 'ubicacion': 'Galpón 3', 'etapa': altas['agregar_etapa'][i],
                'grupo': altas['agregar_grupo'][i], 'cliente': altas['agregar_cliente'][i], 'criticidad': altas['agregar_criti'][i],
                'fecha_ultima': '', 'ultima_lectura': '', 'destec': 'técnica', 'unin': unidad, 'area': altas['agregar_area'][i],
                'proceso': altas['agregar_proceso'][i], 'numse': 'NS-1'}

    # Alta de equipo con dos adjuntos. Fecha y lectura vacías prueban la conversión que hacía MariaDB (E13).
    ra = jload(asset.post_archivo(pa + 'guardar_equipo', form('A', PREFIJO + ' A'), 'inputPDF[]', 'manual uno.pdf', PDF_PRUEBA,
                                  [('inputPDF[]', 'manual dos.pdf', PDF_PRUEBA)]))
    rm = jload(man.post_archivo(pm + 'guardar_equipo', form('M', PREFIJO + ' M'), 'inputPDF[]', 'manual uno.pdf', PDF_PRUEBA,
                                [('inputPDF[]', 'manual dos.pdf', PDF_PRUEBA)]))
    comparar('equipo · alta con adjuntos (respuesta)', ra, rm)
    comparar('equipo · alta con código repetido (respuesta)',
             jload(asset.post_archivo(pa + 'guardar_equipo', form('A', PREFIJO + ' A'), 'x', 'x.pdf', b'')),
             jload(man.post_archivo(pm + 'guardar_equipo', form('M', PREFIJO + ' M'), 'x', 'x.pdf', b'')))
    eq_a = sql("SELECT id_equipo FROM equipos WHERE codigo='%s A'" % PREFIJO)[0][0]
    eq_m = sql("SELECT id_equipo FROM equipos WHERE codigo='%s M'" % PREFIJO)[0][0]
    # Las columnas que apuntan a los catálogos de prueba (marca, sector, grupo, cliente, criticidad, área, proceso) difieren por id.
    fila_eq = ("SELECT fecha_ingreso, fecha_baja, fecha_garantia, ubicacion, id_empresa, id_hubicacion, estado, fecha_ultimalectura, "
               "ultima_lectura, tipo_horas, `id-centrodecosto`, valor_reposicion, fecha_reposicion, id_proveedor, valor, comprobante, "
               "descrip_tecnica, id_unidad, numero_serie, adjunto, meta_disponibilidad FROM equipos WHERE id_equipo=%s")
    comparar('equipo · alta (fila en la base)', sql(fila_eq % eq_a), sql(fila_eq % eq_m))
    adj = "SELECT adjunto FROM equipos_archi_adjuntos WHERE id_equipo=%s AND eliminado=0 ORDER BY id_adjunto"
    aa, am = sql(adj % eq_a), sql(adj % eq_m)
    comparar('equipo · alta (adjuntos registrados)', len(aa), len(am))
    comparar('equipo · alta (los adjuntos de MAN están en la carpeta de asset)', [True] * len(am),
             [os.path.exists(carpeta + f[0]) for f in am], 'asset registra mal el nombre del segundo archivo')

    norm_adj = lambda d: re.sub(r'(manual_(uno|dos))\d+(_\d+)?\.pdf', r'\1.pdf', json.dumps(d, sort_keys=True))
    sin_ids_adj = lambda d: re.sub(r'"id_adjunto": "\d+"', '"id_adjunto": "N"', d)
    eda, edm = jload(asset.post(pa + 'getEditar', {'idEquipo': eq_a})), jload(man.post(pm + 'getEditar', {'idEquipo': eq_m}))
    cat = ('id_equipo', 'codigo', 'deeq', 'archivo', 'marcaid', 'marcadescrip', 'id_sector', 'desec', 'id_grupo', 'degr', 'cliId',
           'cliRazonSocial', 'id_criti', 'decri', 'id_area', 'dear', 'id_proceso', 'depro')
    comparar('equipo · traer para editar', sin_claves(eda, cat), sin_claves(edm, cat))
    comparar('equipo · traer para editar (adjuntos)', len(eda[0].get('archivo') or []) if isinstance(eda, list) else eda,
             len(edm[0].get('archivo') or []) if isinstance(edm, list) else edm)

    def pagina(s, url, buscar):
        r = jload(s.post(url + 'paginado', {'draw': 3, 'start': 0, 'length': 100, 'search[value]': buscar}))
        if isinstance(r, dict) and isinstance(r.get('data'), list):
            r['data'] = [f for f in r['data'] if not es_prueba(f)]
        return r
    comparar('equipo · listado paginado', pagina(asset, pa, ''), pagina(man, pm, ''))
    comparar('equipo · listado paginado con búsqueda', pagina(asset, pa, 'a'), pagina(man, pm, 'a'))
    ra = jload(asset.post(pa + 'paginado', {'draw': 1, 'start': 0, 'length': 10, 'search[value]': PREFIJO}))
    rm = jload(man.post(pm + 'paginado', {'draw': 1, 'start': 0, 'length': 10, 'search[value]': PREFIJO}))
    pag = lambda r: (r.get('recordsFiltered'), [sin_claves(f, cat + ('id_unidad', 'deun', 'cliId', 'clie', 'form_id')) for f in r['data']]) \
        if isinstance(r, dict) and 'data' in r else r
    comparar('equipo · listado paginado (equipos de prueba)', pag(ra), pag(rm))

    def editar(s, url, lado, eq):
        i = 0 if lado == 'A' else 1
        datos = {'descripcion': PREFIJO + ' ' + lado + '2', 'fecha_ingreso': '2026-02-01', 'fecha_garantia': '', 'marca': altas['agregar_marca'][i],
                 'codigo': PREFIJO + ' ' + lado, 'ubicacion': 'Planta 2', 'id_sector': altas['agregar_etapa'][i], 'id_grupo': altas['agregar_grupo'][i],
                 'id_area': altas['agregar_area'][i], 'id_proceso': altas['agregar_proceso'][i], 'id_criticidad': altas['agregar_criti'][i],
                 'id_customer': altas['agregar_cliente'][i], 'numero_serie': 'NS-2', 'estado': 'AL', 'fecha_ultimalectura': '2026-02-02',
                 'ultima_lectura': '15', 'descrip_tecnica': 'otra'}
        return s.post(url + 'editar_equipo', dict({'data[%s]' % k: v for k, v in datos.items()}, idEquipo=eq)).strip()
    comparar('equipo · editar (respuesta)', editar(asset, pa, 'A', eq_a), editar(man, pm, 'M', eq_m))
    comparar('equipo · editar (fila en la base)', sql(fila_eq % eq_a), sql(fila_eq % eq_m))

    lect = ("SELECT lectura, usrId, observacion, operario_nom, turno, estado, obs, info_id FROM historial_lecturas "
            "WHERE id_equipo=%s ORDER BY id_lectura")
    estado = "SELECT estado FROM equipos WHERE id_equipo=%s"
    comparar('equipo · inhabilitar (respuesta)', asset.post(pa + 'cambio_equipo', {'idequipo': eq_a}).strip(),
             man.post(pm + 'cambio_equipo', {'idequipo': eq_m}).strip())
    comparar('equipo · inhabilitar (estado e historial)', (sql(estado % eq_a), sql(lect % eq_a)), (sql(estado % eq_m), sql(lect % eq_m)))
    ra, rm = jload(asset.post(pa + 'estado_alta', {'idequipo': eq_a})), jload(man.post(pm + 'estado_alta', {'idequipo': eq_m}))
    comparar('equipo · habilitar (devuelve el id de la lectura)', isinstance(ra, int) and ra > 0, isinstance(rm, int) and rm > 0)
    comparar('equipo · habilitar (estado e historial)', (sql(estado % eq_a), sql(lect % eq_a)), (sql(estado % eq_m), sql(lect % eq_m)))

    # Lecturas. Asset además consulta y puede marcar un preventivo (T1); MAN no (E16): se compara lo que queda guardado.
    prev = "SELECT prevId, estadoprev FROM preventivo ORDER BY prevId"
    prev_antes = sql(prev)
    for valor, est in (('100', 'RE'), ('250', 'AC')):
        asset.post(pa + 'setLectura', {'id_equipo': eq_a, 'lectura': valor, 'operario': 'Op', 'turno': 'T1', 'observacion': 'obs', 'estado': est})
        rm = jload(man.post(pm + 'setLectura', {'id_equipo': eq_m, 'lectura': valor, 'operario': 'Op', 'turno': 'T1', 'observacion': 'obs', 'estado': est}))
        comparar('equipo · lectura %s %s (MAN la guarda)' % (valor, est), True, rm)
        comparar('equipo · lectura %s %s (estado e historial)' % (valor, est), (sql(estado % eq_a), sql(lect % eq_a)),
                 (sql(estado % eq_m), sql(lect % eq_m)))
    comparar('equipo · lectura (MAN no toca preventivos)', prev_antes, sql(prev))
    comparar('equipo · lectura con falla (MAN la rechaza sin guardar, E14)', False,
             jload(man.post(pm + 'setLectura', {'id_equipo': eq_m, 'lectura': '300', 'estado': 'RE', 'falla': 'x'})).get('status'))
    hist = lambda s, url, eq: sin_claves(jload(s.post(url + 'getHistoriaLect', {'idequipo': eq})), ('id_lectura', 'id_equipo', 'fecha'))
    comparar('equipo · historial de lecturas', hist(asset, pa, eq_a), hist(man, pm, eq_m))
    ult = lambda s, url, eq: sin_claves(jload(s.post(url + 'getEqPorId', {'idEquipo': eq})), ('id_equipo', 'codigo', 'fecha', 'id_grupo'))
    comparar('equipo · última lectura', ult(asset, pa, eq_a), ult(man, pm, eq_m))
    ids_lect = lambda eq: [r[0] for r in sql("SELECT id_lectura FROM historial_lecturas WHERE id_equipo=%s ORDER BY id_lectura" % eq)]
    la, lm = ids_lect(eq_a), ids_lect(eq_m)
    for valor, nota in (('180', 'entre la anterior y la posterior'), ('999', 'mayor que la posterior')):
        ra = jload(asset.post(pa + 'setLecturaObservacionEdit', {'id_equipo': eq_a, 'id_lectura': la[2], 'lectura': valor, 'observacion': 'editada'}))
        rm = jload(man.post(pm + 'setLecturaObservacionEdit', {'id_equipo': eq_m, 'id_lectura': lm[2], 'lectura': valor, 'observacion': 'editada'}))
        comparar('equipo · editar lectura %s (respuesta)' % nota, ra, rm)
    comparar('equipo · editar lectura (historial)', sql(lect % eq_a), sql(lect % eq_m))

    # Contratistas.
    comparar('equipo · asignar contratista (respuesta)',
             asset.post(pa + 'guardarcontra', {'id_equipo': eq_a, 'id_contratista': contratista}).strip(),
             man.post(pm + 'guardarcontra', {'id_equipo': eq_m, 'id_contratista': contratista}).strip())
    contr = lambda s, url, eq: sin_claves(jload(s.post(url + 'getContratistasEquipo', {'id_equipo': eq})), ('id_equipo', 'codigo'))
    comparar('equipo · contratistas del equipo', contr(asset, pa, eq_a), contr(man, pm, eq_m))
    co = lambda s, url, eq: sin_claves(jload(s.post(url + 'getco', {'id_equipo': eq})), cat + ('descripcion', 'marca', 'id_customer', 'id_criticidad', 'id_area', 'id_proceso',
                                                                                              'id_sector', 'id_grupo', 'marcaid', 'marcadescrip'))
    comparar('equipo · equipo con su marca', co(asset, pa, eq_a), co(man, pm, eq_m))
    comparar('equipo · quitar contratista (respuesta)',
             asset.post(pa + 'delContratista', {'id_equipo': eq_a, 'id_contratista': contratista}).strip(),
             man.post(pm + 'delContratista', {'id_equipo': eq_m, 'id_contratista': contratista}).strip())
    comparar('equipo · quitar contratista (contratistas del equipo)', contr(asset, pa, eq_a), contr(man, pm, eq_m))

    # Metas.
    comparar('equipo · asignar meta (respuesta)', jload(asset.post(pa + 'asignarMeta', {'eq': eq_a, 'meta': '80'})),
             jload(man.post(pm + 'asignarMeta', {'eq': eq_m, 'meta': '80'})))
    comparar('equipo · traer meta', jload(asset.post(pa + 'getMeta', {'eq': eq_a})), jload(man.post(pm + 'getMeta', {'eq': eq_m})))

    # Adjuntos: agregar, editar y eliminar. Los nombres llevan un número al azar.
    ra = asset.post_archivo(pa + 'agregarAdjunto', {'idAgregaAdjunto': eq_a}, 'inputPDF[]', 'manual tres.pdf', PDF_PRUEBA)
    rm = man.post_archivo(pm + 'agregarAdjunto', {'idAgregaAdjunto': eq_m}, 'inputPDF[]', 'manual tres.pdf', PDF_PRUEBA)
    comparar('equipo · agregar adjunto (cantidad)', len(jload(ra)), len(jload(rm)))
    aa, am = sql(adj.replace('adjunto FROM', 'id_adjunto FROM') % eq_a), sql(adj.replace('adjunto FROM', 'id_adjunto FROM') % eq_m)
    ra = asset.post_archivo(pa + 'EditarAdjunto', {'idEquipo': eq_a, 'id_adjunto': aa[0][0]}, 'inputEditarPDF', 'nuevo.pdf', PDF_PRUEBA)
    rm = man.post_archivo(pm + 'EditarAdjunto', {'idEquipo': eq_m, 'id_adjunto': am[0][0]}, 'inputEditarPDF', 'nuevo.pdf', PDF_PRUEBA)
    comparar('equipo · editar adjunto (cantidad)', len(jload(ra)), len(jload(rm)))
    comparar('equipo · editar adjunto (el archivo nuevo está en la carpeta de asset)', True,
             os.path.exists(carpeta + sql("SELECT adjunto FROM equipos_archi_adjuntos WHERE id_adjunto=%s" % am[0][0])[0][0]))
    ra = jload(asset.post(pa + 'eliminaAdjunto', {'idEquipo': eq_a, 'idAdjunto': aa[0][0]}))
    rm = jload(man.post(pm + 'eliminaAdjunto', {'idEquipo': eq_m, 'idAdjunto': am[0][0]}))
    comparar('equipo · eliminar adjunto (cantidad)', len(ra), len(rm))

    comparar('equipo · baja (respuesta)', asset.post(pa + 'baja_equipo', {'idEquipo': eq_a}).strip(),
             man.post(pm + 'baja_equipo', {'idEquipo': eq_m}).strip())
    comparar('equipo · baja (estado)', sql(estado % eq_a), sql(estado % eq_m))
    comparar('equipo · equipo dado de baja no se trae para editar', asset.post(pa + 'getEditar', {'idEquipo': eq_a}).strip(),
             man.post(pm + 'getEditar', {'idEquipo': eq_m}).strip())

    # Aislamiento con un equipo de otra empresa.
    ajeno = sql("SELECT id_equipo FROM equipos WHERE id_empresa<>%d AND estado='AC' ORDER BY id_equipo LIMIT 1" % emp_asset)
    if ajeno:
        eq = ajeno[0][0]
        foto = lambda: (sql("SELECT * FROM equipos WHERE id_equipo=%s" % eq), sql(lect % eq),
                        sql("SELECT * FROM equipos_archi_adjuntos WHERE id_equipo=%s" % eq),
                        sql("SELECT * FROM contratistaquipo WHERE id_equipo=%s" % eq))
        antes = foto()
        comparar('equipo · aislamiento: MAN no trae equipos ajenos', 'nada', man.post(pm + 'getEditar', {'idEquipo': eq}).strip())
        comparar('equipo · aislamiento: MAN no trae lecturas ajenas', [], jload(man.post(pm + 'getHistoriaLect', {'idequipo': eq})))
        comparar('equipo · aislamiento: MAN no trae la marca de equipos ajenos', 'nada', man.post(pm + 'getco', {'id_equipo': eq}).strip())
        man.post(pm + 'editar_equipo', {'idEquipo': eq, 'data[descripcion]': 'HACK', 'data[estado]': 'IN'})
        man.post(pm + 'cambio_equipo', {'idequipo': eq})
        man.post(pm + 'estado_alta', {'idequipo': eq})
        man.post(pm + 'setLectura', {'id_equipo': eq, 'lectura': '1', 'operario': 'H', 'turno': 'H', 'observacion': 'H', 'estado': 'RE'})
        man.post(pm + 'asignarMeta', {'eq': eq, 'meta': '1'})
        man.post(pm + 'guardarcontra', {'id_equipo': eq, 'id_contratista': contratista})
        man.post_archivo(pm + 'agregarAdjunto', {'idAgregaAdjunto': eq}, 'inputPDF[]', 'hack.pdf', PDF_PRUEBA)
        man.post(pm + 'baja_equipo', {'idEquipo': eq})
        comparar('equipo · aislamiento: MAN no modifica equipos ajenos ni sus lecturas, adjuntos o contratistas', antes, foto())

    limpiar_equipo(carpeta)

def limpiar_equipo(carpeta):
    """Borra los equipos de prueba, sus lecturas, adjuntos (y archivos), contratistas y catálogos."""
    for f in sql("SELECT a.adjunto FROM equipos_archi_adjuntos a JOIN equipos e ON e.id_equipo=a.id_equipo WHERE e.codigo LIKE '%s%%'" % PREFIJO):
        ruta = carpeta + os.path.basename(f[0])
        if os.path.exists(ruta):
            os.remove(ruta)
    for f in os.listdir(carpeta):
        if re.match(r'(manual_(uno|dos|tres)|nuevo|hack)\d+(_\d+)?\.pdf$', f):
            os.remove(carpeta + f)
    for t in ('historial_lecturas', 'equipos_archi_adjuntos', 'contratistaquipo'):
        sql("DELETE FROM %s WHERE id_equipo IN (SELECT id_equipo FROM equipos WHERE codigo LIKE '%s%%')" % (t, PREFIJO))
    sql("DELETE FROM equipos WHERE codigo LIKE '%s%%'" % PREFIJO)
    for tabla, col in (('area', 'descripcion'), ('proceso', 'descripcion'), ('criticidad', 'descripcion'), ('sector', 'descripcion'),
                       ('grupo', 'descripcion'), ('marcasequipos', 'marcadescrip'), ('admcustomers', 'cliRazonSocial')):
        sql("DELETE FROM %s WHERE %s LIKE '%s%%'" % (tabla, col, PREFIJO))


PANTALLAS = {
    'area': lambda a, m, e: paridad_abm(a, m, e, 'area', 'area', 'id_area'),
    'proceso': lambda a, m, e: paridad_abm(a, m, e, 'proceso', 'proceso', 'id_proceso'),
    'parametro': paridad_parametro,
    'componente': paridad_componente,
    'equipo': paridad_equipo,
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
