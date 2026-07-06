<?php
include 'modelo/conexion.php';
$sql = "ALTER TABLE caja_cuadres 
        DROP COLUMN ventas_reales_bs,
        DROP COLUMN ventas_reales_usd,
        ADD COLUMN punto_venta_bs DECIMAL(10, 2) NOT NULL DEFAULT 0 AFTER gastos_caja_usd,
        ADD COLUMN pago_movil_bs DECIMAL(10, 2) NOT NULL DEFAULT 0 AFTER punto_venta_bs,
        ADD COLUMN zelle_usd DECIMAL(10, 2) NOT NULL DEFAULT 0 AFTER pago_movil_bs,
        ADD COLUMN cashea_usd DECIMAL(10, 2) NOT NULL DEFAULT 0 AFTER zelle_usd";
if ($conexion->query($sql)) {
    echo "Alter successful";
} else {
    echo $conexion->error;
}
$conexion->close();
?>
