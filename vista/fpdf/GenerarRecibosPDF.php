<?php

$mes = isset($_GET["mes"]) ? (int)$_GET["mes"] : date('m');
$anio = isset($_GET["anio"]) ? (int)$_GET["anio"] : date('Y');
$quincena = isset($_GET["quincena"]) ? (int)$_GET["quincena"] : (date('d') <= 15 ? 1 : 2);

require('./fpdf.php');
include '../../modelo/conexion.php';

// Obtener Tasa Actual y Configuración Salarial
$tasa_actual = 36.50;
$sueldo_base_global = 210.00;
$cesta_ticket_global = 30.00;

$config_query = $conexion->query("SELECT tasa, sueldo_base_usd, cesta_ticket_usd FROM tasa_cambio LIMIT 1");
if ($config_query && $config_query->num_rows > 0) {
    $config_data = $config_query->fetch_object();
    $tasa_actual = floatval($config_data->tasa);
    $sueldo_base_global = floatval($config_data->sueldo_base_usd);
    $cesta_ticket_global = floatval($config_data->cesta_ticket_usd);
}

class PDF extends FPDF
{
    function Header()
    {
        include '../../modelo/conexion.php'; 
        $consulta_info = $conexion->query(" select * from institucion "); 
        $dato_info = $consulta_info ? $consulta_info->fetch_object() : null;
        
        $nombre_empresa = "SAGDores";
        $ruc = $dato_info ? $dato_info->ruc : 'N/A';

        $this->Image('../../img/logo.png', 10, 8, 20); 
        $this->SetFont('Arial', 'B', 15);
        $this->SetTextColor(0, 0, 0); 
        
        // Empresa
        $this->Cell(30);
        $this->Cell(100, 8, utf8_decode($nombre_empresa), 0, 1, 'L');
        
        $this->SetFont('Arial', '', 10);
        $this->Cell(30);
        $this->Cell(100, 5, utf8_decode("RIF/RUC: " . $ruc), 0, 1, 'L');
        $this->Ln(10);
    }

    function Footer()
    {
        $this->SetY(-15); 
        $this->SetFont('Arial', 'I', 8); 
        $this->Cell(0, 10, utf8_decode('Recibo de Pago generado por SAGDores'), 0, 0, 'C'); 
    }
}

