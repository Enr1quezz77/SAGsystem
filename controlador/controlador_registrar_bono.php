<?php
session_start();
include "../modelo/conexion.php";

if (!empty($_POST["btn_registrar_bono"])) {
    $id_empleado = isset($_POST["id_empleado"]) ? (int)$_POST["id_empleado"] : 0;
    $mes = isset($_POST["mes_bono"]) ? (int)$_POST["mes_bono"] : date('m');
    $anio = isset($_POST["anio_bono"]) ? (int)$_POST["anio_bono"] : date('Y');
    $quincena = isset($_POST["quincena_bono"]) ? (int)$_POST["quincena_bono"] : (date('d') <= 15 ? 1 : 2);
    $motivo = isset($_POST["motivo_bono"]) ? $_POST["motivo_bono"] : "";
    $monto_usd = isset($_POST["monto_bono_usd"]) ? floatval($_POST["monto_bono_usd"]) : 0.00;

    if ($id_empleado > 0 && !empty($motivo) && $monto_usd > 0) {
        $stmt = $conexion->prepare("INSERT INTO bonificacion (id_empleado, mes, anio, quincena, motivo, monto_usd) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiisd", $id_empleado, $mes, $anio, $quincena, $motivo, $monto_usd);
        
        if ($stmt->execute()) {
            $_SESSION['mensaje'] = "Bono de $" . number_format($monto_usd, 2) . " registrado exitosamente para el empleado.";
        } else {
            $_SESSION['mensaje'] = "Error al registrar bonificación: " . $conexion->error;
        }
        $stmt->close();
    } else {
        $_SESSION['mensaje'] = "Error: Por favor complete todos los campos obligatorios y asegúrese de que el monto sea mayor a 0.";
    }
}

// Redirigir de vuelta a nómina manteniendo los filtros
$url = "../vista/nomina.php";
if(isset($_POST["anio_bono"]) && isset($_POST["mes_bono"]) && isset($_POST["quincena_bono"])){
    $url .= "?anio=".$_POST["anio_bono"]."&mes=".$_POST["mes_bono"]."&quincena=".$_POST["quincena_bono"];
}
header("Location: $url");
exit();
?>
