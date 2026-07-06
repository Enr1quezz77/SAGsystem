<?php

// Usar variables de entorno si están disponibles (Docker), si no usar valores por defecto (local)
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';
$database = getenv('DB_NAME') ?: 'sis_asistencia';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Conexion fallida: ". $conn->connect_error);
} 

?>
