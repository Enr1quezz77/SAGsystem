<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Evitar que el navegador guarde en caché las páginas protegidas
// Esto impide que al presionar "Atrás" después de cerrar sesión se vea la página anterior
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies

// Validar que el usuario tenga sesión activa
if (empty($_SESSION['email'])) {
    header("Location: /login_register12/index.php");
    exit();
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>"S.A.G (DORE'S)"</title>
  <link rel="icon" type="image/png" href="../img/logo.png?v=<?= time() ?>">
  
  <script src="/login_register12/public/vendor/dist/lucide/lucide.min.js"></script>
  <link href="/login_register12/public/vendor/dist/fonts/dm-sans/index.css" rel="stylesheet">
  <link href="/login_register12/public/vendor/dist/fonts/poppins/index.css" rel="stylesheet">
  <link href="/login_register12/public/vendor/dist/fonts/playfair-display/index.css" rel="stylesheet">
  <script>
    tailwind = {
      theme: {
        extend: {}
      }
    }
  </script>
  <script src="/login_register12/public/vendor/dist/js/tailwindcss.js"></script>
  <!-- FontAwesome local -->
  <link rel="stylesheet" href="/login_register12/public/vendor/dist/fontawesome/all.min.css">
  
<!-- Bootstrap for modals -->
  <link href="../public/bootstrap5/css/bootstrap.min.css" rel="stylesheet">
  
  <style>
    /* Fix para ocultar los inputs fantasma de SweetAlert que Tailwind muestra por defecto */
    .swal2-container .swal2-input,
    .swal2-container .swal2-file,
    .swal2-container .swal2-textarea,
    .swal2-container .swal2-select,
    .swal2-container .swal2-radio,
    .swal2-container .swal2-checkbox,
    .swal2-container .swal2-validation-message {
      display: none !important;
    }
    .swal2-container .swal2-input[style*="display: flex"],
    .swal2-container .swal2-input[style*="display: block"] {
      display: flex !important; /* Por si en el futuro sí se usa un input intencionalmente */
    }
  </style>
  <!-- DataTables Core CSS -->
  <link rel="stylesheet" type="text/css" href="/login_register12/public/vendor/dist/datatables/dataTables.dataTables.min.css">
  <!-- DataTables Buttons CSS -->
  <link rel="stylesheet" type="text/css" href="/login_register12/public/vendor/dist/datatables/buttons.dataTables.min.css">
  
  <!-- Flatpickr for beautiful datepickers -->
  <link rel="stylesheet" href="/login_register12/public/vendor/dist/flatpickr/flatpickr.min.css">
  
  <!-- Tom Select for beautiful dropdowns -->
  <link href="/login_register12/public/vendor/dist/tom-select/tom-select.css" rel="stylesheet">
  <style>
    .ts-control {
        background-color: #f8fafc !important; 
        border: 1px solid #e2e8f0 !important; 
        border-radius: 0.75rem !important; 
        padding: 0.5rem 1rem !important; 
        min-height: 3rem !important;
        font-weight: 600 !important;
        color: #1e293b !important; 
        box-shadow: none !important;
        transition: all 0.2s ease;
    }
    .ts-control.focus {
        border-color: #10b981 !important; 
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.2) !important; 
    }
    .ts-dropdown {
        border-radius: 0.75rem !important;
        border: 1px solid #e2e8f0 !important;
        overflow: hidden;
        font-weight: 600;
        color: #475569 !important;
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1),0 4px 6px -2px rgba(0,0,0,0.05) !important;
        margin-top: 4px !important;
    }
    .ts-dropdown .option {
        padding: 0.5rem 1rem !important;
        transition: all 0.2s;
    }
    .ts-dropdown .option.active, .ts-dropdown .option:hover {
        background-color: #ecfdf5 !important; 
        color: #059669 !important; 
    }
    .ts-control input {
        font-weight: 600 !important;
        color: #1e293b !important;
    }
  </style>
  
  
  <style>
    :root { --primary: #dc2626; }
    html, body { height: 100%; }
    /* Anti-FOUC: ocultar hasta que Tailwind procese las clases */
    body { font-family: 'DM Sans', sans-serif; opacity: 0; transition: opacity 0.2s ease-in; }
    body.ready { opacity: 1; }
    .sidebar-item.active { 
        background-color: #fef2f2; 
        color: var(--primary) !important; 
        font-weight: 500;
        border-right: 3px solid var(--primary);
    }
    
    /* Custom scrollbar for sidebar */
    .sidebar-nav::-webkit-scrollbar { width: 6px; }
    .sidebar-nav::-webkit-scrollbar-track { background: transparent; }
    .sidebar-nav::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    .sidebar-nav::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
  </style>
  <script>
    // Revelar el body cuando Tailwind haya procesado (elimina el flash de contenido sin estilos)
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', function() { document.body.classList.add('ready'); });
    } else {
      document.addEventListener('DOMContentLoaded', function() { document.body.classList.add('ready'); });
    }
    // Fallback: si por algún motivo no se dispara, mostrar después de 500ms
    setTimeout(function() { if(document.body) document.body.classList.add('ready'); }, 500);
  </script>
