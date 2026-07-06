<?php
session_start();
include '../modelo/conexion.php';

if (isset($_GET['eliminar'])) {
    
    // Verificamos permisos
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        $_SESSION['mensaje'] = "Error: Permisos insuficientes para borrar expedientes.";
        header("Location: ../vista/visualizar_archivos.php");
        exit;
    }

    $id_documento = $_GET['eliminar'];
    
    // Buscar el archivo para saber su ruta física y borrarlo
    $stmt = $conexion->prepare("SELECT ruta_archivo FROM documento_empleado WHERE id_documento = ?");
    $stmt->bind_param("i", $id_documento);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $documento = $resultado->fetch_assoc();
        
        $rutaFisica = '../' . $documento['ruta_archivo']; 
        
        // 1. Lo borramos en la BBDD
        $stmtDel = $conexion->prepare("DELETE FROM documento_empleado WHERE id_documento = ?");
        $stmtDel->bind_param("i", $id_documento);
        
        if ($stmtDel->execute()) {
            // 2. Lo borramos del servidor
            if (file_exists($rutaFisica)) {
                unlink($rutaFisica);
            }
            $_SESSION['mensaje'] = "Documento removido del expediente con éxito.";
        } else {
            $_SESSION['mensaje'] = "Error al borrar registro en la base de datos.";
        }
    } else {
        $_SESSION['mensaje'] = "Error: El documento solicitado no existe o ya fue eliminado.";
    }
}

header("Location: ../vista/visualizar_archivos.php");
exit();
?>
