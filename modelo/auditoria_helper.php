<?php
function registrar_auditoria($conexion, $accion, $modulo, $detalle, $usuario = null) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Si no se pasa el usuario, intentar obtenerlo de la sesión
    if ($usuario === null) {
        if (isset($_SESSION['login_success']['usuario'])) {
            $usuario = $_SESSION['login_success']['usuario'];
        } elseif (isset($_SESSION['usuario'])) {
            $usuario = $_SESSION['usuario'];
        } elseif (isset($_SESSION['login_success']['email'])) {
            $usuario = $_SESSION['login_success']['email'];
        } else {
            $usuario = 'Sistema';
        }
    }
    
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    
    $stmt = $conexion->prepare("INSERT INTO auditoria (usuario, accion, modulo, detalle, ip) VALUES (?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sssss", $usuario, $accion, $modulo, $detalle, $ip);
        $stmt->execute();
        $stmt->close();
    }
}
?>
