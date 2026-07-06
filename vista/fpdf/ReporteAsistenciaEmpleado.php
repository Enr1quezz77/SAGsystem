<?php

// Recibir y validar parámetros
$fechaInicio = isset($_GET["fecha_inicio"]) ? $_GET["fecha_inicio"] : null;
$fechaFinal = isset($_GET["fecha_fin"]) ? $_GET["fecha_fin"] : null;
$empleado = isset($_GET["empleado"]) ? $_GET["empleado"] : null;
$cargo = isset($_GET["cargo"]) ? $_GET["cargo"] : null;
$tipo = isset($_GET["tipo"]) ? $_GET["tipo"] : '';
$turno = isset($_GET["turno"]) ? $_GET["turno"] : '';

if (empty($empleado) || $empleado === 'todos') {
    die("Error: Debe seleccionar un empleado especifico para este reporte.");
}

require('./fpdf.php');
include '../../modelo/conexion.php';

// Obtener info del empleado
$sql_emp = $conexion->query("SELECT empleado.nombre, empleado.apellido, empleado.dni, cargo.nombre as nomCargo FROM empleado INNER JOIN cargo ON empleado.cargo = cargo.id_cargo WHERE id_empleado = '$empleado'");
$datos_emp_global = $sql_emp->fetch_object();
if (!$datos_emp_global) {
    die("Empleado no encontrado.");
}
$nombre_completo_emp = mb_strtoupper($datos_emp_global->nombre . " " . $datos_emp_global->apellido, 'UTF-8');
$dni_emp = $datos_emp_global->dni;
$cargo_emp = mb_strtoupper($datos_emp_global->nomCargo, 'UTF-8');

class PDF extends FPDF
{
    // Pasar variables globales al header
    public $nombreEmp;
    public $dniEmp;
    public $cargoEmp;

    function Header()
    {
        include '../../modelo/conexion.php'; 

        $consulta_info = $conexion->query(" select * from institucion "); 
        $dato_info = $consulta_info ? $consulta_info->fetch_object() : null;
        
        $nombre_empresa = "SAGDores";
        $ubicacion = $dato_info ? $dato_info->ubicacion : 'Valle de la Pascua';
        $telefono = $dato_info ? $dato_info->telefono : 'N/A';
        $ruc = $dato_info ? $dato_info->ruc : 'N/A';

        $this->Image('../../img/logo.png', 260, 5, 25); 
        $this->SetFont('Arial', 'B', 19); 
        $this->Cell(95); 
        $this->SetTextColor(0, 0, 0); 
        $this->Cell(110, 15, utf8_decode($nombre_empresa), 1, 1, 'C', 0); 
        $this->Ln(3); 
        $this->SetTextColor(103); 

        /* UBICACION */
        $this->Cell(180);  
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(96, 10, utf8_decode("Ubicación : " . $ubicacion), 0, 0, '', 0);
        $this->Ln(5);

        /* TELEFONO */
        $this->Cell(180);  
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(59, 10, utf8_decode("Teléfono : " . $telefono), 0, 0, '', 0);
        $this->Ln(5);

        /* RUC */
        $this->Cell(180);  
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(85, 10, utf8_decode("RIF/RUC : " . $ruc), 0, 0, '', 0);
        $this->Ln(10);

        /* TITULO DE LA TABLA */
        $this->SetTextColor(220, 38, 38); 
        $this->Cell(100); 
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(100, 10, utf8_decode("REPORTE INDIVIDUAL DE ASISTENCIAS"), 0, 1, 'C', 0);
        
        /* INFO DEL EMPLEADO EN EL HEADER */
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Arial', 'B', 12);
        // Reducimos un poco la fuente si el cargo o nombre es muy largo
        $this->Cell(275, 10, utf8_decode("EMPLEADO: " . $this->nombreEmp . "   |   CÉDULA: " . $this->dniEmp . "   |   CARGO: " . $this->cargoEmp), 0, 1, 'C', 0);
        $this->Ln(4);

        /* CAMPOS DE LA TABLA */
        $this->SetFillColor(254, 202, 202); 
        $this->SetTextColor(0, 0, 0); 
        $this->SetDrawColor(220, 38, 38); 
        $this->SetFont('Arial', 'B', 11);
        // Ancho total ~275 (landscape)
        $this->Cell(15, 10, utf8_decode('N°'), 1, 0, 'C', 1);
        $this->Cell(80, 10, utf8_decode('FECHA'), 1, 0, 'C', 1);
        $this->Cell(90, 10, utf8_decode('HORA DE ENTRADA'), 1, 0, 'C', 1);
        $this->Cell(90, 10, utf8_decode('HORA DE SALIDA'), 1, 1, 'C', 1);
    }

