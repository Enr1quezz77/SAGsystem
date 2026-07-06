<?php
session_start();
if (empty($_SESSION['email']) and empty($_SESSION['password'])) {
    die("Acceso denegado");
}

if (!empty($_POST["btn_guardar_biometrico"])) {
    $ip = $_POST["ip_biometrico"];
    $puerto = $_POST["puerto_biometrico"];

    if (!empty($ip) && !empty($puerto)) {
        $file = '../modelo/config_biometrico.json';
        $data = [
            "ip" => $ip,
            "puerto" => $puerto
        ];
        if (file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT))) {
            $_SESSION['mensaje'] = "Configuración del Biométrico actualizada correctamente.";
        } else {
            $_SESSION['mensaje'] = "Error al guardar el archivo de configuración.";
        }
    } else {
        $_SESSION['mensaje'] = "Error: IP y Puerto son obligatorios.";
    }
}

header("Location: ../vista/configuracion.php");
exit();
?>
