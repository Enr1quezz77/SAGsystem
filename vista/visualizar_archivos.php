<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!-- primero se carga el topbar -->
<?php require('./layout/topbar.php'); ?>
<!-- luego se carga el sidebar -->
<?php require('./layout/sidebar.php'); ?>

<!-- inicio del contenido principal -->


<div class="px-4 py-8 md:px-8 bg-[#f8fafc] min-h-[calc(100vh-4rem)]">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h3 class="text-2xl md:text-3xl font-bold text-slate-800 flex items-center gap-3">
                    <div class="p-2.5 bg-blue-600 text-white rounded-xl shadow-lg shadow-blue-500/30">
                        <i class="fas fa-folder-open"></i>
                    </div>
                    Visualizar Archivos
                </h3>
            </div>
                <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <button class="bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 text-white font-bold py-3 px-6 rounded-full shadow-xl shadow-blue-500/30 transition-all duration-300 transform hover:-translate-y-1 flex items-center gap-2 border border-blue-400/20" data-bs-toggle="modal" data-bs-target="#modalSubirArchivo"><i class="fas fa-plus font-bold"></i> Subir Archivo</button>
                <?php endif; ?>
            </div>
        </div>

        <div class="flex flex-col md:flex-row gap-8">
            <!-- Categorías -->
            <div class="w-full md:w-64 shrink-0">
                <h5 class="text-lg font-bold text-slate-800 mb-4 px-2">Categorías</h5>
                <div class="bg-white rounded-[1.5rem] shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100 overflow-hidden text-sm font-semibold text-slate-600 flex flex-col">
                    <a href="?categoria=Contrato" class="px-5 py-4 flex items-center gap-3 hover:bg-slate-50 transition-colors border-b border-slate-50 group cursor-pointer">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center group-hover:bg-blue-100 transition-colors"><i class="fas fa-file-signature"></i></div>
                        Contratos y Acuerdos
                    </a>
                    <a href="?categoria=Identidad" class="px-5 py-4 flex items-center gap-3 hover:bg-slate-50 transition-colors border-b border-slate-50 group cursor-pointer">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-500 flex items-center justify-center group-hover:bg-emerald-100 transition-colors"><i class="fas fa-id-card"></i></div>
                        Identidad (KYC)
                    </a>
                    <a href="?categoria=Salud" class="px-5 py-4 flex items-center gap-3 hover:bg-slate-50 transition-colors border-b border-slate-50 group cursor-pointer">
                        <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-500 flex items-center justify-center group-hover:bg-rose-100 transition-colors"><i class="fas fa-notes-medical"></i></div>
                        Salud y Médicos
                    </a>
                    <a href="?categoria=Laboral" class="px-5 py-4 flex items-center gap-3 hover:bg-slate-50 transition-colors border-b border-slate-50 group cursor-pointer">
                        <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-500 flex items-center justify-center group-hover:bg-amber-100 transition-colors"><i class="fas fa-briefcase"></i></div>
                        Reportes Laborales
                    </a>
                    <a href="visualizar_archivos.php" class="px-5 py-4 flex items-center gap-3 hover:bg-slate-50 transition-colors border-b border-slate-50 group cursor-pointer">
                        <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center group-hover:bg-slate-200 transition-colors"><i class="fas fa-folder-open"></i></div>
                        Mostrar Todos
                    </a>
                </div>
            </div>

            <!-- Tabla de Archivos -->
            <div class="flex-1">
                <div class="bg-white rounded-[2rem] shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100 overflow-hidden">
                    <div class="p-0 overflow-x-auto">
                        <table class="w-full text-sm text-left text-slate-500">
                            <thead class="text-[13px] text-slate-500 uppercase bg-slate-50/70 border-b border-slate-100">
                                <tr>
                                    <th scope="col" class="px-8 py-5 font-bold tracking-wider">Empleado y Archivo</th>
                                    <th scope="col" class="px-6 py-5 font-bold tracking-wider">Categoría</th>
                                    <th scope="col" class="px-6 py-5 font-bold tracking-wider w-32">Fechas</th>
                                    <th class="px-6 py-5 font-bold tracking-wider text-center w-64 no-export">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php
                                include "../modelo/conexion.php";
                                $categoriaSeleccionada = isset($_GET['categoria']) ? $_GET['categoria'] : '';
                                
                                $whereClause = "WHERE 1=1";
                                if ($categoriaSeleccionada !== '') {
                                    $whereClause .= " AND d.tipo_documento = '" . $conexion->real_escape_string($categoriaSeleccionada) . "'";
                                }

                                $query = "SELECT d.*, e.nombre, e.apellido, e.dni 
                                          FROM documento_empleado d
                                          INNER JOIN empleado e ON d.id_empleado = e.id_empleado 
                                          $whereClause 
                                          ORDER BY d.fecha_subida DESC";
                                          
                                $resultado = $conexion->query($query);
                                
                                if ($resultado && $resultado->num_rows > 0): 
                                    while ($doc = $resultado->fetch_object()): 
                                        $extension = strtolower(pathinfo($doc->nombre_original, PATHINFO_EXTENSION));
                                ?>
                                        <tr class="hover:bg-slate-50/80 transition-colors group">
                                            <td class="px-8 py-5">
                                                <div class="flex items-center gap-3 border-l-4 border-transparent group-hover:border-blue-500 pl-3">
                                                    <div>
                                                        <div class="font-bold text-slate-800 text-[14px] flex items-center gap-2">
                                                            <?= htmlspecialchars($doc->nombre) . " " . htmlspecialchars($doc->apellido) ?>
                                                            <span class="text-[10px] bg-slate-100 text-slate-400 px-2 py-0.5 rounded-full font-bold uppercase"><?= $extension ?></span>
                                                        </div>
                                                        <div class="text-xs text-slate-400 font-medium mt-1 w-64 truncate" title="<?= htmlspecialchars($doc->nombre_original) ?>"><?= htmlspecialchars($doc->nombre_original) ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-5">
                                                <?php
                                                $catIcono = match ($doc->tipo_documento) {
                                                    'Contrato' => '<span class="inline-flex items-center px-2.5 py-1.5 rounded-lg text-xs font-bold bg-blue-50 text-blue-700 shadow-sm"><i class="fas fa-file-signature mr-1.5 opacity-70"></i> Contrato</span>',
                                                    'Identidad' => '<span class="inline-flex items-center px-2.5 py-1.5 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 shadow-sm"><i class="fas fa-id-card mr-1.5 opacity-70"></i> Identidad</span>',
                                                    'Salud' => '<span class="inline-flex items-center px-2.5 py-1.5 rounded-lg text-xs font-bold bg-rose-50 text-rose-700 shadow-sm"><i class="fas fa-notes-medical mr-1.5 opacity-70"></i> Salud</span>',
                                                    'Laboral' => '<span class="inline-flex items-center px-2.5 py-1.5 rounded-lg text-xs font-bold bg-amber-50 text-amber-700 shadow-sm"><i class="fas fa-briefcase mr-1.5 opacity-70"></i> Laboral</span>',
                                                    default => '<span class="inline-flex items-center px-2.5 py-1.5 rounded-lg text-xs font-bold bg-slate-100 text-slate-700 shadow-sm"><i class="fas fa-file-alt mr-1.5 opacity-70"></i> Otro</span>',
                                                };
                                                echo $catIcono;
                                                ?>
                                            </td>
                                            <td class="px-6 py-5 whitespace-nowrap">
                                                <div class="flex flex-col gap-1">
                                                    <span class="text-[11px] text-slate-500 font-medium"><i class="fa fa-calendar-plus text-slate-400 mr-1"></i> Subido: <?= date('d/m/Y', strtotime($doc->fecha_subida)) ?></span>
                                                    <?php if (!empty($doc->fecha_vence)): 
                                                        $fechaHoy = new DateTime();
                                                        $fechaVence = new DateTime($doc->fecha_vence);
                                                        $diasRestantes = $fechaHoy->diff($fechaVence)->format("%r%a");
                                                        
                                                        if ($diasRestantes < 0) {
                                                            echo '<span class="text-[11px] text-red-600 font-bold"><i class="fa fa-circle-exclamation mr-1"></i> Vencido el ' . date('d/m/Y', strtotime($doc->fecha_vence)) . '</span>';
                                                        } elseif ($diasRestantes <= 15) {
                                                            echo '<span class="text-[11px] text-amber-600 font-bold"><i class="fa fa-clock mr-1"></i> Expira en ' . $diasRestantes . ' días</span>';
                                                        } else {
                                                            echo '<span class="text-[11px] text-emerald-600 font-medium"><i class="fa fa-calendar-check mr-1"></i> Vence: ' . date('d/m/Y', strtotime($doc->fecha_vence)) . '</span>';
                                                        }
                                                    endif; ?>
                                                </div>
                                            </td>
                                            <td class="px-6 py-5 text-center space-x-1">
                                                <a href="<?= '../' . htmlspecialchars($doc->ruta_archivo) ?>" class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition-colors inline-flex items-center justify-center cursor-pointer shadow-sm" download="<?= htmlspecialchars($doc->nombre_original) ?>" title="Descargar Origen"><i class="fas fa-download"></i></a>
                                                <a href="generar_reporte.php?id_empleado=<?= $doc->id_empleado ?>" target="_blank" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors inline-flex items-center justify-center cursor-pointer shadow-sm" title="Generar Expediente Maestro de <?= htmlspecialchars($doc->nombre) ?>"><i class="fas fa-book-open"></i></a>
                                                <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                                <a href="../controlador/controlador_borrar_documento.php?eliminar=<?= urlencode($doc->id_documento) ?>" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-500 hover:bg-rose-100 hover:text-rose-600 transition-colors inline-flex items-center justify-center cursor-pointer shadow-sm" title="Eliminar"><i class="fas fa-trash"></i></a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="px-8 py-10 text-center text-slate-500 font-medium">No hay documentos cargados en el expediente.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- por ultimo se carga el footer -->
