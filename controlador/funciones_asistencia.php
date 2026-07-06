<?php
// controlador/funciones_asistencia.php

function evaluar_faltas_consecutivas($conexion) {
    // Buscar todos los empleados activos
    $sql_empleados = $conexion->query("SELECT id_empleado FROM empleado WHERE estado = 'Activo'");
    
    if (!$sql_empleados || $sql_empleados->num_rows == 0) {
        return;
    }

    $hoy = date('Y-m-d');
    
    // Preparar query para verificar si ya existe una amonestación reciente (últimos 3 días) para evitar duplicados.
    $stmt_check_amonestacion = $conexion->prepare("SELECT id_amonestacion FROM amonestacion WHERE id_empleado = ? AND motivo = '3 días sin asistir injustificadamente' AND fecha_registro >= (CURDATE() - INTERVAL 3 DAY)");
    
    // Preparar query para contar faltas en los últimos 3 días (Ayer, Antier, Tras-antier)
    // Se trabajan los 7 días de la semana, por lo que tomamos 3 días literales seguidos.
    $query_faltas = "
        SELECT d.fecha
        FROM (
            SELECT CURDATE() - INTERVAL 1 DAY AS fecha UNION ALL
            SELECT CURDATE() - INTERVAL 2 DAY UNION ALL
            SELECT CURDATE() - INTERVAL 3 DAY
        ) d
        LEFT JOIN asistencia a ON a.id_empleado = ? AND DATE(a.entrada) = d.fecha
        LEFT JOIN justificacion_inasistencia ji ON ji.id_empleado = ? AND ji.fecha = d.fecha
        LEFT JOIN permisos p ON p.id_empleado = ? AND d.fecha BETWEEN p.fecha_inicio AND p.fecha_fin AND p.estado = 'Aprobado'
        WHERE a.id_asistencia IS NULL 
          AND (ji.estado IS NULL OR ji.estado = 'falta')
          AND p.id_permiso IS NULL
    ";
    
    $stmt_faltas = $conexion->prepare($query_faltas);
    
    // Query para insertar la amonestación
    $stmt_insert = $conexion->prepare("INSERT INTO amonestacion (id_empleado, motivo, gravedad, observacion) VALUES (?, '3 días sin asistir injustificadamente', 'Grave', 'Generado automáticamente por el sistema tras 3 faltas consecutivas injustificadas.')");

    while ($empleado = $sql_empleados->fetch_assoc()) {
        $id_emp = $empleado['id_empleado'];
        
        // 1. Revisar si ya fue amonestado recientemente por lo mismo
        $stmt_check_amonestacion->bind_param("i", $id_emp);
        $stmt_check_amonestacion->execute();
        $res_check = $stmt_check_amonestacion->get_result();
        
        if ($res_check->num_rows > 0) {
            continue; // Ya tiene una amonestación reciente, saltar
        }
        
        // 2. Contar faltas de los últimos 3 días
        $stmt_faltas->bind_param("iii", $id_emp, $id_emp, $id_emp);
        $stmt_faltas->execute();
        $res_faltas = $stmt_faltas->get_result();
        
        if ($res_faltas->num_rows == 3) {
            // Tiene exactamente 3 faltas en los últimos 3 días seguidos
            $stmt_insert->bind_param("i", $id_emp);
            $stmt_insert->execute();
        }
    }
}
?>
