<?php
include_once '../modelo/conexion.php'; // Aseguramos que se incluya la conexión a la base de datos
include_once '../controlador/puente_biometrico.php'; // Incluir el controlador de biométrico
include_once '../controlador/funciones_asistencia.php';

// Asegurarse de que la sesión esté iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

evaluar_faltas_consecutivas($conexion);

// Obtener el nombre del usuario desde la sesión
$usuario = isset($_SESSION['login_success']['usuario']) ? $_SESSION['login_success']['usuario'] : 'Usuario';

// Obtener el nombre del usuario desde la tabla users si está disponible
if (isset($_SESSION['login_success']['email'])) {
    $email = $_SESSION['login_success']['email'];
    $query = $conexion->prepare("SELECT nombre FROM users WHERE email = ?");
    $query->bind_param("s", $email);
    $query->execute();
    $result = $query->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['login_success']['usuario'] = $row['nombre']; // Actualizar directamente en la sesión
    }
    $query->close();
}

if (empty($_SESSION['email'])) {
    header("Location: /login_register12/index.php");
    exit();
}
// Mostrar SweetAlert si hay login_success
if (!empty($_SESSION['login_success'])) {
   $role = $_SESSION['login_success']['role'] === 'admin' ? 'Administrador' : 'Usuario';
   $usuario = $_SESSION['login_success']['usuario'];
   echo '<link rel="stylesheet" href="/login_register12/public/vendor/dist/sweetalert2/sweetalert2.min.css">';
   echo '<script src="/login_register12/public/vendor/dist/sweetalert2/sweetalert2.all.min.js"></script>';
   echo '<script>Swal.fire({
     title: "Bienvenido ' . $usuario . '",
     text: "Nivel de acceso: ' . $role . '",
     icon: "success",
     showConfirmButton: false,
     timer: 1800
   });</script>';
   unset($_SESSION['login_success']);
}

// Mostrar mensajes globales de sesión (como restauraciones u operaciones)
if (!empty($_SESSION['mensaje'])) {
    $type = (strpos(strtolower($_SESSION['mensaje']), 'error') !== false) ? 'error' : 'success';
    $clean_msg = strip_tags(str_replace('<br>', ' ', $_SESSION['mensaje']));
    $short_msg = substr($clean_msg, 0, 80) . (strlen($clean_msg) > 80 ? '...' : '');

    if (!isset($sweetAlertLoaded)) {
        echo '<link rel="stylesheet" href="/login_register12/public/vendor/dist/sweetalert2/sweetalert2.min.css">';
        echo '<script src="/login_register12/public/vendor/dist/sweetalert2/sweetalert2.all.min.js"></script>';
        $sweetAlertLoaded = true;
    }
    
    echo '<script>Swal.fire({
      title: "' . ($type == 'error' ? 'Aviso Importante' : 'Operación Exitosa') . '",
      html: "' . addslashes($_SESSION['mensaje']) . '",
      icon: "' . $type . '",
      confirmButtonColor: "' . ($type == 'error' ? '#ef4444' : '#10b981') . '"
    });</script>';
    
    echo '<script>document.addEventListener("DOMContentLoaded", function() { if(window.addNotification) { window.addNotification("' . $type . '", "BD: ' . addslashes($short_msg) . '"); } });</script>';
    unset($_SESSION['mensaje']);
}
?>

<style>
  /* Remove legacy styles in favor of Tailwind, but keep modal overrides if any */
</style>

<?php require('./layout/sidebar.php'); ?>



