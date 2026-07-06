<?php
session_start();
include '../modelo/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btnsubir'])) {
    
    $id_empleado = $_POST['id_empleado'];
    $tipo_documento = $_POST['tipo_documento'];
    $fecha_vence = !empty($_POST['fecha_vence']) ? $_POST['fecha_vence'] : null;
    
    // Directorio de subida seguro
    $directorioDestino = '../uploads/expedientes/';
    if (!is_dir($directorioDestino)) {
        mkdir($directorioDestino, 0777, true);
    }

    if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
        $archivo = $_FILES['archivo'];
        $nombreOriginal = basename($archivo['name']);
        $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));
        
        // Bloquear archivos maliciosos
        $extensionesProhibidas = ['php', 'phtml', 'exe', 'bat', 'sh', 'js'];
        if (in_array($extension, $extensionesProhibidas)) {
            $_SESSION['mensaje'] = "Error: Por seguridad no se permite subir este formato de archivo.";
            header("Location: ../vista/visualizar_archivos.php");
            exit;
        }

        // Generar un nombre único para evitar colisiones: [timestamp]_[uniqid].[ext]
        $nombreEncriptado = time() . '_' . uniqid() . '.' . $extension;
        $rutaFinal = $directorioDestino . $nombreEncriptado;

        if (move_uploaded_file($archivo['tmp_name'], $rutaFinal)) {
            // Guardar registro en la BD
            $stmt = $conexion->prepare("INSERT INTO documento_empleado (id_empleado, nombre_original, nombre_encriptado, tipo_documento, ruta_archivo, fecha_vence) VALUES (?, ?, ?, ?, ?, ?)");
            $ruta_relativa = 'uploads/expedientes/' . $nombreEncriptado; // guardamos ruta relativa base
            $stmt->bind_param("isssss", $id_empleado, $nombreOriginal, $nombreEncriptado, $tipo_documento, $ruta_relativa, $fecha_vence);
            
            if ($stmt->execute()) {
                $_SESSION['mensaje'] = "Documento vinculado al expediente del empleado correctamente.";
            } else {
                unlink($rutaFinal); // Revertir archivo subido si falla SQL
                $_SESSION['mensaje'] = "Error al vincular el documento en la base de datos.";
            }
        } else {
            $_SESSION['mensaje'] = "Error al mover el archivo al servidor.";
        }
    } else {
        $_SESSION['mensaje'] = "Error en el archivo cargado, intente nuevamente.";
    }
} else {
    $_SESSION['mensaje'] = "Petición denegada.";
}

header("Location: ../vista/visualizar_archivos.php");
exit();
?>
