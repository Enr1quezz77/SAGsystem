<?php
$c = new mysqli('localhost', 'root', '', 'sis_asistencia');
$res = $c->query('SELECT * FROM permisos');
while($r = $res->fetch_assoc()) {
    print_r($r);
}
?>
