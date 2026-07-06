<?php
require '../modelo/conexion.php';
require_once 'zklibrary.php';

echo "Iniciando sincronización de empleados existentes...\n";

$zk = new ZKLibrary('192.168.1.200', 4370);
if (!$zk->connect()) {
    echo "Error: No se pudo conectar al dispositivo ZKTeco.\n";
    exit;
}

$stmt = $conexion->query("SELECT id_empleado, nombre FROM empleado");

$contador = 0;
if ($stmt->num_rows > 0) {
    while ($row = $stmt->fetch_assoc()) {
        $id = $row['id_empleado'];
        $nombre_corto = substr(strtoupper($row['nombre']), 0, 10);
        
        $zk->setUser($id, $id, $nombre_corto, '', 0);
        $contador++;
        echo "Sincronizado ID: $id - Nombre: $nombre_corto\n";
    }
}

$zk->disconnect();
echo "Resumen: $contador empleados existentes transferidos al reloj correctamente.\n";
?>
