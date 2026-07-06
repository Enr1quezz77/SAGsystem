<?php
session_start();
require('./fpdf/fpdf.php');
include_once '../modelo/conexion.php';

if (!isset($_GET['id_empleado'])) {
    die("Faltan parámetros para generar el reporte.");
}

$id_empleado = intval($_GET['id_empleado']);

class DossierPDF extends FPDF
{
    function Header()
    {
        global $conexion;
        $consulta_info = $conexion->query("SELECT * FROM institucion");
        $dato_info = $consulta_info->fetch_object();
        
        $this->Image('./fpdf/logo.png', 170, 8, 25);
        $this->SetFont('Arial', 'B', 20);
        $this->SetTextColor(30, 64, 175); // Azul Tailwind
        $this->Cell(150, 10, utf8_decode("Expediente Maestro"), 0, 1, 'L');
        
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(100, 116, 139);
        $this->Cell(150, 6, utf8_decode("SAGDores - RRHH | " . $dato_info->nombre), 0, 1, 'L');
        $this->Ln(10);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(148, 163, 184);
        $this->Cell(0, 10, utf8_decode('Página ').$this->PageNo().'/{nb}', 0, 0, 'C');
    }
}

$pdf = new DossierPDF();
$pdf->AliasNbPages();
$pdf->AddPage();

// 1. Datos del Empleado
$qEmp = $conexion->prepare("SELECT e.nombre, e.apellido, e.dni, e.estado, c.nombre as cargo, e.fecha_registro FROM empleado e JOIN cargo c ON e.cargo = c.id_cargo WHERE e.id_empleado = ?");
$qEmp->bind_param("i", $id_empleado);
$qEmp->execute();
$emp = $qEmp->get_result()->fetch_assoc();

if (!$emp) {
    die("Empleado no encontrado.");
}

$pdf->SetFont('Arial', 'B', 14);
$pdf->SetTextColor(15, 23, 42);
$pdf->SetFillColor(241, 245, 249);
$pdf->Cell(0, 10, utf8_decode('  INFORMACIÓN PERSONAL Y LABORAL'), 0, 1, 'L', true);
$pdf->Ln(3);

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(40, 8, utf8_decode('Nombre:'), 0);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(60, 8, utf8_decode($emp['nombre'] . ' ' . $emp['apellido']), 0);

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(40, 8, utf8_decode('Cédula/ID:'), 0);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(60, 8, utf8_decode($emp['dni']), 0, 1);

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(40, 8, utf8_decode('Cargo:'), 0);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(60, 8, utf8_decode($emp['cargo']), 0);

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(40, 8, utf8_decode('Estado:'), 0);
$pdf->SetFont('Arial', 'B', 11);
if ($emp['estado'] == 'Suspendido') {
    $pdf->SetTextColor(220, 38, 38);
} else {
    $pdf->SetTextColor(5, 150, 105);
}
$pdf->Cell(60, 8, utf8_decode($emp['estado']), 0, 1);
$pdf->SetTextColor(15, 23, 42); // reset color

$pdf->Ln(10);

// 2. Historial de Amonestaciones (Gravedades)
$qAmon = $conexion->prepare("SELECT * FROM amonestacion WHERE id_empleado = ? ORDER BY fecha_registro DESC");
$qAmon->bind_param("i", $id_empleado);
$qAmon->execute();
$resAmon = $qAmon->get_result();

if ($resAmon->num_rows > 0) {
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->SetFillColor(254, 242, 242);
    $pdf->Cell(0, 10, utf8_decode('  HISTORIAL DISCIPLINARIO (' . $resAmon->num_rows . ')'), 0, 1, 'L', true);
    $pdf->Ln(3);
    
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(40, 7, utf8_decode('Fecha'), 1, 0, 'C');
    $pdf->Cell(40, 7, utf8_decode('Gravedad'), 1, 0, 'C');
    $pdf->Cell(110, 7, utf8_decode('Motivo Corto'), 1, 1, 'C');
    
    $pdf->SetFont('Arial', '', 10);
    while ($am = $resAmon->fetch_assoc()) {
        $pdf->Cell(40, 7, date('d/m/Y', strtotime($am['fecha_registro'])), 1, 0, 'C');
        $pdf->Cell(40, 7, utf8_decode($am['gravedad']), 1, 0, 'C');
        // Truncate
        $motivo = strlen($am['motivo']) > 50 ? substr($am['motivo'], 0, 50)."..." : $am['motivo'];
        $pdf->Cell(110, 7, utf8_decode($motivo), 1, 1, 'L');
    }
    $pdf->Ln(7);
}

// 3. Documentos Vinculados (Expediente Digital)
$qDoc = $conexion->prepare("SELECT * FROM documento_empleado WHERE id_empleado = ? ORDER BY fecha_subida DESC");
$qDoc->bind_param("i", $id_empleado);
$qDoc->execute();
$resDoc = $qDoc->get_result();

$pdf->SetFont('Arial', 'B', 14);
$pdf->SetFillColor(240, 253, 244);
$pdf->Cell(0, 10, utf8_decode('  EXPEDIENTE DIGITAL - ARCHIVOS (' . $resDoc->num_rows . ')'), 0, 1, 'L', true);
$pdf->Ln(3);

if ($resDoc->num_rows > 0) {
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(50, 7, utf8_decode('Categoría'), 1, 0, 'C');
    $pdf->Cell(95, 7, utf8_decode('Archivo Original'), 1, 0, 'C');
    $pdf->Cell(45, 7, utf8_decode('Vencimiento'), 1, 1, 'C');
    
    $pdf->SetFont('Arial', '', 10);
    while ($dc = $resDoc->fetch_assoc()) {
        $pdf->Cell(50, 7, utf8_decode($dc['tipo_documento']), 1, 0, 'L');
        $nombreDoc = strlen($dc['nombre_original']) > 40 ? substr($dc['nombre_original'], 0, 40)."..." : $dc['nombre_original'];
        $pdf->Cell(95, 7, utf8_decode($nombreDoc), 1, 0, 'L');
        
        $venceStr = "No Expira";
        if (!empty($dc['fecha_vence'])) {
            $venceStr = date('d/m/Y', strtotime($dc['fecha_vence']));
        }
        $pdf->Cell(45, 7, utf8_decode($venceStr), 1, 1, 'C');
    }
} else {
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 10, utf8_decode('No hay documentos anexados en su expediente.'), 0, 1, 'L');
}


$pdf->Output('Dossier_'.$emp['dni'].'.pdf', 'I');
?>