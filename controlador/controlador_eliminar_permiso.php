<?php
session_start();
include '../modelo/conexion.php';
include_once '../modelo/auditoria_helper.php';

if (!empty($_GET["id"])) {
    $id = $_GET["id"];
    
    // Obtener info antes de borrar
    $sql_info = $conexion->query("SELECT p.tipo, e.nombre, e.apellido FROM permisos p INNER JOIN empleado e ON p.id_empleado = e.id_empleado WHERE p.id_permiso = $id");
    $info = $sql_info->fetch_object();
    $detalle_auditoria = $info ? "Eliminó permiso de {$info->tipo} para {$info->nombre} {$info->apellido}" : "Eliminó permiso ID $id";

    $sql = $conexion->query("DELETE FROM permisos WHERE id_permiso = $id");
    if ($sql == true) {
        $_SESSION['mensaje_permiso'] = "Permiso eliminado correctamente.";
        registrar_auditoria($conexion, 'Eliminar', 'Permisos', $detalle_auditoria);
    } else {
        $_SESSION['mensaje_permiso'] = "Error al eliminar el permiso.";
    }
}
header("Location: ../vista/permisos.php");
exit();
?>