    function Footer()
    {
        $this->SetY(-15); 
        $this->SetFont('Arial', 'I', 8); 
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C'); 

        $this->SetY(-15); 
        $this->SetFont('Arial', 'I', 8); 
        $hoy = date('d/m/Y');
        $this->Cell(540, 10, utf8_decode($hoy), 0, 0, 'C'); 
    }
}

// Construir condición de fechas en la unión 
$dateCondition = "";
if (!empty($fechaInicio) && !empty($fechaFinal)) {
    $dateCondition = " AND asistencia.entrada BETWEEN '$fechaInicio 00:00:00' AND '$fechaFinal 23:59:59'";
} else {
    $dateCondition = " AND DATE(asistencia.entrada) = CURDATE()";
}

$where = [];
$where[] = "empleado.id_empleado = '" . $empleado . "'";

if ($tipo === 'entrada') {
    $where[] = "asistencia.entrada IS NOT NULL AND asistencia.salida IS NULL";
} elseif ($tipo === 'salida') {
    $where[] = "asistencia.salida IS NOT NULL";
} elseif ($tipo === 'falta') {
    $where[] = "asistencia.id_asistencia IS NULL";
}

if ($turno === 'mañana') {
    $where[] = "TIME(asistencia.entrada) < '12:00:00'";
} elseif ($turno === 'noche') {
    $where[] = "TIME(asistencia.entrada) >= '12:00:00'";
}

$whereSQL = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

$sql_query = "SELECT
    asistencia.id_asistencia,
    date_format(asistencia.entrada, '%d/%m/%Y') as 'fecha_sola',
    date_format(asistencia.entrada, '%h:%i %p') as 'hora_entrada',
    date_format(asistencia.salida, '%h:%i %p') as 'hora_salida',
    j_i.estado as justificacion_estado
    FROM empleado
    LEFT JOIN asistencia ON asistencia.id_empleado = empleado.id_empleado $dateCondition
    LEFT JOIN justificacion_inasistencia j_i ON j_i.id_empleado = empleado.id_empleado AND j_i.fecha = DATE(asistencia.entrada)
    $whereSQL
    ORDER BY asistencia.entrada ASC";
    
$sql = $conexion->query($sql_query);

$pdf = new PDF();
$pdf->nombreEmp = $nombre_completo_emp;
$pdf->dniEmp = $dni_emp;
$pdf->cargoEmp = $cargo_emp;

$pdf->AddPage("landscape");
$pdf->AliasNbPages();
$i = 0;
$pdf->SetFont('Arial', '', 12);
$pdf->SetDrawColor(163, 163, 163);

if ($sql && $sql->num_rows > 0) {
    while ($datos_reporte = $sql->fetch_object()) {
        $i++;
        
        $estadoJustificacion = $datos_reporte->justificacion_estado ? strtoupper($datos_reporte->justificacion_estado) : 'FALTA';
        
        $fecha = $datos_reporte->fecha_sola ? $datos_reporte->fecha_sola : (!empty($fechaInicio) ? date('d/m/Y', strtotime($fechaInicio)) : date('d/m/Y'));
        
        $entrada = $datos_reporte->hora_entrada ? $datos_reporte->hora_entrada : $estadoJustificacion;
        $salida = $datos_reporte->hora_salida ? $datos_reporte->hora_salida : ($datos_reporte->hora_entrada ? 'EN TURNO' : $estadoJustificacion);
        
        $pdf->Cell(15, 10, utf8_decode($i), 1, 0, 'C', 0);
        $pdf->Cell(80, 10, utf8_decode($fecha), 1, 0, 'C', 0);
        
        if (!$datos_reporte->hora_entrada) { 
            if ($estadoJustificacion === 'FALTA') $pdf->SetTextColor(220, 38, 38);       
            if ($estadoJustificacion === 'REPOSO') $pdf->SetTextColor(217, 119, 6);      
            if ($estadoJustificacion === 'VACACIONES') $pdf->SetTextColor(29, 78, 216);  
            if ($estadoJustificacion === 'PERMISO') $pdf->SetTextColor(126, 34, 206);    
        } else {
            $pdf->SetTextColor(0, 0, 0); 
        }
        $pdf->Cell(90, 10, utf8_decode($entrada), 1, 0, 'C', 0);
        
        if ($salida === 'EN TURNO' || !$datos_reporte->hora_entrada) {
            if ($salida === 'EN TURNO') {
                $pdf->SetTextColor(220, 38, 38);
            }
        } else {
            $pdf->SetTextColor(0, 0, 0);
        }
        $pdf->Cell(90, 10, utf8_decode($salida), 1, 1, 'C', 0);
        
        $pdf->SetTextColor(0, 0, 0);
    }
} else {
    $pdf->Cell(275, 15, utf8_decode('No hay registros para este empleado en el periodo seleccionado.'), 1, 1, 'C', 0);
}

$pdf->Output('Reporte_Asistencia_' . str_replace(' ', '_', $nombre_completo_emp) . '.pdf', 'I');
exit;
?>
