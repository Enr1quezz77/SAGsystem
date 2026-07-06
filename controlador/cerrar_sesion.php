<?php
session_start();

// Limpiar todas las variables de sesión
$_SESSION = array();

// Eliminar la cookie de sesión del navegador (para que no pueda reusar el session ID)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destruir la sesión en el servidor
session_destroy();

// Evitar que el navegador cachee esta respuesta
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Redirigir al login
header("Location: ../index.php");
exit();
?>
