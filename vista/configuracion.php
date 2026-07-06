<?php
session_start();
if (empty($_SESSION['email'])) {
    header("Location: /login_register12/index.php");
    exit();
}
if ($_SESSION['role'] !== 'admin') {
    header("Location: inicio.php");
    exit();
}
include "../modelo/conexion.php";

// Leer datos de Institución
$consulta_inst = $conexion->query("SELECT * FROM institucion LIMIT 1");
$institucion = $consulta_inst && $consulta_inst->num_rows > 0 ? $consulta_inst->fetch_object() : (object)['nombre'=>'', 'ruc'=>'', 'telefono'=>'', 'ubicacion'=>''];

// Leer datos Financieros
$tasa_actual = 36.50;
$sueldo_base = 210.00;
$cesta_ticket = 30.00;
$consulta_tasa = $conexion->query("SELECT * FROM tasa_cambio LIMIT 1");
if ($consulta_tasa && $consulta_tasa->num_rows > 0) {
    $tasa_obj = $consulta_tasa->fetch_object();
    $tasa_actual = floatval($tasa_obj->tasa);
    $sueldo_base = floatval($tasa_obj->sueldo_base_usd);
    $cesta_ticket = floatval($tasa_obj->cesta_ticket_usd);
}

// Leer config biométrico
$ip_bio = "192.168.1.201";
$puerto_bio = "4370";
$bio_file = '../modelo/config_biometrico.json';
if (file_exists($bio_file)) {
    $bio_data = json_decode(file_get_contents($bio_file), true);
    if ($bio_data) {
        $ip_bio = $bio_data['ip'] ?? $ip_bio;
        $puerto_bio = $bio_data['puerto'] ?? $puerto_bio;
    }
}
?>

<link href="/login_register12/public/vendor/dist/fonts/montserrat/index.css" rel="stylesheet">
<?php require('./layout/sidebar.php'); ?>

