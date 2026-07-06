<?php
session_start();
include '../modelo/conexion.php';
include_once '../modelo/auditoria_helper.php';

if (!empty($_POST["id_empleado"]) && !empty($_POST["tipo"]) && !empty($_POST["fecha_inicio"]) && !empty($_POST["fecha_fin"])) {
    $id_empleado = $_POST["id_empleado"];
    $tipo = $_POST["tipo"];
    $fecha_inicio = $_POST["fecha_inicio"];
    $fecha_fin = $_POST["fecha_fin"];
    $motivo = isset($_POST["motivo"]) ? $_POST["motivo"] : '';

    $stmt = $conexion->prepare("INSERT INTO permisos (id_empleado, tipo, fecha_inicio, fecha_fin, motivo, estado) VALUES (?, ?, ?, ?, ?, 'Aprobado')");
    $stmt->bind_param("issss", $id_empleado, $tipo, $fecha_inicio, $fecha_fin, $motivo);
    
    if ($stmt->execute()) {
        $_SESSION['mensaje_permiso'] = "Permiso registrado correctamente.";
        
        $sql_info = $conexion->query("SELECT nombre, apellido FROM empleado WHERE id_empleado=$id_empleado");
        $empleado_info = $sql_info->fetch_object();
        $nombre_completo = $empleado_info ? $empleado_info->nombre . " " . $empleado_info->apellido : "ID $id_empleado";
        
        registrar_auditoria($conexion, 'Registrar', 'Permisos', "Registró $tipo para: $nombre_completo del $fecha_inicio al $fecha_fin");
        
    } else {
        $_SESSION['mensaje_permiso'] = "Error al registrar el permiso.";
    }
    $stmt->close();
} else {
    $_SESSION['mensaje_permiso'] = "Error: Faltan datos requeridos.";
}

header("Location: ../vista/permisos.php");
exit();
?>
