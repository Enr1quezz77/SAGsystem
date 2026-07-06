<?php
require 'zklibrary.php';

$zk = new ZKLibrary('192.168.1.200', 4370);
if ($zk->connect()) {
    $zk->clearAdmin();
    $zk->disconnect();
    echo "Privilegios de administrador borrados correctamente.";
} else {
    echo "Falla al conectar al reloj.";
}
?>
