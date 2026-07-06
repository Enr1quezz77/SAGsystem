<?php
$conexion = new mysqli('localhost', 'root', '', 'sis_asistencia');

// DESCRIBE empleado
$r = $conexion->query('SHOW COLUMNS FROM empleado');
echo "== TABLA EMPLEADO ==\n";
while($row = $r->fetch_assoc()) {
    echo str_pad($row['Field'], 15) . " | " . $row['Type'] . "\n";
}

echo "====================\n";

// Let's test a sample insert
$sql = "insert into empleado(nombre,apellido,cargo,dni,foto) values('Test','Test',1, '123456', '')";
$r = $conexion->query($sql);
if(!$r) {
    echo "ERROR AL INSERTAR: " . $conexion->error . "\n";
} else {
    echo "INSERCION EXITOSA. ELIMINANDO TEST...\n";
    $conexion->query("DELETE FROM empleado WHERE nombre='Test' AND apellido='Test'");
}
?>
