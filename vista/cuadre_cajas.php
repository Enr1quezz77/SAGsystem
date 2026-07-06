<?php
session_start();
if (empty($_SESSION['email'])) {
    header("Location: /login_register12/index.php");
    exit();
}

include "../modelo/conexion.php";

// Obtener Tasa de Cambio
$tasa_actual = 36.50; // default
$config_query = $conexion->query("SELECT tasa FROM tasa_cambio LIMIT 1");
if ($config_query && $config_query->num_rows > 0) {
    $config_data = $config_query->fetch_object();
    $tasa_actual = floatval($config_data->tasa);
}

?>

<link href="/login_register12/public/vendor/dist/fonts/montserrat/index.css" rel="stylesheet">
<?php require('./layout/sidebar.php'); ?>

<div class="px-4 py-8 md:px-8 bg-[#f8fafc] transition-colors duration-300 min-h-[calc(100vh-4rem)]">
    <div class="max-w-7xl mx-auto">
        
        <!-- Header -->
        <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex flex-col justify-start items-start gap-4">
                <h3 class="text-2xl md:text-3xl font-bold text-slate-800 flex items-center gap-3 transition-colors">
                    <div class="p-2.5 bg-blue-600 text-white rounded-xl shadow-lg shadow-blue-600/30">
                        <i class="fa-solid fa-cash-register"></i>
                    </div>
                    Cierre de Caja
                </h3>
                <p class="text-slate-500 font-medium transition-colors">Gestión y auditoría del efectivo diario. Tasa de hoy: <strong class="text-blue-600">Bs. <?= number_format($tasa_actual, 2, ',', '.') ?></strong></p>
            </div>
            <div class="flex flex-wrap gap-3">
                <button data-bs-toggle="modal" data-bs-target="#modalNuevoCuadre" class="bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-bold py-3 px-6 rounded-full shadow-md shadow-blue-500/20 transition-all duration-300 transform hover:-translate-y-1 flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i> Registrar Nuevo Cierre
                </button>
            </div>
        </div>

        <?php
            // Validar mensajes de éxito o error
            if(isset($_SESSION['mensaje_caja'])) {
                $tipo = strpos($_SESSION['mensaje_caja'], 'Error') !== false ? 'rose' : 'emerald';
                $icono = $tipo == 'rose' ? 'circle-xmark' : 'circle-check';
                echo "<div class='bg-{$tipo}-50 border border-{$tipo}-200 text-{$tipo}-700 px-4 py-3 rounded-xl mb-6 font-semibold flex items-center gap-2 shadow-sm'><i class='fa-solid fa-{$icono}'></i> {$_SESSION['mensaje_caja']}</div>";
                unset($_SESSION['mensaje_caja']);
            }
        ?>

        <!-- Tabla Historial -->
        <div class="bg-white rounded-[2rem] shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100 overflow-hidden transition-colors duration-300">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-white">
                <h5 class="text-lg font-bold text-slate-800 m-0 flex items-center gap-2">
                    <i class="fa-solid fa-list text-blue-500"></i>
                    Historial de Cierres
                </h5>
            </div>
            <div class="px-6 pb-6 pt-4 overflow-x-auto">
                <table class="w-full text-sm text-left text-slate-500 whitespace-nowrap">
                    <thead class="text-xs text-slate-500 uppercase bg-slate-50 rounded-xl">
                        <tr>
                            <th class="px-6 py-4 rounded-l-xl">Fecha / Hora</th>
                            <th class="px-6 py-4">Responsable</th>
                            <th class="px-6 py-4">Turno</th>
                            <th class="px-6 py-4">Tasa Usada</th>
                            <th class="px-6 py-4">Teórico (USD)</th>
                            <th class="px-6 py-4">Físico (USD)</th>
                            <th class="px-6 py-4">Diferencia</th>
                            <th class="px-6 py-4">Estado</th>
                            <th class="px-6 py-4 rounded-r-xl text-center"><i class="fa-solid fa-bars"></i></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php
                        $cuadres = $conexion->query("SELECT c.*, e.nombre, e.apellido FROM caja_cuadres c JOIN empleado e ON c.id_empleado = e.id_empleado ORDER BY c.id_cuadre DESC LIMIT 50");
                        
                        if ($cuadres && $cuadres->num_rows > 0) {
                            while($row = $cuadres->fetch_object()) {
                                $teorico_usd = $row->fondo_apertura_usd + ($row->fondo_apertura_bs / $row->tasa_dia) + $row->ventas_sistema_usd + ($row->ventas_sistema_bs / $row->tasa_dia) - $row->gastos_caja_usd;
                                $fisico_usd = $row->efectivo_fisico_usd + ($row->efectivo_fisico_bs / $row->tasa_dia);
                                
                                $color_estado = 'bg-emerald-100 text-emerald-700';
                                if($row->estado == 'Faltante') $color_estado = 'bg-rose-100 text-rose-700';
                                if($row->estado == 'Sobrante') $color_estado = 'bg-amber-100 text-amber-700';
                                
                                $color_diff = $row->diferencia_usd < 0 ? 'text-rose-500' : ($row->diferencia_usd > 0 ? 'text-amber-500' : 'text-emerald-500');
                        ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 font-semibold text-slate-700"><?= date('d/m/Y h:i A', strtotime($row->fecha_registro)) ?></td>
                            <td class="px-6 py-4 font-bold text-slate-800"><?= $row->nombre . ' ' . $row->apellido ?></td>
                            <td class="px-6 py-4 font-semibold text-slate-600"><?= $row->turno ?></td>
                            <td class="px-6 py-4 text-slate-500">Bs. <?= number_format($row->tasa_dia, 2, ',', '.') ?></td>
                            <td class="px-6 py-4 font-semibold text-slate-600">$ <?= number_format($teorico_usd, 2, ',', '.') ?></td>
                            <td class="px-6 py-4 font-semibold text-slate-600">$ <?= number_format($fisico_usd, 2, ',', '.') ?></td>
                            <td class="px-6 py-4 font-black <?= $color_diff ?>">
                                <?= $row->diferencia_usd > 0 ? '+' : '' ?>$ <?= number_format($row->diferencia_usd, 2, ',', '.') ?>
                                <div class="text-xs font-semibold opacity-75">(Bs. <?= number_format($row->diferencia_usd * $row->tasa_dia, 2, ',', '.') ?>)</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-bold <?= $color_estado ?>"><?= $row->estado ?></span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <?php
                                    $obs = trim($row->observaciones ?? '') !== '' ? htmlspecialchars($row->observaciones) : 'No se registraron observaciones.';
                                ?>
                                <div class="flex justify-center items-center gap-2">
                                    <button type="button" onclick="verObservacion('<?= addslashes($obs) ?>')" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:bg-blue-100 hover:text-blue-600 transition-colors flex items-center justify-center shadow-sm" title="Ver Observaciones">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    <a href="cuadre_pdf.php?id_cuadre=<?= $row->id_cuadre ?>" target="_blank" class="w-8 h-8 rounded-full bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white transition-colors flex items-center justify-center shadow-sm" title="Imprimir PDF">
                                        <i class="fa-solid fa-file-pdf"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php 
                            }
                        } else {
                            echo '<tr><td colspan="7" class="text-center py-8 text-slate-500 font-medium">No hay registros de cuadres de caja.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- Modal Nuevo Cuadre -->
<div class="modal fade" id="modalNuevoCuadre" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-2xl rounded-3xl overflow-hidden bg-white">
      <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-6 flex items-center justify-between">
        <h5 class="text-white font-bold text-lg flex items-center gap-3 m-0">
          <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center"><i class="fa-solid fa-calculator"></i></div>
          Formulario de Cierre
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="../controlador/controlador_caja_cuadre.php" method="POST" class="m-0" id="formCuadre">
        
        <div class="p-8 bg-white grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <div class="col-span-1 md:col-span-3 flex flex-col md:flex-row justify-between items-start md:items-center bg-slate-50 p-4 rounded-xl border border-slate-100 gap-4">
                <div class="w-full md:w-1/3">
                    <label class="block text-xs font-bold text-slate-400 uppercase">Tasa del Día</label>
                    <span class="text-lg font-black text-blue-600">Bs. <?= number_format($tasa_actual, 2, ',', '.') ?></span>
                    <input type="hidden" id="tasa_dia" name="tasa_dia" value="<?= $tasa_actual ?>">
                </div>
                <div class="w-full md:w-1/3">
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Turno</label>
                    <select name="turno" required class="w-full bg-white border border-slate-200 text-slate-800 font-semibold rounded-xl px-3 py-2 outline-none">
                        <option value="Mañana">Mañana</option>
                        <option value="Noche">Noche</option>
                    </select>
                </div>
                <div class="w-full md:w-1/3">
                  <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Responsable</label>
                  <select name="id_empleado" required class="w-full bg-white border border-slate-200 text-slate-800 font-semibold rounded-xl px-3 py-2 outline-none">
                    <option value="">Seleccione...</option>
                    <?php
                    $emp_list = $conexion->query("SELECT id_empleado, nombre, apellido FROM empleado");
                    if ($emp_list) {
                        while($e = $emp_list->fetch_object()) {
                            echo "<option value='{$e->id_empleado}'>{$e->nombre} {$e->apellido}</option>";
                        }
                    }
                    ?>
                  </select>
                </div>
            </div>

            <!-- Columna Izquierda: Lo que dice el sistema -->
            <div class="space-y-4 border-r border-slate-100 pr-4">
                <h6 class="font-bold text-slate-800 border-b pb-2"><i class="fa-solid fa-desktop text-slate-400 mr-2"></i> Datos del Sistema</h6>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Fondo de Apertura (Bs)</label>
                    <input type="number" step="0.01" id="fondo_bs" name="fondo_apertura_bs" value="0" class="calc-input w-full bg-slate-50 border border-slate-200 text-slate-700 font-bold rounded-xl px-4 py-2 outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Fondo de Apertura ($)</label>
                    <input type="number" step="0.01" id="fondo_usd" name="fondo_apertura_usd" value="0" class="calc-input w-full bg-slate-50 border border-slate-200 text-slate-700 font-bold rounded-xl px-4 py-2 outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Ventas Sistema (Bs)</label>
                    <input type="number" step="0.01" id="ventas_bs" name="ventas_sistema_bs" value="0" class="calc-input w-full bg-slate-50 border border-slate-200 text-slate-700 font-bold rounded-xl px-4 py-2 outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Ventas Sistema ($)</label>
                    <input type="number" step="0.01" id="ventas_usd" name="ventas_sistema_usd" value="0" class="calc-input w-full bg-slate-50 border border-slate-200 text-slate-700 font-bold rounded-xl px-4 py-2 outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Gastos/Vales ($ retirados)</label>
                    <input type="number" step="0.01" id="gastos_usd" name="gastos_caja_usd" value="0" class="calc-input w-full bg-rose-50 border border-rose-200 text-rose-700 font-bold rounded-xl px-4 py-2 outline-none focus:border-rose-500">
                </div>
            </div>

            <!-- Columna Centro: Efectivo Físico -->
            <div class="space-y-4 border-r border-slate-100 pr-4 pl-0 md:pl-2">
                <h6 class="font-bold text-slate-800 border-b pb-2"><i class="fa-solid fa-money-bill-wave text-slate-400 mr-2"></i> Efectivo Físico</h6>
                
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Efectivo Total (Bs)</label>
                    <input type="number" step="0.01" id="fisico_bs" name="efectivo_fisico_bs" value="0" class="calc-input w-full bg-emerald-50 border border-emerald-200 text-emerald-700 font-bold rounded-xl px-4 py-2 outline-none focus:border-emerald-500">
                </div>
                
                <div class="pt-2">
                    <label class="block text-xs font-bold text-slate-700 mb-2 border-b pb-1">Desglose de Billetes ($)</label>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="flex items-center gap-2"><span class="text-xs font-bold w-8 text-emerald-700">$100</span><input type="number" min="0" id="bill_100" class="calc-input w-full bg-emerald-50 border border-emerald-200 text-emerald-700 font-bold rounded-lg px-2 py-1 outline-none text-center"></div>
                        <div class="flex items-center gap-2"><span class="text-xs font-bold w-8 text-emerald-700">$50</span><input type="number" min="0" id="bill_50" class="calc-input w-full bg-emerald-50 border border-emerald-200 text-emerald-700 font-bold rounded-lg px-2 py-1 outline-none text-center"></div>
                        <div class="flex items-center gap-2"><span class="text-xs font-bold w-8 text-emerald-700">$20</span><input type="number" min="0" id="bill_20" class="calc-input w-full bg-emerald-50 border border-emerald-200 text-emerald-700 font-bold rounded-lg px-2 py-1 outline-none text-center"></div>
                        <div class="flex items-center gap-2"><span class="text-xs font-bold w-8 text-emerald-700">$10</span><input type="number" min="0" id="bill_10" class="calc-input w-full bg-emerald-50 border border-emerald-200 text-emerald-700 font-bold rounded-lg px-2 py-1 outline-none text-center"></div>
                        <div class="flex items-center gap-2"><span class="text-xs font-bold w-8 text-emerald-700">$5</span><input type="number" min="0" id="bill_5" class="calc-input w-full bg-emerald-50 border border-emerald-200 text-emerald-700 font-bold rounded-lg px-2 py-1 outline-none text-center"></div>
                        <div class="flex items-center gap-2"><span class="text-xs font-bold w-8 text-emerald-700">$1</span><input type="number" min="0" id="bill_1" class="calc-input w-full bg-emerald-50 border border-emerald-200 text-emerald-700 font-bold rounded-lg px-2 py-1 outline-none text-center"></div>
                    </div>
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1 mt-2">Total Efectivo ($)</label>
                    <input type="number" step="0.01" id="fisico_usd" name="efectivo_fisico_usd" value="0" readonly class="w-full bg-slate-100 border border-slate-300 text-slate-700 font-bold rounded-xl px-4 py-2 outline-none cursor-not-allowed">
                </div>
            </div>

            <!-- Columna Derecha: Medios Electrónicos y Resumen -->
            <div class="space-y-4 pl-0 md:pl-2 flex flex-col justify-between">
                <div>
                    <h6 class="font-bold text-slate-800 border-b pb-2"><i class="fa-solid fa-mobile-screen text-slate-400 mr-2"></i> Medios Electrónicos</h6>
                    <div class="grid grid-cols-2 gap-3 mt-3">
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-700 mb-1">Punto Venta (Bs)</label>
                            <input type="number" step="0.01" id="punto_venta_bs" name="punto_venta_bs" value="0" class="calc-input w-full bg-indigo-50 border border-indigo-200 text-indigo-700 font-bold rounded-xl px-3 py-2 outline-none focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-700 mb-1">Pago Móvil (Bs)</label>
                            <input type="number" step="0.01" id="pago_movil_bs" name="pago_movil_bs" value="0" class="calc-input w-full bg-indigo-50 border border-indigo-200 text-indigo-700 font-bold rounded-xl px-3 py-2 outline-none focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-700 mb-1">Zelle ($)</label>
                            <input type="number" step="0.01" id="zelle_usd" name="zelle_usd" value="0" class="calc-input w-full bg-indigo-50 border border-indigo-200 text-indigo-700 font-bold rounded-xl px-3 py-2 outline-none focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-700 mb-1">Cashea ($)</label>
                            <input type="number" step="0.01" id="cashea_usd" name="cashea_usd" value="0" class="calc-input w-full bg-indigo-50 border border-indigo-200 text-indigo-700 font-bold rounded-xl px-3 py-2 outline-none focus:border-indigo-500">
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 p-4 rounded-xl shadow-inner bg-slate-100" id="resumen_box">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-slate-500 font-bold">Total Teórico:</span>
                        <span class="text-slate-800 font-black" id="lbl_teorico">$ 0.00</span>
                    </div>
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-slate-500 font-bold">Total Físico:</span>
                        <span class="text-slate-800 font-black" id="lbl_fisico">$ 0.00</span>
                    </div>
                    <div class="h-px w-full bg-slate-300 mb-2"></div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-bold text-slate-700">Diferencia:</span>
                        <span class="text-xl font-black text-slate-400" id="lbl_diferencia">$ 0.00</span>
                    </div>
                    <input type="hidden" name="diferencia_usd" id="input_diferencia" value="0">
                    <input type="hidden" name="estado" id="input_estado" value="Cuadrada">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Observaciones</label>
                    <textarea name="observaciones" rows="1" class="w-full bg-slate-50 border border-slate-200 text-slate-700 font-medium rounded-xl px-4 py-2 outline-none focus:border-blue-500" placeholder="Opcional..."></textarea>
                </div>
            </div>
            
        </div>
        <div class="flex justify-end gap-3 px-8 pb-8 bg-white border-t border-slate-100 pt-4 mt-2">
          <button type="button" class="px-6 py-3 rounded-xl font-bold text-slate-500 bg-slate-100 hover:bg-slate-200 transition-colors" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" name="btn_guardar_cuadre" value="1" class="px-6 py-3 rounded-xl font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-lg shadow-blue-500/30 transition-colors">Guardar Cuadre</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.calc-input');
    const tasa_dia = parseFloat(document.getElementById('tasa_dia').value) || 36.50;
    
    function calcularDiferencia() {
        const fondo_usd = parseFloat(document.getElementById('fondo_usd').value) || 0;
        const fondo_bs = parseFloat(document.getElementById('fondo_bs').value) || 0;
        const ventas_bs = parseFloat(document.getElementById('ventas_bs').value) || 0;
        const ventas_usd = parseFloat(document.getElementById('ventas_usd').value) || 0;
        const gastos_usd = parseFloat(document.getElementById('gastos_usd').value) || 0;
        
        const fisico_bs = parseFloat(document.getElementById('fisico_bs').value) || 0;
        
        // Calculo de billetes
        const bill_100 = parseInt(document.getElementById('bill_100').value) || 0;
        const bill_50 = parseInt(document.getElementById('bill_50').value) || 0;
        const bill_20 = parseInt(document.getElementById('bill_20').value) || 0;
        const bill_10 = parseInt(document.getElementById('bill_10').value) || 0;
        const bill_5 = parseInt(document.getElementById('bill_5').value) || 0;
        const bill_1 = parseInt(document.getElementById('bill_1').value) || 0;
        const fisico_usd = (bill_100 * 100) + (bill_50 * 50) + (bill_20 * 20) + (bill_10 * 10) + (bill_5 * 5) + (bill_1 * 1);
        document.getElementById('fisico_usd').value = fisico_usd.toFixed(2);
        
        // Medios Electronicos
        const punto_venta_bs = parseFloat(document.getElementById('punto_venta_bs').value) || 0;
        const pago_movil_bs = parseFloat(document.getElementById('pago_movil_bs').value) || 0;
        const zelle_usd = parseFloat(document.getElementById('zelle_usd').value) || 0;
        const cashea_usd = parseFloat(document.getElementById('cashea_usd').value) || 0;
        
        // Calcular en dólares
        const teorico_usd = fondo_usd + (fondo_bs / tasa_dia) + ventas_usd + (ventas_bs / tasa_dia) - gastos_usd;
        const total_fisico_usd = fisico_usd + (fisico_bs / tasa_dia) + zelle_usd + cashea_usd + ((punto_venta_bs + pago_movil_bs) / tasa_dia);
        const diferencia = total_fisico_usd - teorico_usd;
        
        // Actualizar UI
        document.getElementById('lbl_teorico').innerText = '$ ' + teorico_usd.toFixed(2);
        document.getElementById('lbl_fisico').innerText = '$ ' + total_fisico_usd.toFixed(2);
        
        const lbl_diff = document.getElementById('lbl_diferencia');
        const resumen_box = document.getElementById('resumen_box');
        
        document.getElementById('input_diferencia').value = diferencia.toFixed(2);
        const diferencia_bs = Math.abs(diferencia * tasa_dia).toFixed(2);
        
        // Tolerancia de centavos para "Cuadrada"
        if (Math.abs(diferencia) < 0.10) {
            lbl_diff.innerHTML = '$ 0.00 <span class="text-sm opacity-75 block">(Bs. 0.00)</span>';
            lbl_diff.className = 'text-xl font-black text-emerald-600 text-right';
            resumen_box.className = 'mt-6 p-4 rounded-xl shadow-inner bg-emerald-50 border border-emerald-200';
            document.getElementById('input_estado').value = 'Cuadrada';
        } else if (diferencia < 0) {
            lbl_diff.innerHTML = '- $ ' + Math.abs(diferencia).toFixed(2) + ' <span class="text-sm opacity-75 block">(- Bs. ' + diferencia_bs + ')</span>';
            lbl_diff.className = 'text-xl font-black text-rose-600 text-right';
            resumen_box.className = 'mt-6 p-4 rounded-xl shadow-inner bg-rose-50 border border-rose-200';
            document.getElementById('input_estado').value = 'Faltante';
        } else {
            lbl_diff.innerHTML = '+ $ ' + diferencia.toFixed(2) + ' <span class="text-sm opacity-75 block">(+ Bs. ' + diferencia_bs + ')</span>';
            lbl_diff.className = 'text-xl font-black text-amber-600 text-right';
            resumen_box.className = 'mt-6 p-4 rounded-xl shadow-inner bg-amber-50 border border-amber-200';
            document.getElementById('input_estado').value = 'Sobrante';
        }
    }
    
    inputs.forEach(input => {
        input.addEventListener('input', calcularDiferencia);
        // Seleccionar todo el texto al hacer focus para edición rápida
        input.addEventListener('focus', function() { this.select(); });
    });
    
    window.verObservacion = function(texto) {
        if(typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Observaciones',
                text: texto,
                icon: 'info',
                confirmButtonColor: '#2563eb',
                confirmButtonText: 'Cerrar'
            });
        } else {
            alert("OBSERVACIONES:\n\n" + texto);
        }
    };
});
</script>

<?php require('./layout/footer.php'); ?>
