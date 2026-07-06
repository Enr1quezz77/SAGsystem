<?php
require 'modelo/conexion.php';
require 'controlador/zklibrary.php';

header('Content-Type: application/json');

// Leer configuración del biométrico
$ip_dispositivo = '192.168.1.201';
$puerto = 4370;

$bio_file = 'modelo/config_biometrico.json';
if (file_exists($bio_file)) {
    $bio_data = json_decode(file_get_contents($bio_file), true);
    if ($bio_data) {
        $ip_dispositivo = $bio_data['ip'] ?? $ip_dispositivo;
        $puerto = (int)($bio_data['puerto'] ?? $puerto);
    }
}

$zk = new ZKLibrary($ip_dispositivo, $puerto);
$zk->setTimeout(5, 0);

if (!$zk->connect()) {
    echo json_encode(['status' => 'error', 'message' => "No se pudo conectar al dispositivo en $ip_dispositivo:$puerto"]);
    exit;
}

// Leer hora actual del dispositivo ANTES de cambiarla
$hora_dispositivo_antes = $zk->getTime();

// Sincronizar con la hora actual del servidor PHP
$hora_servidor = date('Y-m-d H:i:s');
$resultado = $zk->setTime($hora_servidor);

// Leer hora del dispositivo DESPUÉS del cambio para confirmar
$hora_dispositivo_despues = $zk->getTime();

$zk->disconnect();

echo json_encode([
    'status'             => $resultado ? 'success' : 'error',
    'message'            => $resultado ? '¡Hora sincronizada correctamente!' : 'No se pudo sincronizar la hora.',
    'hora_servidor'      => $hora_servidor,
    'hora_bio_antes'     => $hora_dispositivo_antes,
    'hora_bio_despues'   => $hora_dispositivo_despues,
]);
?>
