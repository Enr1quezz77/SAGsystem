<?php
   session_start();
   if (empty($_SESSION['email'])) {
       header("Location: /login_register12/index.php");
       exit();
   }

?>

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
                    Lista de Usuarios
                </h3>
            </div>
            <div class="flex gap-3 flex-wrap">
                <button type="button" class="bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-bold py-3 px-6 rounded-full shadow-xl shadow-amber-500/30 transition-all duration-300 transform hover:-translate-y-1 flex items-center gap-2 border border-amber-400/20" data-bs-toggle="modal" data-bs-target="#modalConfigAdmin">
                    <i class="fa-solid fa-key font-bold"></i> Clave Admin
                </button>
                <button type="button" class="bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-bold py-3 px-6 rounded-full shadow-xl shadow-emerald-500/30 transition-all duration-300 transform hover:-translate-y-1 flex items-center gap-2 border border-emerald-400/20" data-bs-toggle="modal" data-bs-target="#modalRegistrarUsuario">
                    <i class="fa-solid fa-plus font-bold"></i> Registrar Usuario
                </button>
            </div>
        </div>

        <?php
        include "../modelo/conexion.php";
        include "../controlador/controlador_modificar_usuario.php";
        include "../controlador/controlador_eliminar_usuario.php";
        include "../controlador/controlador_registrar_usuario.php";

        // Mostrar mensajes de la configuración de administrador si los hay
        if (isset($_SESSION['config_admin_success'])) {
            echo "<script>Swal.fire({icon: 'success', title: '¡Éxito!', text: '".$_SESSION['config_admin_success']."', confirmButtonColor: '#10b981'});</script>";
            unset($_SESSION['config_admin_success']);
        }
        if (isset($_SESSION['config_admin_error'])) {
            echo "<script>Swal.fire({icon: 'error', title: 'Error', text: '".$_SESSION['config_admin_error']."', confirmButtonColor: '#ef4444'});</script>";
            unset($_SESSION['config_admin_error']);
        }

        // Acción de borrado (eliminación suave con recarga suave)
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $conexion->query("DELETE FROM users WHERE id = $id");
            echo "<script>setTimeout(function(){ location.href='usuario.php'; }, 600);</script>";
        }

        // Acción de modificación (solo usuario y email)
        if (!empty($_POST['btnmodificar']) && !empty($_POST['txtid']) && !empty($_POST['txtusuario']) && !empty($_POST['txtemail'])) {
            $id = intval($_POST['txtid']);
            $usuario = $_POST['txtusuario'];
            $email = $_POST['txtemail'];
            $update = $conexion->query("UPDATE users SET usuario='$usuario', email='$email' WHERE id=$id");
            if ($update === true) {
                echo "<script>setTimeout(function(){ location.href='usuario.php'; }, 600);</script>";
            } else {
                ?>
                <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'INCORRECTO',
                        text: 'Error al modificar usuario. Detalles: <?= htmlspecialchars($conexion->error, ENT_QUOTES, 'UTF-8') ?>',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Aceptar'
                    });
                </script>
                <?php
            }
        }

        $sql = $conexion->query(" SELECT * from users ");
        ?>
        
        <!-- Table Section -->
        <div class="bg-white rounded-[2rem] shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100 overflow-hidden transition-colors duration-300">
            <div class="p-0 overflow-x-auto">
                <table class="w-full text-sm text-left text-slate-500" id="example">
                    <thead class="text-[13px] text-slate-500 uppercase bg-slate-50/70 border-b border-slate-100 transition-colors">
                        <tr>
                            <th scope="col" class="px-8 py-5 font-bold tracking-wider">Usuario</th>
                            <th scope="col" class="px-6 py-5 font-bold tracking-wider">Email</th>
                            <th scope="col" class="px-6 py-5 font-bold tracking-wider">Nivel de Acceso</th>
                            <th class="px-6 py-5 font-bold tracking-wider text-center w-40 no-export">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    <?php
                    while ($datos = $sql->fetch_object()) { ?>
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="w-11 h-11 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center font-bold text-lg group-hover:bg-red-100 group-hover:text-red-600 transition-colors shrink-0">
                                        <i class="fa fa-user"></i>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-extrabold text-slate-800 text-[15px] transition-colors"><?= $datos->usuario ?></span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <span class="font-semibold text-slate-600 transition-colors"><?= $datos->email ?></span>
                            </td>
                            <td class="px-6 py-5">
                                <?php if(isset($datos->role) && $datos->role == 'admin'): ?>
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200 shadow-sm">
                                        <div class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-2"></div> Administrador
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200 shadow-sm">
                                        <div class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-2"></div> Usuario
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-5 text-center space-x-2">
                                <button type="button" class="w-9 h-9 rounded-xl bg-amber-50 text-amber-500 hover:bg-amber-100 transition-colors inline-flex items-center justify-center cursor-pointer shadow-sm border border-amber-100 shrink-0" title="Modificar" data-bs-toggle="modal" data-bs-target="#modalModificarUsuario<?= $datos->id ?>">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <a href="usuario.php?id=<?= $datos->id ?>" class="w-9 h-9 rounded-xl bg-rose-50 text-rose-500 hover:bg-rose-100 hover:text-rose-600 transition-colors inline-flex items-center justify-center cursor-pointer shadow-sm border border-rose-100 btn-eliminar shrink-0" title="Eliminar"><i class="fa-solid fa-trash"></i></a>
                            </td>
                        </tr>
                        <!-- Modal Modificar Usuario -->
                        <div class="modal fade" id="modalModificarUsuario<?= $datos->id ?>" tabindex="-1" aria-labelledby="modalModificarUsuarioLabel<?= $datos->id ?>" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-2xl rounded-3xl overflow-hidden bg-white">
                              <div class="bg-gradient-to-r from-amber-400 to-orange-500 p-6 flex items-center justify-between">
                                <h5 class="text-white font-bold text-lg flex items-center gap-3 m-0" id="modalModificarUsuarioLabel<?= $datos->id ?>">
                                  <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-sm"><i class="fa-solid fa-user-pen"></i></div>
                                  Modificar Usuario
                                </h5>
                                <button type="button" class="btn-close btn-close-white opacity-80 hover:opacity-100 transition-opacity" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="p-8 bg-white text-left">
                                <form action="" method="POST" autocomplete="off" class="space-y-5">
                                  <input type="hidden" name="txtid" value="<?= $datos->id ?>">
                                  
                                  <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Usuario</label>
                                    <div class="relative">
                                      <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fa fa-user text-slate-400"></i>
                                      </div>
                                      <input type="text" class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl pl-11 pr-4 py-3 focus:ring-4 focus:ring-amber-500/20 focus:border-amber-500 transition-all outline-none" name="txtusuario" value="<?= $datos->usuario ?>" required>
                                    </div>
                                  </div>
                                  
                                  <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Email</label>
                                    <div class="relative">
                                      <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fa fa-envelope text-slate-400"></i>
                                      </div>
                                      <input type="email" class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl pl-11 pr-4 py-3 focus:ring-4 focus:ring-amber-500/20 focus:border-amber-500 transition-all outline-none" name="txtemail" value="<?= $datos->email ?>" required>
                                    </div>
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

<!-- Modal Registrar Usuario -->
<div class="modal fade" id="modalRegistrarUsuario" tabindex="-1" aria-labelledby="modalRegistrarUsuarioLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-2xl rounded-3xl overflow-hidden bg-white">
      <div class="bg-gradient-to-r from-emerald-500 to-teal-500 p-6 flex items-center justify-between">
        <h5 class="text-white font-bold text-lg flex items-center gap-3 m-0" id="modalRegistrarUsuarioLabel">
          <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-sm"><i class="fa-solid fa-user-plus"></i></div>
          Registrar Usuario
        </h5>
        <button type="button" class="btn-close btn-close-white opacity-80 hover:opacity-100 transition-opacity" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="p-8 bg-white">
        <form action="" method="POST" autocomplete="off" class="space-y-5">
          
          <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">Usuario</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <i class="fa fa-user text-slate-400"></i>
              </div>
              <input type="text" class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl pl-11 pr-4 py-3 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all outline-none" name="txtusuario" required placeholder="Nombre y Apellido o seudónimo">
            </div>
          </div>
          
          <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">Email</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <i class="fa fa-envelope text-slate-400"></i>
              </div>
              <input type="email" class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl pl-11 pr-4 py-3 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all outline-none" name="txtemail" required placeholder="ejemplo@email.com">
            </div>
          </div>
          
          <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">Contraseña</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <i class="fa fa-lock text-slate-400"></i>
              </div>
              <input type="password" class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl pl-11 pr-4 py-3 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all outline-none" name="txtpassword" required placeholder="Mínimo 6 caracteres">
            </div>
          </div>

          <input type="hidden" name="txtrol" value="user">

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

<!-- Modal Configurar Clave Admin -->
<div class="modal fade" id="modalConfigAdmin" tabindex="-1" aria-labelledby="modalConfigAdminLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-2xl rounded-3xl overflow-hidden bg-white">
      <div class="bg-gradient-to-r from-amber-500 to-orange-500 p-6 flex items-center justify-between">
        <h5 class="text-white font-bold text-lg flex items-center gap-3 m-0" id="modalConfigAdminLabel">
          <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-sm"><i class="fa-solid fa-key"></i></div>
          Configurar Clave Admin
        </h5>
        <button type="button" class="btn-close btn-close-white opacity-80 hover:opacity-100 transition-opacity" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="p-8 bg-white">
        <form action="../controlador/controlador_config_admin.php" method="POST" autocomplete="off" class="space-y-5">
          
          <div class="bg-amber-50 border border-amber-200 text-amber-700 p-4 rounded-xl text-sm mb-4">
            <i class="fa-solid fa-circle-info mr-2"></i> Esta clave es requerida en el registro cuando alguien intenta crear una cuenta con rol de Administrador.
          </div>

          <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">Nueva Clave de Seguridad</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <i class="fa fa-lock text-slate-400"></i>
              </div>
              <input type="text" class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl pl-11 pr-4 py-3 focus:ring-4 focus:ring-amber-500/20 focus:border-amber-500 transition-all outline-none" name="nueva_clave_admin" required placeholder="Ingresa la nueva clave (ej. 12345678)">
            </div>
          </div>

          <div class="flex justify-end gap-3 mt-8 pt-4 border-t border-slate-100">
            <button type="button" class="px-6 py-3 rounded-xl font-bold text-slate-500 bg-slate-100 hover:bg-slate-200 transition-colors" data-bs-dismiss="modal">
              Cancelar
            </button>
            <button type="submit" value="ok" name="btn_config_admin" class="px-6 py-3 rounded-xl font-bold text-white bg-amber-500 hover:bg-amber-600 transition-colors shadow-lg shadow-amber-500/30 flex items-center gap-2">
              <i class="fa-solid fa-save"></i> Guardar Cambios
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
    // Estilos para los botones de acción
    document.querySelectorAll('.btn-warning').forEach(btn => {
        btn.classList.add('shadow-sm');
    });
    document.querySelectorAll('.btn-danger').forEach(btn => {
        btn.classList.add('shadow-sm');
    });

    // Confirmación de eliminación
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