<div class="px-4 py-8 md:px-8 bg-[#f8fafc] min-h-[calc(100vh-4rem)] transition-colors duration-300">
  <!-- Header Section -->
  <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div>
      <h3 class="text-2xl md:text-3xl font-bold text-slate-800 flex items-center gap-3">
        <div class="p-2.5 bg-red-600 text-white rounded-xl shadow-lg shadow-red-600/30">
          <i class="fa-solid fa-store"></i>
        </div>
        Bienvenido a S.A.G (DORE'S)
      </h3>
      <p class="text-slate-500 mt-2 ml-[3.25rem] font-medium">Panel de control y resumen general</p>
    </div>
    <div class="flex gap-3">
        <button class="bg-white text-emerald-600 hover:bg-emerald-50 font-bold py-3 px-6 rounded-full shadow-md transition-all duration-300 transform hover:-translate-y-1 flex items-center gap-2 border border-emerald-100" data-bs-toggle="modal" data-bs-target="#modalReportes">
            <i class="fas fa-file-pdf"></i> Generar Reporte de Asistencia
        </button>
    </div>
  </div>

  <!-- Cards Section -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <!-- Card Empleados -->
    <div class="bg-white rounded-3xl p-6 shadow-[0_2px_20px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-all duration-300 border border-slate-100 flex items-center justify-between group cursor-pointer relative overflow-hidden">
      <div class="absolute -right-10 -top-10 w-32 h-32 bg-red-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500 ease-out"></div>
      <div class="relative z-10">
        <p class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-2">Total Empleados</p>
        <h2 class="text-5xl font-black text-slate-800">
          <?php
          $empleados_count = $conexion->query("SELECT COUNT(*) as total FROM empleado")->fetch_object()->total;
          echo $empleados_count;
          ?>
        </h2>
      </div>
      <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-red-500 to-rose-600 flex items-center justify-center text-white text-2xl shadow-lg shadow-red-500/30 group-hover:-translate-y-1 transition-transform duration-300 relative z-10">
        <i class="fas fa-users"></i>
      </div>
    </div>

    <!-- Card Usuarios -->
    <div class="bg-white rounded-3xl p-6 shadow-[0_2px_20px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-all duration-300 border border-slate-100 flex items-center justify-between group cursor-pointer relative overflow-hidden">
      <div class="absolute -right-10 -top-10 w-32 h-32 bg-amber-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500 ease-out"></div>
      <div class="relative z-10">
        <p class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-2">Total Usuarios</p>
        <h2 class="text-5xl font-black text-slate-800">
          <?php
          $sqlUsuarios = $conexion->query("SELECT COUNT(*) as total_usuarios FROM users");
          echo ($sqlUsuarios && $row = $sqlUsuarios->fetch_assoc()) ? $row['total_usuarios'] : 0;
          ?>
        </h2>
      </div>
      <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-orange-400 to-amber-500 flex items-center justify-center text-white text-2xl shadow-lg shadow-amber-500/30 group-hover:-translate-y-1 transition-transform duration-300 relative z-10">
        <i class="fas fa-user-shield"></i>
      </div>
    </div>
  </div>

  <!-- Status Alerts -->
  <?php
  if (isset($_POST['biometrico_id'])) {
      $biometricoId = $_POST['biometrico_id'];
      $empleadoId = validarBiometrico($biometricoId);
      if ($empleadoId) {
          registrarAsistencia($empleadoId, $biometricoId);
          echo '<div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center gap-3 text-emerald-800 shadow-sm"><div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center"><i class="fa-solid fa-check text-emerald-600"></i></div> <div><strong class="font-semibold">¡Éxito!</strong> Asistencia registrada para empleado ID: '.$empleadoId.'</div></div>';
      } else {
          echo '<div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-100 flex items-center gap-3 text-rose-800 shadow-sm"><div class="w-8 h-8 rounded-full bg-rose-100 flex items-center justify-center"><i class="fa-solid fa-triangle-exclamation text-rose-600"></i></div> <div><strong class="font-semibold">Error.</strong> Datos biométricos no válidos.</div></div>';
      }
  }
  ?>

  <!-- Tables Section -->
  <div id="contenedor-tablas-asistencia" class="grid grid-cols-1 2xl:grid-cols-12 gap-8 pb-10">
    <!-- Table Asistencias -->
    <div class="2xl:col-span-7 bg-white rounded-3xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100 overflow-hidden flex flex-col">
      <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-white">
        <h5 class="text-lg font-bold text-slate-800 m-0 flex items-center gap-2">
          <div class="p-1.5 bg-red-50 text-red-600 rounded-lg"><i class="fa-solid fa-clock-rotate-left"></i></div>
          Asistencias
        </h5>
      </div>
      <div class="p-0 overflow-x-auto overflow-y-auto max-h-[450px]">
        <table class="w-full text-sm text-left text-slate-500 whitespace-nowrap relative">
          <thead class="text-xs text-slate-400 uppercase bg-slate-50/95 sticky top-0 z-10 shadow-sm backdrop-blur-sm">
            <tr>
              <th class="px-4 py-4 font-semibold tracking-wider bg-slate-50 text-center w-14">ID</th>
              <th class="px-6 py-4 font-semibold tracking-wider bg-slate-50">Empleado</th>
              <th class="px-6 py-4 font-semibold tracking-wider bg-slate-50">Cédula</th>
              <th class="px-6 py-4 font-semibold tracking-wider bg-slate-50">Entrada</th>
              <th class="px-6 py-4 font-semibold tracking-wider bg-slate-50">Cargo</th>
              <th class="px-6 py-4 font-semibold tracking-wider bg-slate-50">Salida</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <?php
            // Se modificó para:
            // 1. Mostrar sólo los registros del día actual (DATE(asistencia.entrada) = CURDATE())
            // 2. Mostrar incluso si no tienen salida (asistencia.salida puede ser NULL)
            // 3. Así evitas que la lista se llene de registros históricos repetidos de los empleados.
            $sqlAsistencias = $conexion->query("SELECT asistencia.id_asistencia, asistencia.id_empleado, asistencia.entrada, asistencia.salida, empleado.id_empleado, empleado.nombre as 'nom_empleado', empleado.apellido, empleado.dni, empleado.cargo, empleado.foto, cargo.id_cargo, cargo.nombre as 'nom_cargo' FROM asistencia INNER JOIN empleado ON asistencia.id_empleado = empleado.id_empleado INNER JOIN cargo ON empleado.cargo = cargo.id_cargo WHERE asistencia.entrada IS NOT NULL AND DATE(asistencia.entrada) = CURDATE() ORDER BY asistencia.entrada DESC LIMIT 15");
            if ($sqlAsistencias && $sqlAsistencias->num_rows > 0): 
                while ($datos = $sqlAsistencias->fetch_object()): ?>
                <tr class="hover:bg-slate-50/80 transition-colors group">
                  <td class="px-4 py-4 text-center">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-700 font-extrabold text-xs border border-indigo-100" title="ID Biométrico"><?= $datos->id_empleado ?></span>
                  </td>
                  <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                      <?php if (!empty($datos->foto)) { ?>
                          <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center shrink-0 overflow-hidden shadow-sm border border-slate-200 group-hover:ring-2 group-hover:ring-emerald-200 transition-all">
                              <img src="../img/empleados/<?= $datos->foto ?>" alt="Foto" class="w-full h-full object-cover">
                          </div>
                      <?php } else { ?>
                          <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center font-bold text-lg group-hover:bg-red-100 group-hover:text-red-600 transition-colors shrink-0">
                            <?= substr($datos->nom_empleado, 0, 1) ?>
                          </div>
                      <?php } ?>
                      <div class="flex flex-col">
                        <span class="font-bold text-slate-800"><?= $datos->nom_empleado . " " . $datos->apellido ?></span>
                        <span class="text-xs text-slate-400 font-medium"><?= $datos->nom_cargo ?></span>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 font-medium text-slate-600"><?= $datos->dni ?></td>
                  <td class="px-6 py-4">
                    <div class="flex flex-col gap-1">
                        <?php
                            $minutos_entrada = date('H:i', strtotime($datos->entrada));
                            $tiempo_entrada = strtotime($minutos_entrada);
                            $hora_12 = date('h:i A', strtotime($datos->entrada));
                            
                            // Determinar turno asumiendo que antes de mediodía es turno mañana (8am) y después es turno tarde (3pm)
                            if ($tiempo_entrada < strtotime('12:00')) {
                                $limite = strtotime('08:00'); 
                                $etiqueta_turno = "Turno M.";
                            } else {
                                $limite = strtotime('16:00');
                                $etiqueta_turno = "Turno T.";
                            }

                            if ($tiempo_entrada <= $limite) {
                                $estado_texto = "A tiempo";
                                $estado_clase = "text-emerald-700 bg-emerald-100 border border-emerald-200";
                            } else {
                                $estado_texto = "Tarde";
                                $estado_clase = "text-amber-700 bg-amber-100 border border-amber-200";
                            }
                        ?>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100 w-fit">
                              <i class="fa-solid fa-arrow-right-to-bracket mr-1"></i> <?= $hora_12 ?>
                            </span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold tracking-wide <?= $estado_clase ?>">
                                <?= $estado_texto ?> (<?= $etiqueta_turno ?>)
                            </span>
                        </div>
                        <span class="text-[11px] text-slate-400 font-medium"><?= date('d M, Y', strtotime($datos->entrada)) ?></span>
                    </div>
                  </td>
                  <td class="px-6 py-4 font-medium text-slate-600 capitalize">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                      <i class="fa-solid fa-briefcase mr-1.5 opacity-70"></i> <?= $datos->nom_cargo ?>
                    </span>
                  </td>
                  <td class="px-6 py-4">
                    <?php if (!empty($datos->salida)): ?>
                    <div class="flex flex-col gap-1">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-rose-50 text-rose-700 border border-rose-100 w-fit">
                          <i class="fa-solid fa-arrow-right-from-bracket mr-1"></i> <?= date('h:i A', strtotime($datos->salida)) ?>
                        </span>
                        <span class="text-[11px] text-slate-400 font-medium"><?= date('d M, Y', strtotime($datos->salida)) ?></span>
                    </div>
                    <?php else: ?>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-slate-100 text-slate-500 border border-slate-200 shadow-inner">
                        <i class="fa-solid fa-spinner fa-spin mr-1.5 opacity-70"></i> En Turno
                    </span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" class="text-center py-12">
                  <div class="flex flex-col items-center justify-center text-slate-400">
                    <i class="fa-regular fa-calendar-xmark text-4xl mb-3 text-slate-300"></i>
                    <p class="font-medium">No hay registros de asistencias disponibles.</p>
                  </div>
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Table Inasistencias -->
    <div class="2xl:col-span-5 bg-white rounded-3xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100 overflow-hidden flex flex-col">
      <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-white">
        <h5 class="text-lg font-bold text-slate-800 m-0 flex items-center gap-2">
          <div class="p-1.5 bg-rose-50 text-rose-600 rounded-lg"><i class="fa-solid fa-user-xmark"></i></div>
          Inasistencias
        </h5>
      </div>
      <div class="p-0 overflow-x-auto overflow-y-auto max-h-[450px]">
        <table class="w-full text-sm text-left text-slate-500 whitespace-nowrap relative">
          <thead class="text-xs text-slate-400 uppercase bg-slate-50/95 sticky top-0 z-10 shadow-sm backdrop-blur-sm">
            <tr>
              <th class="px-4 py-4 font-semibold tracking-wider bg-slate-50 text-center w-14">ID</th>
              <th class="px-6 py-4 font-semibold tracking-wider bg-slate-50">Empleado</th>
              <th class="px-6 py-4 font-semibold tracking-wider bg-slate-50">Cédula</th>
              <th class="px-6 py-4 font-semibold tracking-wider bg-slate-50">Estado</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <?php
            // Se modificó la consulta de Inasistencias para:
            // 1. Mostrar como inasistentes sólo a los empleados que no tengan NINGÚN registro de entrada el día de HOY.
            // 2. Extraer el estado guardado en justificacion_inasistencia o si tiene un permiso aprobado en la tabla permisos (este último tiene prioridad).
            $sqlInasistencias = $conexion->query("SELECT empleado.id_empleado, empleado.nombre as 'nom_empleado', empleado.apellido, empleado.dni, empleado.cargo, empleado.foto, empleado.estado as estado_empleado, cargo.id_cargo, cargo.nombre as 'nom_cargo', CASE WHEN permisos.id_permiso IS NOT NULL THEN CASE permisos.tipo WHEN 'Vacaciones' THEN 'vacaciones' WHEN 'Permiso Médico' THEN 'reposo' WHEN 'Asunto Personal' THEN 'permiso' ELSE 'permiso' END WHEN justificacion_inasistencia.estado IS NOT NULL THEN justificacion_inasistencia.estado ELSE 'falta' END as justificacion_estado, IF(permisos.id_permiso IS NOT NULL, 1, 0) as tiene_permiso FROM empleado INNER JOIN cargo ON empleado.cargo = cargo.id_cargo LEFT JOIN justificacion_inasistencia ON empleado.id_empleado = justificacion_inasistencia.id_empleado AND justificacion_inasistencia.fecha = CURDATE() LEFT JOIN permisos ON empleado.id_empleado = permisos.id_empleado AND CURDATE() BETWEEN permisos.fecha_inicio AND permisos.fecha_fin AND permisos.estado = 'Aprobado' WHERE empleado.id_empleado NOT IN ( SELECT id_empleado FROM asistencia WHERE DATE(entrada) = CURDATE() ) LIMIT 15");
            if ($sqlInasistencias && $sqlInasistencias->num_rows > 0): 
                while ($datos = $sqlInasistencias->fetch_object()): ?>
                <tr class="hover:bg-slate-50/80 transition-colors group">
                  <td class="px-4 py-4 text-center">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-700 font-extrabold text-xs border border-indigo-100" title="ID Biométrico"><?= $datos->id_empleado ?></span>
                  </td>
                  <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                      <?php if (!empty($datos->foto)) { ?>
                          <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center shrink-0 overflow-hidden shadow-sm border border-slate-200 group-hover:ring-2 group-hover:ring-rose-200 transition-all">
                              <img src="../img/empleados/<?= $datos->foto ?>" alt="Foto" class="w-full h-full object-cover">
                          </div>
                      <?php } else { ?>
                          <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center font-bold text-lg group-hover:bg-rose-100 group-hover:text-rose-600 transition-colors shrink-0">
                            <?= substr($datos->nom_empleado, 0, 1) ?>
                          </div>
                      <?php } ?>
                      <div class="flex flex-col">
                        <span class="font-bold text-slate-800"><?= $datos->nom_empleado . " " . $datos->apellido ?></span>
                        <span class="text-xs text-slate-400 font-medium"><?= $datos->nom_cargo ?></span>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 font-medium text-slate-600"><?= $datos->dni ?></td>
                  <td class="px-6 py-4">
                    <?php 
                    $estado_laboral = $datos->estado_empleado;
                    $estado_justificacion = $datos->justificacion_estado;
                    $tiene_permiso = isset($datos->tiene_permiso) && $datos->tiene_permiso == 1;
                    $color_bg = 'bg-rose-100';
                    $color_text = 'text-rose-800';
                    $color_border = 'border-rose-200';
                    $color_dot = 'bg-rose-600';
                    $label = 'Falta';

                    if ($estado_justificacion === 'reposo') {
                        $color_bg = 'bg-amber-100';
                        $color_text = 'text-amber-800';
                        $color_border = 'border-amber-200';
                        $color_dot = 'bg-amber-600';
                        $label = 'Reposo';
                    } elseif ($estado_justificacion === 'vacaciones') {
                        $color_bg = 'bg-blue-100';
                        $color_text = 'text-blue-800';
                        $color_border = 'border-blue-200';
                        $color_dot = 'bg-blue-600';
                        $label = 'Vacaciones';
                    } elseif ($estado_justificacion === 'permiso') {
                        $color_bg = 'bg-purple-100';
                        $color_text = 'text-purple-800';
                        $color_border = 'border-purple-200';
                        $color_dot = 'bg-purple-600';
                        $label = 'Permiso';
                    }

                    $is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
                    ?>
                    
                    <?php if ($estado_laboral === 'Suspendido'): ?>
                        <span class="inline-flex items-center px-3 py-1.5 rounded-md text-[11px] font-black tracking-wider uppercase bg-slate-800 text-white border border-slate-700 shadow-sm shadow-slate-400/30 w-[120px] justify-center">
                            <i class="fa-solid fa-ban mr-2 text-rose-500"></i> SUSPENDIDO
                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-bold <?= $color_bg ?> <?= $color_text ?> border <?= $color_border ?> <?= $tiene_permiso ? 'shadow-sm' : '' ?>">
                            <div class="w-1.5 h-1.5 rounded-full <?= $color_dot ?> mr-2"></div>
                            <?= $label ?>
                            <?php if ($tiene_permiso): ?>
                                <i class="fa-solid fa-lock text-[9px] ml-2 opacity-50" title="Permiso Aprobado por Sistema"></i>
                            <?php endif; ?>
                        </span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="3" class="text-center py-12">
                  <div class="flex flex-col items-center justify-center text-slate-400">
                    <i class="fa-solid fa-users-viewfinder text-4xl mb-3 text-slate-300"></i>
                    <p class="font-medium">No hay registros de inasistencias.</p>
                  </div>
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
function actualizarJustificacion(idEmpleado, selectElement) {
    const estado = selectElement.value;
    const formData = new FormData();
    formData.append('id_empleado', idEmpleado);
    formData.append('estado', estado);
    
    const wrapper = selectElement.parentElement;
    const iconWrapper = wrapper.querySelector('div.pointer-events-none');

    // Quitar todos los colores del wrapper
    wrapper.classList.remove('bg-rose-100', 'border-rose-200', 'bg-amber-100', 'border-amber-200', 'bg-blue-100', 'border-blue-200', 'bg-purple-100', 'border-purple-200');
    
    // Quitar color de texto del select y del icono
    selectElement.classList.remove('text-rose-800', 'text-amber-800', 'text-blue-800', 'text-purple-800');
    if (iconWrapper) iconWrapper.classList.remove('text-rose-800', 'text-amber-800', 'text-blue-800', 'text-purple-800');
    
    // Aplicar los nuevos colores según la selección
    if (estado === 'falta') {
        wrapper.classList.add('bg-rose-100', 'border-rose-200');
        selectElement.classList.add('text-rose-800');
        if (iconWrapper) iconWrapper.classList.add('text-rose-800');
    } else if (estado === 'reposo') {
        wrapper.classList.add('bg-amber-100', 'border-amber-200');
        selectElement.classList.add('text-amber-800');
        if (iconWrapper) iconWrapper.classList.add('text-amber-800');
    } else if (estado === 'vacaciones') {
        wrapper.classList.add('bg-blue-100', 'border-blue-200');
        selectElement.classList.add('text-blue-800');
        if (iconWrapper) iconWrapper.classList.add('text-blue-800');
    } else if (estado === 'permiso') {
        wrapper.classList.add('bg-purple-100', 'border-purple-200');
        selectElement.classList.add('text-purple-800');
        if (iconWrapper) iconWrapper.classList.add('text-purple-800');
    }

    fetch('../controlador/guardar_justificacion.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Guardado silencioso, el color ya cambió
        } else {
            console.error('Error al actualizar justificación:', data.message);
            alert('Error al actualizar el estado: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error en la petición:', error);
    });
}
</script>
<!-- Modal Más Reportes -->
<div class="modal fade" id="modalReportes" tabindex="-1" aria-labelledby="modalReportesLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-2xl rounded-3xl overflow-hidden bg-white">
      <div class="bg-gradient-to-r from-emerald-500 to-teal-500 p-6 flex items-center justify-between">
        <h5 class="text-white font-bold text-lg flex items-center gap-3 m-0" id="modalReportesLabel">
          <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-sm"><i class="fa-solid fa-file-invoice"></i></div>
          Generar Reportes de Asistencia
        </h5>
        <button type="button" class="btn-close btn-close-white opacity-80 hover:opacity-100 transition-opacity" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formReportesInicio" action="fpdf/ReporteAsistenciaFecha.php" method="GET" target="_blank" class="m-0">
        <div class="p-8 bg-white grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <div class="md:col-span-2 mb-2">
              <label class="block text-sm font-bold text-slate-700 mb-2"><i class="fa-solid fa-file-invoice text-emerald-500 mr-1.5"></i>Tipo de Reporte</label>
              <select id="tipoReporteInicio" onchange="cambiarTipoReporteInicio()" class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl px-4 py-3 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all outline-none cursor-pointer">
                <option value="diario">Asistencias por Rango / Diario</option>
                <option value="mensual">Mensual Consolidado (Faltas y Amonestaciones)</option>
              </select>
            </div>

            <div id="camposMensual" class="grid grid-cols-1 md:grid-cols-2 gap-6 md:col-span-2 hidden">
                <div>
                  <label class="block text-sm font-bold text-slate-700 mb-2"><i class="fa-solid fa-calendar text-emerald-500 mr-1.5"></i>Mes</label>
                  <select name="mes" class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl px-4 py-3 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all outline-none cursor-pointer">
                     <?php
                     $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                     foreach ($meses as $idx => $m) {
                         $val = $idx + 1;
                         $sel = ($val == date('n')) ? 'selected' : '';
                         echo "<option value=\"$val\" $sel>$m</option>";
                     }
                     ?>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-bold text-slate-700 mb-2"><i class="fa-solid fa-calendar-day text-emerald-500 mr-1.5"></i>Año</label>
                  <input type="number" name="anio" value="<?= date('Y') ?>" min="2020" max="<?= date('Y')+1 ?>" class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl px-4 py-3 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all outline-none">
                </div>
            </div>

            <div id="camposDiario" class="grid grid-cols-1 md:grid-cols-2 gap-6 md:col-span-2">
                <div>
                  <label class="block text-sm font-bold text-slate-700 mb-2"><i class="fa-solid fa-calendar-day text-emerald-500 mr-1.5"></i>Fecha inicio</label>
                  <input type="date" name="fecha_inicio" class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl px-4 py-3 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all outline-none" style="color-scheme: light dark;">
                </div>
                
                <div>
                  <label class="block text-sm font-bold text-slate-700 mb-2"><i class="fa-solid fa-calendar-check text-emerald-500 mr-1.5"></i>Fecha fin</label>
                  <input type="date" name="fecha_fin" class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl px-4 py-3 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all outline-none" style="color-scheme: light dark;">
                </div>
                
                <div>
                  <label class="block text-sm font-bold text-slate-700 mb-2"><i class="fa-solid fa-users text-amber-500 mr-1.5"></i>Empleado</label>
                  <select id="selectEmpleadoInicio" name="empleado" onchange="cambiarRutaReporteInicio()" class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl px-4 py-3 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all outline-none cursor-pointer">
                    <option value="todos">Todos los empleados</option>
                    <?php
                    $empleados = $conexion->query("SELECT id_empleado, nombre, apellido FROM empleado");
                    while($emp = $empleados->fetch_object()) {
                      echo '<option value="'.$emp->id_empleado.'">'.htmlspecialchars($emp->nombre.' '.$emp->apellido).'</option>';
                    }
                    ?>
                  </select>
                </div>
                
                <div>
                  <label class="block text-sm font-bold text-slate-700 mb-2"><i class="fa-solid fa-filter text-purple-500 mr-1.5"></i>Tipo de Registro <span class="text-[10px] font-normal text-slate-400">(Solo individual)</span></label>
                  <select id="selectFiltroTemporalInicio" disabled onchange="aplicarFiltroTemporalInicio()" class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl px-4 py-3 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all outline-none cursor-not-allowed opacity-60">
                    <option value="manual">Rango de fechas manual</option>
                    <option value="mensual">Histórico Mensual (Este mes)</option>
                    <option value="hoy">Individual (Día de hoy)</option>
                  </select>
                </div>
                
                <div>
                  <label class="block text-sm font-bold text-slate-700 mb-2"><i class="fa-solid fa-briefcase text-blue-500 mr-1.5"></i>Cargo</label>
                  <select name="cargo" class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl px-4 py-3 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all outline-none cursor-pointer">
                    <option value="todos">Todos los cargos</option>
                    <?php
                    $cargos = $conexion->query("SELECT id_cargo, nombre FROM cargo");
                    while($cargo = $cargos->fetch_object()) {
                      echo '<option value="'.$cargo->id_cargo.'">'.htmlspecialchars($cargo->nombre).'</option>';
                    }
                    ?>
                  </select>
                </div>
                
                <div>
                  <label class="block text-sm font-bold text-slate-700 mb-2"><i class="fa-solid fa-clipboard-check text-indigo-500 mr-1.5"></i>Tipo de asistencia</label>
                  <select name="tipo" class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl px-4 py-3 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all outline-none cursor-pointer">
                    <option value="todas">Todas</option>
                    <option value="entrada">Solo entradas</option>
                    <option value="salida">Solo salidas</option>
                    <option value="falta">Faltas</option>
                    <option value="retardo">Retardos</option>
                  </select>
                </div>
                
                <div>
                  <label class="block text-sm font-bold text-slate-700 mb-2"><i class="fa-solid fa-clock text-rose-500 mr-1.5"></i>Turno</label>
                  <select name="turno" class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl px-4 py-3 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all outline-none cursor-pointer">
                    <option value="todos">Todos los turnos</option>
                    <option value="mañana">Turno 1 (Mañana)</option>
                    <option value="noche">Turno 2 (Noche)</option>
                  </select>
                </div>
            </div>
        <div class="flex justify-end gap-3 px-8 pb-8 pt-4 bg-white border-t border-slate-100">
          <button type="button" class="px-6 py-3 rounded-xl font-bold text-slate-500 bg-slate-100 hover:bg-slate-200 transition-colors" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" onclick="if(window.addNotification) window.addNotification('info', 'Generando Reporte de Asistencias PDF...');" class="px-6 py-3 rounded-xl font-bold text-white bg-emerald-500 hover:bg-emerald-600 transition-colors shadow-lg shadow-emerald-500/30 flex items-center gap-2">
            <i class="fa-solid fa-file-pdf"></i> Generar Reporte
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<script src="/login_register12/public/vendor/dist/sweetalert2/sweetalert2.all.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
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

