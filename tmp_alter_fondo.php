<?php
include 'modelo/conexion.php';
if ($conexion->query("ALTER TABLE caja_cuadres ADD COLUMN fondo_apertura_bs DECIMAL(10, 2) NOT NULL DEFAULT 0 AFTER fondo_apertura_usd")) {
    echo "Alter successful";
} else {
    echo $conexion->error;
}
$conexion->close();
?>
