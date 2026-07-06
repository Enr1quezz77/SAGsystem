<?php
include 'modelo/conexion.php';
$r = $conexion->query('SELECT * FROM institucion');
if ($r) {
    print_r($r->fetch_all(MYSQLI_ASSOC));
} else {
    echo "Query failed: " . $conexion->error;
}
?>
