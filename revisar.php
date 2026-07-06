<?php
require 'controlador/zklibrary.php';
$zk = new ZKLibrary('192.168.1.200', 4370);
$zk->connect();
$users = $zk->getUser();
if(is_array($users)) {
    foreach($users as $uid => $data) {
        $userid = $data[0];
        $name = trim($data[1]);
        echo "UID_Interno: $uid | ID_Pantalla: $userid | Nombre: $name\n";
    }
} else {
    echo "Falla al obtener usuarios.";
}
$zk->disconnect();
?>
