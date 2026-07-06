<?php
session_start();
if (empty($_SESSION['email'])) {
    header("Location: /login_register12/index.php");
    exit();
}

// Opcional: Solo permitir a administradores
$is_admin = isset($_SESSION['login_success']['role']) && $_SESSION['login_success']['role'] === 'admin';
if (!$is_admin && !isset($_SESSION['role']) || (isset($_SESSION['role']) && $_SESSION['role'] !== 'admin')) {
    $_SESSION['mensaje'] = "Error: Acceso denegado. Solo administradores pueden ver el registro de auditoría.";
    header("Location: inicio.php");
    exit();
}
?>

<link href="/login_register12/public/vendor/dist/fonts/montserrat/index.css" rel="stylesheet">
<!-- luego se carga el sidebar -->
<?php require('./layout/sidebar.php'); ?>

<!-- inicio del contenido principal -->
<div class="px-4 py-8 md:px-8 bg-[#f8fafc] transition-colors duration-300 min-h-[calc(100vh-4rem)]">
    <div class="max-w-7xl mx-auto">
        
        <!-- Header Section -->
        <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex flex-col justify-start items-start gap-4">
                <h3 class="text-2xl md:text-3xl font-bold text-slate-800 flex items-center gap-3 transition-colors">
                    <div class="p-2.5 bg-slate-800 text-white rounded-xl shadow-lg shadow-slate-800/30">
                        <i class="fa-solid fa-clipboard-list"></i>
                    </div>
                    Registro de Auditoría
                </h3>
                <p class="text-slate-500 font-medium transition-colors">Historial completo de acciones y movimientos administrativos en el sistema.</p>
            </div>
            <div class="flex gap-3">
                <button onclick="window.location.reload()" class="bg-white text-slate-600 hover:bg-slate-50 font-bold py-3 px-6 rounded-full shadow-md transition-all duration-300 transform hover:-translate-y-1 flex items-center gap-2 border border-slate-200">
                    <i class="fa-solid fa-arrows-rotate"></i> Actualizar
                </button>
            </div>
        </div>

        <!-- Auditoria Table Card -->
        <div class="bg-white rounded-[2rem] shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100 overflow-hidden transition-colors duration-300">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-white transition-colors">
                <h5 class="text-lg font-bold text-slate-800 m-0 flex items-center gap-2 transition-colors">
                    <i class="fa-solid fa-clock-rotate-left text-slate-400"></i>
                    Logs del Sistema
                </h5>
            </div>

            <div class="p-6">
                <table class="table w-full text-sm text-left text-slate-500" id="example" style="width:100%">
                    <thead class="text-xs text-slate-400 uppercase bg-slate-50 font-semibold transition-colors">
                        <tr>
                            <th scope="col" class="px-4 py-4 rounded-l-xl">Fecha/Hora</th>
                            <th scope="col" class="px-4 py-4">Usuario</th>
                            <th scope="col" class="px-4 py-4">Acción</th>
                            <th scope="col" class="px-4 py-4">Módulo</th>
                            <th scope="col" class="px-4 py-4">Detalle</th>
                            <th scope="col" class="px-4 py-4 rounded-r-xl">IP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php
                        include "../modelo/conexion.php";
                        $sql = $conexion->query("SELECT * FROM auditoria ORDER BY fecha DESC");
                        while ($datos = $sql->fetch_object()) { 
                            $accionClase = "bg-slate-100 text-slate-600";
                            $accionIcon = "fa-info-circle";
                            
                            $accion_lower = strtolower($datos->accion);
                            if (strpos($accion_lower, 'eliminar') !== false || strpos($accion_lower, 'borrar') !== false) {
                                $accionClase = "bg-rose-100 text-rose-700";
                                $accionIcon = "fa-trash";
                            } elseif (strpos($accion_lower, 'crear') !== false || strpos($accion_lower, 'registrar') !== false) {
                                $accionClase = "bg-emerald-100 text-emerald-700";
                                $accionIcon = "fa-plus";
                            } elseif (strpos($accion_lower, 'modificar') !== false || strpos($accion_lower, 'actualizar') !== false) {
                                $accionClase = "bg-amber-100 text-amber-700";
                                $accionIcon = "fa-pen";
                            } elseif (strpos($accion_lower, 'login') !== false || strpos($accion_lower, 'acceso') !== false) {
                                $accionClase = "bg-indigo-100 text-indigo-700";
                                $accionIcon = "fa-right-to-bracket";
                            }
                        ?>
                            <tr class="hover:bg-slate-50/80 transition-colors group">
                                <td class="px-4 py-4">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-700"><?= date('d/m/Y', strtotime($datos->fecha)) ?></span>
                                        <span class="text-xs text-slate-400 font-medium"><i class="fa-regular fa-clock mr-1"></i><?= date('h:i:s A', strtotime($datos->fecha)) ?></span>
                                    </div>
                                </td>
                                <td class="px-4 py-4 font-bold text-slate-800">
                                    <?= $datos->usuario ?>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold <?= $accionClase ?>">
                                        <i class="fa-solid <?= $accionIcon ?> mr-1.5 opacity-70"></i> <?= $datos->accion ?>
                                    </span>
                                </td>
                                <td class="px-4 py-4 font-semibold text-slate-600 uppercase text-xs tracking-wider">
                                    <?= $datos->modulo ?>
                                </td>
                                <td class="px-4 py-4 text-slate-500 max-w-md truncate" title="<?= htmlspecialchars($datos->detalle) ?>">
                                    <?= htmlspecialchars($datos->detalle) ?>
                                </td>
                                <td class="px-4 py-4 text-slate-400 text-xs font-mono">
                                    <?= $datos->ip ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
<!-- fin del contenido principal -->

<!-- por ultimo se carga el footer -->
<?php require('./layout/footer.php'); ?>

