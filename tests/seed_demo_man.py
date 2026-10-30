#!/usr/bin/env python3
"""
Datos de ejemplo de Mantenimiento para la empresa de prueba (empresa 1 de assetv2), para poder ver todas las pantallas de MAN con contenido.

Crea (todo marcado, para poder borrarlo): equipos con cliente, preventivos por tiempo y por contador, predictivos, backlogs, órdenes de
trabajo repartidas en octubre y noviembre de 2026 (sin caso de Bonita: se ven en el calendario y en los listados, pero no se pueden
asignar), informes de servicio y lecturas de parámetros.

Uso: MAN_MYCNF=<opciones de mysql> python3 tests/seed_demo_man.py [--borrar]
Los textos van sin tildes (la conexión de la línea de comandos no es UTF-8). Marcas: órdenes e informes "DEMO MAN"; planes con prev_canth, pred_canth o back_canth = 777; lecturas con valor "DEMO MAN".
"""
import os
import subprocess
import sys

EMP = 1


def sql(q):
    r = subprocess.run(['mysql', '--defaults-file=' + os.environ['MAN_MYCNF'], '-N', '-B', '-e', q], capture_output=True, text=True, errors='replace')
    if r.returncode:
        sys.exit(r.stderr)
    return [l.split('\t') for l in r.stdout.strip().split('\n') if l]


def borrar():
    sql("DELETE FROM orden_servicio WHERE comprobante='DEMO MAN' AND id_empresa=%d" % EMP)
    sql("DELETE FROM orden_trabajo WHERE descripcion LIKE 'DEMO MAN%%' AND id_empresa=%d" % EMP)
    sql("DELETE FROM parametroequipo WHERE valor='DEMO MAN' AND id_empresa=%d" % EMP)
    sql("DELETE FROM preventivo WHERE prev_canth=777 AND id_empresa=%d" % EMP)
    sql("DELETE FROM predictivo WHERE pred_canth=777 AND id_empresa=%d" % EMP)
    sql("DELETE FROM tbl_back WHERE back_canth=777 AND id_empresa=%d" % EMP)
    print('datos de ejemplo borrados')