<div class="px-4 py-8 md:px-8 bg-[#f8fafc] min-h-[calc(100vh-4rem)] transition-colors duration-300">
    <div class="max-w-6xl mx-auto">
        
        <!-- Header Section -->
        <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex flex-col justify-start items-start gap-4">
                <h3 class="text-2xl md:text-3xl font-bold text-slate-800 flex items-center gap-3 transition-colors duration-300">
                    <div class="p-2.5 bg-slate-800 text-white rounded-xl shadow-lg shadow-slate-800/30">
                        <i class="fa-solid fa-gears"></i>
                    </div>
                    Configuraciones Generales
                </h3>
                <p class="text-slate-500 font-medium transition-colors duration-300">Administra los parámetros globales del sistema mediante módulos independientes.</p>
            </div>
        </div>

        <!-- Alertas globales -->
        <?php if (!empty($_SESSION['mensaje'])): ?>
            <?php 
                $tipo = strpos(strtolower($_SESSION['mensaje']), 'error') !== false ? 'error' : 'success'; 
                $icon = $tipo == 'error' ? 'fa-triangle-exclamation text-rose-600' : 'fa-check text-emerald-600';
                $bg_icon = $tipo == 'error' ? 'bg-rose-100' : 'bg-emerald-100';
                $bg_alert = $tipo == 'error' ? 'bg-rose-50 border-rose-100 text-rose-800' : 'bg-emerald-50 border-emerald-100 text-emerald-800';
            ?>
            <div class="mb-6 p-4 rounded-2xl border flex items-center gap-3 shadow-sm <?= $bg_alert ?>">
                <div class="w-8 h-8 rounded-full flex items-center justify-center <?= $bg_icon ?>">
                    <i class="fa-solid <?= $icon ?>"></i>
                </div>
                <div><strong class="font-semibold"><?= $tipo == 'error' ? 'Atención:' : '¡Éxito!' ?></strong> <?= $_SESSION['mensaje'] ?></div>
            </div>
            <?php unset($_SESSION['mensaje']); ?>
        <?php endif; ?>

        <!-- Módulos de Configuración (Tarjetas Clicables) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <!-- Opción: Perfil de la Empresa -->
            <div data-bs-toggle="modal" data-bs-target="#modalInstitucion" class="bg-white rounded-[2rem] p-6 shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100 hover:border-blue-200 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 cursor-pointer group relative overflow-hidden">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-blue-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500"></div>
                <div class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center text-2xl mb-6 relative z-10 shadow-inner">
                    <i class="fa-solid fa-building"></i>
                </div>
                <h4 class="text-xl font-bold text-slate-800 mb-2 relative z-10 transition-colors">Perfil de la Empresa</h4>
                <p class="text-slate-500 text-sm relative z-10 transition-colors">Modifica el logo, RIF, nombre y dirección física para los reportes y recibos.</p>
                <div class="mt-6 flex items-center text-blue-600 font-bold text-sm opacity-0 group-hover:opacity-100 transition-opacity -translate-x-2 group-hover:translate-x-0">
                    Configurar <i class="fa-solid fa-arrow-right ml-2"></i>
                </div>
            </div>

            <!-- Opción: Parámetros Nómina -->
            <div data-bs-toggle="modal" data-bs-target="#modalNomina" class="bg-white rounded-[2rem] p-6 shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100 hover:border-emerald-200 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 cursor-pointer group relative overflow-hidden">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-emerald-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500"></div>
                <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-2xl mb-6 relative z-10 shadow-inner">
                    <i class="fa-solid fa-money-bill-transfer"></i>
                </div>
                <h4 class="text-xl font-bold text-slate-800 mb-2 relative z-10 transition-colors">Parámetros Nómina</h4>
                <p class="text-slate-500 text-sm relative z-10 transition-colors">Actualiza la Tasa BCV, el Sueldo Base Global y el monto de Cesta Ticket.</p>
                <div class="mt-6 flex items-center text-emerald-600 font-bold text-sm opacity-0 group-hover:opacity-100 transition-opacity -translate-x-2 group-hover:translate-x-0">
                    Configurar <i class="fa-solid fa-arrow-right ml-2"></i>
                </div>
            </div>

            <!-- Opción: Hardware Biométrico -->
            <div data-bs-toggle="modal" data-bs-target="#modalBiometrico" class="bg-white rounded-[2rem] p-6 shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100 hover:border-indigo-200 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 cursor-pointer group relative overflow-hidden">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-indigo-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500"></div>
                <div class="w-14 h-14 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-2xl mb-6 relative z-10 shadow-inner">
                    <i class="fa-solid fa-fingerprint"></i>
                </div>
                <h4 class="text-xl font-bold text-slate-800 mb-2 relative z-10 transition-colors">Red Biométrico</h4>
                <p class="text-slate-500 text-sm relative z-10 transition-colors">Ajusta la IP y el Puerto de conexión del dispositivo captahuellas ZKTeco.</p>
                <div class="mt-6 flex items-center text-indigo-600 font-bold text-sm opacity-0 group-hover:opacity-100 transition-opacity -translate-x-2 group-hover:translate-x-0">
                    Configurar <i class="fa-solid fa-arrow-right ml-2"></i>
                </div>
            </div>



            <!-- Espacio reservado para futuras configuraciones -->
            <div class="bg-transparent rounded-[2rem] p-6 border-2 border-dashed border-slate-200 flex flex-col items-center justify-center text-center opacity-50">
                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center text-xl mb-3">
                    <i class="fa-solid fa-plus"></i>
                </div>
                <p class="text-slate-400 font-bold text-sm">Más módulos<br>próximamente</p>
            </div>

        </div>
    </div>
</div>

<!-- ================= MODALES DE CONFIGURACIÓN ================= -->

