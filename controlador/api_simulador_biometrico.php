<?php
require '../modelo/conexion.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $dni = $data['dni'] ?? '';

    if (empty($dni)) {
        echo json_encode(['status' => 'error', 'message' => 'DNI (huella) es requerido.']);
        exit;
    }

    // Buscar empleado por DNI
    $stmt = $conexion->prepare("SELECT id_empleado, nombre, apellido, estado FROM empleado WHERE dni = ?");
    $stmt->bind_param("s", $dni);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Huella no reconocida en el sistema.']);
        exit;
    }

    $empleado = $result->fetch_assoc();
    
    // Verificar si está suspendido
    if (isset($empleado['estado']) && $empleado['estado'] === 'Suspendido') {
        echo json_encode(['status' => 'error', 'message' => 'Acceso denegado: Empleado suspendido.']);
        exit;
    }

    $id_empleado = $empleado['id_empleado'];
    $fecha_hoy = date('Y-m-d');

    // Verificar si tiene un permiso aprobado para hoy
    $stmt_permiso = $conexion->prepare("SELECT tipo FROM permisos WHERE id_empleado = ? AND ? BETWEEN fecha_inicio AND fecha_fin AND estado = 'Aprobado'");
    $stmt_permiso->bind_param("is", $id_empleado, $fecha_hoy);
    $stmt_permiso->execute();
    $res_permiso = $stmt_permiso->get_result();
    
    if ($res_permiso->num_rows > 0) {
        $permiso = $res_permiso->fetch_assoc();
        echo json_encode(['status' => 'error', 'message' => 'Acceso denegado: Empleado de permiso (' . $permiso['tipo'] . ').']);
        exit;
    }
    
    $nombre_completo = $empleado['nombre'] . ' ' . $empleado['apellido'];

    // Verificar si ya tiene entrada sin salida
    $fecha_hoy = date('Y-m-d');
    $stmt_asistencia = $conexion->prepare("SELECT id_asistencia, entrada, salida FROM asistencia WHERE id_empleado = ? AND DATE(entrada) = ? ORDER BY id_asistencia DESC LIMIT 1");
    $stmt_asistencia->bind_param("is", $id_empleado, $fecha_hoy);
    $stmt_asistencia->execute();
    $res_asistencia = $stmt_asistencia->get_result();

    // Generar un ID de transacción biométrica simulada
    $biometrico_id = uniqid('sim_');

    if ($res_asistencia->num_rows > 0) {
        $asistencia = $res_asistencia->fetch_assoc();
        if (empty($asistencia['salida'])) {
            // El empleado no tiene salida registrada aún, entonces registramos salida
            $id_asistencia = $asistencia['id_asistencia'];
            $stmt_update = $conexion->prepare("UPDATE asistencia SET salida = NOW() WHERE id_asistencia = ?");
            $stmt_update->bind_param("i", $id_asistencia);
            
            if ($stmt_update->execute()) {
                echo json_encode([
                    'status' => 'success', 
                    'accion' => 'salida', 
                    'empleado' => $nombre_completo, 
                    'hora' => date('h:i:s A'),
                    'mensaje' => 'Salida registrada correctamente'
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al comunicar con la base de datos.']);
            }
            exit;
        }
    }

    // Si no hay registro previo hoy o el último ya tiene salida, registramos una nueva entrada
    $stmt_insert = $conexion->prepare("INSERT INTO asistencia (id_empleado, biometrico_id, estado_biometrico, entrada) VALUES (?, ?, 'exito', NOW())");
    $stmt_insert->bind_param("is", $id_empleado, $biometrico_id);
    
    if ($stmt_insert->execute()) {
        echo json_encode([
            'status' => 'success', 
            'accion' => 'entrada', 
            'empleado' => $nombre_completo, 
            'hora' => date('h:i:s A'),
            'mensaje' => 'Entrada registrada correctamente'
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al registrar la asistencia.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido.']);
}
?>
