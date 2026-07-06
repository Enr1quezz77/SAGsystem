<?php
// vista/fpdf/ReporteMensualConsolidado.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['email'])) {
    header("Location: ../../index.php");
    exit();
}

require('./fpdf.php');
include '../../modelo/conexion.php';

$mes = isset($_GET['mes']) ? (int)$_GET['mes'] : date('n');
$anio = isset($_GET['anio']) ? (int)$_GET['anio'] : date('Y');

$nombres_meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
$nombre_mes = $nombres_meses[$mes - 1];

// Calcular dias a evaluar en el mes
$total_days = cal_days_in_month(CAL_GREGORIAN, $mes, $anio);
$end_day = $total_days;
if ($anio == date('Y') && $mes == date('n')) {
    $end_day = date('j'); // Si es el mes actual, evaluar hasta hoy
} elseif ($anio > date('Y') || ($anio == date('Y') && $mes > date('n'))) {
    $end_day = 0; // Futuro
}

class PDF extends FPDF
{
    public $mesTexto;
    public $anioTexto;

    function Header()
    {
        include '../../modelo/conexion.php';
        $consulta_info = $conexion->query(" select * from institucion ");
        $dato_info = $consulta_info ? $consulta_info->fetch_object() : null;
        
        $nombre_empresa = "SAGDores";
        $ubicacion = $dato_info ? $dato_info->ubicacion : 'Valle de la Pascua';
        
        $this->Image('../../img/logo.png', 260, 5, 25);
        $this->SetFont('Arial', 'B', 19);
        $this->Cell(95);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(110, 15, utf8_decode($nombre_empresa), 1, 1, 'C', 0);
        $this->Ln(3);
        
        $this->Cell(180);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(96, 10, utf8_decode("Ubicación : " . $ubicacion), 0, 1, '', 0);
        $this->Ln(5);
        
        // TITULO
        $this->SetTextColor(37, 99, 235); // Blue-600
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, utf8_decode("REPORTE MENSUAL CONSOLIDADO ($this->mesTexto $this->anioTexto)"), 0, 1, 'C', 0);
        $this->Ln(7);
        
