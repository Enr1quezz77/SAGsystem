<?php
session_start();
if (empty($_SESSION['email'])) {
    header("Location: /login_register12/index.php");
    exit();
}
?>

<link href="/login_register12/public/vendor/dist/fonts/montserrat/index.css" rel="stylesheet">
<?php require('./layout/sidebar.php'); ?>

<div class="px-4 py-8 md:px-8 bg-[#f8fafc] transition-colors duration-300 min-h-[calc(100vh-4rem)]">
    <div class="max-w-7xl mx-auto">
        
        <!-- Header Section -->
        <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex flex-col justify-start items-start gap-4">
                <h3 class="text-2xl md:text-3xl font-bold text-slate-800 flex items-center gap-3 transition-colors">
                    <div class="p-2.5 bg-blue-600 text-white rounded-xl shadow-lg shadow-blue-600/30">
                        <i class="fa-solid fa-calendar-clock"></i>
                    </div>
                    Gestión de Permisos
                </h3>
                <p class="text-slate-500 font-medium transition-colors">Asigna y controla los días de vacaciones, reposos y permisos especiales.</p>
            </div>
            <div class="flex gap-3">
                <button data-bs-toggle="modal" data-bs-target="#modalRegistrarPermiso" class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold py-3 px-6 rounded-full shadow-lg shadow-blue-500/30 transition-all duration-300 transform hover:-translate-y-1 flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i> Nuevo Permiso
                </button>
            </div>
        </div>

        <!-- Alertas -->
        <?php if (!empty($_SESSION['mensaje_permiso'])): ?>
            <?php 
                $tipo = strpos($_SESSION['mensaje_permiso'], 'Error') !== false ? 'error' : 'success'; 
                $icon = $tipo == 'error' ? 'fa-triangle-exclamation text-rose-600' : 'fa-check text-emerald-600';
                $bg_icon = $tipo == 'error' ? 'bg-rose-100' : 'bg-emerald-100';
                $bg_alert = $tipo == 'error' ? 'bg-rose-50 border-rose-100 text-rose-800' : 'bg-emerald-50 border-emerald-100 text-emerald-800';
            ?>
            <div class="mb-6 p-4 rounded-2xl border flex items-center gap-3 shadow-sm <?= $bg_alert ?>">
                <div class="w-8 h-8 rounded-full flex items-center justify-center <?= $bg_icon ?>">
                    <i class="fa-solid <?= $icon ?>"></i>
                </div>
                <div><strong class="font-semibold"><?= $tipo == 'error' ? 'Atención:' : '¡Éxito!' ?></strong> <?= $_SESSION['mensaje_permiso'] ?></div>
            </div>
            <?php unset($_SESSION['mensaje_permiso']); ?>
        <?php endif; ?>

        <!-- Tabla Permisos -->
        <div class="bg-white rounded-[2rem] shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100 overflow-hidden transition-colors duration-300">
            <div class="p-6 overflow-x-auto">
                <table class="table w-full text-sm text-left text-slate-500" id="example" style="width:100%">
                    <thead class="text-xs text-slate-400 uppercase bg-slate-50 font-semibold transition-colors">
                        <tr>
                            <th scope="col" class="px-4 py-4 rounded-l-xl">Empleado</th>
                            <th scope="col" class="px-4 py-4">Tipo</th>
                            <th scope="col" class="px-4 py-4">Desde</th>
                            <th scope="col" class="px-4 py-4">Hasta</th>
                            <th scope="col" class="px-4 py-4">Motivo</th>
                            <th scope="col" class="px-4 py-4 rounded-r-xl">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php
                        include "../modelo/conexion.php";
                        $sql = $conexion->query("SELECT p.*, e.nombre, e.apellido, e.dni FROM permisos p INNER JOIN empleado e ON p.id_empleado = e.id_empleado ORDER BY p.id_permiso DESC");
                        while ($datos = $sql->fetch_object()) { 
                            $tipoColor = "bg-slate-100 text-slate-600";
                            if ($datos->tipo == 'Vacaciones') $tipoColor = "bg-blue-100 text-blue-700 border border-blue-200";
                            if ($datos->tipo == 'Permiso Médico') $tipoColor = "bg-amber-100 text-amber-700 border border-amber-200";
                            if ($datos->tipo == 'Asunto Personal') $tipoColor = "bg-purple-100 text-purple-700 border border-purple-200";
                        ?>
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-4 py-4">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-800"><?= $datos->nombre . " " . $datos->apellido ?></span>
                                        <span class="text-xs text-slate-400 font-medium"><?= $datos->dni ?></span>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold <?= $tipoColor ?>">
                                        <?= $datos->tipo ?>
                                    </span>
                                </td>
                                <td class="px-4 py-4 font-bold text-slate-600">
                                    <?= date('d/m/Y', strtotime($datos->fecha_inicio)) ?>
                                </td>
                                <td class="px-4 py-4 font-bold text-slate-600">
                                    <?= date('d/m/Y', strtotime($datos->fecha_fin)) ?>
                                </td>
                                <td class="px-4 py-4 text-slate-500 max-w-xs truncate" title="<?= htmlspecialchars($datos->motivo) ?>">
                                    <?= htmlspecialchars($datos->motivo) ?>
                                </td>
                                <td class="px-4 py-4">
                                    <a href="../controlador/controlador_eliminar_permiso.php?id=<?= $datos->id_permiso ?>" class="btn-eliminar p-2 bg-rose-50 text-rose-600 hover:bg-rose-100 hover:text-rose-700 rounded-lg transition-colors inline-flex items-center justify-center">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Registro -->
<div class="modal fade" id="modalRegistrarPermiso" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-2xl rounded-3xl overflow-hidden bg-white transition-colors">
      <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6 flex items-center justify-between">
        <h5 class="text-white font-bold text-lg flex items-center gap-3 m-0">
          <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-sm"><i class="fa-solid fa-calendar-plus"></i></div>
          Registrar Permiso
        </h5>
        <button type="button" class="btn-close btn-close-white opacity-80 hover:opacity-100 transition-opacity" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="../controlador/controlador_registrar_permiso.php" method="POST" class="m-0">
        <div class="p-8 bg-white space-y-6">
            <div>
              <label class="block text-sm font-bold text-slate-700 mb-2">Empleado</label>
              <select name="id_empleado" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl px-4 py-3 focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                <option value="" class="bg-white">Seleccione un empleado</option>
                <?php
                $empleados = $conexion->query("SELECT id_empleado, nombre, apellido FROM empleado");
                while($emp = $empleados->fetch_object()) {
                  echo '<option value="'.$emp->id_empleado.'" class="bg-white">'.htmlspecialchars($emp->nombre.' '.$emp->apellido).'</option>';
                }
                ?>
              </select>
            </div>
            
            <div>
              <label class="block text-sm font-bold text-slate-700 mb-2">Tipo de Permiso</label>
              <select name="tipo" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl px-4 py-3 focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                <option value="Vacaciones" class="bg-white">Vacaciones</option>
                <option value="Permiso Médico" class="bg-white">Permiso Médico (Reposo)</option>
                <option value="Asunto Personal" class="bg-white">Asunto Personal</option>
              </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-bold text-slate-700 mb-2">Fecha Inicio</label>
                  <input type="date" name="fecha_inicio" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl px-4 py-3 focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none" style="color-scheme: light dark;">
                </div>
                <div>
                  <label class="block text-sm font-bold text-slate-700 mb-2">Fecha Fin</label>
                  <input type="date" name="fecha_fin" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl px-4 py-3 focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none" style="color-scheme: light dark;">
                </div>
            </div>

            <div>
              <label class="block text-sm font-bold text-slate-700 mb-2">Motivo / Detalle</label>
              <textarea name="motivo" rows="3" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-3 focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none"></textarea>
            </div>
        </div>
        <div class="flex justify-end gap-3 px-8 pb-8 pt-4 bg-white border-t border-slate-100 transition-colors">
          <button type="button" class="px-6 py-3 rounded-xl font-bold text-slate-500 bg-slate-100 hover:bg-slate-200 transition-colors" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="px-6 py-3 rounded-xl font-bold text-white bg-blue-500 hover:bg-blue-600 transition-colors shadow-lg shadow-blue-500/30">Guardar Permiso</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="/login_register12/public/vendor/dist/sweetalert2/sweetalert2.all.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('a.btn-eliminar').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const url = btn.getAttribute('href');
            Swal.fire({
                title: '¿Eliminar Permiso?',
                text: "No podrás deshacer esta acción",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3b82f6',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    });
});
</script>

<?php require('./layout/footer.php'); ?>

