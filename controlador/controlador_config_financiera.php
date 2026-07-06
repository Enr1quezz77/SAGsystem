<?php
session_start();
if (empty($_SESSION['email']) and empty($_SESSION['password'])) {
    die("Acceso denegado");
}

include "../modelo/conexion.php";

if (!empty($_POST["btn_guardar_financiera"])) {
    $tasa = isset($_POST["tasa"]) ? floatval($_POST["tasa"]) : 0;
    $sueldo = isset($_POST["sueldo_base_usd"]) ? floatval($_POST["sueldo_base_usd"]) : 0;
    $cesta = isset($_POST["cesta_ticket_usd"]) ? floatval($_POST["cesta_ticket_usd"]) : 0;

    if ($tasa > 0 && $sueldo > 0 && $cesta >= 0) {
        $query = $conexion->query("SELECT id_tasa FROM tasa_cambio LIMIT 1");
        if ($query && $query->num_rows > 0) {
            $id = $query->fetch_object()->id_tasa;
            $stmt = $conexion->prepare("UPDATE tasa_cambio SET tasa=?, sueldo_base_usd=?, cesta_ticket_usd=?, fecha_actualizacion=NOW() WHERE id_tasa=?");
            $stmt->bind_param("dddi", $tasa, $sueldo, $cesta, $id);
            $stmt->execute();
            $stmt->close();
        } else {
            $stmt = $conexion->prepare("INSERT INTO tasa_cambio (tasa, sueldo_base_usd, cesta_ticket_usd, fecha_actualizacion) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("ddd", $tasa, $sueldo, $cesta);
            $stmt->execute();
            $stmt->close();
        }
        $_SESSION['mensaje'] = "Parámetros financieros y salariales actualizados correctamente.";
    } else {
        $_SESSION['mensaje'] = "Error: Asegúrese de ingresar valores válidos mayores a cero.";
    }
}

header("Location: ../vista/configuracion.php");
exit();
?>
