<?php

session_start();
if (!isset($_SESSION['email'])) {
	header("Location: index.php");
	exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Usuario</title>
    <link rel="stylesheet" href="style.css">
</head>

<body style="background: #fff;">

  <div class="box">
   <h1>Bienvenido, <span><?= $_SESSION['name']; ?></span></h1>
   <p>Esta es una <span>página</span> de usuario</p>
   <button onclick="window.location.href='logout.php'">Cerrar Sesión</button>

  </div>
    
</body>
</html>