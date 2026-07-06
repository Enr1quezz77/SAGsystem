<?php
/**
 * API ligera que retorna las tablas de asistencias e inasistencias del día en JSON.
 * Esto reemplaza la necesidad de recargar toda la página HTML (39KB) cada 5 segundos.
 * Solo retorna los datos necesarios (~2KB de JSON).
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// Verificar sesión
if (empty($_SESSION['email'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'No autorizado']);
    exit;
}

include_once '../modelo/conexion.php';

$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

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
$sqlInasistencias = $conexion->query("SELECT empleado.id_empleado, empleado.nombre as nom_empleado, empleado.apellido, empleado.dni, empleado.foto, empleado.estado as estado_empleado, cargo.nombre as nom_cargo, CASE WHEN permisos.id_permiso IS NOT NULL THEN CASE permisos.tipo WHEN 'Vacaciones' THEN 'vacaciones' WHEN 'Permiso Médico' THEN 'reposo' WHEN 'Asunto Personal' THEN 'permiso' ELSE 'permiso' END WHEN justificacion_inasistencia.estado IS NOT NULL THEN justificacion_inasistencia.estado ELSE 'falta' END as justificacion_estado, IF(permisos.id_permiso IS NOT NULL, 1, 0) as tiene_permiso FROM empleado INNER JOIN cargo ON empleado.cargo = cargo.id_cargo LEFT JOIN justificacion_inasistencia ON empleado.id_empleado = justificacion_inasistencia.id_empleado AND justificacion_inasistencia.fecha = CURDATE() LEFT JOIN permisos ON empleado.id_empleado = permisos.id_empleado AND CURDATE() BETWEEN permisos.fecha_inicio AND permisos.fecha_fin AND permisos.estado = 'Aprobado' WHERE empleado.id_empleado NOT IN ( SELECT id_empleado FROM asistencia WHERE DATE(entrada) = CURDATE() ) LIMIT 15");

if ($sqlInasistencias) {
    while ($row = $sqlInasistencias->fetch_assoc()) {
        $inasistencias[] = $row;
    }
}

echo json_encode([
    'status' => 'success',
    'asistencias' => $asistencias,
    'inasistencias' => $inasistencias,
    'is_admin' => $is_admin,
    'timestamp' => date('Y-m-d H:i:s')
]);
?>