function cambiarTipoReporteInicio() {
    var tipo = document.getElementById('tipoReporteInicio').value;
    var form = document.getElementById('formReportesInicio');
    var divDiario = document.getElementById('camposDiario');
    var divMensual = document.getElementById('camposMensual');
    
    if (tipo === 'mensual') {
        divDiario.classList.add('hidden');
        divMensual.classList.remove('hidden');
        form.action = "fpdf/ReporteMensualConsolidado.php";
    } else {
        divDiario.classList.remove('hidden');
        divMensual.classList.add('hidden');
        // Let the other logic decide if it's per employee or global
        if(typeof cambiarRutaReporteInicio === 'function') {
            cambiarRutaReporteInicio();
        } else {
            form.action = "fpdf/ReporteAsistenciaFecha.php";
        }
    }
}

// ========== POLLING OPTIMIZADO CON AUTO-RECARGA ==========
(function() {
    let lastDataHash = null;

    async function checkUpdates() {
        try {
            const container = document.getElementById('contenedor-tablas-asistencia');
            if (!container) return;

            // Sincronización biométrica silenciosa
            fetch('/login_register12/controlador/integracion_biometrico.php').catch(() => {});

            const response = await fetch('/login_register12/controlador/api_asistencias.php');
            const data = await response.json();

            if (data.status === 'success') {
                // Crear un hash o string simple para comparar si hubo cambios
                const currentHash = JSON.stringify(data.asistencias) + JSON.stringify(data.inasistencias);
                
                if (lastDataHash === null) {
                    // Primera carga, solo guardamos el estado
                    lastDataHash = currentHash;
                } else if (lastDataHash !== currentHash) {
                    // ¡Se detectó un cambio real en la BD! 
                    // Recargamos la página automáticamente para que se vean todos los cambios.
                    window.location.reload();
                }
            }
        } catch (error) {
            console.error('Error verificando actualizaciones:', error);
        }
    }

    // Verificar cada 5 segundos
    setInterval(checkUpdates, 5000);

    function renderAsistencias(asistencias, container) {
        const tbody = container.querySelector('.2xl\\:col-span-7 tbody');
        if (!tbody) return;

        if (asistencias.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center py-12">
                <div class="flex flex-col items-center justify-center text-slate-400">
                    <i class="fa-regular fa-calendar-xmark text-4xl mb-3 text-slate-300"></i>
                    <p class="font-medium">No hay registros de asistencias disponibles.</p>
                </div></td></tr>`;
            return;
        }

        let html = '';
        asistencias.forEach(d => {
            const inicial = d.nom_empleado.charAt(0);
            const horaEntrada = new Date('2000-01-01 ' + d.entrada.split(' ')[1]);
            const hora12 = horaEntrada.toLocaleTimeString('en-US', {hour: '2-digit', minute: '2-digit', hour12: true});
            const fechaEntrada = formatFecha(d.entrada);
            
            // Determinar turno y estado
            const horas = horaEntrada.getHours();
            const mins = horaEntrada.getMinutes();
            const totalMin = horas * 60 + mins;
            let estadoTexto, estadoClase, etiquetaTurno;
            
            if (totalMin < 720) { // antes de mediodía
                estadoTexto = totalMin <= 480 ? 'A tiempo' : 'Tarde'; // 8:00 = 480 min
                estadoClase = totalMin <= 480 ? 'text-emerald-700 bg-emerald-100 border border-emerald-200' : 'text-amber-700 bg-amber-100 border border-amber-200';
                etiquetaTurno = 'Turno M.';
            } else {
                estadoTexto = totalMin <= 960 ? 'A tiempo' : 'Tarde'; // 16:00 = 960 min
                estadoClase = totalMin <= 960 ? 'text-emerald-700 bg-emerald-100 border border-emerald-200' : 'text-amber-700 bg-amber-100 border border-amber-200';
                etiquetaTurno = 'Turno T.';
            }

            const fotoHTML = d.foto 
                ? `<div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center shrink-0 overflow-hidden shadow-sm border border-slate-200 group-hover:ring-2 group-hover:ring-emerald-200 transition-all"><img src="../img/empleados/${d.foto}" alt="Foto" class="w-full h-full object-cover"></div>`
                : `<div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center font-bold text-lg group-hover:bg-red-100 group-hover:text-red-600 transition-colors shrink-0">${inicial}</div>`;

            let salidaHTML;
            if (d.salida) {
                const horaSalida = new Date('2000-01-01 ' + d.salida.split(' ')[1]);
                const salida12 = horaSalida.toLocaleTimeString('en-US', {hour: '2-digit', minute: '2-digit', hour12: true});
                salidaHTML = `<div class="flex flex-col gap-1">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-rose-50 text-rose-700 border border-rose-100 w-fit">
                        <i class="fa-solid fa-arrow-right-from-bracket mr-1"></i> ${salida12}
                    </span>
                    <span class="text-[11px] text-slate-400 font-medium">${formatFecha(d.salida)}</span>
                </div>`;
            } else {
                salidaHTML = `<span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-slate-100 text-slate-500 border border-slate-200 shadow-inner">
                    <i class="fa-solid fa-spinner fa-spin mr-1.5 opacity-70"></i> En Turno
                </span>`;
            }

            html += `<tr class="hover:bg-slate-50/80 transition-colors group">
                <td class="px-4 py-4 text-center">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-700 font-extrabold text-xs border border-indigo-100" title="ID Biométrico">${d.id_empleado}</span>
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        ${fotoHTML}
                        <div class="flex flex-col">
                            <span class="font-bold text-slate-800">${d.nom_empleado} ${d.apellido}</span>
                            <span class="text-xs text-slate-400 font-medium">${d.nom_cargo}</span>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 font-medium text-slate-600">${d.dni}</td>
                <td class="px-6 py-4">
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100 w-fit">
                                <i class="fa-solid fa-arrow-right-to-bracket mr-1"></i> ${hora12}
                            </span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold tracking-wide ${estadoClase}">
                                ${estadoTexto} (${etiquetaTurno})
                            </span>
                        </div>
                        <span class="text-[11px] text-slate-400 font-medium">${fechaEntrada}</span>
                    </div>
                </td>
                <td class="px-6 py-4 font-medium text-slate-600 capitalize">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                        <i class="fa-solid fa-briefcase mr-1.5 opacity-70"></i> ${d.nom_cargo}
                    </span>
                </td>
                <td class="px-6 py-4">${salidaHTML}</td>
            </tr>`;
        });
        tbody.innerHTML = html;
    }

    function renderInasistencias(inasistencias, isAdmin, container) {
        const tbody = container.querySelector('.2xl\\:col-span-5 tbody');
        if (!tbody) return;

        if (inasistencias.length === 0) {
            tbody.innerHTML = `<tr><td colspan="4" class="text-center py-12">
                <div class="flex flex-col items-center justify-center text-slate-400">
                    <i class="fa-solid fa-users-viewfinder text-4xl mb-3 text-slate-300"></i>
                    <p class="font-medium">No hay registros de inasistencias.</p>
                </div></td></tr>`;
            return;
        }

        let html = '';
        inasistencias.forEach(d => {
            const inicial = d.nom_empleado.charAt(0);
            const est = d.justificacion_estado;
            const estEmpleado = d.estado_empleado;
            const tienePermiso = parseInt(d.tiene_permiso) === 1;

            const fotoHTML = d.foto 
                ? `<div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center shrink-0 overflow-hidden shadow-sm border border-slate-200 group-hover:ring-2 group-hover:ring-rose-200 transition-all"><img src="../img/empleados/${d.foto}" alt="Foto" class="w-full h-full object-cover"></div>`
                : `<div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center font-bold text-lg group-hover:bg-rose-100 group-hover:text-rose-600 transition-colors shrink-0">${inicial}</div>`;

            let estadoHTML;
            if (estEmpleado === 'Suspendido') {
                estadoHTML = `<span class="inline-flex items-center px-3 py-1.5 rounded-md text-[11px] font-black tracking-wider uppercase bg-slate-800 text-white border border-slate-700 shadow-sm shadow-slate-400/30 w-[120px] justify-center">
                    <i class="fa-solid fa-ban mr-2 text-rose-500"></i> SUSPENDIDO
                </span>`;
            } else {
                const colorMap = {
                    falta: {bg: 'bg-rose-100', text: 'text-rose-800', border: 'border-rose-200', dot: 'bg-rose-600', label: 'Falta'},
                    reposo: {bg: 'bg-amber-100', text: 'text-amber-800', border: 'border-amber-200', dot: 'bg-amber-600', label: 'Reposo'},
                    vacaciones: {bg: 'bg-blue-100', text: 'text-blue-800', border: 'border-blue-200', dot: 'bg-blue-600', label: 'Vacaciones'},
                    permiso: {bg: 'bg-purple-100', text: 'text-purple-800', border: 'border-purple-200', dot: 'bg-purple-600', label: 'Permiso'}
                };
                const c = colorMap[est] || colorMap.falta;
                estadoHTML = `<span class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-bold ${c.bg} ${c.text} border ${c.border} ${tienePermiso ? 'shadow-sm' : ''}">
                    <div class="w-1.5 h-1.5 rounded-full ${c.dot} mr-2"></div>${c.label}
                    ${tienePermiso ? '<i class="fa-solid fa-lock text-[9px] ml-2 opacity-50" title="Permiso Aprobado por Sistema"></i>' : ''}
                </span>`;
            }

            html += `<tr class="hover:bg-slate-50/80 transition-colors group">
                <td class="px-4 py-4 text-center">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-700 font-extrabold text-xs border border-indigo-100" title="ID Biométrico">${d.id_empleado}</span>
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        ${fotoHTML}
                        <div class="flex flex-col">
                            <span class="font-bold text-slate-800">${d.nom_empleado} ${d.apellido}</span>
                            <span class="text-xs text-slate-400 font-medium">${d.nom_cargo}</span>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 font-medium text-slate-600">${d.dni}</td>
                <td class="px-6 py-4">${estadoHTML}</td>
            </tr>`;
        });
        tbody.innerHTML = html;
    }

    function formatFecha(datetime) {
        const d = new Date(datetime);
        const meses = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
        return `${String(d.getDate()).padStart(2,'0')} ${meses[d.getMonth()]}, ${d.getFullYear()}`;
    }

})();

