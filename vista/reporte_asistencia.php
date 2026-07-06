<?php
   session_start();
   if (empty($_SESSION['email'])) {
       header("Location: /login_register12/index.php");
       exit();
   }

?>

<link href="/login_register12/public/vendor/dist/fonts/montserrat/index.css" rel="stylesheet">

<!-- primero se carga el topbar -->
<?php require('./layout/topbar.php'); ?>
<!-- luego se carga el sidebar -->
<?php require('./layout/sidebar.php'); ?>

<!-- inicio del contenido principal -->


<div class="px-4 py-8 md:px-8 bg-[#f8fafc] min-h-[calc(100vh-4rem)]">
    <div class="max-w-4xl mx-auto">
        
        <!-- Header Section -->
        <div class="mb-8 flex flex-col justify-start items-start gap-4">
            <h3 class="text-2xl md:text-3xl font-bold text-slate-800 flex items-center gap-3">
                <div class="p-2.5 bg-red-600 text-white rounded-xl shadow-lg shadow-red-600/30">
                    <i class="fa-solid fa-file-invoice"></i>
                </div>
                Reporte de Asistencias
            </h3>
            <p class="text-slate-500 font-medium">Genera un documento PDF estructurado con las asistencias de los empleados en un periodo determinado.</p>
        </div>

        <!-- Form Section -->
        <div class="bg-white rounded-[2rem] shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100 overflow-hidden p-8">
            <form id="formReporte" action="fpdf/ReporteAsistenciaFecha.php" target="_blank" method="GET" class="space-y-6">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Fecha Inicio -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Fecha Inicio</label>
                        <div class="relative">
                            <input required type="date" name="fecha_inicio" class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl px-4 py-3 pl-11 focus:ring-4 focus:ring-red-500/20 focus:border-red-500 transition-all outline-none">
                            <i class="fa-regular fa-calendar-days text-slate-400 absolute left-4 top-1/2 -translate-y-1/2"></i>
                        </div>
                    </div>
                    
                    <!-- Fecha Fin -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Fecha Final</label>
                        <div class="relative">
                            <input required type="date" name="fecha_fin" class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl px-4 py-3 pl-11 focus:ring-4 focus:ring-red-500/20 focus:border-red-500 transition-all outline-none">
                            <i class="fa-regular fa-calendar text-slate-400 absolute left-4 top-1/2 -translate-y-1/2"></i>
                        </div>
                    </div>
                </div>

                <!-- Empleado y Filtro Temporal -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Empleado</label>
                        <div class="relative">
                            <select id="selectEmpleado" name="empleado" onchange="cambiarRutaReporte()" class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl px-4 py-3 pl-11 focus:ring-4 focus:ring-red-500/20 focus:border-red-500 transition-all outline-none appearance-none cursor-pointer">
                                <option value="">Todos los empleados</option>
                                <?php
                                include "../modelo/conexion.php";
                                $sql_emp = $conexion->query("SELECT * FROM empleado");
                                while ($datos_emp = $sql_emp->fetch_object()) { ?>
                                    <option value="<?= $datos_emp->id_empleado ?>"><?= $datos_emp->nombre . " " . $datos_emp->apellido ?></option>
                                <?php } ?>
                            </select>
                            <i class="fa-solid fa-users text-slate-400 absolute left-4 top-[17px]"></i>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Tipo de Registro <span class="text-xs font-normal text-slate-400">(Solo individual)</span></label>
                        <div class="relative">
                            <select id="selectFiltroTemporal" disabled onchange="aplicarFiltroTemporal()" class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl px-4 py-3 pl-11 focus:ring-4 focus:ring-red-500/20 focus:border-red-500 transition-all outline-none appearance-none cursor-not-allowed opacity-60">
                                <option value="manual">Rango de fechas manual</option>
                                <option value="mensual">Histórico Mensual (Este mes)</option>
                                <option value="hoy">Individual (Día de hoy)</option>
                            </select>
                            <i class="fa-solid fa-filter text-slate-400 absolute left-4 top-[17px]"></i>
                        </div>
                    </div>
                </div>

                <!-- Cargo y Turno -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Cargo</label>
                        <div class="relative">
                            <select name="cargo" class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl px-4 py-3 pl-11 focus:ring-4 focus:ring-red-500/20 focus:border-red-500 transition-all outline-none appearance-none cursor-pointer">
                                <option value="">Todos los cargos</option>
                                <?php
                                $sql_cargo = $conexion->query("SELECT * FROM cargo");
                                while ($datos_cargo = $sql_cargo->fetch_object()) { ?>
                                    <option value="<?= $datos_cargo->id_cargo ?>"><?= $datos_cargo->nombre ?></option>
                                <?php } ?>
                            </select>
                            <i class="fa-solid fa-briefcase text-slate-400 absolute left-4 top-[17px]"></i>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Turno</label>
                        <div class="relative">
                            <select name="turno" class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl px-4 py-3 pl-11 focus:ring-4 focus:ring-red-500/20 focus:border-red-500 transition-all outline-none appearance-none cursor-pointer">
                                <option value="">Todos los turnos</option>
                                <option value="mañana">Turno M. (Mañana)</option>
                                <option value="noche">Turno T. (Tarde/Noche)</option>
                            </select>
                            <i class="fa-regular fa-clock text-slate-400 absolute left-4 top-[17px]"></i>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Estado</label>
                        <div class="relative">
                            <select name="tipo" class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl px-4 py-3 pl-11 focus:ring-4 focus:ring-red-500/20 focus:border-red-500 transition-all outline-none appearance-none cursor-pointer">
                                <option value="">Todos los estados</option>
                                <option value="entrada">Solo Entradas (En Turno)</option>
                                <option value="salida">Jornada Completa (Con Salida)</option>
                                <option value="falta">Inasistencias</option>
                            </select>
                            <i class="fa-solid fa-clipboard-check text-slate-400 absolute left-4 top-[17px]"></i>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100 flex justify-end">
                    <button type="submit" name="btngenerar" onclick="if(window.addNotification) window.addNotification('info', 'Generando Reporte de Asistencias PDF...');" class="w-full md:w-auto bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-bold py-3 px-8 rounded-xl shadow-xl shadow-red-500/30 transition-all duration-300 transform hover:-translate-y-1 flex items-center justify-center gap-3">
                        <i class="fa-solid fa-file-pdf text-xl"></i> Generar Reporte PDF
                    </button>
                </div>
            </form>
        </div>

        <!-- Monthly Report Form Section -->
        <div class="mt-8 mb-8 flex flex-col justify-start items-start gap-4">
            <h3 class="text-2xl md:text-3xl font-bold text-slate-800 flex items-center gap-3">
                <div class="p-2.5 bg-blue-600 text-white rounded-xl shadow-lg shadow-blue-600/30">
                    <i class="fa-solid fa-chart-pie"></i>
                </div>
                Reporte Mensual Consolidado
            </h3>
            <p class="text-slate-500 font-medium">Genera un reporte consolidado con el total de asistencias, faltas, permisos y amonestaciones del mes.</p>
        </div>

        <div class="bg-white rounded-[2rem] shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100 overflow-hidden p-8">
            <form id="formReporteMensual" action="fpdf/ReporteMensualConsolidado.php" target="_blank" method="GET" class="space-y-6">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Mes</label>
                        <div class="relative">
                            <select name="mes" class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl px-4 py-3 pl-11 focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none appearance-none cursor-pointer" required>
                                <?php
                                $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                                $mes_actual = date('n');
                                foreach ($meses as $index => $mes) {
                                    $num_mes = $index + 1;
                                    $selected = ($num_mes == $mes_actual) ? 'selected' : '';
                                    echo "<option value=\"$num_mes\" $selected>$mes</option>";
                                }
                                ?>
                            </select>
                            <i class="fa-regular fa-calendar text-slate-400 absolute left-4 top-[17px]"></i>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Año</label>
                        <div class="relative">
                            <input required type="number" name="anio" value="<?= date('Y') ?>" min="2020" max="<?= date('Y') + 1 ?>" class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl px-4 py-3 pl-11 focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                            <i class="fa-solid fa-calendar-day text-slate-400 absolute left-4 top-1/2 -translate-y-1/2"></i>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100 flex justify-end">
                    <button type="submit" onclick="if(window.addNotification) window.addNotification('info', 'Generando Reporte Mensual PDF...');" class="w-full md:w-auto bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold py-3 px-8 rounded-xl shadow-xl shadow-blue-500/30 transition-all duration-300 transform hover:-translate-y-1 flex items-center justify-center gap-3">
                        <i class="fa-solid fa-file-pdf text-xl"></i> Generar Reporte Mensual
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
<!-- fin del contenido principal -->

