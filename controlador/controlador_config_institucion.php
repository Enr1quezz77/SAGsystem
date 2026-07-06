<?php
session_start();
if (empty($_SESSION['email']) and empty($_SESSION['password'])) {
    die("Acceso denegado");
}

include "../modelo/conexion.php";

if (!empty($_POST["btn_guardar_institucion"])) {
    $nombre = $_POST["nombre"];
    $ruc = $_POST["ruc"];
    $telefono = $_POST["telefono"];
    $ubicacion = $_POST["ubicacion"];

    // Actualizar datos de texto
    $query = $conexion->query("SELECT id_institucion FROM institucion LIMIT 1");
    if ($query && $query->num_rows > 0) {
        $id = $query->fetch_object()->id_institucion;
        $stmt = $conexion->prepare("UPDATE institucion SET nombre=?, ruc=?, telefono=?, ubicacion=? WHERE id_institucion=?");
        $stmt->bind_param("ssssi", $nombre, $ruc, $telefono, $ubicacion, $id);
        $stmt->execute();
        $stmt->close();
    } else {
        $stmt = $conexion->prepare("INSERT INTO institucion (nombre, ruc, telefono, ubicacion) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nombre, $ruc, $telefono, $ubicacion);
        $stmt->execute();
        $stmt->close();
    }

    // Manejar subida de logo
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['logo']['tmp_name'];
        $fileName = $_FILES['logo']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        if (in_array($fileExtension, ['png', 'jpg', 'jpeg'])) {
            $dest_path = '../img/logo.png'; // Reemplazar siempre con logo.png
            if(move_uploaded_file($fileTmpPath, $dest_path)) {
                // Éxito al subir logo
            }
        }
    }

    $_SESSION['mensaje'] = "Perfil de la institución actualizado correctamente.";
}

header("Location: ../vista/configuracion.php");
exit();
?>
