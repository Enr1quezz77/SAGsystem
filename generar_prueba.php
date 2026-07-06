<?php
require 'modelo/conexion.php';

$mes = date('m');
$anio = date('Y');
$hoy_dia = (int)date('d');

$sql_empleados = $conexion->query("SELECT id_empleado, dni FROM empleado WHERE estado = 'Activo'");
$empleados = [];
while ($emp = $sql_empleados->fetch_assoc()) {
    $empleados[] = $emp;
}

$insertados = 0;

for ($d = 1; $d < $hoy_dia; $d++) {
    $fecha_str = sprintf('%04d-%02d-%02d', $anio, $mes, $d);
    
    foreach ($empleados as $emp) {
        $id_emp = $emp['id_empleado'];
        $dni = $emp['dni'];
        
        // Verificar si ya tiene asistencia ese día
        $check = $conexion->query("SELECT id_asistencia FROM asistencia WHERE id_empleado = $id_emp AND DATE(entrada) = '$fecha_str'");
        if ($check->num_rows > 0) continue;
        
        // 80% de probabilidad de asistir
        if (rand(1, 100) <= 80) {
            // Generar hora aleatoria entre 7:30 AM y 8:30 AM
            $hora_in = rand(7, 8);
            $min_in = rand(0, 59);
            $entrada = sprintf('%s %02d:%02d:00', $fecha_str, $hora_in, $min_in);
            
            // Generar hora aleatoria entre 4:00 PM y 6:00 PM
            $hora_out = rand(16, 18);
            $min_out = rand(0, 59);
            $salida = sprintf('%s %02d:%02d:00', $fecha_str, $hora_out, $min_out);
            
            $conexion->query("INSERT INTO asistencia (id_empleado, entrada, salida, biometrico_id, estado_biometrico) VALUES ($id_emp, '$entrada', '$salida', '$dni', 'exito')");
            $insertados++;
        }
    }
}

echo "Proceso completado. Se insertaron $insertados registros de asistencia de prueba para este mes.";
?>