<script>
function cambiarRutaReporte() {
    var select = document.getElementById("selectEmpleado");
    var form = document.getElementById("formReporte");
    var filtroTemporal = document.getElementById("selectFiltroTemporal");
    
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

function aplicarFiltroTemporal() {
    var filtro = document.getElementById("selectFiltroTemporal").value;
    var inputInicio = document.querySelector('input[name="fecha_inicio"]');
    var inputFin = document.querySelector('input[name="fecha_fin"]');
    
    var hoy = new Date();
    
    if (filtro === "hoy") {
        // Formatear hoy a YYYY-MM-DD
        var dd = String(hoy.getDate()).padStart(2, '0');
        var mm = String(hoy.getMonth() + 1).padStart(2, '0'); //Enero es 0!
        var yyyy = hoy.getFullYear();
        var hoyStr = yyyy + '-' + mm + '-' + dd;
        
        inputInicio.value = hoyStr;
        inputFin.value = hoyStr;
    } else if (filtro === "mensual") {
        // Primer dia del mes
        var primerDia = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
        // Ultimo dia del mes
        var ultimoDia = new Date(hoy.getFullYear(), hoy.getMonth() + 1, 0);
        
        var d1 = String(primerDia.getDate()).padStart(2, '0');
        var m1 = String(primerDia.getMonth() + 1).padStart(2, '0');
        var y1 = primerDia.getFullYear();
        
        var d2 = String(ultimoDia.getDate()).padStart(2, '0');
        var m2 = String(ultimoDia.getMonth() + 1).padStart(2, '0');
        var y2 = ultimoDia.getFullYear();
        
        inputInicio.value = y1 + '-' + m1 + '-' + d1;
        inputFin.value = y2 + '-' + m2 + '-' + d2;
    } else {
        // Manual, limpiar si quieres o dejar como esta
        // inputInicio.value = '';
        // inputFin.value = '';
    }
}
</script>

<!-- por ultimo se carga el footer -->
<?php require('./layout/footer.php'); ?>