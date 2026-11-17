#!/usr/bin/env python3
"""
Prueba de las fechas de una serie del Plan de Mantenimiento (nivel 4 del plan: golden tests del motor).

Compara la consulta de MAN (MANDataService POST /plan/fechas, que usa el motor al programar una serie) con el código de asset:

  * Diario y Semanal: iguales a asset. Se corre el código de asset (getDiasDuracion y el bucle de setOTenSerie, copiados textuales)
    con php de la línea de comandos y se comparan las fechas una a una.
  * Mensual, Semestral y Anual (decisión E45, por calendario: mismo día de cada mes, y si el mes no lo tiene, el último): asset usa días
    fijos (mes = 30, semestre = 180, año = 365) y se va corriendo, así que no se compara con asset. Se compara con listas escritas a mano
    y con una segunda implementación independiente en Python. Aparte se informa cuánto se corre asset (para el informe, no falla).
  * Casos negativos: frecuencia 0 y cantidad de meses 0 no cuelgan (una sola fecha, como asset con 0 meses). Más de 400 fechas se corta en 401.

No toca datos. Variables: MAN_DS_URL (default http://localhost:8290/services/MANDataService), PHP (default php).
Uso: python3 tests/paridad/golden_fechas.py
"""
import calendar
import datetime
import itertools
import json
import os
import subprocess
import sys
import tempfile
import urllib.request

DS = os.environ.get('MAN_DS_URL', 'http://localhost:8290/services/MANDataService')
PHP = os.environ.get('PHP', 'php')
FMT = '%Y-%m-%d %H:%M:%S'
fallas = []

# Código de asset, textual: Calendario::getDiasDuracion y el bucle de Calendario::setOTenSerie (sin base de datos ni Bonita)
ASSET_PHP = r'''<?php
function getDiasDuracion($info)
{
    $cantidad = $info[0]["cantidad"];
    $especie = $info[0]["descripcion"];
    $dias = 0;
    switch ($especie) {
        case 'Mensual':
            $dias = 30 * $cantidad;
            break;
        case 'Semanal':
            $dias = 7 * $cantidad;
            break;
        case 'Semestral':
            $dias = 180 * $cantidad;
            break;
        case 'Anual':
            $dias = 365 * $cantidad;
            break;
        default:
            $dias = $cantidad;
            break;
    }
    return $dias;
}
date_default_timezone_set('UTC');
$fec_programacion = $argv[1];
$fecha_limite = $argv[2];
$diasFrecuencia = getDiasDuracion(array(array('cantidad' => $argv[4], 'descripcion' => $argv[3])));
$out = array();
$tope = 1000; // asset no tiene tope: con frecuencia 0 no termina (T3)
while ($fecha_limite >= $fec_programacion && $tope-- > 0) {
    $out[] = $fec_programacion;
    $nuev_fecha = strtotime('+' . $diasFrecuencia . 'day', strtotime($fec_programacion));
    $nuev_fecha = date('Y-m-d H:i:s', $nuev_fecha);
    $fec_programacion = $nuev_fecha;
}
echo json_encode($out);
'''


def sumar_meses(d, m):
    """d + m meses, con el último día del mes si el mes no tiene ese día (segunda implementación, independiente de SQL)."""
    k = d.month - 1 + m
    y, mes = d.year + k // 12, k % 12 + 1
    return d.replace(year=y, month=mes, day=min(d.day, calendar.monthrange(y, mes)[1]))


def esperadas(periodo, cant, ini, meses):
    """Serie por calendario (regla E45)."""
    lim = sumar_meses(ini, meses)
    out, n = [], 0
    while True:
        if periodo == 'Semanal':
            f = ini + datetime.timedelta(days=7 * cant * n)
        elif periodo == 'Mensual':
            f = sumar_meses(ini, cant * n)
        elif periodo == 'Semestral':
            f = sumar_meses(ini, 6 * cant * n)
        elif periodo == 'Anual':
            f = sumar_meses(ini, 12 * cant * n)
        else:
            f = ini + datetime.timedelta(days=cant * n)
        if f > lim or n > 400:
            break
        out.append(f.strftime(FMT))
        n += 1
        if cant == 0:
            break
    return out


