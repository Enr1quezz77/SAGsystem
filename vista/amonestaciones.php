<?php
include_once '../modelo/conexion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['email'])) {
    header("Location: /login_register12/index.php");
    exit();
}
?>
<?php 
include '../controlador/controlador_registrar_amonestacion.php';
include '../controlador/controlador_modificar_amonestacion.php';
include '../controlador/controlador_eliminar_amonestacion.php';
include '../controlador/controlador_cambiar_estado_empleado.php';

// Contar Amonestaciones del Mes
$sqlMes = $conexion->query("SELECT COUNT(*) as total FROM amonestacion WHERE MONTH(fecha_registro) = MONTH(CURRENT_DATE()) AND YEAR(fecha_registro) = YEAR(CURRENT_DATE())");
$amonestacionesMes = $sqlMes ? $sqlMes->fetch_assoc()['total'] : 0;

// Empleados en Riesgo
$sqlRiesgo = $conexion->query("
    SELECT COUNT(*) as total FROM (
        SELECT id_empleado, COUNT(*) as cantidad, SUM(IF(gravedad='Grave',1,0)) as graves 
        FROM amonestacion 
        GROUP BY id_empleado 
        HAVING cantidad >= 3 OR graves >= 1
    ) as subquery
");
$empleadosRiesgo = $sqlRiesgo ? $sqlRiesgo->fetch_assoc()['total'] : 0;

// Empleados Suspendidos
$sqlSuspendidos = $conexion->query("SELECT COUNT(*) as total FROM empleado WHERE estado='Suspendido'");
$empleadosSuspendidos = $sqlSuspendidos ? $sqlSuspendidos->fetch_assoc()['total'] : 0;
?>

<?php require('./layout/sidebar.php'); ?>



<div class="px-4 py-8 md:px-8 bg-[#f8fafc] transition-colors duration-300 min-h-[calc(100vh-4rem)] w-full">
  <!-- Header Section -->
  <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div>
      <h3 class="text-2xl md:text-3xl font-bold text-slate-800 flex items-center gap-3 transition-colors">
        <div class="p-2.5 bg-rose-500 text-white rounded-xl shadow-lg shadow-rose-500/30">
          <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        Amonestaciones
      </h3>
      <p class="text-slate-500 mt-2 ml-[3.25rem] font-medium transition-colors">Gestión de llamados de atención y reportes de disciplina</p>
    </div>
    <div class="flex gap-3">
        <button data-bs-toggle="modal" data-bs-target="#modalRegistrar" class="bg-white text-rose-600 hover:bg-rose-50 font-bold py-3 px-6 rounded-full shadow-[0_4px_15px_rgb(0,0,0,0.05)] transition-all duration-300 transform hover:-translate-y-1 flex items-center gap-2 border border-rose-100">
            <i class="fa-solid fa-plus"></i> Registrar Amonestación
        </button>
    </div>
  </div>

  <!-- Cards Section -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-3xl p-6 shadow-[0_2px_20px_rgb(0,0,0,0.04)] border border-slate-100 flex items-center justify-between group relative overflow-hidden transition-colors duration-300">
      <div class="absolute -right-10 -top-10 w-32 h-32 bg-rose-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500 ease-out"></div>
      <div class="relative z-10">
        <p class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-2">Amonestaciones del Mes</p>
        <h2 class="text-5xl font-black text-slate-800 transition-colors"><?= $amonestacionesMes ?></h2>
      </div>
      <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-rose-400 to-rose-500 flex items-center justify-center text-white text-2xl shadow-lg shadow-rose-500/30 group-hover:-translate-y-1 transition-transform duration-300 relative z-10">
        <i class="fa-solid fa-file-signature"></i>
      </div>
    </div>

    <div class="bg-white rounded-3xl p-6 shadow-[0_2px_20px_rgb(0,0,0,0.04)] border border-slate-100 flex items-center justify-between group relative overflow-hidden transition-colors duration-300">
      <div class="absolute -right-10 -top-10 w-32 h-32 bg-amber-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500 ease-out"></div>
      <div class="relative z-10">
        <p class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-2">Empleados en Riesgo</p>
        <h2 class="text-5xl font-black text-slate-800 transition-colors"><?= $empleadosRiesgo ?></h2>
      </div>
      <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-white text-2xl shadow-lg shadow-orange-500/30 group-hover:-translate-y-1 transition-transform duration-300 relative z-10">
        <i class="fa-solid fa-user-shield"></i>
      </div>
    </div>

    <div class="bg-white rounded-3xl p-6 shadow-[0_2px_20px_rgb(0,0,0,0.04)] border border-slate-100 flex items-center justify-between group relative overflow-hidden transition-colors duration-300">
      <div class="absolute -right-10 -top-10 w-32 h-32 bg-red-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500 ease-out"></div>
      <div class="relative z-10">
        <p class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-2">Suspendidos</p>
        <h2 class="text-5xl font-black text-slate-800 transition-colors"><?= $empleadosSuspendidos ?></h2>
      </div>
      <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center text-white text-2xl shadow-lg shadow-red-500/30 group-hover:-translate-y-1 transition-transform duration-300 relative z-10">
        <i class="fa-solid fa-user-xmark"></i>
      </div>
    </div>
  </div>

  <!-- Tables Section -->
  <div class="bg-white rounded-3xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100 overflow-hidden flex flex-col mb-10 w-full col-span-12 transition-colors duration-300">
    <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-white transition-colors">
      <h5 class="text-lg font-bold text-slate-800 m-0 flex items-center gap-2 transition-colors">
        <div class="p-1.5 bg-rose-50 text-rose-600 rounded-lg"><i class="fa-solid fa-list-check"></i></div>
        Listado de Amonestaciones
      </h5>
    </div>
    <div class="p-0 overflow-x-auto w-full">
      <table class="w-full text-sm text-left text-slate-500 whitespace-nowrap">
        <thead class="text-xs text-slate-400 uppercase bg-slate-50/50 transition-colors">
          <tr>
            <th class="px-6 py-4 font-semibold tracking-wider">Empleado</th>
            <th class="px-6 py-4 font-semibold tracking-wider">Motivo</th>
            <th class="px-6 py-4 font-semibold tracking-wider">Gravedad</th>
            <th class="px-6 py-4 font-semibold tracking-wider">Fecha</th>
            <th class="px-6 py-4 font-semibold tracking-wider text-right">Acciones</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <?php
          $sqlQuery = "SELECT a.id_amonestacion, a.motivo, a.gravedad, a.observacion, a.fecha_registro, e.id_empleado, e.nombre, e.apellido, e.cargo, e.estado 
                       FROM amonestacion a 
                       INNER JOIN empleado e ON a.id_empleado = e.id_empleado 
                       ORDER BY a.fecha_registro DESC";
          $sqlQueryExe = $conexion->query($sqlQuery);
          while ($datos = $sqlQueryExe->fetch_object()) { 
              $inicial = strtoupper(substr($datos->nombre, 0, 1));
              
              if ($datos->gravedad == 'Grave') {
                  $badgeCls = 'bg-rose-50 text-rose-700 border-rose-100';
                  $icon = 'fa-triangle-exclamation';
              } else if ($datos->gravedad == 'Moderada') {
                  $badgeCls = 'bg-orange-50 text-orange-700 border-orange-100';
                  $icon = 'fa-circle-exclamation';
              } else {
                  $badgeCls = 'bg-amber-50 text-amber-700 border-amber-100';
                  $icon = 'fa-circle-info';
              }
          ?>
            <tr class="hover:bg-slate-50/80 transition-colors group">
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center font-bold text-lg group-hover:bg-rose-100 group-hover:text-rose-600 transition-colors shrink-0 relative">
                        <?= $inicial ?>
                        <?php if($datos->estado === 'Suspendido') { ?>
                           <span class="absolute -top-1 -right-1 w-3.5 h-3.5 bg-rose-500 border-2 border-slate-50 rounded-full" title="Suspendido"></span>
                        <?php } ?>
                    </div>
                  <div class="flex flex-col">
                    <span class="font-bold text-slate-800 transition-colors"><?= $datos->nombre . " " . $datos->apellido ?></span>
                    <span class="text-xs text-slate-400 font-medium transition-colors">Cargo ID: <?= $datos->cargo ?></span>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 font-medium text-slate-600 whitespace-normal min-w-[200px] transition-colors">
                 <?= htmlspecialchars($datos->motivo) ?>
                 <?php if($datos->observacion) { ?>
                    <p class="text-xs text-slate-400 mt-1 italic transition-colors"><?= htmlspecialchars($datos->observacion) ?></p>
                 <?php } ?>
              </td>
              <td class="px-6 py-4">
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold border <?= $badgeCls ?>">
                  <i class="fa-solid <?= $icon ?> mr-1"></i> <?= $datos->gravedad ?>
                </span>
              </td>
              <td class="px-6 py-4 font-medium text-slate-500 transition-colors"><?= date('d M, Y', strtotime($datos->fecha_registro)) ?></td>
              <td class="px-6 py-4 text-right">
                <?php if($datos->estado === 'Suspendido') { ?>
                    <a href="amonestaciones.php?id_empleado_estado=<?= $datos->id_empleado ?>&nuevo_estado=Activo" title="Levantar Suspensión" class="text-slate-400 hover:text-emerald-600 transition-colors p-2"><i class="fa-solid fa-user-check"></i></a>
                <?php } else { ?>
                    <a href="amonestaciones.php?id_empleado_estado=<?= $datos->id_empleado ?>&nuevo_estado=Suspendido" title="Suspender Empleado" onclick="return confirmarSuspension(event, this.href);" class="text-slate-400 hover:text-amber-600 transition-colors p-2"><i class="fa-solid fa-user-lock"></i></a>
                <?php } ?>
                <button data-bs-toggle="modal" data-bs-target="#modificarA<?= $datos->id_amonestacion ?>" class="text-slate-400 hover:text-blue-600 transition-colors p-2"><i class="fa-solid fa-pen-to-square"></i></button>
                <a href="amonestaciones.php?id_amonestacion=<?= $datos->id_amonestacion ?>" onclick="return confirmarEliminacion(event, this.href);" class="text-slate-400 hover:text-rose-600 transition-colors p-2"><i class="fa-solid fa-trash"></i></a>
              </td>
            </tr>

            <!-- Modal Modificar -->
            <div class="modal fade" id="modificarA<?= $datos->id_amonestacion ?>" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-2xl rounded-3xl overflow-hidden bg-white transition-colors">
                  <div class="bg-gradient-to-r from-rose-500 to-rose-600 p-6 flex flex-row items-center justify-between">
                    <h5 class="flex items-center text-white font-bold text-lg m-0"><i class="fa-solid fa-pen-to-square mr-2"></i> Modificar Amonestación</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <form action="" method="POST" class="m-0">
                    <div class="p-6 bg-white space-y-4 text-left transition-colors">
                      <input type="hidden" name="id_amonestacion" value="<?= $datos->id_amonestacion ?>">
                      
                      <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1 transition-colors">Empleado</label>
                        <select name="empleado" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 text-slate-800 rounded-xl outline-none focus:border-rose-500 transition-colors" required>
                          <?php
                          $sqlEmp = $conexion->query("SELECT id_empleado, nombre, apellido FROM empleado");
                          while ($emp = $sqlEmp->fetch_object()) {
                            $selected = ($emp->id_empleado == $datos->id_empleado) ? "selected" : "";
                            echo "<option value='".$emp->id_empleado."' class='bg-white' $selected>".$emp->nombre." ".$emp->apellido."</option>";
                          }
                          ?>
                        </select>
                      </div>

                      <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1 transition-colors">Motivo</label>
                        <input type="text" name="motivo" value="<?= htmlspecialchars($datos->motivo) ?>" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 text-slate-800 rounded-xl outline-none focus:border-rose-500 transition-colors" required>
                      </div>

                      <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1 transition-colors">Gravedad</label>
                        <select name="gravedad" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 text-slate-800 rounded-xl outline-none focus:border-rose-500 transition-colors" required>
                          <option value="Leve" class="bg-white" <?= $datos->gravedad == 'Leve' ? 'selected' : '' ?>>Leve</option>
                          <option value="Moderada" class="bg-white" <?= $datos->gravedad == 'Moderada' ? 'selected' : '' ?>>Moderada</option>
                          <option value="Grave" class="bg-white" <?= $datos->gravedad == 'Grave' ? 'selected' : '' ?>>Grave</option>
                        </select>
                      </div>
                      
                      <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1 transition-colors">Observaciones</label>
                        <textarea name="observacion" rows="3" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 text-slate-800 rounded-xl outline-none focus:border-rose-500 transition-colors"><?= htmlspecialchars($datos->observacion) ?></textarea>
                      </div>

                    </div>
                    <div class="p-5 bg-slate-50 border-t border-slate-100 flex justify-end gap-3 rounded-b-3xl transition-colors">
                      <button type="button" class="px-5 py-2.5 rounded-xl text-slate-600 bg-white border border-slate-200 hover:bg-slate-100 font-semibold transition-colors" data-bs-dismiss="modal">Cancelar</button>
                      <button type="submit" name="btnmodificar" value="ok" class="px-5 py-2.5 rounded-xl text-white bg-rose-600 hover:bg-rose-700 font-semibold shadow-md active:scale-95 transition-transform">Guardar Cambios</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Registrar -->
<div class="modal fade" id="modalRegistrar" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-2xl rounded-3xl overflow-hidden bg-white transition-colors">
      <div class="bg-gradient-to-r from-rose-500 to-rose-600 p-6 flex flex-row items-center justify-between">
        <h5 class="flex items-center text-white font-bold text-lg m-0"><i class="fa-solid fa-plus mr-2"></i> Registrar Amonestación</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="" method="POST" class="m-0">
        <div class="p-6 bg-white space-y-4 transition-colors">
          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1 transition-colors">Empleado</label>
            <select name="empleado" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 text-slate-800 rounded-xl outline-none focus:border-rose-500 transition-colors" required>
              <option disabled selected value="" class="bg-white">Seleccionar Empleado...</option>
              <?php
              $sqlEmp = $conexion->query("SELECT id_empleado, nombre, apellido, estado FROM empleado");
              while ($emp = $sqlEmp->fetch_object()) {
                $indicator = $emp->estado === 'Suspendido' ? ' (Suspendido)' : '';
                echo "<option value='".$emp->id_empleado."' class='bg-white'>".$emp->nombre." ".$emp->apellido.$indicator."</option>";
              }
              ?>
            </select>
          </div>

          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1 transition-colors">Motivo</label>
            <input type="text" name="motivo" placeholder="Ej: Llegada tarde injustificada..." class="w-full px-4 py-3 bg-slate-50 border border-slate-200 text-slate-800 rounded-xl outline-none focus:border-rose-500 transition-colors" required>
          </div>

          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1 transition-colors">Gravedad</label>
            <select name="gravedad" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 text-slate-800 rounded-xl outline-none focus:border-rose-500 transition-colors" required>
              <option value="Leve" class="bg-white" selected>Leve</option>
              <option value="Moderada" class="bg-white">Moderada</option>
              <option value="Grave" class="bg-white">Grave</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1 transition-colors">Observaciones</label>
            <textarea name="observacion" rows="3" placeholder="Información adicional sobre la incidencia..." class="w-full px-4 py-3 bg-slate-50 border border-slate-200 text-slate-800 rounded-xl outline-none focus:border-rose-500 transition-colors"></textarea>
          </div>

          <div class="mt-4 flex items-center gap-3 p-4 bg-rose-50 border border-rose-100 rounded-xl cursor-pointer transition-colors">
            <input type="checkbox" id="suspender_emp" name="suspender" value="1" class="w-5 h-5 text-rose-600 border-gray-300 rounded focus:ring-rose-500 cursor-pointer">
            <label for="suspender_emp" class="text-sm font-bold text-rose-800 cursor-pointer selection:bg-transparent transition-colors">
               Suspender a este empleado inmediatamente
               <br>
               <span class="text-xs font-normal text-rose-600">Al marcar esto el empleado quedará como "Suspendido".</span>
            </label>
          </div>

        </div>
        <div class="p-5 bg-slate-50 border-t border-slate-100 flex justify-end gap-3 rounded-b-3xl transition-colors">
          <button type="button" class="px-5 py-2.5 rounded-xl text-slate-600 bg-white border border-slate-200 hover:bg-slate-100 font-semibold transition-colors" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" name="btnregistrar" value="ok" class="px-5 py-2.5 rounded-xl text-white bg-rose-600 hover:bg-rose-700 font-semibold shadow-md active:scale-95 transition-transform flex items-center gap-2">
            <i class="fa-solid fa-triangle-exclamation"></i> Registrar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function confirmarEliminacion(e, url) {
    e.preventDefault();
    Swal.fire({
        title: '¿Eliminar Amonestación?',
        text: "¡Esta acción eliminará el registro del historial!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        borderRadius: "1rem"
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    })
    return false;
}

function confirmarSuspension(e, url) {
    e.preventDefault();
    Swal.fire({
        title: '¿Suspender Empleado?',
        text: "¡El empleado quedará marcado como Suspendido temporalmente!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f59e0b',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: 'Sí, suspender',
        cancelButtonText: 'Cancelar',
        borderRadius: "1rem"
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    })
    return false;
}
</script>

<?php require('./layout/footer.php'); ?>

