<?php
session_start();

if (empty($_SESSION['email']) || empty($_SESSION['password']) || $_SESSION['role'] !== 'admin') {
    die("Acceso denegado");
}

if (!empty($_POST['btn_config_admin'])) {
    if (!empty($_POST['nueva_clave_admin'])) {
        $nueva_clave = $_POST['nueva_clave_admin'];
        // Guardamos en el archivo json el nuevo valor
        $config_file = '../modelo/admin_config.json';
        $data = ['admin_key' => $nueva_clave];
        
        if (file_put_contents($config_file, json_encode($data, JSON_PRETTY_PRINT))) {
            $_SESSION['config_admin_success'] = "Clave de administrador actualizada correctamente.";
        } else {
            $_SESSION['config_admin_error'] = "No se pudo guardar la configuración. Verifique los permisos del archivo.";
        }
    } else {
        $_SESSION['config_admin_error'] = "El campo de clave no puede estar vacío.";
    }
}
header("Location: ../vista/usuario.php");
exit();
?>