<?php require('./layout/footer.php'); ?>

    <!-- Formulario para subir archivos -->
    <!-- Formulario para subir archivos -->
<div class="modal fade" id="modalSubirArchivo" tabindex="-1" aria-labelledby="modalSubirArchivoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-2xl rounded-3xl overflow-hidden">
      <div class="bg-gradient-to-r from-blue-500 to-indigo-500 p-6 flex items-center justify-between">
        <h5 class="text-white font-bold text-lg flex items-center gap-3 m-0" id="modalSubirArchivoLabel">
          <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-sm"><i class="fas fa-upload"></i></div>
          Subir Nuevo Archivo
        </h5>
        <button type="button" class="btn-close btn-close-white opacity-80 hover:opacity-100 transition-opacity" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="../controlador/controlador_subir_documento.php" method="POST" enctype="multipart/form-data">
        <div class="p-8 bg-white space-y-5">
          <div>
            <label for="id_empleado" class="block text-sm font-bold text-slate-700 mb-2">Vincular a Empleado</label>
            <select class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl px-4 py-3 focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none appearance-none cursor-pointer" id="id_empleado" name="id_empleado" required>
              <option value="" disabled selected>Seleccione un empleado...</option>
              <?php
              include "../modelo/conexion.php";
              $empleados = $conexion->query("SELECT id_empleado, nombre, apellido FROM empleado");
              while ($emp = $empleados->fetch_object()) {
                  echo '<option value="' . $emp->id_empleado . '">' . htmlspecialchars($emp->nombre . ' ' . $emp->apellido) . '</option>';
              }
              ?>
            </select>
          </div>
          <div>
            <label for="tipo_documento" class="block text-sm font-bold text-slate-700 mb-2">Categoría del Documento</label>
            <select name="tipo_documento" id="tipo_documento" class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl px-4 py-3 focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none appearance-none cursor-pointer" required>
                <option value="Contrato">Contratos y Acuerdos</option>
                <option value="Identidad">Documento de Identidad (DNI, Pasaporte)</option>
                <option value="Salud">Salud / Certificado Médico</option>
                <option value="Laboral">Laboral (Amonestaciones/Evaluaciones)</option>
                <option value="Otro">Otro Documento Administrativo</option>
            </select>
          </div>
          <div>
            <label for="fecha_vence" class="block text-sm font-bold text-slate-700 mb-2">Fecha de Vencimiento <span class="text-xs text-slate-400 font-normal">(Opcional)</span></label>
            <input type="date" class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl px-4 py-3 focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none" id="fecha_vence" name="fecha_vence">
          </div>
          <div>
            <label for="archivo" class="block text-sm font-bold text-slate-700 mb-2">Seleccionar archivo</label>
            <input class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl px-4 py-3 focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer" type="file" id="archivo" name="archivo" required>
          </div>
        </div>
        <div class="flex justify-end gap-3 px-8 pb-8 pt-4 bg-white border-t border-slate-100">
          <button type="button" class="px-6 py-3 rounded-xl font-bold text-slate-500 bg-slate-100 hover:bg-slate-200 transition-colors" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" name="btnsubir" class="px-6 py-3 rounded-xl font-bold text-white bg-blue-500 hover:bg-blue-600 transition-colors shadow-lg shadow-blue-500/30 flex items-center gap-2"><i class="fas fa-cloud-upload-alt"></i> Subir Documento</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php
// Mostrar mensajes de sesión si existen
if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    $tipo = strpos($mensaje, 'Error') !== false ? 'error' : 'success';
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: '$tipo',
                title: '" . ($tipo == 'error' ? 'Oops...' : '¡Éxito!') . "',
                text: '$mensaje',
                confirmButtonColor: '#3085d6'
            });
        });
    </script>";
    unset($_SESSION['mensaje']);
}
?>
