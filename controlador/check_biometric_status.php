<?php
header('Content-Type: application/json');
error_reporting(0); // Evitar que warnings rompan el JSON

require_once 'zklibrary.php';

// Leer configuración del archivo JSON
$ip_dispositivo = '192.168.1.201'; // Default
$puerto = 4370;

$bio_file = '../modelo/config_biometrico.json';
if (file_exists($bio_file)) {
    $bio_data = json_decode(file_get_contents($bio_file), true);
    if ($bio_data) {
        $ip_dispositivo = $bio_data['ip'] ?? $ip_dispositivo;
        $puerto = (int)($bio_data['puerto'] ?? $puerto);
    }
}

$zk = new ZKLibrary($ip_dispositivo, $puerto);
// Timeout corto para que el dashboard no se quede esperando mucho si está desconectado
$zk->setTimeout(1, 0);

if ($zk->connect()) {
    $zk->disconnect();
    echo json_encode(['status' => 'connected', 'message' => 'Dispositivo Biométrico En Línea']);
} else {
    echo json_encode(['status' => 'disconnected', 'message' => 'Dispositivo Biométrico Fuera de Línea']);
}
?>
