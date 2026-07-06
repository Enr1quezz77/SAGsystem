<?php
$conexion = new mysqli('localhost', 'root', '', 'sis_asistencia');

function describeTable($conn, $table) {
    echo "Table: $table\n";
    $res = $conn->query("DESCRIBE $table");
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            echo " - {$row['Field']} ({$row['Type']})\n";
        }
    } else {
        echo "Table does not exist or error: " . $conn->error . "\n";
    }
    echo "\n";
}

describeTable($conexion, 'institucion');
describeTable($conexion, 'tasa_cambio');
?>