$meses = ['1'=>'Enero','2'=>'Febrero','3'=>'Marzo','4'=>'Abril','5'=>'Mayo','6'=>'Junio','7'=>'Julio','8'=>'Agosto','9'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre'];
$nombre_mes = $meses[$mes];
$texto_quincena = $quincena == 1 ? "1ra Quincena" : "2da Quincena";
$periodo = "$texto_quincena de $nombre_mes $anio";

$empleados = $conexion->query("SELECT empleado.id_empleado, empleado.nombre, empleado.apellido, empleado.dni, empleado.cargo, empleado.salario_base FROM empleado INNER JOIN cargo ON empleado.cargo = cargo.id_cargo");

$pdf = new PDF();

if ($empleados && $empleados->num_rows > 0) {
    while($emp = $empleados->fetch_object()) {
        $pdf->AddPage();
        
        // Título del Recibo
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetTextColor(16, 185, 129); // Emerald-500
        $pdf->Cell(0, 10, utf8_decode('RECIBO DE PAGO DE NÓMINA'), 0, 1, 'C');
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->Cell(0, 8, utf8_decode("Período: " . $periodo), 0, 1, 'C');
        $pdf->Ln(5);

        // Datos del Empleado
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFillColor(51, 65, 85); // Slate-700
        $pdf->Cell(0, 8, utf8_decode(' DATOS DEL EMPLEADO'), 1, 1, 'L', true);
        
        $pdf->SetFont('Arial', '', 11);
        $pdf->SetTextColor(0, 0, 0);
        
        $pdf->Cell(40, 8, utf8_decode('Nombre:'), 1, 0, 'L');
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(150, 8, utf8_decode($emp->nombre . ' ' . $emp->apellido), 1, 1, 'L');
        
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(40, 8, utf8_decode('Cédula de Identidad:'), 1, 0, 'L');
        $pdf->Cell(55, 8, utf8_decode($emp->dni), 1, 0, 'L');
        $pdf->Cell(40, 8, utf8_decode('Tasa de Cambio:'), 1, 0, 'L');
        $pdf->Cell(55, 8, utf8_decode("Bs. " . number_format($tasa_actual, 2, ',', '.')), 1, 1, 'L');
        
        $pdf->Ln(10);
        
        // Cálculos estándar basados en BD
        $salario_mensual_usd = $sueldo_base_global;
        $cesta_ticket_mensual_usd = $cesta_ticket_global;
        $salario_quincena_usd = $salario_mensual_usd / 2;
        $cesta_ticket_quincena_usd = $cesta_ticket_mensual_usd / 2;
        
        $asignaciones_quincena_usd = $salario_quincena_usd + $cesta_ticket_quincena_usd;
        
        $query_ded = $conexion->query("SELECT SUM(monto_usd) as total_deduccion FROM deduccion WHERE id_empleado = {$emp->id_empleado} AND mes = $mes AND anio = $anio AND quincena = $quincena");
        $deducciones_usd = ($query_ded && $row_ded = $query_ded->fetch_object()) ? floatval($row_ded->total_deduccion) : 0;
        
        $query_bonos = $conexion->query("SELECT SUM(monto_usd) as total_bonos FROM bonificacion WHERE id_empleado = {$emp->id_empleado} AND mes = $mes AND anio = $anio AND quincena = $quincena");
        $bonos_usd = ($query_bonos && $row_bonos = $query_bonos->fetch_object()) ? floatval($row_bonos->total_bonos) : 0;
        
        $total_pagar_usd = $asignaciones_quincena_usd + $bonos_usd - $deducciones_usd;
        
        // Conversión BCV
        $salario_quincena_bs = $salario_quincena_usd * $tasa_actual;
        $cesta_ticket_quincena_bs = $cesta_ticket_quincena_usd * $tasa_actual;
        $bonos_bs = $bonos_usd * $tasa_actual;
        $deducciones_bs = $deducciones_usd * $tasa_actual;
        $total_pagar_bs = $total_pagar_usd * $tasa_actual;
        
        // Tabla de Conceptos
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFillColor(16, 185, 129); // Emerald-500
        $pdf->Cell(90, 8, utf8_decode('CONCEPTO'), 1, 0, 'C', true);
        $pdf->Cell(50, 8, utf8_decode('ASIGNACIONES ($ / Bs)'), 1, 0, 'C', true);
        $pdf->Cell(50, 8, utf8_decode('DEDUCCIONES ($ / Bs)'), 1, 1, 'C', true);
        
        $pdf->SetFont('Arial', '', 11);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(90, 10, utf8_decode('Salario Base Quincenal'), 1, 0, 'L');
        $pdf->Cell(50, 10, utf8_decode("$ " . number_format($salario_quincena_usd, 2, ',', '.') . " / Bs. " . number_format($salario_quincena_bs, 2, ',', '.')), 1, 0, 'C');
        $pdf->Cell(50, 10, utf8_decode(''), 1, 1, 'C');

        $pdf->Cell(90, 10, utf8_decode('Cesta Ticket Quincenal'), 1, 0, 'L');
        $pdf->Cell(50, 10, utf8_decode("$ " . number_format($cesta_ticket_quincena_usd, 2, ',', '.') . " / Bs. " . number_format($cesta_ticket_quincena_bs, 2, ',', '.')), 1, 0, 'C');
        $pdf->Cell(50, 10, utf8_decode(''), 1, 1, 'C');
        
        $pdf->Cell(90, 10, utf8_decode('Bonificaciones Adicionales'), 1, 0, 'L');
        $pdf->Cell(50, 10, utf8_decode("$ " . number_format($bonos_usd, 2, ',', '.') . " / Bs. " . number_format($bonos_bs, 2, ',', '.')), 1, 0, 'C');
        $pdf->Cell(50, 10, utf8_decode(''), 1, 1, 'C');
        
        $pdf->Cell(90, 10, utf8_decode('Deducciones (Inasistencias / Retrasos)'), 1, 0, 'L');
        $pdf->Cell(50, 10, utf8_decode(''), 1, 0, 'C');
        $pdf->Cell(50, 10, utf8_decode("$ " . number_format($deducciones_usd, 2, ',', '.') . " / Bs. " . number_format($deducciones_bs, 2, ',', '.')), 1, 1, 'C');
        
        // Totales
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(241, 245, 249); // Slate-100
        $pdf->Cell(90, 12, utf8_decode('TOTAL A RECIBIR ($ / Bs)'), 1, 0, 'R', true);
        $pdf->SetTextColor(16, 185, 129); // Emerald
        $pdf->Cell(50, 12, utf8_decode("$ " . number_format($total_pagar_usd, 2, ',', '.')), 1, 0, 'C', true);
        $pdf->Cell(50, 12, utf8_decode("Bs. " . number_format($total_pagar_bs, 2, ',', '.')), 1, 1, 'C', true);
        
        $pdf->Ln(20);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 11);
        
        $pdf->Cell(95, 8, '______________________________', 0, 0, 'C');
        $pdf->Cell(95, 8, '______________________________', 0, 1, 'C');
        
        $pdf->Cell(95, 8, utf8_decode('Firma del Empleador'), 0, 0, 'C');
        $pdf->Cell(95, 8, utf8_decode('Firma del Empleado'), 0, 1, 'C');
    }
} else {
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, utf8_decode('No hay empleados registrados para generar recibos.'), 0, 1, 'C');
}

$pdf->Output('Recibos_Nomina_'.$mes.'_'.$anio.'_Q'.$quincena.'.pdf', 'I');
exit;
?>
