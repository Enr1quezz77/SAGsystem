<?php
require '../modelo/conexion.php';
require 'zklibrary.php';

header('Content-Type: application/json');

// Configuración del dispositivo ZKTeco (Por defecto)
$zk_ip = '192.168.1.201';
$zk_port = 4370;

// Leer configuración del archivo JSON
$bio_file = '../modelo/config_biometrico.json';
if (file_exists($bio_file)) {
    $bio_data = json_decode(file_get_contents($bio_file), true);
    if ($bio_data) {
        $zk_ip = $bio_data['ip'] ?? $zk_ip;
        $zk_port = (int)($bio_data['puerto'] ?? $zk_port);
    }
}

$zk = new ZKLibrary($zk_ip, $zk_port);

if (!$zk->connect()) {
    echo json_encode(['status' => 'error', 'message' => "No se pudo conectar al dispositivo ZKTeco en $zk_ip:$zk_port"]);
    exit;
}

$zk->disableDevice(); // Bloquear dispositivo mientras leemos
$attendanceLogs = $zk->getAttendance(); // Leer registros
$zk->enableDevice(); // Desbloquear dispositivo
$zk->disconnect(); // Desconectar

if (!$attendanceLogs || empty($attendanceLogs)) {
    echo json_encode(['status' => 'info', 'message' => 'No hay nuevos registros de asistencia en el dispositivo.']);
    exit;
}

$registrosNuevos = 0;
$actualizaciones = 0;

foreach ($attendanceLogs as $log) {
    // El SDK devuelve el $log como un array, donde $log[1] es el ID de usuario (que usualmente corresponde al DNI)
    // $log[3] es la fecha y hora "Y-m-d H:i:s"
    $userid = $log[1]; 
    $timestamp = $log[3];
    $fecha = date('Y-m-d', strtotime($timestamp));

    // Buscar empleado por el ID numérico del reloj checador (que coincidirá con id_empleado)
    $stmt = $conexion->prepare("SELECT id_empleado, estado FROM empleado WHERE id_empleado = ?");
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $empleado = $result->fetch_assoc();
        
        // Si está suspendido, no se permite el registro de su asistencia proveniente del biométrico
        if (isset($empleado['estado']) && $empleado['estado'] === 'Suspendido') {
            continue; 
        }

        $id_empleado = $empleado['id_empleado'];

        // Verificar si tiene un permiso aprobado para el día del registro
        $stmt_permiso = $conexion->prepare("SELECT tipo FROM permisos WHERE id_empleado = ? AND ? BETWEEN fecha_inicio AND fecha_fin AND estado = 'Aprobado'");
        $stmt_permiso->bind_param("is", $id_empleado, $fecha);
        $stmt_permiso->execute();
        $res_permiso = $stmt_permiso->get_result();
        
        if ($res_permiso->num_rows > 0) {
            continue; // Si tiene permiso o vacaciones, ignorar asistencia
        }

        // Buscar si ya existe la entrada para hoy
        $stmt_asistencia = $conexion->prepare("SELECT id_asistencia, entrada, salida FROM asistencia WHERE id_empleado = ? AND DATE(entrada) = ? ORDER BY id_asistencia DESC LIMIT 1");
        $stmt_asistencia->bind_param("is", $id_empleado, $fecha);
        $stmt_asistencia->execute();
        $res_asistencia = $stmt_asistencia->get_result();

        if ($res_asistencia->num_rows > 0) {
            $asistencia = $res_asistencia->fetch_assoc();
            
            // Si la hora de entrada es exacta al timestamp, significa que es el mismo registro (ya guardado)
            if ($asistencia['entrada'] === $timestamp) {
                continue; // Saltar, ya lo insertamos
            }
            
            // Si tiene entrada pero NO tiene salida (o la salida es nula/vacia), actualizamos la salida con el timestamp más reciente
            // Siempre y cuando este timestamp sea mayor que la entrada.
            if (empty($asistencia['salida']) && strtotime($timestamp) > strtotime($asistencia['entrada'])) {
                $id_asistencia = $asistencia['id_asistencia'];
                $stmt_update = $conexion->prepare("UPDATE asistencia SET salida = ? WHERE id_asistencia = ?");
                $stmt_update->bind_param("si", $timestamp, $id_asistencia);
                if ($stmt_update->execute()) {
                    $actualizaciones++;
                }
            } 
            // Si ya hay salida registrada, si el timestamp es aún más nuevo, actualizamos esa salida (último punch del día)
            else if (!empty($asistencia['salida']) && strtotime($timestamp) > strtotime($asistencia['salida'])) {
                $id_asistencia = $asistencia['id_asistencia'];
                $stmt_update = $conexion->prepare("UPDATE asistencia SET salida = ? WHERE id_asistencia = ?");
                $stmt_update->bind_param("si", $timestamp, $id_asistencia);
                if ($stmt_update->execute()) {
                    $actualizaciones++;
                }
            }
        } else {
            // No existe asistencia para este día, insertamos una nueva
            $estado = 'exito';
            $stmt_insert = $conexion->prepare("INSERT INTO asistencia (id_empleado, biometrico_id, estado_biometrico, entrada) VALUES (?, ?, ?, ?)");
            $stmt_insert->bind_param("isss", $id_empleado, $userid, $estado, $timestamp);
            if ($stmt_insert->execute()) {
                $registrosNuevos++;
            }
        }
    }
}

echo json_encode([
    'status' => 'success', 
    'message' => 'Sincronización completada.',
    'registros_insertados' => $registrosNuevos,
    'registros_actualizados' => $actualizaciones
]);
?>