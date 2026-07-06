<?php
require '../modelo/conexion.php';

// Simulación de captura biométrica
function capturarBiometrico($empleadoId) {
    // Aquí se usaría el SDK/API del dispositivo biométrico
    $biometricoId = uniqid(); // Generar un ID único para la biometría
    $estado = 'exito'; // Simular éxito en la captura

    // Registrar asistencia en la base de datos
    global $conn;
    $stmt = $conn->prepare("INSERT INTO asistencia (id_empleado, biometrico_id, estado_biometrico, entrada) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param('iss', $empleadoId, $biometricoId, $estado);

    if ($stmt->execute()) {
        echo "Asistencia registrada exitosamente.";
    } else {
        echo "Error al registrar la asistencia.";
    }
}

// Ejemplo de uso
$empleadoId = 1; // ID del empleado (simulado)
capturarBiometrico($empleadoId);
?>