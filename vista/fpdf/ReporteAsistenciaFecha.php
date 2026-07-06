<?php

// Recibir y validar todos los parámetros del formulario avanzado
$fechaInicio = isset($_GET["fecha_inicio"]) ? $_GET["fecha_inicio"] : null;
$fechaFinal = isset($_GET["fecha_fin"]) ? $_GET["fecha_fin"] : null;
$empleado = isset($_GET["empleado"]) ? $_GET["empleado"] : null;
$cargo = isset($_GET["cargo"]) ? $_GET["cargo"] : null;

   require('./fpdf.php');

   class PDF extends FPDF
   {
      // Cabecera de página
      function Header()
      {
         include '../../modelo/conexion.php'; //llamamos a la conexion BD
   
         $consulta_info = $conexion->query(" select * from institucion "); //traemos datos de la empresa desde BD
         $dato_info = $consulta_info ? $consulta_info->fetch_object() : null;
         
         $nombre_empresa = "SAGDores";
         $ubicacion = $dato_info ? $dato_info->ubicacion : 'Valle de la Pascua';
         $telefono = $dato_info ? $dato_info->telefono : 'N/A';
         $ruc = $dato_info ? $dato_info->ruc : 'N/A';

         $this->Image('../../img/logo.png', 260, 5, 25); //logo de la institucion,moverDerecha,moverAbajo,tamañoIMG
         $this->SetFont('Arial', 'B', 19); //tipo fuente, negrita(B-I-U-BIU), tamañoTexto
         $this->Cell(95); // Movernos a la derecha
         $this->SetTextColor(0, 0, 0); //color
         //creamos una celda o fila
         $this->Cell(110, 15, utf8_decode($nombre_empresa), 1, 1, 'C', 0); // AnchoCelda,AltoCelda,titulo,borde(1-0),saltoLinea(1-0),posicion(L-C-R),ColorFondo(1-0)
         $this->Ln(3); // Salto de línea
         $this->SetTextColor(103); //color
   
         /* UBICACION */
         $this->Cell(180);  // mover a la derecha
         $this->SetFont('Arial', 'B', 10);
         $this->Cell(96, 10, utf8_decode("Ubicación : " . $ubicacion), 0, 0, '', 0);
         $this->Ln(5);
   
         /* TELEFONO */
         $this->Cell(180);  // mover a la derecha
         $this->SetFont('Arial', 'B', 10);
         $this->Cell(59, 10, utf8_decode("Teléfono : " . $telefono), 0, 0, '', 0);
         $this->Ln(5);
   
         /* RUC */
         $this->Cell(180);  // mover a la derecha
         $this->SetFont('Arial', 'B', 10);
         $this->Cell(85, 10, utf8_decode("RIF/RUC : " . $ruc), 0, 0, '', 0);
         $this->Ln(10);
   
         /* TITULO DE LA TABLA */
         //color
         $this->SetTextColor(220, 38, 38); // Rojo Tailwind (Red-600)
         $this->Cell(100); // mover a la derecha
         $this->SetFont('Arial', 'B', 15);
         $this->Cell(100, 10, utf8_decode("REPORTE MAESTRO DE ASISTENCIAS"), 0, 1, 'C', 0);
         $this->Ln(7);
   
         /* CAMPOS DE LA TABLA */
         //color
         $this->SetFillColor(254, 202, 202); // colorFondo (Rojo claro Red-200)
         $this->SetTextColor(0, 0, 0); //colorTexto
         $this->SetDrawColor(220, 38, 38); //colorBorde
         $this->SetFont('Arial', 'B', 11);
         $this->Cell(15, 10, utf8_decode('N°'), 1, 0, 'C', 1);
         $this->Cell(80, 10, utf8_decode('EMPLEADO'), 1, 0, 'C', 1);
         $this->Cell(30, 10, utf8_decode('CÉDULA'), 1, 0, 'C', 1);
         $this->Cell(50, 10, utf8_decode('CARGO'), 1, 0, 'C', 1);
         $this->Cell(50, 10, utf8_decode('ENTRADA'), 1, 0, 'C', 1);
         $this->Cell(50, 10, utf8_decode('SALIDA'), 1, 1, 'C', 1);
      }
   
      // Pie de página
      function Footer()
      {
         $this->SetY(-15); // Posición: a 1,5 cm del final
         $this->SetFont('Arial', 'I', 8); //tipo fuente, negrita(B-I-U-BIU), tamañoTexto
         $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C'); //pie de pagina(numero de pagina)
   
         $this->SetY(-15); // Posición: a 1,5 cm del final
         $this->SetFont('Arial', 'I', 8); //tipo fuente, cursiva, tamañoTexto
         $hoy = date('d/m/Y');
         $this->Cell(540, 10, utf8_decode($hoy), 0, 0, 'C'); // pie de pagina(fecha de pagina)
      }
   }

   include '../../modelo/conexion.php';

   // Limpiar y obtener filtros adicionales si existen
   $tipo = isset($_GET["tipo"]) ? $_GET["tipo"] : '';
   $turno = isset($_GET["turno"]) ? $_GET["turno"] : '';

   // Construir condición de fechas en la unión 
   $dateCondition = "";
   if (!empty($fechaInicio) && !empty($fechaFinal)) {
       $dateCondition = " AND asistencia.entrada BETWEEN '$fechaInicio 00:00:00' AND '$fechaFinal 23:59:59'";
   } else {
       // Por defecto solo asistencias del día que está corriendo
       $dateCondition = " AND DATE(asistencia.entrada) = CURDATE()";
   }

   $where = [];
   
   if (!empty($empleado) && $empleado !== 'todos') {
      $where[] = "empleado.id_empleado = '" . $empleado . "'";
   }
   if (!empty($cargo) && $cargo !== 'todos') {
      $where[] = "empleado.cargo = '" . $cargo . "'";
   }
   
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

   $fecha_filtro_sql = !empty($fechaInicio) ? "'$fechaInicio'" : "CURDATE()";
   
   $sql_query = "SELECT
      asistencia.id_asistencia,
      empleado.id_empleado,
      date_format(asistencia.entrada, '%d-%m-%Y %h:%i %p') as 'entrada',
      date_format(asistencia.salida, '%d-%m-%Y %h:%i %p') as 'salida',
      empleado.nombre,
      empleado.apellido,
      empleado.dni,
      cargo.nombre AS 'nomCargo',
      j_i.estado as justificacion_estado
      FROM empleado
      LEFT JOIN asistencia ON asistencia.id_empleado = empleado.id_empleado $dateCondition
      INNER JOIN cargo ON empleado.cargo = cargo.id_cargo
      LEFT JOIN justificacion_inasistencia j_i ON j_i.id_empleado = empleado.id_empleado AND j_i.fecha = $fecha_filtro_sql
      $whereSQL
      ORDER BY CASE WHEN asistencia.entrada IS NULL THEN 1 ELSE 0 END ASC, asistencia.entrada ASC, empleado.nombre ASC, empleado.apellido ASC";
      
   $sql = $conexion->query($sql_query);

   $pdf = new PDF();
   $pdf->AddPage("landscape");
   $pdf->AliasNbPages();
   $i = 0;
   $pdf->SetFont('Arial', '', 12);
   $pdf->SetDrawColor(163, 163, 163);

   if ($sql && $sql->num_rows > 0) {
      while ($datos_reporte = $sql->fetch_object()) {
         $i++;
         
         $estadoJustificacion = $datos_reporte->justificacion_estado ? strtoupper($datos_reporte->justificacion_estado) : 'FALTA';
         $entrada = $datos_reporte->entrada ? $datos_reporte->entrada : $estadoJustificacion;
         $salida = $datos_reporte->salida ? $datos_reporte->salida : ($datos_reporte->entrada ? 'EN TURNO' : $estadoJustificacion);
         
         $pdf->Cell(15, 10, utf8_decode($i), 1, 0, 'C', 0);
         $pdf->Cell(80, 10, utf8_decode($datos_reporte->nombre . " " . $datos_reporte->apellido), 1, 0, 'C', 0);
         $pdf->Cell(30, 10, utf8_decode($datos_reporte->dni), 1, 0, 'C', 0);
         $pdf->Cell(50, 10, utf8_decode($datos_reporte->nomCargo), 1, 0, 'C', 0);
         
         if (!$datos_reporte->entrada) { // Es inasistencia (Falta, reposo, vacaciones, etc)
            if ($estadoJustificacion === 'FALTA') $pdf->SetTextColor(220, 38, 38);       // Rojo
            if ($estadoJustificacion === 'REPOSO') $pdf->SetTextColor(217, 119, 6);      // Ámbar
            if ($estadoJustificacion === 'VACACIONES') $pdf->SetTextColor(29, 78, 216);  // Azul
            if ($estadoJustificacion === 'PERMISO') $pdf->SetTextColor(126, 34, 206);    // Morado
         } else {
             $pdf->SetTextColor(0, 0, 0); // Texto normal
         }
         $pdf->Cell(50, 10, utf8_decode($entrada), 1, 0, 'C', 0);
         
         if ($salida === 'EN TURNO' || !$datos_reporte->entrada) {
            // Si está en turno (rojo), o si es justificación (usan el color ya seteado arriba por el if). 
            // Si es EN TURNO forzamos rojo:
            if ($salida === 'EN TURNO') {
                $pdf->SetTextColor(220, 38, 38);
            }
         } else {
             $pdf->SetTextColor(0, 0, 0);
         }
         $pdf->Cell(50, 10, utf8_decode($salida), 1, 1, 'C', 0);
         
         // reset color for next iteration
         $pdf->SetTextColor(0, 0, 0);
      }
   } else {
      $pdf->Cell(275, 15, utf8_decode('No hay registros para las fechas y filtros seleccionados.'), 1, 1, 'C', 0);
   }

   $pdf->Output('Reporte Asistencia.pdf', 'I');
   exit;
?>

