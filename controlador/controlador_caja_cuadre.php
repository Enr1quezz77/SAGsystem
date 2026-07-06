<?php
session_start();
include '../modelo/conexion.php';

if (isset($_POST['btn_guardar_cuadre'])) {
    
    // Obtener datos
    $id_empleado = (int)$_POST['id_empleado'];
    $tasa_dia = (float)$_POST['tasa_dia'];
    $fondo_apertura_usd = (float)$_POST['fondo_apertura_usd'];
    $fondo_apertura_bs = (float)$_POST['fondo_apertura_bs'];
    $ventas_sistema_bs = (float)$_POST['ventas_sistema_bs'];
    $ventas_sistema_usd = (float)$_POST['ventas_sistema_usd'];
    $gastos_caja_usd = (float)$_POST['gastos_caja_usd'];
    $punto_venta_bs = (float)$_POST['punto_venta_bs'];
    $pago_movil_bs = (float)$_POST['pago_movil_bs'];
    $zelle_usd = (float)$_POST['zelle_usd'];
    $cashea_usd = (float)$_POST['cashea_usd'];
    $efectivo_fisico_bs = (float)$_POST['efectivo_fisico_bs'];
    $efectivo_fisico_usd = (float)$_POST['efectivo_fisico_usd'];
    $diferencia_usd = (float)$_POST['diferencia_usd'];
    $estado = $conexion->real_escape_string($_POST['estado']);
    $turno = $conexion->real_escape_string($_POST['turno']);
    $observaciones = $conexion->real_escape_string($_POST['observaciones'] ?? '');
    
    // Validar empleado
    if ($id_empleado <= 0) {
        $_SESSION['mensaje_caja'] = "Error: Debe seleccionar un empleado responsable.";
        header("Location: ../vista/cuadre_cajas.php");
        exit();
    }
    
    // Insertar Cuadre
    $stmt = $conexion->prepare("INSERT INTO caja_cuadres 
        (id_empleado, turno, fondo_apertura_usd, fondo_apertura_bs, tasa_dia, ventas_sistema_bs, ventas_sistema_usd, 
         gastos_caja_usd, punto_venta_bs, pago_movil_bs, zelle_usd, cashea_usd, efectivo_fisico_bs, efectivo_fisico_usd, diferencia_usd, estado, observaciones) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
    $stmt->bind_param("isdddddddddddddss", 
        $id_empleado, $turno, $fondo_apertura_usd, $fondo_apertura_bs, $tasa_dia, $ventas_sistema_bs, $ventas_sistema_usd,
        $gastos_caja_usd, $punto_venta_bs, $pago_movil_bs, $zelle_usd, $cashea_usd, $efectivo_fisico_bs, $efectivo_fisico_usd, $diferencia_usd, $estado, $observaciones
    );
    
    if ($stmt->execute()) {
        $id_insertado = $stmt->insert_id;
        $_SESSION['mensaje_caja'] = "Cuadre de caja registrado exitosamente. (Estado: $estado)";
        
        // Registrar en auditoría
        $usuario_sesion = $_SESSION['usuario'] ?? $_SESSION['email'] ?? 'Desconocido';
        $ip = $_SERVER['REMOTE_ADDR'];
        $detalle_audit = "Registró cuadre #$id_insertado. Dif: $diferencia_usd ($estado).";
        
        $stmt_audit = $conexion->prepare("INSERT INTO auditoria (usuario, accion, modulo, detalle, ip) VALUES (?, 'REGISTRO', 'Cajas', ?, ?)");
        if ($stmt_audit) {
            $stmt_audit->bind_param("sss", $usuario_sesion, $detalle_audit, $ip);
            $stmt_audit->execute();
            $stmt_audit->close();
        }
        
    } else {
        $_SESSION['mensaje_caja'] = "Error al guardar el cuadre: " . $stmt->error;
    }
    
    $stmt->close();
    $conexion->close();
    
    header("Location: ../vista/cuadre_cajas.php");
    exit();
} else {
    header("Location: ../vista/cuadre_cajas.php");
    exit();
}
?>