def crear():
    borrar()
    # Los equipos de la empresa de prueba no tenían cliente y el listado (que une con clientes) no los mostraba
    cli = sql("SELECT cliId FROM admcustomers WHERE id_empresa=%d ORDER BY cliId LIMIT 1" % EMP)[0][0]
    sql("UPDATE equipos SET id_customer=%s WHERE id_empresa=%d AND id_customer IS NULL" % (cli, EMP))
    per = dict((d, i) for i, d in sql("SELECT idperiodo, descripcion FROM periodo WHERE id_empresa=%d" % EMP))
    tareas = [t[0] for t in sql("SELECT id_tarea FROM tareas WHERE id_empresa=%d AND estado='AC' ORDER BY id_tarea" % EMP)]
    eqs = [e[0] for e in sql("SELECT id_equipo FROM equipos WHERE id_empresa=%d AND estado='AC' ORDER BY id_equipo" % EMP)]
    comp = sql("SELECT id_componente FROM componentes WHERE id_empresa=%d ORDER BY id_componente LIMIT 1" % EMP)[0][0]
    ce = sql("SELECT idcomponenteequipo FROM componenteequipo WHERE id_empresa=%d ORDER BY idcomponenteequipo LIMIT 1" % EMP)[0][0]
    usr = sql("SELECT usrId FROM sisusers WHERE usrNick='mig.man.a@trazalog.test'")[0][0]
    # Para que el preventivo por contador figure en alerta
    sql("UPDATE equipos SET ultima_lectura=120, fecha_ultimalectura='2026-10-29 08:00:00' WHERE id_equipo=%s" % eqs[-1])

    prev = ("INSERT INTO preventivo (id_equipo,id_tarea,perido,cantidad,ultimo,id_componente,critico1,horash,estadoprev,prev_duracion,id_unidad,prev_canth,id_empresa,lectura_base) "
            "VALUES (%s,%s,'%s',%s,'%s',%s,%s,1,'C',%s,1,777,%d,%s)")
    for e, t, p, c, u, cr, d, lb in ((eqs[0], tareas[0], per['Diario'], 7, '2026-10-28', 'NULL', 60, 'NULL'), (eqs[1], tareas[1], per['Mensual'], 1, '2026-10-15', 'NULL', 120, 'NULL'),
                                      (eqs[2], tareas[2], per['Anual'], 1, '2025-11-10', 'NULL', 240, 'NULL'), (eqs[-1], tareas[0], per['Horas'], 250, '2026-10-01', 100, 90, 0)):
        sql(prev % (e, t, p, c, u, comp, cr, d, EMP, lb))
    pred = ("INSERT INTO predictivo (id_equipo,tarea_descrip,fecha,periodo,cantidad,horash,estado,pred_duracion,id_unidad,pred_canth,id_empresa) "
            "VALUES (%s,'%s','%s','%s',%s,1,'C',%s,1,777,%d)")
    sql(pred % (eqs[0], tareas[1], '2026-10-22', per['Mensual'], 2, 90, EMP))
    sql(pred % (eqs[1], tareas[2], '2026-11-12', per['Diario'], 5, 45, EMP))
    back = ("INSERT INTO tbl_back (id_equipo,id_tarea,fecha,horash,estado,back_duracion,id_unidad,back_canth,id_empresa,sore_id,tarea_opcional,idcomponenteequipo) "
            "VALUES (%s,%s,'%s',1,'C',%s,1,777,%d,NULL,'DEMO MAN %s'," + str(ce) + ")")
    sql(back % (eqs[0], tareas[0], '2026-10-20', 60, EMP, 'cambiar correa'))
    sql(back % (eqs[2], tareas[1], '2026-11-05', 180, EMP, 'revisar sellos'))
    ot = ("INSERT INTO orden_trabajo (tipo,id_solicitud,id_empresa,estado,id_equipo,id_tarea,descripcion,fecha,fecha_program,duracion,id_usuario,id_usuario_a,nro) "
          "VALUES ('%s',0,%d,'%s',%s,%s,'DEMO MAN %s','2026-10-30','%s',%s,%s,%s,'1')")
    ots = []
    for i, (tipo, est, dia, dur, asig, desc) in enumerate((
            ('3', 'PL', '2026-10-05 08:00:00', 60, 'NULL', 'lubricacion semanal'), ('3', 'AS', '2026-10-12 09:30:00', 90, usr, 'inspeccion de rodamientos'),
            ('2', 'AS', '2026-10-14 14:00:00', 120, usr, 'falla de bomba'), ('5', 'PL', '2026-10-19 10:00:00', 45, 'NULL', 'analisis de vibracion'),
            ('4', 'PL', '2026-10-21 07:30:00', 180, 'NULL', 'reparacion de sello'), ('1', 'AS', '2026-10-26 08:00:00', 60, usr, 'orden manual'),
            ('3', 'PL', '2026-10-30 08:00:00', 60, 'NULL', 'lubricacion semanal'), ('6', 'PL', '2026-10-30 11:00:00', 90, 'NULL', 'correctivo programado'),
            ('3', 'PL', '2026-11-03 08:00:00', 60, 'NULL', 'lubricacion semanal'), ('2', 'PL', '2026-11-04 15:00:00', 60, 'NULL', 'ruido anormal'),
            ('5', 'PL', '2026-11-12 09:00:00', 45, 'NULL', 'analisis de aceite'), ('3', 'T', '2026-10-08 08:00:00', 60, usr, 'ya terminada'))):
        sql(ot % (tipo, EMP, est, eqs[i % len(eqs)], tareas[i % len(tareas)], desc, dia, dur, usr, asig))
    # Informes de servicio de las dos últimas órdenes (para la pantalla de informes)
    for o in [r[0] for r in sql("SELECT id_orden FROM orden_trabajo WHERE descripcion LIKE 'DEMO MAN%%' AND estado IN ('T','AS') AND id_empresa=%d ORDER BY id_orden DESC LIMIT 2" % EMP)]:
        sql("INSERT INTO orden_servicio (lectura, fecha, comprobante, id_equipo, id_contratista, id_solicitudreparacion, valesid, estado, id_ordenherraminetas, id_orden_insumo, id_ot, id_empresa, "
            "fechahorainicio, fechahorafin, horometroinicio, horometrofin) VALUES (0,'2026-10-28','DEMO MAN',%s,0,0,0,'C',0,0,%s,%d,'2026-10-28 08:00:00','2026-10-28 10:00:00',100,123.4)" % (eqs[0], o, EMP))
    # Lecturas de parámetros
    for eq, prm in sql("SELECT id_equipo, id_parametro FROM setupparam WHERE id_empresa=%d LIMIT 3" % EMP):
        sql("INSERT INTO parametroequipo (paramId, id_equipo, valor, fechahora, id_empresa) VALUES (%s,%s,'DEMO MAN','2026-10-29 08:00:00',%d)" % (prm, eq, EMP))
    print('datos de ejemplo creados')


if __name__ == '__main__':
    borrar() if '--borrar' in sys.argv else crear()
