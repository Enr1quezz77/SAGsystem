<?php
$conexion = new mysqli('localhost', 'root', '', 'sis_asistencia');
if ($conexion->connect_error) {
    die("Connection failed: " . $conexion->connect_error);
}

$result = $conexion->query("SHOW COLUMNS FROM empleado LIKE 'foto'");
if ($result->num_rows == 0) {
    if ($conexion->query("ALTER TABLE empleado ADD COLUMN foto VARCHAR(255) NULL")) {
        echo "Column 'foto' added successfully.\n";
    } else {
        echo "Error adding column: " . $conexion->error . "\n";
    }
} else {
    echo "Column 'foto' already exists.\n";
}
$conexion->close();
?>