def man(periodo, cant, ini, meses):
    cuerpo = {'_post_plan_fechas': {'fecha': ini.strftime(FMT), 'periodo': periodo, 'cantidad': str(cant), 'meses': str(meses), 'maximo': '401'}}
    req = urllib.request.Request(DS + '/_post_plan_fechas', json.dumps(cuerpo).encode(),
                                 {'Content-Type': 'application/json', 'Accept': 'application/json'})
    d = json.loads(urllib.request.urlopen(req, timeout=60).read().decode())
    filas = d.get('fechas', {}).get('fecha', []) if isinstance(d.get('fechas'), dict) else []
    return [f['fecha_ot'] for f in sorted(filas, key=lambda f: int(f['n']))]


def asset(php, periodo, cant, ini, limite):
    r = subprocess.run([PHP, php, ini.strftime(FMT), limite.strftime(FMT), periodo, str(cant)], capture_output=True, text=True, check=True)
    return json.loads(r.stdout)


def comparar(nombre, a, m):
    ok = a == m
    if not ok:
        fallas.append(nombre)
        print('FALLA | ' + nombre)
        print('       esperado:', a[:6], '... (%d)' % len(a))
        print('       MAN     :', m[:6], '... (%d)' % len(m))
    return ok


def main():
    with tempfile.TemporaryDirectory() as tmp:
        php = os.path.join(tmp, 'asset_serie.php')
        open(php, 'w').write(ASSET_PHP)

        inicios = [datetime.datetime(2026, 1, 31, 0, 0), datetime.datetime(2027, 2, 28, 17, 30), datetime.datetime(2028, 2, 29, 8, 0),
                   datetime.datetime(2026, 6, 15, 23, 59), datetime.datetime(2026, 12, 31, 12, 0), datetime.datetime(2026, 10, 30, 0, 0)]
        total = ok = 0

        # Diario y Semanal: contra asset (con el mismo límite: el de asset se corre en los días 29 a 31, T55)
        for per, cant, ini, meses in itertools.product(('Diario', 'Semanal'), (1, 2, 3, 4), inicios, (1, 3, 12, 36)):
            lim = sumar_meses(ini, meses)
            total += 1
            a = asset(php, per, cant, ini, lim)
            # El motor corta en 401 fechas (más de 400 se rechaza): hasta ahí tiene que coincidir con asset
            ok += comparar('asset %s x%d %s %d meses' % (per, cant, ini.strftime('%Y-%m-%d %H:%M'), meses), a[:401], man(per, cant, ini, meses))
        print('Diario y Semanal contra asset: %d casos' % total)

        # Mensual, Semestral y Anual: segunda implementación
        n2 = 0
        for per, cant, ini, meses in itertools.product(('Mensual', 'Semestral', 'Anual'), (1, 2, 3, 4), inicios, (1, 3, 12, 36)):
            n2 += 1
            ok += comparar('calendario %s x%d %s %d meses' % (per, cant, ini.strftime('%Y-%m-%d %H:%M'), meses),
                           esperadas(per, cant, ini, meses)[:401], man(per, cant, ini, meses))
        total += n2
        print('Mensual, Semestral y Anual contra la segunda implementación: %d casos' % n2)

        # Listas escritas a mano
        a_mano = [
            ('Mensual', 1, datetime.datetime(2026, 1, 31, 8, 0), 6, ['2026-01-31 08:00:00', '2026-02-28 08:00:00', '2026-03-31 08:00:00',
                                                                       '2026-04-30 08:00:00', '2026-05-31 08:00:00', '2026-06-30 08:00:00',
                                                                       '2026-07-31 08:00:00']),
            ('Mensual', 1, datetime.datetime(2028, 1, 29, 9, 30), 3, ['2028-01-29 09:30:00', '2028-02-29 09:30:00', '2028-03-29 09:30:00',
                                                                        '2028-04-29 09:30:00']),
            ('Mensual', 2, datetime.datetime(2026, 12, 31, 0, 0), 7, ['2026-12-31 00:00:00', '2027-02-28 00:00:00', '2027-04-30 00:00:00',
                                                                       '2027-06-30 00:00:00']),
            ('Semestral', 1, datetime.datetime(2026, 8, 31, 6, 0), 12, ['2026-08-31 06:00:00', '2027-02-28 06:00:00', '2027-08-31 06:00:00']),
            ('Anual', 1, datetime.datetime(2028, 2, 29, 7, 0), 36, ['2028-02-29 07:00:00', '2029-02-28 07:00:00', '2030-02-28 07:00:00',
                                                                      '2031-02-28 07:00:00']),
            ('Anual', 2, datetime.datetime(2026, 6, 15, 10, 0), 36, ['2026-06-15 10:00:00', '2028-06-15 10:00:00']),
            ('Diario', 1, datetime.datetime(2026, 2, 26, 5, 0), 1, ['2026-02-26 05:00:00', '2026-02-27 05:00:00', '2026-02-28 05:00:00',
                                                                     '2026-03-01 05:00:00', '2026-03-02 05:00:00', '2026-03-03 05:00:00',
                                                                     '2026-03-04 05:00:00', '2026-03-05 05:00:00', '2026-03-06 05:00:00',
                                                                     '2026-03-07 05:00:00', '2026-03-08 05:00:00', '2026-03-09 05:00:00',
                                                                     '2026-03-10 05:00:00', '2026-03-11 05:00:00', '2026-03-12 05:00:00',
                                                                     '2026-03-13 05:00:00', '2026-03-14 05:00:00', '2026-03-15 05:00:00',
                                                                     '2026-03-16 05:00:00', '2026-03-17 05:00:00', '2026-03-18 05:00:00',
                                                                     '2026-03-19 05:00:00', '2026-03-20 05:00:00', '2026-03-21 05:00:00',
                                                                     '2026-03-22 05:00:00', '2026-03-23 05:00:00', '2026-03-24 05:00:00',
                                                                     '2026-03-25 05:00:00', '2026-03-26 05:00:00']),
        ]
        for per, cant, ini, meses, lista in a_mano:
            total += 1
            ok += comparar('a mano %s x%d %s %d meses' % (per, cant, ini.strftime('%Y-%m-%d'), meses), lista, man(per, cant, ini, meses))
        print('Listas escritas a mano: %d casos' % len(a_mano))

        # Negativos: no cuelgan
        ini = datetime.datetime(2026, 3, 1, 8, 0)
        for per in ('Diario', 'Semanal', 'Mensual', 'Semestral', 'Anual'):
            total += 1
            ok += comparar('frecuencia 0 (%s): una sola fecha' % per, [ini.strftime(FMT)], man(per, 0, ini, 12))
        total += 1
        ok += comparar('0 meses: una sola fecha, como asset', [ini.strftime(FMT)], man('Mensual', 1, ini, 0))
        total += 1
        largo = man('Diario', 1, ini, 36)
        ok += comparar('tope: diario por 36 meses devuelve más de 400 (el motor lo rechaza)', [True], [len(largo) > 400])

        # Informe (no falla): cuánto se corre asset en Mensual / Semestral / Anual
        ini = datetime.datetime(2026, 1, 31, 8, 0)
        for per, cant, meses in (('Mensual', 1, 12), ('Semestral', 1, 36), ('Anual', 1, 36)):
            a = asset(php, per, cant, ini, sumar_meses(ini, meses))
            m = man(per, cant, ini, meses)
            print('INFO | %s x%d desde %s por %d meses: última fecha asset %s, MAN %s' % (per, cant, ini.date(), meses, a[-1][:10], m[-1][:10]))

    print('\n%d de %d casos OK' % (ok, total))
    if fallas:
        print('FALLAS:', len(fallas))
        sys.exit(1)
    print('GOLDEN FECHAS OK')


if __name__ == '__main__':
    main()
