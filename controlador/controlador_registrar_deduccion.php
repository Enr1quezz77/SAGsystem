<?php
session_start();
include "../modelo/conexion.php";

if (!empty($_POST["btn_registrar_deduccion"])) {
    $id_empleado = isset($_POST["id_empleado"]) ? (int)$_POST["id_empleado"] : 0;
    $mes = isset($_POST["mes_deduccion"]) ? (int)$_POST["mes_deduccion"] : date('m');
    $anio = isset($_POST["anio_deduccion"]) ? (int)$_POST["anio_deduccion"] : date('Y');
    $quincena = isset($_POST["quincena_deduccion"]) ? (int)$_POST["quincena_deduccion"] : (date('d') <= 15 ? 1 : 2);
    $motivo = isset($_POST["motivo_deduccion"]) ? $_POST["motivo_deduccion"] : "";
    $monto_usd = isset($_POST["monto_deduccion_usd"]) ? floatval($_POST["monto_deduccion_usd"]) : 0.00;

    if ($id_empleado > 0 && !empty($motivo) && $monto_usd > 0) {
        $stmt = $conexion->prepare("INSERT INTO deduccion (id_empleado, mes, anio, quincena, motivo, monto_usd) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiisd", $id_empleado, $mes, $anio, $quincena, $motivo, $monto_usd);
        
        if ($stmt->execute()) {
            $_SESSION['mensaje'] = "Deducción de $" . number_format($monto_usd, 2) . " registrada exitosamente para el empleado.";
        } else {
            $_SESSION['mensaje'] = "Error al registrar deducción: " . $conexion->error;
        }
        $stmt->close();
    } else {
        $_SESSION['mensaje'] = "Error: Por favor complete todos los campos obligatorios y asegúrese de que el monto sea mayor a 0.";
    }
}

// Redirigir de vuelta a nómina manteniendo los filtros
$url = "../vista/nomina.php";
if(isset($_POST["anio_deduccion"]) && isset($_POST["mes_deduccion"]) && isset($_POST["quincena_deduccion"])){
    $url .= "?anio=".$_POST["anio_deduccion"]."&mes=".$_POST["mes_deduccion"]."&quincena=".$_POST["quincena_deduccion"];
}
header("Location: $url");
exit();
?>
