<?php
require 'controlador/zklibrary.php';
$zk = new ZKLibrary('192.168.1.200', 4370);
echo date('Y-m-d H:i:s', $zk->decodeTime(845606987));
?>
