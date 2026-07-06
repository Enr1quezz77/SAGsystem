<?php
session_start();
include '../modelo/conexion.php';

header('Content-Type: application/json');

$notificaciones = [];

// 1. Alertar Empleados en Riesgo Disciplinario (Amonestaciones)
$qDisc = $conexion->query("
    SELECT e.nombre, e.apellido, count(a.id_amonestacion) as cantidad,
    SUM(CASE WHEN a.gravedad = 'Grave' THEN 1 ELSE 0 END) as faltas_graves
    FROM empleado e 
    JOIN amonestacion a ON e.id_empleado = a.id_empleado 
    WHERE e.estado != 'Suspendido' 
    GROUP BY e.id_empleado
    HAVING cantidad >= 3 OR faltas_graves > 0
");
if ($qDisc) {
    while ($r = $qDisc->fetch_object()) {
        $motivo = ($r->cantidad >= 3) ? "acumuló 3+ faltas" : "cruzó falta grave";
        $notificaciones[] = [
            'id' => 'disc_' . uniqid(),
            'tipo' => 'rojo',
            'icono' => 'fas fa-gavel',
            'titulo' => 'Riesgo Disciplinario',
            'mensaje' => $r->nombre . ' ' . $r->apellido . " $motivo. Requiere suspensión."
        ];
    }
}

// 2. Alertar Documentos/Expedientes Vencidos o por Vencer
$qDoc = $conexion->query("
    SELECT d.tipo_documento, e.nombre, d.fecha_vence,
    DATEDIFF(d.fecha_vence, CURDATE()) as dias_restantes
    FROM documento_empleado d
    JOIN empleado e ON d.id_empleado = e.id_empleado
    WHERE d.fecha_vence IS NOT NULL 
    AND DATEDIFF(d.fecha_vence, CURDATE()) <= 15
");
if ($qDoc) {
    while ($d = $qDoc->fetch_object()) {
        if ($d->dias_restantes < 0) {
            $notificaciones[] = [
                'id' => 'doc_v_' . uniqid(),
                'tipo' => 'r_naranja',
                'icono' => 'fas fa-file-excel',
                'titulo' => 'Documento Vencido',
                'mensaje' => 'El ' . $d->tipo_documento . ' de ' . $d->nombre . ' ha caducado.'
            ];
        } else {
            $notificaciones[] = [
                'id' => 'doc_p_' . uniqid(),
                'tipo' => 'amarillo',
                'icono' => 'fas fa-clock',
                'titulo' => 'Vencimiento Próximo',
                'mensaje' => 'El ' . $d->tipo_documento . ' de ' . $d->nombre . ' vence en ' . $d->dias_restantes . ' días.'
            ];
        }
    }
}

// 3. Notificaciones Volátiles de Sesión (Reportes, backups temporales)
if (isset($_SESSION['notificaciones_temporales']) && is_array($_SESSION['notificaciones_temporales'])) {
    foreach ($_SESSION['notificaciones_temporales'] as $index => $nt) {
        $notificaciones[] = [
            'id' => 'ses_' . $index,
            'tipo' => 'esmeralda',
            'icono' => 'fas fa-check-circle',
            'titulo' => $nt['titulo'] ?? 'Actividad del Sistema',
            'mensaje' => $nt['mensaje'],
            'is_transient' => true
        ];
    }
}

// Si la petición era limpiar las transcientes
if (isset($_GET['clear_transient']) && $_GET['clear_transient'] == '1') {
    $_SESSION['notificaciones_temporales'] = [];
    echo json_encode(['status' => 'cleared']);
    exit;
}

echo json_encode([
    'status' => 'success', 
    'cantidad' => count($notificaciones), 
    'data' => $notificaciones
]);
?>
