<?php
include 'modelo/conexion.php';

$sql = "
CREATE TABLE IF NOT EXISTS caja_cuadres (
    id_cuadre INT AUTO_INCREMENT PRIMARY KEY,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    id_empleado INT NOT NULL,
    fondo_apertura_usd DECIMAL(10, 2) NOT NULL DEFAULT 0,
    tasa_dia DECIMAL(10, 2) NOT NULL,
    ventas_sistema_bs DECIMAL(10, 2) NOT NULL DEFAULT 0,
    ventas_sistema_usd DECIMAL(10, 2) NOT NULL DEFAULT 0,
    gastos_caja_usd DECIMAL(10, 2) NOT NULL DEFAULT 0,
    efectivo_fisico_bs DECIMAL(10, 2) NOT NULL DEFAULT 0,
    efectivo_fisico_usd DECIMAL(10, 2) NOT NULL DEFAULT 0,
    diferencia_usd DECIMAL(10, 2) NOT NULL DEFAULT 0,
    estado ENUM('Cuadrada', 'Faltante', 'Sobrante') NOT NULL,
    observaciones TEXT,
    FOREIGN KEY (id_empleado) REFERENCES empleado(id_empleado) ON DELETE CASCADE
);
";

if ($conexion->multi_query($sql)) {
    do {
        if ($res = $conexion->store_result()) {
            $res->free();
        }
    } while ($conexion->more_results() && $conexion->next_result());
    echo "Tabla caja_cuadres creada exitosamente.\n";
} else {
    echo "Error creando tabla: " . $conexion->error . "\n";
}

$conexion->close();
?>
