<?php
$conexion = new mysqli('localhost', 'root', '', 'sis_asistencia');

$sql = "
CREATE TABLE IF NOT EXISTS deduccion (
    id_deduccion INT(11) AUTO_INCREMENT PRIMARY KEY,
    id_empleado INT(11) NOT NULL,
    mes INT(11) NOT NULL,
    anio INT(11) NOT NULL,
    quincena INT(11) NOT NULL,
    motivo VARCHAR(255) NOT NULL,
    monto_usd DECIMAL(10,2) NOT NULL,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_empleado) REFERENCES empleado(id_empleado) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

if ($conexion->query($sql)) {
    echo "Tabla 'deduccion' creada exitosamente.";
} else {
    echo "Error al crear la tabla: " . $conexion->error;
}
?>
