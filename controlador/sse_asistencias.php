<?php
/**
 * API SSE (Server-Sent Events) que emite las tablas de asistencias e inasistencias del día en JSON.
 * Permite actualización en tiempo real en la vista sin recargar la página ni hacer peticiones AJAX continuas.
 */

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

// Evitar buffer de salida para que los mensajes se envíen inmediatamente
if (ob_get_level() > 0) ob_end_clean();

// Desactivar el límite de tiempo de ejecución
set_time_limit(0);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['email'])) {
    echo "event: error\n";
    echo "data: No autorizado\n\n";
    flush();
    exit;
}

$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// ¡IMPORTANTE! Liberar el bloqueo de la sesión para permitir que otras peticiones del mismo usuario funcionen
session_write_close();

include_once '../modelo/conexion.php';

$lastHash = '';

while (true) {
    // Si el cliente cierra la pestaña, terminamos el script para no dejar procesos zombie
    if (connection_aborted()) {
        break;
    }

    // 1. Asistencias del día
    $asistencias = [];
    $sqlAsistencias = $conexion->query("SELECT asistencia.id_asistencia, asistencia.id_empleado, asistencia.entrada, asistencia.salida, empleado.nombre as nom_empleado, empleado.apellido, empleado.dni, empleado.foto, cargo.nombre as nom_cargo FROM asistencia INNER JOIN empleado ON asistencia.id_empleado = empleado.id_empleado INNER JOIN cargo ON empleado.cargo = cargo.id_cargo WHERE asistencia.entrada IS NOT NULL AND DATE(asistencia.entrada) = CURDATE() ORDER BY asistencia.entrada DESC LIMIT 15");
    
    if ($sqlAsistencias) {
        while ($row = $sqlAsistencias->fetch_assoc()) {
            $asistencias[] = $row;
        }
    }

    // 2. Inasistencias del día
    $inasistencias = [];
    $sqlInasistencias = $conexion->query("SELECT empleado.id_empleado, empleado.nombre as nom_empleado, empleado.apellido, empleado.dni, empleado.foto, empleado.estado as estado_empleado, cargo.nombre as nom_cargo, COALESCE(justificacion_inasistencia.estado, 'falta') as justificacion_estado FROM empleado INNER JOIN cargo ON empleado.cargo = cargo.id_cargo LEFT JOIN justificacion_inasistencia ON empleado.id_empleado = justificacion_inasistencia.id_empleado AND justificacion_inasistencia.fecha = CURDATE() WHERE empleado.id_empleado NOT IN ( SELECT id_empleado FROM asistencia WHERE DATE(entrada) = CURDATE() ) LIMIT 15");
    
    if ($sqlInasistencias) {
        while ($row = $sqlInasistencias->fetch_assoc()) {
            $inasistencias[] = $row;
        }
    }

    $data = [
        'status' => 'success',
        'asistencias' => $asistencias,
        'inasistencias' => $inasistencias,
        'is_admin' => $is_admin,
        'timestamp' => date('Y-m-d H:i:s')
    ];

    // Calculamos un hash (firma) de los datos para saber si hubo cambios
    $currentHash = md5(json_encode($data));

    // Si los datos son diferentes a los enviados la última vez, los enviamos
    if ($currentHash !== $lastHash) {
        $lastHash = $currentHash;
        echo "data: " . json_encode($data) . "\n\n";
        ob_flush();
        flush();
    }

    // Esperar 1 segundo antes de volver a verificar la base de datos
    sleep(1);
}
?>
