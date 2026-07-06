<?php
// controlador/guardar_justificacion.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario es administrador
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'No autorizado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include_once '../modelo/conexion.php';

    // Obtener y sanitizar los datos
    $id_empleado = isset($_POST['id_empleado']) ? (int)$_POST['id_empleado'] : 0;
    $estado = isset($_POST['estado']) ? $_POST['estado'] : '';

    $estados_validos = ['falta', 'reposo', 'vacaciones', 'permiso'];

    if ($id_empleado > 0 && in_array($estado, $estados_validos)) {
        // Usar UPSERT (Insert On Duplicate Key Update) 
        // para guardar el estado del empleado para HOY
        $query = "INSERT INTO justificacion_inasistencia (id_empleado, fecha, estado) 
                  VALUES (?, CURDATE(), ?) 
                  ON DUPLICATE KEY UPDATE estado = VALUES(estado)";
        
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("is", $id_empleado, $estado);
        
        if ($stmt->execute()) {
            include_once 'funciones_asistencia.php';
            evaluar_faltas_consecutivas($conexion);
            
            echo json_encode(['status' => 'success', 'message' => 'Estado actualizado correctamente.']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Error al guardar en la base de datos: ' . $conexion->error]);
        }
        $stmt->close();
    } else {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Datos inválidos.']);
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido.']);
}
?>
