<?php
$conexion = new mysqli('localhost', 'root', '', 'sis_asistencia');
$r = $conexion->query('SHOW TABLES');
while($t = $r->fetch_row()) {
    echo "Table: {$t[0]}\n";
    $cols = $conexion->query("DESCRIBE {$t[0]}");
    while($col = $cols->fetch_assoc()) {
        echo "  {$col['Field']} - {$col['Type']}\n";
    }
}
?>
