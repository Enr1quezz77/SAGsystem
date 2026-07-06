<?php
session_start();
include '../modelo/conexion.php';
include_once '../modelo/auditoria_helper.php';

if (isset($_POST["sueldo_base_usd"]) && isset($_POST["cesta_ticket_usd"])) {
    $sueldo_base = floatval($_POST["sueldo_base_usd"]);
    $cesta_ticket = floatval($_POST["cesta_ticket_usd"]);
    
    $check = $conexion->query("SELECT id_tasa FROM tasa_cambio LIMIT 1");
    if ($check->num_rows > 0) {
        $id_tasa = $check->fetch_object()->id_tasa;
        $sql = $conexion->query("UPDATE tasa_cambio SET sueldo_base_usd = $sueldo_base, cesta_ticket_usd = $cesta_ticket WHERE id_tasa = $id_tasa");
    } else {
        $sql = $conexion->query("INSERT INTO tasa_cambio (sueldo_base_usd, cesta_ticket_usd) VALUES ($sueldo_base, $cesta_ticket)");
    }
    
    if ($sql == true) {
        $_SESSION['mensaje'] = "Configuración salarial actualizada: Salario $$sueldo_base | Cesta Ticket $$cesta_ticket";
        registrar_auditoria($conexion, 'Actualizar', 'Nómina', "Actualizó configuración salarial (Base: $sueldo_base, Cesta Ticket: $cesta_ticket)");
    } else {
        $_SESSION['mensaje'] = "Error al actualizar la configuración salarial.";
    }
}
header("Location: ../vista/nomina.php");
exit();
?>
