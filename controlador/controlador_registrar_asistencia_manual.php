<?php
require '../modelo/conexion.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$id_empleado  = intval($data['id_empleado'] ?? 0);
$fecha        = $data['fecha'] ?? '';
$hora_entrada = $data['hora_entrada'] ?? '';
$hora_salida  = $data['hora_salida'] ?? '';

if (!$id_empleado || !$fecha || !$hora_entrada) {
    echo json_encode(['status' => 'error', 'message' => 'Faltan datos requeridos.']);
    exit;
}

// Validar que la fecha no sea futura
if ($fecha > date('Y-m-d')) {
    echo json_encode(['status' => 'error', 'message' => 'No se puede registrar asistencia en una fecha futura.']);
    exit;
}

// Verificar si ya tiene asistencia ese día
$stmt_check = $conexion->prepare("SELECT id_asistencia FROM asistencia WHERE id_empleado = ? AND DATE(entrada) = ?");
$stmt_check->bind_param("is", $id_empleado, $fecha);
$stmt_check->execute();
$res_check = $stmt_check->get_result();

if ($res_check->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Este empleado ya tiene una asistencia registrada para ese día.']);
    exit;
}

// Construir datetime
$entrada_dt = $fecha . ' ' . $hora_entrada . ':00';
$salida_dt  = (!empty($hora_salida)) ? $fecha . ' ' . $hora_salida . ':00' : null;

// Validar que salida sea después de entrada
if ($salida_dt && $salida_dt <= $entrada_dt) {
    echo json_encode(['status' => 'error', 'message' => 'La hora de salida debe ser posterior a la de entrada.']);
    exit;
}

$bio_id = 'manual_' . uniqid();

if ($salida_dt) {
    $stmt = $conexion->prepare("INSERT INTO asistencia (id_empleado, biometrico_id, estado_biometrico, entrada, salida) VALUES (?, ?, 'manual', ?, ?)");
    $stmt->bind_param("isss", $id_empleado, $bio_id, $entrada_dt, $salida_dt);
} else {
    $stmt = $conexion->prepare("INSERT INTO asistencia (id_empleado, biometrico_id, estado_biometrico, entrada) VALUES (?, ?, 'manual', ?)");
    $stmt->bind_param("iss", $id_empleado, $bio_id, $entrada_dt);
}

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Asistencia registrada correctamente.', 'id' => $conexion->insert_id]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error al insertar en la base de datos.']);
}
?>
