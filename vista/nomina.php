<?php
session_start();
if (empty($_SESSION['email'])) {
    header("Location: /login_register12/index.php");
    exit();
}

include "../modelo/conexion.php";

// Obtener Configuración de Nómina (Tasa y Salario Base)
$tasa_actual = 36.50; // default
$sueldo_base_global = 210.00;
$cesta_ticket_global = 30.00;

$config_query = $conexion->query("SELECT tasa, sueldo_base_usd, cesta_ticket_usd FROM tasa_cambio LIMIT 1");
if ($config_query && $config_query->num_rows > 0) {
    $config_data = $config_query->fetch_object();
    $tasa_actual = floatval($config_data->tasa);
    $sueldo_base_global = floatval($config_data->sueldo_base_usd);
    $cesta_ticket_global = floatval($config_data->cesta_ticket_usd);
}

// Filtros
$mes = isset($_GET['mes']) ? (int)$_GET['mes'] : (int)date('m');
$anio = isset($_GET['anio']) ? (int)$_GET['anio'] : (int)date('Y');
$quincena = isset($_GET['quincena']) ? (int)$_GET['quincena'] : (date('d') <= 15 ? 1 : 2);

?>

<link href="/login_register12/public/vendor/dist/fonts/montserrat/index.css" rel="stylesheet">
<?php require('./layout/sidebar.php'); ?>

