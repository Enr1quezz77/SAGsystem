<?php
require '../modelo/conexion.php';

// Simulación de comunicación con el dispositivo biométrico
function recibirDatosBiometricos() {
    // Aquí se implementaría la lógica para recibir datos del dispositivo
    // Por ejemplo, leer datos desde un puerto serial o una API HTTP
    $biometricoId = uniqid(); // Simulación de ID único
    return $biometricoId;
}

function validarBiometrico($biometricoId) {
    global $conexion;
    // Obtenemos el empleado asociado al biometrico_id
    $stmt = $conexion->prepare("SELECT e.id_empleado, e.estado FROM empleado e INNER JOIN asistencia a ON e.id_empleado = a.id_empleado WHERE a.biometrico_id = ? LIMIT 1");
    $stmt->bind_param('s', $biometricoId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['estado'] === 'Suspendido') {
            return false;
        }

        $id_empleado = $row['id_empleado'];
        $fecha_hoy = date('Y-m-d');

        // Verificar si tiene un permiso aprobado para el día de hoy
        $stmt_permiso = $conexion->prepare("SELECT tipo FROM permisos WHERE id_empleado = ? AND ? BETWEEN fecha_inicio AND fecha_fin AND estado = 'Aprobado'");
        $stmt_permiso->bind_param("is", $id_empleado, $fecha_hoy);
        $stmt_permiso->execute();
        $res_permiso = $stmt_permiso->get_result();
        
        if ($res_permiso->num_rows > 0) {
            return false; // Actúa como suspendido, no permitir marcaje
        }

        return $id_empleado;
    } else {
        return false;
    }
}

// Registrar asistencia
function registrarAsistencia($empleadoId, $biometricoId) {
    global $conexion; // Cambiado de $conn a $conexion
    $stmt = $conexion->prepare("INSERT INTO asistencia (id_empleado, biometrico_id, estado_biometrico, entrada) VALUES (?, ?, 'exito', NOW())");
    $stmt->bind_param('is', $empleadoId, $biometricoId);

    if ($stmt->execute()) {
        echo "Asistencia registrada exitosamente.";
    } else {
        echo "Error al registrar la asistencia.";
    }
}


?>