        // CABECERA DE TABLA
        $this->SetFillColor(219, 234, 254); // Blue-100
        $this->SetTextColor(0, 0, 0);
        $this->SetDrawColor(37, 99, 235); // Blue-600
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(10, 10, utf8_decode('N°'), 1, 0, 'C', 1);
        $this->Cell(55, 10, utf8_decode('EMPLEADO'), 1, 0, 'C', 1);
        $this->Cell(40, 10, utf8_decode('CARGO'), 1, 0, 'C', 1);
        $this->Cell(20, 10, utf8_decode('ASIST.'), 1, 0, 'C', 1);
        $this->Cell(20, 10, utf8_decode('FALTAS'), 1, 0, 'C', 1);
        $this->Cell(20, 10, utf8_decode('PERM.'), 1, 0, 'C', 1);
        $this->Cell(25, 10, utf8_decode('AMONEST.'), 1, 0, 'C', 1);
        $this->Cell(85, 10, utf8_decode('FECHAS DE FALTAS'), 1, 1, 'C', 1);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
        $this->SetY(-15);
        $hoy = date('d/m/Y');
        $this->Cell(540, 10, utf8_decode('Generado el: ' . $hoy), 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->mesTexto = strtoupper($nombre_mes);
$pdf->anioTexto = $anio;
$pdf->AddPage("landscape");
$pdf->AliasNbPages();

$pdf->SetFont('Arial', '', 10);
$pdf->SetDrawColor(163, 163, 163);
$pdf->SetTextColor(0, 0, 0);

// Obtener todos los empleados activos
$sql_empleados = $conexion->query("SELECT e.id_empleado, e.nombre, e.apellido, c.nombre as cargo FROM empleado e INNER JOIN cargo c ON e.cargo = c.id_cargo WHERE e.estado = 'Activo' ORDER BY e.nombre ASC");

if ($sql_empleados && $sql_empleados->num_rows > 0) {
    $i = 0;
    while ($emp = $sql_empleados->fetch_object()) {
        $i++;
        
        // 1. Asistencias: Dias distintos donde hay asistencia
        $q_asis = $conexion->query("SELECT DISTINCT DATE(entrada) as dia FROM asistencia WHERE id_empleado = $emp->id_empleado AND MONTH(entrada) = $mes AND YEAR(entrada) = $anio");
        $asistencias_arr = [];
        if ($q_asis) {
            while ($row = $q_asis->fetch_assoc()) {
                $asistencias_arr[] = $row['dia'];
            }
        }
        $asistencias = count($asistencias_arr);
        
        // 2. Permisos/Justificadas
        $q_perm = $conexion->query("SELECT DISTINCT fecha FROM justificacion_inasistencia WHERE id_empleado = $emp->id_empleado AND MONTH(fecha) = $mes AND YEAR(fecha) = $anio AND estado IN ('reposo', 'vacaciones', 'permiso') AND fecha NOT IN (SELECT DATE(entrada) FROM asistencia WHERE id_empleado = $emp->id_empleado AND MONTH(entrada) = $mes AND YEAR(entrada) = $anio)");
        $permisos_arr = [];
        if ($q_perm) {
            while ($row = $q_perm->fetch_assoc()) {
                $permisos_arr[] = $row['fecha'];
            }
        }
        $permisos = count($permisos_arr);
        
        // 3. Faltas: Calculadas iterando sobre todos los días a evaluar
        $faltas_fechas = [];
        for ($d = 1; $d <= $end_day; $d++) {
            $fecha_eval = sprintf('%04d-%02d-%02d', $anio, $mes, $d);
            if (!in_array($fecha_eval, $asistencias_arr) && !in_array($fecha_eval, $permisos_arr)) {
                $faltas_fechas[] = $d; // Guardamos solo el día
            }
        }
        $faltas = count($faltas_fechas);
        
        $fechas_str = '';
        if ($faltas > 0) {
            if ($faltas > 18) {
                $fechas_str = implode(", ", array_slice($faltas_fechas, 0, 18)) . " ...";
            } else {
                $fechas_str = implode(", ", $faltas_fechas);
            }
        }
        
        // 4. Amonestaciones en el mes
        $q_amon = $conexion->query("SELECT COUNT(*) as total FROM amonestacion WHERE id_empleado = $emp->id_empleado AND MONTH(fecha_registro) = $mes AND YEAR(fecha_registro) = $anio");
        $amonestaciones = $q_amon ? $q_amon->fetch_object()->total : 0;
        
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(10, 10, utf8_decode($i), 1, 0, 'C', 0);
        $pdf->Cell(55, 10, utf8_decode(substr($emp->nombre . " " . $emp->apellido, 0, 25)), 1, 0, 'L', 0);
        $pdf->Cell(40, 10, utf8_decode(substr($emp->cargo, 0, 20)), 1, 0, 'C', 0);
        
        $pdf->SetTextColor(21, 128, 61); // Verde
        $pdf->Cell(20, 10, utf8_decode($asistencias), 1, 0, 'C', 0);
        
        $pdf->SetTextColor(220, 38, 38); // Rojo
        $pdf->Cell(20, 10, utf8_decode($faltas), 1, 0, 'C', 0);
        
        $pdf->SetTextColor(126, 34, 206); // Morado
        $pdf->Cell(20, 10, utf8_decode($permisos), 1, 0, 'C', 0);
        
        if ($amonestaciones > 0) {
            $pdf->SetTextColor(220, 38, 38); // Rojo si tiene
        } else {
            $pdf->SetTextColor(156, 163, 175); // Gris si no tiene
        }
        $pdf->Cell(25, 10, utf8_decode($amonestaciones), 1, 0, 'C', 0);
        
        $pdf->SetTextColor(220, 38, 38); // Rojo para las fechas
        $pdf->SetFont('Arial', '', 7.5);
        $pdf->Cell(85, 10, utf8_decode($fechas_str), 1, 1, 'C', 0);
        
        $pdf->SetTextColor(0, 0, 0); // Reset color
    }
} else {
    $pdf->Cell(275, 15, utf8_decode('No se encontraron empleados activos para evaluar.'), 1, 1, 'C', 0);
}

$pdf->Output('Reporte Mensual Consolidado.pdf', 'I');
?>