function cambiarRutaReporteInicio() {
    var select = document.getElementById("selectEmpleadoInicio");
    var form = document.getElementById("formReportesInicio");
    var filtroTemporal = document.getElementById("selectFiltroTemporalInicio");
    
    if (select.value !== "" && select.value !== "todos") {
        form.action = "fpdf/ReporteAsistenciaEmpleado.php";
        // Habilitar filtro temporal
        filtroTemporal.disabled = false;
        filtroTemporal.classList.remove("cursor-not-allowed", "opacity-60");
        filtroTemporal.classList.add("cursor-pointer");
    } else {
        form.action = "fpdf/ReporteAsistenciaFecha.php";
        // Deshabilitar filtro temporal
        filtroTemporal.disabled = true;
        filtroTemporal.value = "manual";
        filtroTemporal.classList.add("cursor-not-allowed", "opacity-60");
        filtroTemporal.classList.remove("cursor-pointer");
    }
}

function aplicarFiltroTemporalInicio() {
    var filtro = document.getElementById("selectFiltroTemporalInicio").value;
    var form = document.getElementById("formReportesInicio");
    var inputInicio = form.querySelector('input[name="fecha_inicio"]');
    var inputFin = form.querySelector('input[name="fecha_fin"]');
    
    var hoy = new Date();
    
    if (filtro === "hoy") {
        var dd = String(hoy.getDate()).padStart(2, '0');
        var mm = String(hoy.getMonth() + 1).padStart(2, '0');
        var yyyy = hoy.getFullYear();
        var hoyStr = yyyy + '-' + mm + '-' + dd;
        
        inputInicio.value = hoyStr;
        inputFin.value = hoyStr;
    } else if (filtro === "mensual") {
        var primerDia = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
        var ultimoDia = new Date(hoy.getFullYear(), hoy.getMonth() + 1, 0);
        
        var d1 = String(primerDia.getDate()).padStart(2, '0');
        var m1 = String(primerDia.getMonth() + 1).padStart(2, '0');
        var y1 = primerDia.getFullYear();
        
        var d2 = String(ultimoDia.getDate()).padStart(2, '0');
        var m2 = String(ultimoDia.getMonth() + 1).padStart(2, '0');
        var y2 = ultimoDia.getFullYear();
        
        inputInicio.value = y1 + '-' + m1 + '-' + d1;
        inputFin.value = y2 + '-' + m2 + '-' + d2;
    }
}
</script>
<?php require('./layout/footer.php'); ?>
