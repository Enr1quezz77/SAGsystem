<?php
   session_start();
   if (empty($_SESSION['email'])) {
       header("Location: /login_register12/index.php");
       exit();
   }

?>

<link href="/login_register12/public/vendor/dist/fonts/montserrat/index.css" rel="stylesheet">

<style>
  /* DataTables Tailwind Integration overrides */
  .dataTables_wrapper > div:first-child { padding: 1.5rem 2rem 0.5rem 2rem; }
  .dataTables_wrapper > div:last-child { padding: 1.5rem 2rem 2rem 2rem; }
  
  /* Fix native DataTable buttons */
  .dt-buttons { display: flex; gap: 0.75rem; flex-wrap: wrap; }
  button.dt-button, div.dt-button, a.dt-button {
      background-image: none !important;
      background-color: transparent !important;
      border: none !important;
      box-shadow: none !important;
      margin: 0 !important;
      padding: 0 !important;
  }
  
  .dataTables_length { color: #64748b; font-size: 0.875rem; font-weight: 600; }
  .dataTables_length select { margin: 0 0.5rem; padding: 0.25rem 2rem 0.25rem 0.75rem; border-radius: 0.5rem; border: 1px solid #e2e8f0; background-color: #f8fafc; color: #475569; outline: none; }
  
  .dataTables_filter { color: #64748b; font-size: 0.875rem; font-weight: 600; display: flex; align-items: center; justify-content: flex-end; }
  .dataTables_filter label { display: flex; align-items: center; gap: 0.5rem; }
  .dataTables_filter input { padding: 0.5rem 1rem; border-radius: 0.75rem; border: 1px solid #e2e8f0; background-color: #f8fafc; color: #475569; outline: none; font-weight: 500; transition: all 0.2s; width: 250px; }
  .dataTables_filter input:focus { border-color: #10b981; box-shadow: 0 0 0 3px rgba(16,185,129,0.1); }
  
  .dataTables_info { color: #94a3b8; font-size: 0.875rem; font-weight: 500; }
  .dataTables_paginate { display: flex; gap: 0.25rem; font-size: 0.875rem; justify-content: flex-end; }
  .dataTables_paginate .paginate_button { padding: 0.4rem 0.8rem; border-radius: 0.5rem; border: 1px solid #e2e8f0; cursor: pointer; color: #64748b !important; font-weight: 600; text-decoration: none !important; transition: all 0.2s; }
  .dataTables_paginate .paginate_button:hover { background-color: #f8fafc; color: #10b981 !important; border-color: #10b981; }
  .dataTables_paginate .paginate_button.current { background-color: #ecfdf5; color: #059669 !important; border-color: #a7f3d0; }
  .dataTables_paginate .paginate_button.disabled { opacity: 0.5; cursor: not-allowed; hover:bg-transparent; }
  table.dataTable { border-collapse: collapse !important; border-bottom: 1px solid #f1f5f9; }
</style>

<!-- primero se carga el topbar -->
<?php require('./layout/topbar.php'); ?>
<!-- luego se carga el sidebar -->
<?php require('./layout/sidebar.php'); ?>

<!-- inicio del contenido principal -->


<div class="px-4 py-8 md:px-8 bg-[#f8fafc] transition-colors duration-300 min-h-[calc(100vh-4rem)]">
    <div class="max-w-7xl mx-auto">
        
        <!-- Header Section -->
        <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h3 class="text-2xl md:text-3xl font-bold text-slate-800 flex items-center gap-3 transition-colors">
                    <div class="p-2.5 bg-red-600 text-white rounded-xl shadow-lg shadow-red-600/30">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    Lista de Empleados
                </h3>
            </div>
            <button type="button" class="bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-bold py-3 px-6 rounded-full shadow-xl shadow-emerald-500/30 transition-all duration-300 transform hover:-translate-y-1 flex items-center gap-2 border border-emerald-400/20" data-bs-toggle="modal" data-bs-target="#modalRegistrarEmpleado">
                <i class="fa-solid fa-plus font-bold"></i> Registrar Empleado
            </button>
        </div>

        <!-- Los controles de mostrar registros y búsqueda son generados por DataTables -->

        <!-- Table Section -->
        <div class="bg-white rounded-[2rem] shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100 overflow-hidden transition-colors duration-300">
            <div class="p-0 overflow-x-auto overflow-y-auto max-h-[500px]">
                <table class="w-full text-sm text-left text-slate-500 relative" id="example">
                    <thead class="text-[13px] text-slate-500 uppercase bg-slate-50/95 border-b border-slate-100 sticky top-0 z-10 shadow-sm backdrop-blur-sm transition-colors">
                        <tr>
                            <th scope="col" class="px-4 py-5 font-bold tracking-wider bg-slate-50 text-center w-16">ID</th>
                            <th scope="col" class="px-8 py-5 font-bold tracking-wider bg-slate-50">Empleado</th>
                            <th scope="col" class="px-6 py-5 font-bold tracking-wider bg-slate-50">Cédula</th>
                            <th scope="col" class="px-6 py-5 font-bold tracking-wider bg-slate-50">Cargo</th>
                            <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <th class="px-6 py-5 font-bold tracking-wider text-center w-40 no-export bg-slate-50">Acciones</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    <?php
                    include "../modelo/conexion.php";
                    include "../controlador/controlador_registrar_empleado.php";
                    include "../controlador/controlador_modificar_empleado.php";
                    include "../controlador/controlador_eliminar_empleado.php";

                    $sql = $conexion->query(" SELECT empleado.id_empleado, empleado.nombre, empleado.apellido, empleado.dni, empleado.cargo, empleado.foto, cargo.nombre as 'nom_cargo' FROM empleado INNER JOIN cargo ON empleado.cargo = cargo.id_cargo ");

                    while ($datos = $sql->fetch_object()) { ?>
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            <td class="px-4 py-5 text-center">
                                <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-indigo-50 text-indigo-700 font-extrabold text-sm border border-indigo-100 shadow-sm" title="ID para registrar en biométrico"><?= $datos->id_empleado ?></span>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-4">
                                    <?php if (!empty($datos->foto)) { ?>
                                        <div class="w-11 h-11 rounded-full bg-slate-100 flex items-center justify-center shrink-0 overflow-hidden shadow-sm border border-slate-200 group-hover:ring-2 group-hover:ring-red-100 transition-all">
                                            <img src="../img/empleados/<?= $datos->foto ?>" alt="Foto" class="w-full h-full object-cover">
                                        </div>
                                    <?php } else { ?>
                                        <div class="w-11 h-11 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center font-bold text-lg group-hover:bg-red-100 group-hover:text-red-600 transition-colors shrink-0">
                                            <?= substr($datos->nombre, 0, 1) ?>
                                        </div>
                                    <?php } ?>
                                    <div class="flex flex-col">
                                        <span class="font-extrabold text-slate-800 text-[15px] transition-colors"><?= $datos->nombre . " " . $datos->apellido ?></span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5 font-semibold text-slate-600 transition-colors"><?= $datos->dni ?></td>
                            <td class="px-6 py-5">
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200 shadow-sm transition-colors">
                                    <?= $datos->nom_cargo ?>
                                </span>
                            </td>
                            <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <td class="px-6 py-5 text-center space-x-2">
                                <button type="button" class="w-9 h-9 rounded-xl bg-amber-50 text-amber-500 hover:bg-amber-100 transition-colors inline-flex items-center justify-center cursor-pointer shadow-sm border border-amber-100 shrink-0" title="Modificar" data-bs-toggle="modal" data-bs-target="#modalModificarEmpleado<?= $datos->id_empleado ?>">
                                  <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <a href="empleado.php?id=<?= $datos->id_empleado ?>" class="w-9 h-9 rounded-xl bg-rose-50 text-rose-500 hover:bg-rose-100 hover:text-rose-600 transition-colors inline-flex items-center justify-center cursor-pointer shadow-sm border border-rose-100 btn-eliminar shrink-0" title="Eliminar"><i class="fa-solid fa-trash"></i></a>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <!-- Modal Modificar Empleado -->
                        <div class="modal fade" id="modalModificarEmpleado<?= $datos->id_empleado ?>" tabindex="-1" aria-labelledby="modalModificarEmpleadoLabel<?= $datos->id_empleado ?>" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-2xl rounded-3xl overflow-hidden bg-white">
                              <div class="bg-gradient-to-r from-amber-400 to-orange-500 p-6 flex items-center justify-between">
                                <h5 class="text-white font-bold text-lg flex items-center gap-3 m-0" id="modalModificarEmpleadoLabel<?= $datos->id_empleado ?>">
                                  <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-sm"><i class="fa-solid fa-user-pen"></i></div>
                                  Modificar Empleado
                               </h5>
                                <button type="button" class="btn-close btn-close-white opacity-80 hover:opacity-100 transition-opacity" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="p-8 bg-white text-left">
                                <form action="" method="POST" autocomplete="off" class="space-y-5" enctype="multipart/form-data">
                                  <input type="hidden" name="txtid" value="<?= $datos->id_empleado ?>">
                                  
                                  <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Nombre</label>
                                    <input type="text" class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl px-4 py-3 focus:ring-4 focus:ring-amber-500/20 focus:border-amber-500 transition-all outline-none" name="txtnombre" value="<?= $datos->nombre ?>">
                                  </div>
                                  
                                  <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Apellido</label>
                                    <input type="text" class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl px-4 py-3 focus:ring-4 focus:ring-amber-500/20 focus:border-amber-500 transition-all outline-none" name="txtapellido" value="<?= $datos->apellido ?>">
                                  </div>
                                  
                                  <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Cédula</label>
                                    <?php 
                                        $nac = substr($datos->dni, 0, 2);
                                        if ($nac == 'V-' || $nac == 'E-') {
                                            $nacionalidad = $nac;
                                            $numero_dni = substr($datos->dni, 2);
                                        } else {
                                            $nacionalidad = 'V-';
                                            $numero_dni = $datos->dni;
                                        }
                                    ?>
                                    <div class="flex gap-2">
                                        <select name="nacionalidad" class="w-1/4 bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl px-4 py-3 focus:ring-4 focus:ring-amber-500/20 focus:border-amber-500 transition-all outline-none appearance-none cursor-pointer">
                                            <option value="V-" <?= $nacionalidad == 'V-' ? 'selected' : '' ?> class="bg-white">V</option>
                                            <option value="E-" <?= $nacionalidad == 'E-' ? 'selected' : '' ?> class="bg-white">E</option>
                                        </select>
                                        <input type="text" class="w-3/4 bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl px-4 py-3 focus:ring-4 focus:ring-amber-500/20 focus:border-amber-500 transition-all outline-none" name="txtdni" value="<?= $numero_dni ?>" required>
                                    </div>
                                  </div>
                                  
                                  <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Foto de Perfil (Opcional)</label>
                                    <input type="file" class="w-full text-sm text-slate-500 font-medium bg-slate-50 border border-slate-200 rounded-xl cursor-pointer file:cursor-pointer file:border-0 file:py-3 file:px-4 file:mr-4 file:bg-amber-100 file:hover:bg-amber-200 file:text-amber-700 focus:outline-none focus:ring-4 focus:ring-amber-500/20 focus:border-amber-500 transition-all" name="foto" accept="image/*">
                                    <p class="text-xs text-slate-400 mt-1">Selecciona una imagen si deseas actualizar la foto actual.</p>
                                  </div>

                                  <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Cargo</label>
                                    <select name="txtcargo" class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl px-4 py-3 focus:ring-4 focus:ring-amber-500/20 focus:border-amber-500 transition-all outline-none appearance-none cursor-pointer">
                                      <?php
                                      $sql2 = $conexion->query(" select * from cargo ");
                                      while ($datos2 = $sql2->fetch_object()) { ?>
                                          <option class="bg-white" <?= $datos->cargo==$datos2->id_cargo ? 'selected' : '' ?> value="<?= $datos2->id_cargo ?>"><?= $datos2->nombre ?></option>
                                      <?php } ?>
                                    </select>
                                  </div>

                                  <div class="flex justify-end gap-3 mt-8 pt-4 border-t border-slate-100">
                                    <button type="button" class="px-6 py-3 rounded-xl font-bold text-slate-500 bg-slate-100 hover:bg-slate-200 transition-colors" data-bs-dismiss="modal">
                                      Cancelar
                                    </button>
                                    <button type="submit" value="ok" name="btnmodificar" class="px-6 py-3 rounded-xl font-bold text-white bg-amber-500 hover:bg-amber-600 transition-colors shadow-lg shadow-amber-500/30 flex items-center gap-2">
                                      <i class="fa fa-check"></i> Guardar Cambios
                                    </button>
                                  </div>
                                </form>
                              </div>
                            </div>
                          </div>
                        </div>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Paginación controlada automáticamente por DataTables -->
        
    </div>
</div>

<!-- Modal Registrar Empleado -->
<div class="modal fade" id="modalRegistrarEmpleado" tabindex="-1" aria-labelledby="modalRegistrarEmpleadoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-2xl rounded-3xl overflow-hidden bg-white">
      <div class="bg-gradient-to-r from-emerald-500 to-teal-500 p-6 flex items-center justify-between">
        <h5 class="text-white font-bold text-lg flex items-center gap-3 m-0" id="modalRegistrarEmpleadoLabel">
          <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-sm"><i class="fa-solid fa-user-plus"></i></div>
          Registrar Empleado
        </h5>
        <button type="button" class="btn-close btn-close-white opacity-80 hover:opacity-100 transition-opacity" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="p-8 bg-white">
        <form action="" method="POST" autocomplete="off" class="space-y-5" enctype="multipart/form-data">
          <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">Nombre</label>
            <input type="text" class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl px-4 py-3 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all outline-none" name="txtnombre" required placeholder="Nombre del empleado">
          </div>
          
          <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">Apellido</label>
            <input type="text" class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl px-4 py-3 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all outline-none" name="txtapellido" required placeholder="Apellido del empleado">
          </div>
          
          <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">Cédula</label>
            <div class="flex gap-2">
                <select name="nacionalidad" class="w-1/4 bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl px-4 py-3 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all outline-none appearance-none cursor-pointer">
                    <option value="V-" class="bg-white">V</option>
                    <option value="E-" class="bg-white">E</option>
                </select>
                <input type="text" class="w-3/4 bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl px-4 py-3 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all outline-none" name="txtdni" required placeholder="Número de cédula">
            </div>
          </div>
          
          <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">Foto de Perfil (Opcional)</label>
            <input type="file" class="w-full text-sm text-slate-500 font-medium bg-slate-50 border border-slate-200 rounded-xl cursor-pointer file:cursor-pointer file:border-0 file:py-3 file:px-4 file:mr-4 file:bg-emerald-100 file:hover:bg-emerald-200 file:text-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all" name="foto" accept="image/*">
            <p class="text-xs text-slate-400 mt-1">Formatos permitidos: JPG, PNG, WEBP.</p>
          </div>

          <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">Cargo</label>
            <select name="txtcargo" class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl px-4 py-3 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all outline-none appearance-none cursor-pointer">
              <option value="" disabled selected class="bg-white">Seleccione un cargo...</option>
              <?php
              $sql2 = $conexion->query(" select * from cargo ");
              while ($datos2 = $sql2->fetch_object()) { ?>
                  <option value="<?= $datos2->id_cargo ?>" class="bg-white"><?= $datos2->nombre ?></option>
              <?php } ?>
            </select>
          </div>

          <div class="flex justify-end gap-3 mt-8 pt-4 border-t border-slate-100">
            <button type="button" class="px-6 py-3 rounded-xl font-bold text-slate-500 bg-slate-100 hover:bg-slate-200 transition-colors" data-bs-dismiss="modal">
              Cancelar
            </button>
            <button type="submit" value="ok" name="btnregistrar" class="px-6 py-3 rounded-xl font-bold text-white bg-emerald-500 hover:bg-emerald-600 transition-colors shadow-lg shadow-emerald-500/30 flex items-center gap-2">
              <i class="fa-solid fa-check"></i> Registrar
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- fin del contenido principal -->

<!-- por ultimo se carga el footer -->
<?php require('./layout/footer.php'); ?>
<script src="/login_register12/public/sweet/js/sweetalert2.all.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-warning').forEach(btn => {
        btn.classList.add('shadow-sm');
    });
    document.querySelectorAll('.btn-danger').forEach(btn => {
        btn.classList.add('shadow-sm');
    });
    document.querySelectorAll('a.btn-eliminar').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const url = btn.getAttribute('href');
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡No podrás revertir esto!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
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
