<?php
$c = new mysqli('localhost', 'root', '', 'sis_asistencia');
$res = $c->query('SHOW TABLES');
while($r = $res->fetch_array()) {
    echo "\nTable: {$r[0]}\n";
    $res2 = $c->query("DESCRIBE {$r[0]}");
    while($r2 = $res2->fetch_assoc()) {
        echo "  {$r2['Field']} ({$r2['Type']})\n";
    }
}
?>
