<?php
// Este script corre en bucle infinito sincronizando el reloj cada 5 segundos
// No lo corras en el navegador web, es exclusivo para terminal/segundo plano.
set_time_limit(0);

echo "=================================================\n";
echo "DEMONIO DE SINCRONIZACIÓN ZKTECO INICIADO\n";
echo "Escuchando marcaciones cada 5 segundos...\n";
echo "=================================================\n\n";

while (true) {
    // Ejecutamos el archivo principal de integracion
    $output = shell_exec('php integracion_biometrico.php');
    
    // Si queremos ver que está haciendo en la consola:
    if (strpos($output, "insertados\":0") === false) {
       echo "[" . date('H:i:s') . "] ¡NUEVA MARCACIÓN DETECTADA Y GUARDADA!\n";
    }

    // Esperar 5 segundos y volver a preguntar al reloj
    sleep(5);
}
?>
