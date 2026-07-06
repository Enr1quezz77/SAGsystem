<?php
session_start();
include '../modelo/conexion.php';
include_once '../modelo/auditoria_helper.php';

if (!empty($_POST["tasa"])) {
    $tasa = floatval($_POST["tasa"]);
    
    // Asumiendo que solo hay un registro de tasa
    $check = $conexion->query("SELECT id_tasa FROM tasa_cambio LIMIT 1");
    if ($check->num_rows > 0) {
        $id_tasa = $check->fetch_object()->id_tasa;
        $sql = $conexion->query("UPDATE tasa_cambio SET tasa = $tasa WHERE id_tasa = $id_tasa");
    } else {
        $sql = $conexion->query("INSERT INTO tasa_cambio (tasa) VALUES ($tasa)");
    }
    
    if ($sql == true) {
        $_SESSION['mensaje'] = "Tasa de cambio actualizada correctamente a Bs. $tasa";
        registrar_auditoria($conexion, 'Actualizar', 'Nómina', "Actualizó tasa de cambio a Bs. $tasa");
    } else {
        $_SESSION['mensaje'] = "Error al actualizar la tasa.";
    }
}
header("Location: ../vista/nomina.php");
exit();
?>
