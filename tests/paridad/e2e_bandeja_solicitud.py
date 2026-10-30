#!/usr/bin/env python3
"""
Pantalla de las tareas que todavía no tienen orden (Análisis de Solicitud de Servicio, Planificar Solicitud, Editar Backlog y Planificar
Backlog) con casos reales de la Bonita de DEV, abiertas por la pantalla de MAN. Se agregó después de encontrar que esas cuatro daban error
500 (el controller llamaba a un método que no existía): las pruebas anteriores solo abrían tareas de casos con orden. Limpia todo al final.
Uso: MAN_ENV_FILE=... MAN_MYCNF=... python3 tests/paridad/e2e_bandeja_solicitud.py
"""
import os
import re
import sys
import time
import urllib.parse

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
import paridad as P
import e2e_calendario as E


def main():
    env = P.leer_env(os.environ['MAN_ENV_FILE'])
    man = P.sesion_tools(env['DOCTEST_LOCAL_MAN_USER'], env['DOCTEST_LOCAL_MAN_PASS'])
    bonita, put, bpost = P.bonita_admin()
    uid = bonita('/API/identity/user?p=0&c=1&f=userName%%3D%s' % urllib.parse.quote(E.NICK))[0]['id']
    ids = {'prev': [], 'pred': [], 'back': [], 'sol': []}
    casos = []

    def abrir(caso, esperado):
        t = bonita('/API/bpm/humanTask?p=0&c=10&f=caseId%%3D%s' % caso)
        nombre = t[0]['name'] if t else ''
        r = man.get(P.MAN + 'Tarea/detaTarea/Add-Edit-Del/%s' % (t[0]['id'] if t else 0))
        texto = re.sub(r'\s+', ' ', re.sub(r'<script.*?</script>|<style.*?</style>|<[^>]+>', ' ', r, flags=re.S))
        P.comparar('tarea sin orden · "%s": se abre con la cabecera del equipo y sin errores' % esperado,
                   (esperado, True, True), (nombre, not r.startswith('<HTTP') and 'A PHP Error' not in r, 'Equipo:' in texto and 'Solicitud' in texto))

    try:
        urgente = E.crear_solicitud(bonita, put, bpost, uid, urgente=True, ids=ids, casos=casos)
        abrir(urgente[1], 'Planificar Solicitud')
        no = E.crear_solicitud(bonita, put, bpost, uid, urgente=False, ids=ids, casos=casos)
        abrir(no[1], 'Planificar Backlog')
        # Análisis de Solicitud y Editar Backlog: casos recién creados
        r = E.api('solicitud', {'solicitud': {'f_solicitado': time.strftime(E.FMT), 'usrId': P.sql("SELECT usrId FROM sisusers WHERE usrNick='%s'" % E.NICK)[0][0],
                                              'solicitante': 'MIG', 'causa': E.PREF + ' analisis', 'id_equipo': str(E.EQ), 'id_empresa': str(E.EMP)}, 'session': '', 'emprId': '1'})
        ids['sol'].append(r['id'])
        casos.append(r['caseId'])
        abrir(r['caseId'], 'Analisis de Solicitud de Servicio')
        tid = bonita('/API/bpm/humanTask?p=0&c=10&f=caseId%%3D%s' % r['caseId'])[0]['id']
        base = {'usrNick': E.NICK, 'id_empresa': str(E.EMP), 'session': '', 'id_tarea': tid}
        E.api('bandeja/tarea/asignar', dict(base, accion='tomar'))
        E.api('bandeja/tarea/urgencia', dict(base, opcion='false'))
        for _ in range(10):
            time.sleep(2)
            if [t for t in bonita('/API/bpm/humanTask?p=0&c=10&f=caseId%%3D%s' % r['caseId']) if t['name'] == 'Editar Backlog']:
                break
        abrir(r['caseId'], 'Editar Backlog')
    finally:
        E.limpiar_todo(bonita, ids, casos, P.sql("SELECT CAST(ultima_lectura AS CHAR), CAST(fecha_ultimalectura AS CHAR) FROM equipos WHERE id_equipo=%d" % E.EQ)[0])
    print('\n%s' % ('E2E BANDEJA SOLICITUD OK' if not P.fallas else 'E2E BANDEJA SOLICITUD CON FALLAS: ' + ', '.join(P.fallas)))
    sys.exit(1 if P.fallas else 0)


if __name__ == '__main__':
    os.environ.setdefault('MAN_PGENV', os.path.join(os.path.dirname(os.environ.get('MAN_MYCNF', '/tmp/x')), 'pg.env'))
    main()