<!-- Modal 1: Perfil de la Institución -->
<div class="modal fade" id="modalInstitucion" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-2xl rounded-3xl overflow-hidden">
      <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-6 flex items-center justify-between">
        <h5 class="text-white font-bold text-lg flex items-center gap-3 m-0">
          <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center"><i class="fa-solid fa-building"></i></div>
          Perfil de la Empresa
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="../controlador/controlador_config_institucion.php" method="POST" enctype="multipart/form-data" class="m-0">
        <div class="p-8 bg-white flex flex-col md:flex-row gap-8">
            <div class="flex flex-col items-center gap-4 w-full md:w-1/3">
                <div class="w-32 h-32 rounded-full border-4 border-slate-100 bg-slate-50 overflow-hidden flex items-center justify-center relative group">
                    <img src="../img/logo.png?v=<?= time() ?>" alt="Logo Empresa" class="w-full h-full object-contain">
                    <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center pointer-events-none">
                        <i class="fa-solid fa-camera text-white text-2xl"></i>
                    </div>
                </div>
                <label class="cursor-pointer bg-blue-50 text-blue-600 hover:bg-blue-100 font-bold py-2 px-4 rounded-xl text-sm transition-colors text-center w-full">
                    Cambiar Logo
                    <input type="file" name="logo" accept=".png,.jpg,.jpeg" class="hidden">
                </label>
                <p class="text-xs text-slate-400 text-center">Formatos: PNG, JPG.<br>Fondo transparente.</p>
            </div>
            
            <div class="w-full md:w-2/3 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Nombre de la Empresa</label>
                    <input type="text" name="nombre" value="<?= htmlspecialchars($institucion->nombre) ?>" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl px-4 py-3 focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">RIF / RUC</label>
                    <input type="text" name="ruc" value="<?= htmlspecialchars($institucion->ruc) ?>" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl px-4 py-3 focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Teléfono</label>
                    <input type="text" name="telefono" value="<?= htmlspecialchars($institucion->telefono) ?>" class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl px-4 py-3 focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 outline-none">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Dirección Física</label>
                    <textarea name="ubicacion" rows="2" class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl px-4 py-3 focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 outline-none"><?= htmlspecialchars($institucion->ubicacion) ?></textarea>
                </div>
            </div>
        </div>
        <div class="flex justify-end gap-3 px-8 pb-8 bg-white">
          <button type="button" class="px-6 py-3 rounded-xl font-bold text-slate-500 bg-slate-100 hover:bg-slate-200" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" name="btn_guardar_institucion" value="1" class="px-6 py-3 rounded-xl font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-lg shadow-blue-500/30">Guardar Perfil</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal 2: Parámetros Nómina -->
<div class="modal fade" id="modalNomina" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-2xl rounded-3xl overflow-hidden">
      <div class="bg-gradient-to-r from-emerald-500 to-teal-500 p-6 flex items-center justify-between">
        <h5 class="text-white font-bold text-lg flex items-center gap-3 m-0">
          <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center"><i class="fa-solid fa-money-bill-transfer"></i></div>
          Parámetros de Nómina
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="../controlador/controlador_config_financiera.php" method="POST" class="m-0">
        <div class="p-8 bg-white space-y-6">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Tasa de Cambio BCV Actual</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">Bs.</span>
                    <input type="number" step="0.01" name="tasa" value="<?= $tasa_actual ?>" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-black text-xl rounded-xl pl-12 pr-4 py-4 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none text-right">
                </div>
            </div>
            <hr class="border-slate-100">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Sueldo Base Mensual</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">$</span>
                    <input type="number" step="0.01" name="sueldo_base_usd" value="<?= $sueldo_base ?>" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-bold text-lg rounded-xl pl-10 pr-4 py-3 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none">
                </div>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Cesta Ticket Mensual</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">$</span>
                    <input type="number" step="0.01" name="cesta_ticket_usd" value="<?= $cesta_ticket ?>" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-bold text-lg rounded-xl pl-10 pr-4 py-3 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none">
                </div>
            </div>
        </div>
        <div class="flex justify-end gap-3 px-8 pb-8 bg-white">
          <button type="button" class="px-6 py-3 rounded-xl font-bold text-slate-500 bg-slate-100 hover:bg-slate-200" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" name="btn_guardar_financiera" value="1" class="px-6 py-3 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-lg shadow-emerald-500/30">Guardar Nómina</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal 3: Hardware Biométrico -->
