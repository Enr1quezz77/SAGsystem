<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <form action="process_forgot_password.php" method="post">
            <h2>Recuperación de Contraseña</h2>
            <p>Introduce tu correo para recibir un enlace de reinicio.</p>
            <input type="email" name="email" placeholder="Correo electrónico" required>
            <button type="submit">Enviar Enlace</button>
        </form>
    </div>
</body>
</html>
