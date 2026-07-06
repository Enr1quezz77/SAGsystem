<?php
include 'modelo/conexion.php';

$sql1 = "ALTER TABLE tasa_cambio ADD COLUMN sueldo_base_usd DECIMAL(10,2) NOT NULL DEFAULT 210.00";
$sql2 = "ALTER TABLE tasa_cambio ADD COLUMN cesta_ticket_usd DECIMAL(10,2) NOT NULL DEFAULT 30.00";

if ($conexion->query($sql1)) {
    echo "Columna sueldo_base_usd agregada.\n";
} else {
    echo "Error 1: " . $conexion->error . "\n";
}

if ($conexion->query($sql2)) {
    echo "Columna cesta_ticket_usd agregada.\n";
} else {
    echo "Error 2: " . $conexion->error . "\n";
}
$conexion->close();
?>
