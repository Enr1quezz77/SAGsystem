<?php
include 'modelo/conexion.php';
$sql = "ALTER TABLE caja_cuadres 
        ADD COLUMN ventas_reales_bs DECIMAL(10, 2) NOT NULL DEFAULT 0 AFTER gastos_caja_usd,
        ADD COLUMN ventas_reales_usd DECIMAL(10, 2) NOT NULL DEFAULT 0 AFTER ventas_reales_bs";
if ($conexion->query($sql)) {
    echo "Alter successful";
} else {
    echo $conexion->error;
}
$conexion->close();
?>
