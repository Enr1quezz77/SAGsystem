<?php
// restaurar_bd.php
session_start();
// Permitir acceso solo a administradores (acepta tanto login_success como role en la sesión)
$esAdmin = false;
if (isset($_SESSION['login_success']) && $_SESSION['login_success']['role'] === 'admin') {
    $esAdmin = true;
} elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $esAdmin = true;
}
if (!$esAdmin) {
    $_SESSION['mensaje'] = 'Error: Acceso denegado. Solo los administradores pueden restaurar la base de datos.';
    header('Location: vista/inicio.php');
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['sql_file'])) {
    $fileTmpPath = $_FILES['sql_file']['tmp_name'];
    $fileName = $_FILES['sql_file']['name'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    if ($fileExtension !== 'sql') {
        $_SESSION['mensaje'] = 'Solo se permiten archivos .sql';
        header('Location: vista/inicio.php');
        exit();
    }
    if (!is_uploaded_file($fileTmpPath) || !file_exists($fileTmpPath)) {
        $_SESSION['mensaje'] = 'Error: No se pudo subir el archivo o el archivo no existe.';
        header('Location: vista/inicio.php');
        exit();
    }
    // Datos de conexión
    $db_host = 'localhost';
    $db_user = 'root';
    $db_pass = '';
    $db_name = 'sis_asistencia';
    $db_port = '3306';
    // Comando para restaurar usando mysql (Windows: usar rutas absolutas si es necesario)
    $mysqlCmd = 'mysql';
    // Si XAMPP está en C:\xampp, buscar el ejecutable
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $possiblePath = 'C:\\xampp\\mysql\\bin\\mysql.exe';
        if (file_exists($possiblePath)) {
            $mysqlCmd = '"' . $possiblePath . '"';
        }
    }
    $cmd = $mysqlCmd .
        " --host=$db_host --user=$db_user --port=$db_port ".
        ($db_pass !== '' ? "--password=$db_pass " : '').
        "$db_name < \"$fileTmpPath\"";
    // Ejecutar el comando
    $output = [];
    $result = null;
    // Para Windows, usar shell para redirección
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $cmd = 'cmd /c ' . $cmd;
    }
    exec($cmd . ' 2>&1', $output, $result);
    $mensaje = 'Resultado comando mysql:<br>';
    $mensaje .= '<b>Comando ejecutado:</b> <pre>' . htmlspecialchars($cmd) . '</pre>';
    $mensaje .= '<b>Resultado (código de salida):</b> ' . $result . '<br>';
    $mensaje .= '<b>Salida:</b> <pre>' . htmlspecialchars(implode("\n", $output)) . '</pre>';
    if ($result !== 0) {
        $mensaje = '<b style="color:red">Error al restaurar la base de datos.</b><br>' . $mensaje;
    } else {
        $mensaje = '<b style="color:green">Restauración completada correctamente.</b><br>' . $mensaje;
    }
    $_SESSION['mensaje'] = $mensaje;
    header('Location: vista/inicio.php');
    exit();
} else {
    $_SESSION['mensaje'] = 'Error: No se recibió ningún archivo para restaurar.';
    header('Location: vista/inicio.php');
    exit();
}
