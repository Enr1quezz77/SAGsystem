<?php
// Script para descargar respaldo de la base de datos sis_asistencia
// Solo accesible para administradores
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die('Acceso denegado');
}

$usuario = 'root';
$password = '';
$bd = 'sis_asistencia';
$nombreArchivo = 'respaldo_sis_asistencia_' . date('Ymd_His') . '.sql';

// Ruta absoluta de mysqldump para XAMPP en Windows
$mysqldump = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';
if (!file_exists($mysqldump)) {
    die('No se encontró mysqldump en: ' . $mysqldump);
}

// Archivo temporal para el respaldo
$tmpFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $nombreArchivo;

// Construir comando
$comando = '"' . $mysqldump . '" -u ' . $usuario;
if ($password !== '') {
    $comando .= ' -p' . $password;
}
$comando .= ' ' . $bd . ' > "' . $tmpFile . '" 2>&1';

// Ejecutar comando
exec($comando, $output, $resultCode);

// Verificar si el archivo tiene contenido
if ($resultCode !== 0 || !file_exists($tmpFile) || filesize($tmpFile) < 10) {
    $errorMsg = file_exists($tmpFile) ? file_get_contents($tmpFile) : 'No se pudo crear el archivo de respaldo.';
    unlink($tmpFile);
    echo '<!DOCTYPE html><html lang="es"><head><meta charset="utf-8"><title>Error de respaldo</title></head><body>';
    echo '<h2 style="color:red;">Error al realizar el respaldo de la base de datos</h2>';
    echo '<pre>' . htmlspecialchars($errorMsg) . '</pre>';
    echo '<a href="javascript:window.close();">Cerrar ventana</a>';
    echo '</body></html>';
    exit;
}

// Descargar el archivo
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename=' . $nombreArchivo);
header('Pragma: no-cache');
header('Expires: 0');
readfile($tmpFile);
unlink($tmpFile);
exit;
?>
