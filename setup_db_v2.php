<?php
include 'modelo/conexion.php';

$sql = "
CREATE TABLE IF NOT EXISTS auditoria (
    id_auditoria INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(100) NOT NULL,
    accion VARCHAR(50) NOT NULL,
    modulo VARCHAR(50) NOT NULL,
    detalle TEXT NOT NULL,
    ip VARCHAR(45) NOT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS permisos (
    id_permiso INT AUTO_INCREMENT PRIMARY KEY,
    id_empleado INT NOT NULL,
    tipo ENUM('Vacaciones', 'Permiso Médico', 'Asunto Personal') NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    motivo TEXT,
    estado ENUM('Pendiente', 'Aprobado', 'Rechazado') DEFAULT 'Pendiente',
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_empleado) REFERENCES empleado(id_empleado) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS tasa_cambio (
    id_tasa INT AUTO_INCREMENT PRIMARY KEY,
    tasa DECIMAL(10, 2) NOT NULL,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS nomina (
    id_nomina INT AUTO_INCREMENT PRIMARY KEY,
    id_empleado INT NOT NULL,
    mes INT NOT NULL,
    anio INT NOT NULL,
    quincena INT NOT NULL COMMENT '1 o 2',
    salario_base_bs DECIMAL(10, 2) NOT NULL,
    tasa_dolar_dia DECIMAL(10, 2) NOT NULL,
    dias_trabajados INT NOT NULL,
    dias_permiso INT NOT NULL DEFAULT 0,
    faltas_injustificadas INT NOT NULL DEFAULT 0,
    bonos_bs DECIMAL(10, 2) DEFAULT 0,
    deducciones_bs DECIMAL(10, 2) DEFAULT 0,
    total_pagar_bs DECIMAL(10, 2) NOT NULL,
    total_pagar_usd DECIMAL(10, 2) NOT NULL,
    estado ENUM('Pendiente', 'Pagado') DEFAULT 'Pendiente',
    fecha_generacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_empleado) REFERENCES empleado(id_empleado) ON DELETE CASCADE
);
";

if ($conexion->multi_query($sql)) {
    do {
        // flush multi_queries
        if ($res = $conexion->store_result()) {
            $res->free();
        }
    } while ($conexion->more_results() && $conexion->next_result());
    echo "Tablas creadas correctamente.\n";
} else {
    echo "Error creando tablas: " . $conexion->error . "\n";
}

// Añadir salario_base a empleado si no existe
$check_col = $conexion->query("SHOW COLUMNS FROM empleado LIKE 'salario_base'");
if ($check_col->num_rows == 0) {
    if ($conexion->query("ALTER TABLE empleado ADD COLUMN salario_base DECIMAL(10, 2) NOT NULL DEFAULT 0")) {
        echo "Columna salario_base añadida a empleados.\n";
    } else {
        echo "Error alterando empleados: " . $conexion->error . "\n";
    }
} else {
    echo "Columna salario_base ya existe.\n";
}

// Insertar tasa de cambio inicial si está vacía
$check_tasa = $conexion->query("SELECT * FROM tasa_cambio");
if ($check_tasa->num_rows == 0) {
    $conexion->query("INSERT INTO tasa_cambio (tasa) VALUES (36.50)"); // Tasa por defecto
    echo "Tasa de cambio inicial creada.\n";
}

$conexion->close();
?>