</head>
<body class="flex h-screen overflow-hidden bg-[#f8fafc] transition-colors duration-300">

  <aside class="w-64 bg-white border-r border-gray-200 flex flex-col shrink-0 transition-colors duration-300">
    <div class="flex items-center gap-3 h-16 border-b border-gray-200 px-6 transition-colors duration-300">
      <img src="../img/logo.png?v=<?= time() ?>" alt="Dore's Logo" class="w-10 h-10 object-cover rounded-full shadow-sm border border-gray-100 ring-2 ring-red-50">
      <h1 class="text-xl font-bold text-gray-800 tracking-tight transition-colors duration-300" style="font-family: 'Poppins', sans-serif;">Dore's</h1>
    </div>
    
    <nav class="flex-1 space-y-2 p-4 overflow-y-auto sidebar-nav">
      <h3 class="px-3 text-xs text-gray-400 uppercase font-semibold">Inicio</h3>
      <a href="../vista/inicio.php" class="sidebar-item w-full flex items-center gap-3 py-2 px-3 rounded-lg text-gray-600 hover:bg-red-50 hover:text-red-600 transition-all">
        <i data-lucide="home" class="w-5 h-5"></i> Inicio
      </a>
      <a href="../vista/empleado.php" class="sidebar-item w-full flex items-center gap-3 py-2 px-3 rounded-lg text-gray-600 hover:bg-red-50 hover:text-red-600 transition-all">
        <i data-lucide="contact" class="w-5 h-5"></i> Empleados
      </a>
      <a href="../vista/visualizar_archivos.php" class="sidebar-item w-full flex items-center gap-3 py-2 px-3 rounded-lg text-gray-600 hover:bg-red-50 hover:text-red-600 transition-all">
        <i data-lucide="folder-open" class="w-5 h-5"></i> Archivos
      </a>
      <a href="../vista/destacado.php" class="sidebar-item w-full flex items-center gap-3 py-2 px-3 rounded-lg text-gray-600 hover:bg-red-50 hover:text-red-600 transition-all">
        <i data-lucide="star" class="w-5 h-5"></i> Destacado
      </a>
      <a href="../vista/amonestaciones.php" class="sidebar-item w-full flex items-center gap-3 py-2 px-3 rounded-lg text-gray-600 hover:bg-red-50 hover:text-red-600 transition-all">
        <i data-lucide="alert-triangle" class="w-5 h-5"></i> Amonestaciones
      </a>
      <a href="../vista/permisos.php" class="sidebar-item w-full flex items-center gap-3 py-2 px-3 rounded-lg text-gray-600 hover:bg-red-50 hover:text-red-600 transition-all">
        <i data-lucide="calendar-clock" class="w-5 h-5"></i> Permisos
      </a>
      
      <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
      <h3 class="px-3 text-xs text-gray-400 uppercase font-semibold pt-4">Admin</h3>
      <a href="../vista/usuario.php" class="sidebar-item w-full flex items-center gap-3 py-2 px-3 rounded-lg text-gray-600 hover:bg-red-50 hover:text-red-600 transition-all">
        <i data-lucide="users" class="w-5 h-5"></i> Usuarios
      </a>
      <a href="../vista/cuadre_cajas.php" class="sidebar-item w-full flex items-center gap-3 py-2 px-3 rounded-lg text-gray-600 hover:bg-red-50 hover:text-red-600 transition-all">
        <i data-lucide="calculator" class="w-5 h-5"></i> Cierre de Cajas
      </a>
      <a href="../vista/nomina.php" class="sidebar-item w-full flex items-center gap-3 py-2 px-3 rounded-lg text-gray-600 hover:bg-red-50 hover:text-red-600 transition-all">
        <i data-lucide="banknote" class="w-5 h-5"></i> Nómina
      </a>
      <a href="../vista/auditoria.php" class="sidebar-item w-full flex items-center gap-3 py-2 px-3 rounded-lg text-gray-600 hover:bg-red-50 hover:text-red-600 transition-all">
        <i data-lucide="clipboard-list" class="w-5 h-5"></i> Auditoría
      </a>
      <a href="../vista/configuracion.php" class="sidebar-item w-full flex items-center gap-3 py-2 px-3 rounded-lg text-gray-600 hover:bg-red-50 hover:text-red-600 transition-all">
        <i data-lucide="settings" class="w-5 h-5"></i> Configuración
      </a>
      
      <h3 class="px-3 text-xs text-gray-400 uppercase font-semibold pt-4">Base de Datos</h3>
      <a href="#" data-bs-toggle="modal" data-bs-target="#modalRestaurarBD" class="sidebar-item w-full flex items-center gap-3 py-2 px-3 rounded-lg text-gray-600 hover:bg-red-50 hover:text-red-600 transition-all">
        <i data-lucide="upload" class="w-5 h-5"></i> Restaurar BD
      </a>
      <a href="<?php echo '/login_register12/respaldo_bd.php'; ?>" target="_blank" onclick="if(window.addNotification) window.addNotification('info', 'Generando respaldo de la base de datos...');" class="sidebar-item w-full flex items-center gap-3 py-2 px-3 rounded-lg text-gray-600 hover:bg-red-50 hover:text-red-600 transition-all">
        <i data-lucide="database" class="w-5 h-5"></i> Respaldar BD
      </a>
      <?php endif; ?>
    </nav>

    <div class="p-4 mt-auto">
      <a href="../controlador/cerrar_sesion.php" class="sidebar-item w-full flex items-center gap-3 py-2 px-3 rounded-lg text-red-600 hover:bg-red-50 transition-all">
        <i data-lucide="log-out" class="w-5 h-5"></i> Cerrar sesión
      </a>
    </div>
  </aside>

  <div class="flex-1 flex flex-col min-w-0">
    <header class="h-16 flex items-center justify-between border-b bg-white border-gray-200 px-6 transition-colors duration-300">
        <h2 class="text-xl font-semibold text-gray-700 transition-colors duration-300">
            Panel de <?php echo (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ? 'Administrador' : 'Usuario'; ?>
        </h2>
        <div class="flex items-center gap-4">
            <!-- Indicador Biométrico -->
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-50 border border-slate-200 h-9 transition-colors duration-300" title="Estado del Biométrico">
                <div id="biometric-led" class="w-2.5 h-2.5 rounded-full bg-slate-400 shadow-[0_0_8px_rgba(148,163,184,0.6)] transition-colors duration-300"></div>
                <span id="biometric-text" class="text-xs font-semibold text-slate-600">Comprobando...</span>
            </div>

            <!-- Bandeja de Notificaciones -->
            <div class="relative">
                <button id="notification-btn" class="relative p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-full transition-colors h-9 w-9 flex justify-center items-center">
                    <i data-lucide="bell" class="w-4 h-4"></i>
                    <span id="notification-badge" class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 rounded-full hidden border border-white"></span>
                </button>
                
                <!-- Dropdown -->
                <div id="notification-dropdown" class="absolute right-0 mt-2 w-80 bg-white border border-gray-100 rounded-xl shadow-xl z-50 hidden opacity-0 transition-opacity duration-200 transform scale-95 origin-top-right">
                    <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-slate-50 rounded-t-xl">
                        <h3 class="text-sm font-bold text-gray-700 flex items-center gap-2"><i class="fa-solid fa-bell text-slate-400 text-xs"></i> Notificaciones</h3>
                        <span id="notification-count" class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-full font-bold">0</span>
                    </div>
                    <div id="notification-list" class="max-h-[300px] overflow-y-auto w-full p-2 space-y-1">
                        <div class="text-sm text-gray-400 p-4 text-center">No hay notificaciones nuevas</div>
                    </div>
                </div>
            </div>

            <?php 
                // Corregido: La variable correcta en login_register.php es $_SESSION['usuario']
                $nombre_usuario = $_SESSION['usuario'] ?? '';
                $empleado_o_usuario = 'Usuario';
                
                // Determinar el nivel exacto
                $rol_bd = $_SESSION['role'] ?? 'user';
                $rol_usuario = ($rol_bd === 'admin') ? 'Administrador' : 'Usuario';
            ?>
            <div class="text-sm border border-red-100 bg-red-50 text-red-800 px-4 py-1.5 rounded-full flex items-center gap-1 shadow-sm transition-colors duration-300">
                <span>Bienvenido,</span>
                <strong class="capitalize ml-1"><?php echo $nombre_usuario ? $nombre_usuario . " ($rol_usuario)" : $rol_usuario; ?></strong>
            </div>
        </div>
    </header>
    <main class="flex-1 overflow-y-auto">
        <!-- El contenido de la página específica se renderizará aquí -->
