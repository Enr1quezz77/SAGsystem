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
include '../controlador/controlador_registrar_destacado.php';
include '../controlador/controlador_modificar_destacado.php';
include '../controlador/controlador_eliminar_destacado.php';

// Totales para tarjetas
$sqlTotales = $conexion->query("SELECT (SELECT COUNT(*) FROM destacado) as total, (SELECT COUNT(*) FROM destacado WHERE MONTH(fecha_registro) = MONTH(CURRENT_DATE()) AND YEAR(fecha_registro) = YEAR(CURRENT_DATE())) as mes");
if ($sqlTotales) {
    $totales = $sqlTotales->fetch_assoc();
    $totalMeritos = $totales['total'] ?? 0;
    $totalMes = $totales['mes'] ?? 0;
} else {
    $totalMeritos = 0; $totalMes = 0;
}
?>

<?php require('./layout/sidebar.php'); ?>



<div class="px-4 py-8 md:px-8 bg-[#f8fafc] min-h-[calc(100vh-4rem)] w-full">
  <!-- Header Section -->
  <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div>
      <h3 class="text-2xl md:text-3xl font-bold text-slate-800 flex items-center gap-3">
        <div class="p-2.5 bg-amber-500 text-white rounded-xl shadow-lg shadow-amber-500/30">
          <i class="fa-solid fa-star"></i>
        </div>
        Empleados Destacados
      </h3>
      <p class="text-slate-500 mt-2 ml-[3.25rem] font-medium">Reconocimiento al desempeño y excelencia</p>
    </div>
    <div class="flex gap-3">
        <button data-bs-toggle="modal" data-bs-target="#exampleModal" class="bg-white text-amber-600 hover:bg-amber-50 font-bold py-3 px-6 rounded-full shadow-[0_4px_15px_rgb(0,0,0,0.05)] transition-all duration-300 transform hover:-translate-y-1 flex items-center gap-2 border border-amber-100">
            <i class="fa-solid fa-plus"></i> Registrar Mérito
        </button>
    </div>
  </div>

  <!-- Cards Section -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-3xl p-6 shadow-[0_2px_20px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-all duration-300 border border-slate-100 flex items-center justify-between group relative overflow-hidden">
      <div class="absolute -right-10 -top-10 w-32 h-32 bg-amber-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500 ease-out"></div>
      <div class="relative z-10">
        <p class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-2">Destacados del Mes</p>
        <h2 class="text-5xl font-black text-slate-800"><?= $totalMes ?></h2>
      </div>
      <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-amber-400 to-amber-500 flex items-center justify-center text-white text-2xl shadow-lg shadow-amber-500/30 group-hover:-translate-y-1 transition-transform duration-300 relative z-10">
        <i class="fa-solid fa-medal"></i>
      </div>
    </div>

    <div class="bg-white rounded-3xl p-6 shadow-[0_2px_20px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-all duration-300 border border-slate-100 flex items-center justify-between group relative overflow-hidden">
      <div class="absolute -right-10 -top-10 w-32 h-32 bg-blue-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500 ease-out"></div>
      <div class="relative z-10">
        <p class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-2">Méritos Totales</p>
        <h2 class="text-5xl font-black text-slate-800"><?= $totalMeritos ?></h2>
      </div>
      <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-400 to-blue-500 flex items-center justify-center text-white text-2xl shadow-lg shadow-blue-500/30 group-hover:-translate-y-1 transition-transform duration-300 relative z-10">
        <i class="fa-solid fa-award"></i>
      </div>
    </div>
  </div>

  <!-- Tables Section -->
  <div class="bg-white rounded-3xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100 overflow-hidden flex flex-col mb-10 w-full col-span-12">
    <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-white">
      <h5 class="text-lg font-bold text-slate-800 m-0 flex items-center gap-2">
        <div class="p-1.5 bg-amber-50 text-amber-600 rounded-lg"><i class="fa-solid fa-list-check"></i></div>
        Listado de Destacados
      </h5>
    </div>
    <div class="p-0 overflow-x-auto w-full">
      <table class="w-full text-sm text-left text-slate-500 whitespace-nowrap">
        <thead class="text-xs text-slate-400 uppercase bg-slate-50/50">
          <tr>
            <th class="px-6 py-4 font-semibold tracking-wider">Empleado</th>
            <th class="px-6 py-4 font-semibold tracking-wider">Motivo</th>
            <th class="px-6 py-4 font-semibold tracking-wider">Puntos/Nivel</th>
            <th class="px-6 py-4 font-semibold tracking-wider">Fecha</th>
            <th class="px-6 py-4 font-semibold tracking-wider text-right">Acciones</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <?php
          $sqlQuery = "SELECT d.id_destacado, d.motivo, d.nivel, d.fecha_registro, e.id_empleado, e.nombre, e.apellido, e.cargo 
                       FROM destacado d 
                       INNER JOIN empleado e ON d.id_empleado = e.id_empleado 
                       ORDER BY d.fecha_registro DESC";
          $sqlQueryExe = $conexion->query($sqlQuery);
          while ($datos = $sqlQueryExe->fetch_object()) { 
              $inicial = strtoupper(substr($datos->nombre, 0, 1));
              
              if ($datos->nivel == 'Oro') {
                  $badgeCls = 'bg-amber-50 text-amber-700 border-amber-100';
              } else if ($datos->nivel == 'Plata') {
                  $badgeCls = 'bg-slate-100 text-slate-700 border-slate-200';
              } else {
                  $badgeCls = 'bg-orange-50 text-orange-700 border-orange-100';
              }
          ?>
            <tr class="hover:bg-slate-50/80 transition-colors group">
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center font-bold text-lg group-hover:bg-amber-100 group-hover:text-amber-600 transition-colors shrink-0">
                        <?= $inicial ?>
                    </div>
                  <div class="flex flex-col">
                    <span class="font-bold text-slate-800"><?= $datos->nombre . " " . $datos->apellido ?></span>
                    <span class="text-xs text-slate-400 font-medium">Cargo ID: <?= $datos->cargo ?></span>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 font-medium text-slate-600 whitespace-normal min-w-[200px]"><?= $datos->motivo ?></td>
              <td class="px-6 py-4">
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold border <?= $badgeCls ?>">
                  <i class="fa-solid fa-star mr-1"></i> <?= $datos->nivel ?>
                </span>
              </td>
              <td class="px-6 py-4 font-medium text-slate-500"><?= date('d M, Y', strtotime($datos->fecha_registro)) ?></td>
              <td class="px-6 py-4 text-right">
                <button data-bs-toggle="modal" data-bs-target="#modificar<?= $datos->id_destacado ?>" class="text-slate-400 hover:text-amber-600 transition-colors p-2"><i class="fa-solid fa-pen-to-square"></i></button>
                <a href="destacado.php?id_destacado=<?= $datos->id_destacado ?>" onclick="return confirmarEliminacion(event, this.href);" class="text-slate-400 hover:text-rose-600 transition-colors p-2"><i class="fa-solid fa-trash"></i></a>
              </td>
            </tr>

            <!-- Modal Modificar -->
            <div class="modal fade" id="modificar<?= $datos->id_destacado ?>" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-2xl rounded-3xl overflow-hidden">
                  <div class="bg-gradient-to-r from-amber-400 to-amber-500 p-6 flex flex-row items-center justify-between">
                    <h5 class="flex items-center text-white font-bold text-lg m-0"><i class="fa-solid fa-pen-to-square mr-2"></i> Modificar Mérito</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <form action="" method="POST" class="m-0">
                    <div class="p-6 bg-white space-y-4 text-left">
                      <input type="hidden" name="id_destacado" value="<?= $datos->id_destacado ?>">
                      
                      <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Empleado</label>
                        <select name="empleado" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-amber-500" required>
                          <option disabled value="">Seleccionar Empleado</option>
                          <?php
                          $sqlEmp = $conexion->query("SELECT id_empleado, nombre, apellido FROM empleado");
                          while ($emp = $sqlEmp->fetch_object()) {
                            $selected = ($emp->id_empleado == $datos->id_empleado) ? "selected" : "";
                            echo "<option value='".$emp->id_empleado."' $selected>".$emp->nombre." ".$emp->apellido."</option>";
                          }
                          ?>
                        </select>
                      </div>

                      <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Motivo del Mérito</label>
                        <input type="text" name="motivo" value="<?= htmlspecialchars($datos->motivo) ?>" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-amber-500" required>
                      </div>

                      <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Nivel</label>
                        <select name="nivel" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-amber-500" required>
                          <option value="Oro" <?= $datos->nivel == 'Oro' ? 'selected' : '' ?>>Oro</option>
                          <option value="Plata" <?= $datos->nivel == 'Plata' ? 'selected' : '' ?>>Plata</option>
                          <option value="Bronce" <?= $datos->nivel == 'Bronce' ? 'selected' : '' ?>>Bronce</option>
                        </select>
                      </div>

                    </div>
                    <div class="p-5 bg-slate-50 border-t border-slate-100 flex justify-end gap-3 rounded-b-3xl">
                      <button type="button" class="px-5 py-2.5 rounded-xl text-slate-600 bg-white border border-slate-200 hover:bg-slate-100 font-semibold" data-bs-dismiss="modal">Cancelar</button>
                      <button type="submit" name="btnmodificar" value="ok" class="px-5 py-2.5 rounded-xl text-white bg-amber-500 hover:bg-amber-600 font-semibold shadow-md active:scale-95 transition-transform">Guardar Cambios</button>
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
<div class="modal fade" id="exampleModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-2xl rounded-3xl overflow-hidden">
      <div class="bg-gradient-to-r from-amber-400 to-amber-500 p-6 flex flex-row items-center justify-between">
        <h5 class="flex items-center text-white font-bold text-lg m-0"><i class="fa-solid fa-plus mr-2"></i> Registrar Nuevo Mérito</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="" method="POST" class="m-0">
        <div class="p-6 bg-white space-y-4">
          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Empleado</label>
            <select name="empleado" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-amber-500" required>
              <option disabled selected value="">Seleccionar Empleado...</option>
              <?php
              $sqlEmp = $conexion->query("SELECT id_empleado, nombre, apellido FROM empleado");
              while ($emp = $sqlEmp->fetch_object()) {
                echo "<option value='".$emp->id_empleado."'>".$emp->nombre." ".$emp->apellido."</option>";
              }
              ?>
            </select>
          </div>

          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Motivo del Mérito</label>
            <input type="text" name="motivo" placeholder="Ej: Mejor vendedor del trimestre" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-amber-500" required>
          </div>

          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Nivel / Distinción</label>
            <select name="nivel" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-amber-500" required>
              <option value="Oro" selected>Oro (Sobresaliente)</option>
              <option value="Plata">Plata (Avanzado)</option>
              <option value="Bronce">Bronce (Mención Especial)</option>
            </select>
          </div>

        </div>
        <div class="p-5 bg-slate-50 border-t border-slate-100 flex justify-end gap-3 rounded-b-3xl">
          <button type="button" class="px-5 py-2.5 rounded-xl text-slate-600 bg-white border border-slate-200 hover:bg-slate-100 font-semibold" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" name="btnregistrar" value="ok" class="px-5 py-2.5 rounded-xl text-white bg-amber-500 hover:bg-amber-600 font-semibold shadow-md active:scale-95 transition-transform flex items-center gap-2">
            <i class="fa-solid fa-star"></i> Registrar
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
        title: '¿Estás seguro?',
        text: "¡No podrás revertir esto!",
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
</script>

<?php require('./layout/footer.php'); ?>
