<?php
session_start();
if (empty($_SESSION['email'])) {
    die("Acceso denegado.");
}

require('./fpdf/fpdf.php');
include_once '../modelo/conexion.php';

if (!isset($_GET['id_cuadre'])) {
    die("Error: Faltan parámetros para generar el reporte de cuadre.");
}

$id_cuadre = intval($_GET['id_cuadre']);

// Obtener datos del cuadre
$query = $conexion->prepare("SELECT c.*, e.nombre, e.apellido, e.dni FROM caja_cuadres c JOIN empleado e ON c.id_empleado = e.id_empleado WHERE c.id_cuadre = ?");
$query->bind_param("i", $id_cuadre);
$query->execute();
$cuadre = $query->get_result()->fetch_object();

if (!$cuadre) {
    die("Error: Cuadre de caja no encontrado.");
}

class CuadrePDF extends FPDF
{
    function Header()
    {
        global $conexion;
        $consulta_info = $conexion->query("SELECT * FROM institucion LIMIT 1");
        $dato_info = $consulta_info->fetch_object();
        $nombre_inst = $dato_info ? $dato_info->nombre : "SAGDores";
        
        $this->Image('./fpdf/logo.png', 170, 8, 25);
        $this->SetFont('Arial', 'B', 18);
        $this->SetTextColor(30, 64, 175);
        $this->Cell(150, 8, utf8_decode("REPORTE DE CIERRE DE CAJA"), 0, 1, 'L');
        
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(100, 116, 139);
        $this->Cell(150, 6, utf8_decode("SAGDores - Control Financiero | " . $nombre_inst), 0, 1, 'L');
        $this->Ln(8);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(148, 163, 184);
        $this->Cell(0, 10, utf8_decode('Documento generado automáticamente por SAGDores - Página ').$this->PageNo().'/{nb}', 0, 0, 'C');
    }
}

$pdf = new CuadrePDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->AddPage();

