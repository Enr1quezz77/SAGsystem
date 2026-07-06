<?php
// IP y puerto por defecto de los dispositivos ZKTeco
$ip_dispositivo = '192.168.1.201'; // Cambia esto a la IP de tu biométrico
$puerto = 4370; // Puerto UDP por defecto

echo "<h3>Prueba de Conexión de Sockets</h3>";
echo "Intentando conectar al dispositivo en <b>$ip_dispositivo:$puerto</b>...<br><br>";

// Las variables de conexión
$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
if (!$sock) {
    die("❌ Error al crear el socket: " . socket_strerror(socket_last_error()) . "\n");
}

socket_set_option($sock, SOL_SOCKET, SO_RCVTIMEO, array('sec' => 3, 'usec' => 0));

$connect = @socket_connect($sock, $ip_dispositivo, $puerto);

if ($connect) {
    echo "✅ <b>Éxito:</b> Se pudo establecer la conexión de red (Socket) con la IP del dispositivo.<br>";
    echo "Ya puedes proceder a usar una librería ZKTeco para PHP (como zklib) para extraer las huellas y registros.";
} else {
    echo "❌ <b>Fallo:</b> No se pudo conectar usando sockets al dispositivo $ip_dispositivo en el puerto $puerto.<br>";
    echo "Asegúrate de que la IP sea correcta y de que ambos equipos estén en la misma red.\n";
}

socket_close($sock);
?>