<div class="px-4 py-8 md:px-8 bg-[#f8fafc] transition-colors duration-300 min-h-[calc(100vh-4rem)]">
    <div class="max-w-7xl mx-auto">
        
        <!-- Header -->
        <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex flex-col justify-start items-start gap-4">
                <h3 class="text-2xl md:text-3xl font-bold text-slate-800 flex items-center gap-3 transition-colors">
                    <div class="p-2.5 bg-emerald-600 text-white rounded-xl shadow-lg shadow-emerald-600/30">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                    </div>
                    Cálculo de Nómina
                </h3>
                <p class="text-slate-500 font-medium transition-colors">Gestión de pagos quincenales, cálculo en Bolívares y Dólares.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <button data-bs-toggle="modal" data-bs-target="#modalRegistrarBono" class="bg-gradient-to-r from-teal-500 to-emerald-500 hover:from-teal-600 hover:to-emerald-600 text-white font-bold py-3 px-6 rounded-full shadow-md shadow-emerald-500/20 transition-all duration-300 transform hover:-translate-y-1 flex items-center gap-2">
                    <i class="fa-solid fa-hand-holding-dollar"></i> Registrar Bono
                </button>
                <button data-bs-toggle="modal" data-bs-target="#modalRegistrarDeduccion" class="bg-white text-rose-600 hover:bg-rose-50 border border-rose-200 font-bold py-3 px-6 rounded-full shadow-sm transition-all duration-300 transform hover:-translate-y-1 flex items-center gap-2">
                    <i class="fa-solid fa-file-invoice-dollar"></i> Registrar Deducción
                </button>
                <a href="../vista/configuracion.php" class="bg-white text-slate-600 hover:bg-slate-50 border border-slate-200 font-bold py-3 px-6 rounded-full shadow-sm transition-all duration-300 transform hover:-translate-y-1 flex items-center gap-2">
                    <i class="fa-solid fa-gear"></i> Ajustes Generales
                </a>
            </div>
        </div>

        <?php
        // Pre-calcular totales para el Dashboard
        $query_totales_emp = $conexion->query("SELECT COUNT(*) as num_activos FROM empleado");
        $num_activos = ($query_totales_emp && $row = $query_totales_emp->fetch_object()) ? $row->num_activos : 0;
        $salario_fijo_qna_total = $num_activos * (($sueldo_base_global / 2) + ($cesta_ticket_global / 2));

        $query_totales_ded = $conexion->query("SELECT SUM(monto_usd) as total FROM deduccion WHERE mes = $mes AND anio = $anio AND quincena = $quincena");
        $total_deducciones_dash = ($query_totales_ded && $row = $query_totales_ded->fetch_object()) ? floatval($row->total) : 0;

        $query_totales_bonos = $conexion->query("SELECT SUM(monto_usd) as total FROM bonificacion WHERE mes = $mes AND anio = $anio AND quincena = $quincena");
        $total_bonos_dash = ($query_totales_bonos && $row = $query_totales_bonos->fetch_object()) ? floatval($row->total) : 0;

        $gran_total_nomina_dash = $salario_fijo_qna_total + $total_bonos_dash - $total_deducciones_dash;
        $gran_total_nomina_bs = $gran_total_nomina_dash * $tasa_actual;
        ?>
        <!-- Tarjetas Dashboard -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-[0_4px_20px_rgb(0,0,0,0.03)] flex items-center gap-4 relative overflow-hidden group hover:border-emerald-200 transition-all">
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center text-2xl font-bold group-hover:scale-110 transition-transform"><i class="fa-solid fa-wallet"></i></div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total a Desembolsar</p>
                    <h4 class="text-2xl font-black text-slate-800 m-0 transition-colors">$ <?= number_format($gran_total_nomina_dash, 2) ?></h4>
                    <p class="text-xs text-slate-500 mt-1 font-semibold">Bs. <?= number_format($gran_total_nomina_bs, 2, ',', '.') ?></p>
                </div>
            </div>
            
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-[0_4px_20px_rgb(0,0,0,0.03)] flex items-center gap-4 relative overflow-hidden group hover:border-teal-200 transition-all">
                <div class="w-14 h-14 rounded-2xl bg-teal-50 text-teal-500 flex items-center justify-center text-2xl font-bold group-hover:scale-110 transition-transform"><i class="fa-solid fa-gift"></i></div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Bonos Repartidos</p>
                    <h4 class="text-2xl font-black text-teal-600 m-0 transition-colors">+ $ <?= number_format($total_bonos_dash, 2) ?></h4>
                </div>
            </div>
            
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-[0_4px_20px_rgb(0,0,0,0.03)] flex items-center gap-4 relative overflow-hidden group hover:border-rose-200 transition-all">
                <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center text-2xl font-bold group-hover:scale-110 transition-transform"><i class="fa-solid fa-scissors"></i></div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Deducciones (Ahorro)</p>
                    <h4 class="text-2xl font-black text-rose-500 m-0 transition-colors">- $ <?= number_format($total_deducciones_dash, 2) ?></h4>
                </div>
            </div>
        </div>

        <!-- Filtros Nómina -->
        <div class="bg-white rounded-3xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100 p-6 mb-8 transition-colors duration-300">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end m-0">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Año</label>
                    <select name="anio" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 font-semibold text-slate-800 outline-none focus:border-emerald-500 transition-colors cursor-pointer">
                        <?php for($i = date('Y')-2; $i <= date('Y'); $i++): ?>
                            <option value="<?= $i ?>" <?= $i == $anio ? 'selected' : '' ?> class="bg-white"><?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Mes</label>
                    <select name="mes" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 font-semibold text-slate-800 outline-none focus:border-emerald-500 transition-colors cursor-pointer">
                        <?php 
                        $meses = ['1'=>'Enero','2'=>'Febrero','3'=>'Marzo','4'=>'Abril','5'=>'Mayo','6'=>'Junio','7'=>'Julio','8'=>'Agosto','9'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre'];
                        foreach($meses as $num => $nombre): ?>
                            <option value="<?= $num ?>" <?= $num == $mes ? 'selected' : '' ?> class="bg-white"><?= $nombre ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Quincena</label>
                    <select name="quincena" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 font-semibold text-slate-800 outline-none focus:border-emerald-500 transition-colors cursor-pointer">
                        <option value="1" <?= $quincena == 1 ? 'selected' : '' ?> class="bg-white">1ra Quincena (Día 1 - 15)</option>
                        <option value="2" <?= $quincena == 2 ? 'selected' : '' ?> class="bg-white">2da Quincena (Día 16 - Fin)</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="w-full bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 font-bold py-2.5 px-6 rounded-xl transition-colors flex items-center justify-center gap-2">
                        <i class="fa-solid fa-magnifying-glass"></i> Calcular
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabla Cálculos -->
        <div class="bg-white rounded-[2rem] shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100 overflow-hidden transition-colors duration-300">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-white">
                <h5 class="text-lg font-bold text-slate-800 m-0 flex items-center gap-2">
                    <i class="fa-solid fa-users text-emerald-500"></i>
                    Nómina Empleados (Bs y USD)
                </h5>
                <form action="fpdf/GenerarRecibosPDF.php" method="GET" target="_blank" class="m-0">
                    <input type="hidden" name="mes" value="<?= $mes ?>">
                    <input type="hidden" name="anio" value="<?= $anio ?>">
                    <input type="hidden" name="quincena" value="<?= $quincena ?>">
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-5 rounded-lg shadow-md transition-colors flex items-center gap-2 text-sm">
                        <i class="fa-solid fa-file-pdf"></i> Generar Recibos PDF
                    </button>
                </form>
            </div>
            <div class="px-6 pb-6 pt-4 overflow-x-auto">
                <table class="w-full text-sm text-left text-slate-500 whitespace-nowrap">
                    <thead class="text-xs text-slate-500 uppercase bg-slate-50 rounded-xl">
                        <tr>
                            <th class="px-6 py-4 rounded-l-xl">Empleado</th>
                            <th class="px-6 py-4">Salario Base (Qna)</th>
                            <th class="px-6 py-4">Cesta Ticket (Qna)</th>
                            <th class="px-6 py-4">Bonos</th>
                            <th class="px-6 py-4">Deducciones</th>
                            <th class="px-6 py-4 bg-emerald-100 font-bold text-emerald-800">Total a Pagar ($)</th>
                            <th class="px-6 py-4 bg-slate-100 font-bold text-slate-700 rounded-r-xl">Equivalente (Bs)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php
                        // Obtenemos todos los empleados
                        $empleados = $conexion->query("SELECT id_empleado, nombre, apellido, dni, cargo, salario_base FROM empleado");
                        $total_bs_general = 0;
                        $total_usd_general = 0;

                        if ($empleados && $empleados->num_rows > 0) {
                            while($emp = $empleados->fetch_object()) {
                                
                                // Cálculos basados en configuración global
                                $salario_mensual_usd = $sueldo_base_global;
                                $cesta_ticket_mensual_usd = $cesta_ticket_global;
                                
                                $salario_quincena_usd = $salario_mensual_usd / 2;
                                $cesta_ticket_quincena_usd = $cesta_ticket_mensual_usd / 2;
                                
                                $asignaciones_quincena_usd = $salario_quincena_usd + $cesta_ticket_quincena_usd; // $120
                                
                                // Consultar deducciones y bonos
                                $query_ded = $conexion->query("SELECT SUM(monto_usd) as total_deduccion FROM deduccion WHERE id_empleado = {$emp->id_empleado} AND mes = $mes AND anio = $anio AND quincena = $quincena");
                                $deducciones_usd = ($query_ded && $row_ded = $query_ded->fetch_object()) ? floatval($row_ded->total_deduccion) : 0;
                                
                                $query_bonos = $conexion->query("SELECT SUM(monto_usd) as total_bonos FROM bonificacion WHERE id_empleado = {$emp->id_empleado} AND mes = $mes AND anio = $anio AND quincena = $quincena");
                                $bonos_usd = ($query_bonos && $row_bonos = $query_bonos->fetch_object()) ? floatval($row_bonos->total_bonos) : 0;
                                
                                $total_pagar_usd = $asignaciones_quincena_usd + $bonos_usd - $deducciones_usd;
                                $total_pagar_bs = $total_pagar_usd * $tasa_actual;
                                
                                $total_usd_general += $total_pagar_usd;
                                $total_bs_general += $total_pagar_bs;
                        ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800"><?= $emp->nombre . " " . $emp->apellido ?></div>
                                <div class="text-xs text-slate-400">CI: <?= $emp->dni ?></div>
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-600">$ <?= number_format($salario_quincena_usd, 2, ',', '.') ?> <span class="text-xs text-slate-400 font-normal ml-1">(Bs. <?= number_format($salario_quincena_usd * $tasa_actual, 2, ',', '.') ?>)</span></td>
                            <td class="px-6 py-4 font-semibold text-slate-600">$ <?= number_format($cesta_ticket_quincena_usd, 2, ',', '.') ?> <span class="text-xs text-slate-400 font-normal ml-1">(Bs. <?= number_format($cesta_ticket_quincena_usd * $tasa_actual, 2, ',', '.') ?>)</span></td>
                            <td class="px-6 py-4 font-bold text-teal-600">+ $ <?= number_format($bonos_usd, 2, ',', '.') ?></td>
                            <td class="px-6 py-4 font-bold text-rose-500">- $ <?= number_format($deducciones_usd, 2, ',', '.') ?></td>
                            <td class="px-6 py-4 font-black text-emerald-600 bg-emerald-50/50">$ <?= number_format($total_pagar_usd, 2, ',', '.') ?></td>
                            <td class="px-6 py-4 font-black text-slate-800 bg-slate-50">Bs. <?= number_format($total_pagar_bs, 2, ',', '.') ?></td>
                        </tr>
                        <?php 
                            }
                        } else {
                            echo '<tr><td colspan="7" class="text-center py-8 text-slate-500">No hay empleados registrados.</td></tr>';
                        }
                        ?>
                    </tbody>
                    <tfoot class="bg-slate-800 text-white rounded-xl">
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-right font-bold uppercase text-xs rounded-l-xl">Total Nómina Quincenal:</td>
                            <td class="px-6 py-4 font-black text-emerald-400 text-base">$ <?= number_format($total_usd_general, 2, ',', '.') ?></td>
                            <td class="px-6 py-4 font-black text-base rounded-r-xl">Bs. <?= number_format($total_bs_general, 2, ',', '.') ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</div>


<!-- Modal Registrar Bono -->
<div class="modal fade" id="modalRegistrarBono" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-2xl rounded-3xl overflow-hidden bg-white">
      <div class="bg-gradient-to-r from-teal-500 to-emerald-600 p-6 flex items-center justify-between">
        <h5 class="text-white font-bold text-lg flex items-center gap-3 m-0">
          <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center"><i class="fa-solid fa-plus-circle"></i></div>
          Registrar Bonificación
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="../controlador/controlador_registrar_bono.php" method="POST" class="m-0">
        <!-- Mantener filtros actuales para redirigir correctamente -->
        <input type="hidden" name="mes_bono" value="<?= $mes ?>">
        <input type="hidden" name="anio_bono" value="<?= $anio ?>">
        <input type="hidden" name="quincena_bono" value="<?= $quincena ?>">
        
        <div class="p-8 bg-white space-y-5">
            <div>
              <label class="block text-sm font-bold text-slate-700 mb-2">Empleado</label>
              <select name="id_empleado" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl px-4 py-3 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none cursor-pointer">
                <option value="" class="bg-white">Seleccione un empleado...</option>
                <?php
                $emp_list_bono = $conexion->query("SELECT id_empleado, nombre, apellido, dni FROM empleado");
                if ($emp_list_bono && $emp_list_bono->num_rows > 0) {
                    while($e = $emp_list_bono->fetch_object()) {
                        echo "<option value='{$e->id_empleado}' class='bg-white'>{$e->nombre} {$e->apellido} (CI: {$e->dni})</option>";
                    }
                }
                ?>
              </select>
            </div>

            <div>
              <label class="block text-sm font-bold text-slate-700 mb-2">Motivo del Bono</label>
              <select name="motivo_bono" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl px-4 py-3 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none cursor-pointer">
                <option value="Horas Extras" class="bg-white">Horas Extras</option>
                <option value="Bono de Producción/Desempeño" class="bg-white">Bono de Producción/Desempeño</option>
                <option value="Trabajo en Día Feriado" class="bg-white">Trabajo en Día Feriado</option>
                <option value="Bono Nocturno" class="bg-white">Bono Nocturno</option>
                <option value="Bono Especial" class="bg-white">Bono Especial</option>
                <option value="Otro" class="bg-white">Otro</option>
              </select>
            </div>
            
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Monto a Asignar ($)</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">$</span>
                    <input type="number" step="0.01" name="monto_bono_usd" required placeholder="Ej. 10.00" class="w-full bg-slate-50 border border-slate-200 text-emerald-600 font-bold text-lg rounded-xl pl-10 pr-4 py-3 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none">
                </div>
            </div>

            <div class="p-4 bg-emerald-50 border border-emerald-100 rounded-xl">
                <p class="text-xs text-emerald-800 m-0"><i class="fa-solid fa-info-circle mr-1"></i> Este bono se sumará automáticamente a la <strong><?= $quincena == 1 ? '1ra' : '2da' ?> quincena de <?= $meses[$mes] ?? $mes ?> del <?= $anio ?></strong> para el empleado seleccionado.</p>
            </div>
            
        </div>
        <div class="flex justify-end gap-3 px-8 pb-8 bg-white">
          <button type="button" class="px-6 py-3 rounded-xl font-bold text-slate-500 bg-slate-100 hover:bg-slate-200 transition-colors" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" name="btn_registrar_bono" value="1" class="px-6 py-3 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-lg shadow-emerald-500/30 transition-colors">Registrar Bono</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Registrar Deducción -->
<div class="modal fade" id="modalRegistrarDeduccion" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-2xl rounded-3xl overflow-hidden bg-white">
      <div class="bg-gradient-to-r from-rose-500 to-red-600 p-6 flex items-center justify-between">
        <h5 class="text-white font-bold text-lg flex items-center gap-3 m-0">
          <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center"><i class="fa-solid fa-minus-circle"></i></div>
          Registrar Deducción
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="../controlador/controlador_registrar_deduccion.php" method="POST" class="m-0">
        <!-- Mantener filtros actuales para redirigir correctamente -->
        <input type="hidden" name="mes_deduccion" value="<?= $mes ?>">
        <input type="hidden" name="anio_deduccion" value="<?= $anio ?>">
        <input type="hidden" name="quincena_deduccion" value="<?= $quincena ?>">
        
        <div class="p-8 bg-white space-y-5">
            <div>
              <label class="block text-sm font-bold text-slate-700 mb-2">Empleado</label>
              <select name="id_empleado" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl px-4 py-3 focus:ring-4 focus:ring-rose-500/20 focus:border-rose-500 outline-none cursor-pointer">
                <option value="" class="bg-white">Seleccione un empleado...</option>
                <?php
                $emp_list = $conexion->query("SELECT id_empleado, nombre, apellido, dni FROM empleado");
                if ($emp_list && $emp_list->num_rows > 0) {
                    while($e = $emp_list->fetch_object()) {
                        echo "<option value='{$e->id_empleado}' class='bg-white'>{$e->nombre} {$e->apellido} (CI: {$e->dni})</option>";
                    }
                }
                ?>
              </select>
            </div>

            <div>
              <label class="block text-sm font-bold text-slate-700 mb-2">Motivo de la Deducción</label>
              <select name="motivo_deduccion" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl px-4 py-3 focus:ring-4 focus:ring-rose-500/20 focus:border-rose-500 outline-none cursor-pointer">
                <option value="Falta Injustificada" class="bg-white">Falta Injustificada</option>
                <option value="Permiso No Remunerado" class="bg-white">Permiso No Remunerado</option>
                <option value="Amonestación" class="bg-white">Amonestación</option>
                <option value="Retardo" class="bg-white">Retardo / Llegada Tarde</option>
                <option value="Adelanto de Nómina" class="bg-white">Adelanto de Nómina</option>
                <option value="Deuda Pendiente" class="bg-white">Deuda Pendiente</option>
                <option value="Otro" class="bg-white">Otro</option>
              </select>
            </div>
            
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Monto a Deducir ($)</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">$</span>
                    <input type="number" step="0.01" name="monto_deduccion_usd" required placeholder="Ej. 7.00" class="w-full bg-slate-50 border border-slate-200 text-rose-600 font-bold text-lg rounded-xl pl-10 pr-4 py-3 focus:ring-4 focus:ring-rose-500/20 focus:border-rose-500 outline-none">
                </div>
            </div>

            <div class="p-4 bg-rose-50 border border-rose-100 rounded-xl">
                <p class="text-xs text-rose-800 m-0"><i class="fa-solid fa-info-circle mr-1"></i> Esta deducción se aplicará automáticamente a la <strong><?= $quincena == 1 ? '1ra' : '2da' ?> quincena de <?= $meses[$mes] ?? $mes ?> del <?= $anio ?></strong> para el empleado seleccionado.</p>
            </div>
            
        </div>
        <div class="flex justify-end gap-3 px-8 pb-8 bg-white">
          <button type="button" class="px-6 py-3 rounded-xl font-bold text-slate-500 bg-slate-100 hover:bg-slate-200 transition-colors" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" name="btn_registrar_deduccion" value="1" class="px-6 py-3 rounded-xl font-bold text-white bg-rose-600 hover:bg-rose-700 shadow-lg shadow-rose-500/30 transition-colors">Registrar Deducción</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require('./layout/footer.php'); ?>