// INFO GENERAL
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetTextColor(15, 23, 42);
$pdf->SetFillColor(241, 245, 249);
$pdf->Cell(0, 8, utf8_decode('  INFORMACIÓN GENERAL DEL CIERRE #'.str_pad($cuadre->id_cuadre, 5, '0', STR_PAD_LEFT)), 0, 1, 'L', true);
$pdf->Ln(2);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(35, 7, utf8_decode('Cajero/Resp:'), 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(60, 7, utf8_decode($cuadre->nombre . ' ' . $cuadre->apellido . ' (CI: '.$cuadre->dni.')'), 0);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(35, 7, utf8_decode('Fecha y Hora:'), 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(60, 7, date('d/m/Y h:i A', strtotime($cuadre->fecha_registro)), 0, 1);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(35, 7, utf8_decode('Turno:'), 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(60, 7, utf8_decode($cuadre->turno), 0);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(35, 7, utf8_decode('Tasa del Día:'), 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(60, 7, 'Bs. ' . number_format($cuadre->tasa_dia, 2, ',', '.'), 0, 1);
$pdf->Ln(5);

// DATOS DEL SISTEMA
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(241, 245, 249);
$pdf->Cell(90, 8, utf8_decode('  DATOS DEL SISTEMA (TEÓRICO)'), 0, 0, 'L', true);
$pdf->Cell(10, 8, '', 0, 0); // Spacing
$pdf->Cell(90, 8, utf8_decode('  ARQUEO FÍSICO Y ELECTRÓNICO'), 0, 1, 'L', true);
$pdf->Ln(2);

$startX = $pdf->GetX();
$startY = $pdf->GetY();

// Bloque Izquierdo
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(50, 7, utf8_decode('Fondo Apertura (Bs):'), 0);
$pdf->Cell(40, 7, 'Bs. '.number_format($cuadre->fondo_apertura_bs, 2, ',', '.'), 0, 1, 'R');
$pdf->Cell(50, 7, utf8_decode('Fondo Apertura ($):'), 0);
$pdf->Cell(40, 7, '$ '.number_format($cuadre->fondo_apertura_usd, 2, ',', '.'), 0, 1, 'R');
$pdf->Cell(50, 7, utf8_decode('Ventas Sistema (Bs):'), 0);
$pdf->Cell(40, 7, 'Bs. '.number_format($cuadre->ventas_sistema_bs, 2, ',', '.'), 0, 1, 'R');
$pdf->Cell(50, 7, utf8_decode('Ventas Sistema ($):'), 0);
$pdf->Cell(40, 7, '$ '.number_format($cuadre->ventas_sistema_usd, 2, ',', '.'), 0, 1, 'R');
$pdf->SetTextColor(220, 38, 38); // Rojo
$pdf->Cell(50, 7, utf8_decode('Gastos / Vales ($):'), 0);
$pdf->Cell(40, 7, '- $ '.number_format($cuadre->gastos_caja_usd, 2, ',', '.'), 0, 1, 'R');
$pdf->SetTextColor(15, 23, 42); // Reset

// Bloque Derecho
$pdf->SetXY($startX + 100, $startY);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(90, 7, utf8_decode('Efectivo en Caja'), 'B', 2, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(50, 7, utf8_decode('Efectivo Total (Bs):'), 0);
$pdf->Cell(40, 7, 'Bs. '.number_format($cuadre->efectivo_fisico_bs, 2, ',', '.'), 0, 2, 'R');
$pdf->SetX($startX + 100);
$pdf->Cell(50, 7, utf8_decode('Efectivo Total ($):'), 0);
$pdf->Cell(40, 7, '$ '.number_format($cuadre->efectivo_fisico_usd, 2, ',', '.'), 0, 2, 'R');

$pdf->SetX($startX + 100);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(90, 7, utf8_decode('Medios Electrónicos'), 'B', 2, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(50, 7, utf8_decode('Punto de Venta (Bs):'), 0);
$pdf->Cell(40, 7, 'Bs. '.number_format($cuadre->punto_venta_bs, 2, ',', '.'), 0, 2, 'R');
$pdf->SetX($startX + 100);
$pdf->Cell(50, 7, utf8_decode('Pago Móvil (Bs):'), 0);
$pdf->Cell(40, 7, 'Bs. '.number_format($cuadre->pago_movil_bs, 2, ',', '.'), 0, 2, 'R');
$pdf->SetX($startX + 100);
$pdf->Cell(50, 7, utf8_decode('Zelle ($):'), 0);
$pdf->Cell(40, 7, '$ '.number_format($cuadre->zelle_usd, 2, ',', '.'), 0, 2, 'R');
$pdf->SetX($startX + 100);
$pdf->Cell(50, 7, utf8_decode('Cashea ($):'), 0);
$pdf->Cell(40, 7, '$ '.number_format($cuadre->cashea_usd, 2, ',', '.'), 0, 1, 'R');

// Espaciado dinámico basado en cuál lado terminó más abajo
$currentY = max($pdf->GetY(), $startY + 40);
$pdf->SetY($currentY + 10);

// RESUMEN Y DIFERENCIA
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(241, 245, 249);
$pdf->Cell(0, 8, utf8_decode('  RESUMEN FINAL Y CUADRE'), 0, 1, 'L', true);
$pdf->Ln(3);

// Re-calcular el teorico y fisico para mostrarlo
$teorico_usd = $cuadre->fondo_apertura_usd + ($cuadre->fondo_apertura_bs / $cuadre->tasa_dia) + $cuadre->ventas_sistema_usd + ($cuadre->ventas_sistema_bs / $cuadre->tasa_dia) - $cuadre->gastos_caja_usd;
$fisico_usd = $cuadre->efectivo_fisico_usd + ($cuadre->efectivo_fisico_bs / $cuadre->tasa_dia) + $cuadre->zelle_usd + $cuadre->cashea_usd + (($cuadre->punto_venta_bs + $cuadre->pago_movil_bs) / $cuadre->tasa_dia);

$pdf->SetFont('Arial', '', 11);
$pdf->Cell(70, 8, utf8_decode('Total Sistema (Teórico):'), 0);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(40, 8, '$ '.number_format($teorico_usd, 2, ',', '.'), 0, 1, 'R');

$pdf->SetFont('Arial', '', 11);
$pdf->Cell(70, 8, utf8_decode('Total Registrado (Físico + Digital):'), 0);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(40, 8, '$ '.number_format($fisico_usd, 2, ',', '.'), 0, 1, 'R');

// Línea divisoria
$pdf->Cell(110, 0, '', 'T', 1);
$pdf->Ln(2);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(70, 8, utf8_decode('DIFERENCIA:'), 0);

if ($cuadre->diferencia_usd < 0) {
    $pdf->SetTextColor(220, 38, 38); // Rojo
    $pdf->Cell(40, 8, '- $ '.number_format(abs($cuadre->diferencia_usd), 2, ',', '.'), 0, 1, 'R');
} elseif ($cuadre->diferencia_usd > 0 && abs($cuadre->diferencia_usd) > 0.09) {
    $pdf->SetTextColor(217, 119, 6); // Ambar
    $pdf->Cell(40, 8, '+ $ '.number_format($cuadre->diferencia_usd, 2, ',', '.'), 0, 1, 'R');
} else {
    $pdf->SetTextColor(5, 150, 105); // Verde
    $pdf->Cell(40, 8, '$ 0.00', 0, 1, 'R');
}
$pdf->SetTextColor(15, 23, 42); // Reset

// Estado
$pdf->Ln(2);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(30, 8, utf8_decode('Estado:'), 0);

if ($cuadre->estado == 'Cuadrada') {
    $pdf->SetFillColor(209, 250, 229);
    $pdf->SetTextColor(6, 95, 70);
} elseif ($cuadre->estado == 'Faltante') {
    $pdf->SetFillColor(254, 226, 226);
    $pdf->SetTextColor(153, 27, 27);
} else {
    $pdf->SetFillColor(254, 243, 199);
    $pdf->SetTextColor(146, 64, 14);
}
$pdf->Cell(40, 8, utf8_decode(strtoupper($cuadre->estado)), 0, 1, 'C', true);
$pdf->SetTextColor(15, 23, 42);

$pdf->Ln(8);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 6, utf8_decode('Observaciones / Notas:'), 0, 1, 'L');
$pdf->SetFont('Arial', '', 10);
$obs = trim($cuadre->observaciones) !== '' ? $cuadre->observaciones : 'Ninguna observación registrada.';
$pdf->MultiCell(0, 6, utf8_decode($obs), 0, 'L');

// FIRMAS
$pdf->Ln(25);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(95, 6, '________________________________', 0, 0, 'C');
$pdf->Cell(95, 6, '________________________________', 0, 1, 'C');
$pdf->Cell(95, 6, utf8_decode('Firma del Cajero'), 0, 0, 'C');
$pdf->Cell(95, 6, utf8_decode('Firma del Supervisor / Gerente'), 0, 1, 'C');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(95, 6, utf8_decode($cuadre->nombre . ' ' . $cuadre->apellido), 0, 0, 'C');

$pdf->Output('Cierre_Caja_'.$cuadre->id_cuadre.'.pdf', 'I');
?>
