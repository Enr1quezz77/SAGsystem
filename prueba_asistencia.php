<?php
require 'controlador/zklibrary.php';
$zk = new ZKLibrary('192.168.1.200', 4370);
$zk->connect();
$att = $zk->getAttendance();
// Desactivar warnings para ver el output limpio
error_reporting(E_ERROR | E_PARSE);
print_r($att);
$zk->disconnect();
?>
