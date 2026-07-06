<?php
include 'modelo/conexion.php';
if ($conexion->query("ALTER TABLE caja_cuadres ADD COLUMN turno ENUM('Mañana', 'Noche') NOT NULL DEFAULT 'Mañana' AFTER id_empleado")) {
    echo "Alter successful";
} else {
    echo $conexion->error;
}
$conexion->close();
?>