<div class="modal fade" id="modalBiometrico" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-2xl rounded-3xl overflow-hidden">
      <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-6 flex items-center justify-between">
        <h5 class="text-white font-bold text-lg flex items-center gap-3 m-0">
          <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center"><i class="fa-solid fa-fingerprint"></i></div>
          Red Biométrico (ZKTeco)
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="../controlador/controlador_config_biometrico.php" method="POST" class="m-0">
        <div class="p-8 bg-white space-y-5">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Dirección IP</label>
                <input type="text" name="ip_biometrico" value="<?= htmlspecialchars($ip_bio) ?>" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-bold rounded-xl px-4 py-3 focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none" placeholder="Ej: 192.168.1.201">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Puerto UDP</label>
                <input type="number" name="puerto_biometrico" value="<?= htmlspecialchars($puerto_bio) ?>" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-bold rounded-xl px-4 py-3 focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none" placeholder="Ej: 4370">
            </div>
            <div class="mt-4 p-4 bg-indigo-50 border border-indigo-100 rounded-xl flex gap-3 text-indigo-800 text-sm">
                <i class="fa-solid fa-circle-info mt-0.5 shrink-0"></i>
                <div>Asegúrese de que el dispositivo biométrico y este servidor se encuentren en la misma red local.</div>
            </div>

            <!-- Sincronizar Reloj -->
            <div class="mt-2 p-4 bg-amber-50 border border-amber-200 rounded-xl">
                <p class="text-sm font-bold text-amber-800 mb-1"><i class="fa-solid fa-clock mr-2"></i>Sincronizar Fecha y Hora</p>
                <p class="text-xs text-amber-700 mb-3">Si el reloj biométrico muestra una fecha/hora incorrecta, haz clic en el botón para sincronizarlo con la hora de este servidor.</p>
                <div id="syncResult" class="hidden mb-3 p-3 rounded-lg text-sm font-semibold"></div>
                <button type="button" id="btnSyncTime" onclick="sincronizarReloj()" class="w-full flex items-center justify-center gap-2 bg-amber-500 hover:bg-amber-600 text-white font-bold py-2.5 px-4 rounded-xl transition-all shadow-md shadow-amber-400/30">
                    <i class="fa-solid fa-rotate" id="syncIcon"></i>
                    Sincronizar Reloj del Dispositivo con Servidor
                </button>
            </div>
        </div>
        <div class="flex justify-end gap-3 px-8 pb-8 bg-white">
          <button type="button" class="px-6 py-3 rounded-xl font-bold text-slate-500 bg-slate-100 hover:bg-slate-200" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" name="btn_guardar_biometrico" value="1" class="px-6 py-3 rounded-xl font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-lg shadow-indigo-500/30">Actualizar Conexión</button>
        </div>
      </form>
    </div>
  </div>
</div>



<script>
function sincronizarReloj() {
    const btn = document.getElementById('btnSyncTime');
    const icon = document.getElementById('syncIcon');
    const result = document.getElementById('syncResult');

    btn.disabled = true;
    icon.classList.add('fa-spin');
    result.className = 'mb-3 p-3 rounded-lg text-sm font-semibold bg-slate-100 text-slate-600';
    result.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i>Conectando con el dispositivo...';
    result.classList.remove('hidden');

    fetch('/login_register12/sincronizar_reloj.php')
        .then(r => r.json())
        .then(data => {
            btn.disabled = false;
            icon.classList.remove('fa-spin');
            if (data.status === 'success') {
                result.className = 'mb-3 p-3 rounded-lg text-sm font-semibold bg-emerald-50 border border-emerald-200 text-emerald-800';
                result.innerHTML = `<i class="fa-solid fa-check-circle mr-2"></i>${data.message}<br>
                    <span class="font-normal text-xs mt-1 block">
                        <strong>Hora Servidor:</strong> ${data.hora_servidor}<br>
                        <strong>Reloj Antes:</strong> ${data.hora_bio_antes}<br>
                        <strong>Reloj Después:</strong> ${data.hora_bio_despues}
                    </span>`;
            } else {
                result.className = 'mb-3 p-3 rounded-lg text-sm font-semibold bg-rose-50 border border-rose-200 text-rose-800';
                result.innerHTML = `<i class="fa-solid fa-triangle-exclamation mr-2"></i>${data.message}<br><span class="font-normal text-xs">Verifica que el dispositivo esté encendido y en la misma red.</span>`;
            }
        })
        .catch(() => {
            btn.disabled = false;
            icon.classList.remove('fa-spin');
            result.className = 'mb-3 p-3 rounded-lg text-sm font-semibold bg-rose-50 border border-rose-200 text-rose-800';
            result.innerHTML = '<i class="fa-solid fa-triangle-exclamation mr-2"></i>Error de red. No se pudo contactar el servidor.';
        });
}
</script>

<?php require('./layout/footer.php'); ?>
