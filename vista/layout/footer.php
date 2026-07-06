</main> <!-- Cierra el <main> de sidebar.php -->
</div> <!-- Cierra el <div class="flex-1..."> de sidebar.php -->

<!-- Modal Restaurar BD (movido desde el antiguo sidebar.php) -->
<div class="modal fade" id="modalRestaurarBD" tabindex="-1" aria-labelledby="modalRestaurarBDLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-2xl rounded-3xl overflow-hidden">
      <div class="bg-gradient-to-r from-amber-400 to-orange-500 p-6 flex items-center justify-between">
        <h5 class="text-white font-bold text-lg flex items-center gap-3 m-0" id="modalRestaurarBDLabel">
            <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-sm"><i class="fa-solid fa-database"></i></div>
            Restaurar Base de Datos
        </h5>
        <button type="button" class="btn-close btn-close-white opacity-80 hover:opacity-100 transition-opacity" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="../restaurar_bd.php" method="POST" enctype="multipart/form-data">
        <div class="p-8 bg-white">
          <div class="mb-4">
            <label class="block text-sm font-bold text-slate-700 mb-2">Selecciona archivo .sql</label>
            <input type="file" name="sql_file" accept=".sql" class="w-full bg-slate-50 border border-slate-200 text-slate-800 font-semibold rounded-xl px-4 py-3 focus:ring-4 focus:ring-amber-500/20 focus:border-amber-500 transition-all outline-none file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-amber-50 file:text-amber-600 hover:file:bg-amber-100 cursor-pointer" required>
          </div>
          
          <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex gap-3 text-amber-700 mt-6">
            <i class="fa-solid fa-triangle-exclamation text-lg flex-shrink-0 mt-0.5"></i>
            <div class="text-sm font-medium">Esta acción reemplazará los datos actuales. ¡Haz un respaldo antes!</div>
          </div>
        </div>
        <div class="flex justify-end gap-3 px-8 pb-8 pt-4 bg-white border-t border-slate-100">
          <button type="button" class="px-6 py-3 rounded-xl font-bold text-slate-500 bg-slate-100 hover:bg-slate-200 transition-colors" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="px-6 py-3 rounded-xl font-bold text-white bg-amber-500 hover:bg-amber-600 transition-colors shadow-lg shadow-amber-500/30 flex items-center gap-2">
            <i class="fa-solid fa-cloud-arrow-up"></i> Restaurar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Bootstrap JS (local) -->
<script src="/login_register12/public/bootstrap5/js/popper.min.js"></script>
<script src="/login_register12/public/bootstrap5/js/bootstrap.min.js"></script>
<script>
    lucide.createIcons();

    // Lógica para el enlace activo
    document.addEventListener("DOMContentLoaded", function() {
        const currentPath = window.location.pathname.split('/').pop();
        const sidebarLinks = document.querySelectorAll('aside nav a');
        
        sidebarLinks.forEach(link => {
            const linkPath = link.getAttribute('href').split('/').pop();
            if (linkPath === currentPath) {
                link.classList.add('active');
            }
        });
    });
</script>



<!-- jQuery (required by DataTables) -->
<script src="/login_register12/public/vendor/dist/js/jquery.min.js"></script>

<!-- datatables core -->
<script src="/login_register12/public/vendor/dist/datatables/jquery.dataTables.min.js"></script>

<!-- DataTables Buttons and Export Dependencies -->
<script type="text/javascript" src="/login_register12/public/vendor/dist/datatables/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="/login_register12/public/vendor/dist/js/jszip.min.js"></script>
<script type="text/javascript" src="/login_register12/public/vendor/dist/js/pdfmake.min.js"></script>
<script type="text/javascript" src="/login_register12/public/vendor/dist/js/vfs_fonts.js"></script>
<script type="text/javascript" src="/login_register12/public/vendor/dist/datatables/buttons.html5.min.js"></script>
<script type="text/javascript" src="/login_register12/public/vendor/dist/datatables/buttons.print.min.js"></script>



<!-- sweet alert -->
<script src="../public/sweet/js/sweetalert2.js"></script>
<script src="../public/sweet/js/sweet.js"></script>


<?php
$logo_path = __DIR__ . '/../../img/logo.png';
$base64_logo = '';
if (file_exists($logo_path)) {
    $base64_logo = 'data:image/png;base64,' . base64_encode(file_get_contents($logo_path));
}
?>
<script>
    const logoBase64 = "<?= $base64_logo ?>";

    $(function() {
        var table = $('#example').DataTable({
            select: {
                //style: 'multi'
            },
            responsive: true,
            dom: '<"flex flex-col md:flex-row justify-between items-center mb-6 pt-6"<"flex items-center gap-2"B><"flex items-center gap-2"f>>rt<"flex flex-col md:flex-row justify-between items-center mt-6 pb-6"<"text-sm text-gray-500"i><"flex border rounded-lg"p>>',
            buttons: window.location.pathname.includes('usuario.php') ? [] : [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fa-solid fa-file-excel mr-1"></i> Excel',
                    title: 'S.A.G (DORE\'S)',
                    messageTop: 'Reporte Analítico del Sistema',
                    className: '!bg-emerald-50 !text-emerald-600 hover:!bg-emerald-100 hover:!text-emerald-700 !font-bold !px-5 !py-2 !rounded-xl !shadow-sm !border !border-emerald-200 transition-colors',
                    exportOptions: { 
                        columns: ':visible:not(.no-export)',
                        format: {
                            body: function ( data, row, column, node ) {
                                return $(node).text().replace(/\s+/g, ' ').trim();
                            }
                        }
                    },
                    customize: function(xlsx) {
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];
                        var styles = xlsx.xl['styles.xml'];

                        // 1. Añadir borde custom inferior fino (similar al PDF y vista Imprimir)
                        var borderNode = '<border><left/><right/><top/><bottom style="thin"><color rgb="FFCBD5E1"/></bottom><diagonal/></border>';
                        $('borders', styles).append(borderNode);
                        var customBorderId = $('borders border', styles).length - 1;
                        $('borders', styles).attr('count', $('borders border', styles).length);

                        // 2. Añadir fuentes
                        // Rojo fuerte grande para el TÍTULO
                        var fontNode = '<font><b/><sz val="18"/><color rgb="FFDC2626"/><name val="Calibri"/></font>';
                        $('fonts', styles).append(fontNode);
                        var titleFontId = $('fonts font', styles).length - 1;
                        $('fonts', styles).attr('count', $('fonts font', styles).length);
                        
                        // Gris sutil para el SUBTÍTULO
                        var fontSub = '<font><b/><sz val="12"/><color rgb="FF64748B"/><name val="Calibri"/></font>';
                        $('fonts', styles).append(fontSub);
                        var subFontId = $('fonts font', styles).length - 1;
                        $('fonts', styles).attr('count', $('fonts font', styles).length);
                        
                        // Gris oscuro para los ENCABEZADOS DE TABLA (reemplaza el fondo gris por defecto)
                        var fontTh = '<font><b/><sz val="11"/><color rgb="FF334155"/><name val="Calibri"/></font>';
                        $('fonts', styles).append(fontTh);
                        var thFontId = $('fonts font', styles).length - 1;
                        $('fonts', styles).attr('count', $('fonts font', styles).length);

                        // 3. Crear configuraciones de celda (cellXfs) asociando fuente, borde, alineación y quitando relleno (fillId=0)
                        
                        // XF para Título
                        var xfTitle = '<xf numFmtId="0" fontId="'+titleFontId+'" fillId="0" borderId="0" applyFont="1" applyFill="1" applyBorder="1" xfId="0" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>';
                        $('cellXfs', styles).append(xfTitle);
                        var titleXfId = $('cellXfs xf', styles).length - 1;
                        $('cellXfs', styles).attr('count', $('cellXfs xf', styles).length);

                        // XF para Subtítulo (con border bottom fino)
                        var xfSub = '<xf numFmtId="0" fontId="'+subFontId+'" fillId="0" borderId="'+customBorderId+'" applyFont="1" applyFill="1" applyBorder="1" xfId="0" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>';
                        $('cellXfs', styles).append(xfSub);
                        var subXfId = $('cellXfs xf', styles).length - 1;
                        $('cellXfs', styles).attr('count', $('cellXfs xf', styles).length);

                        // XF para Cabecera de la tabla (con border bottom fino)
                        var xfTh = '<xf numFmtId="0" fontId="'+thFontId+'" fillId="0" borderId="'+customBorderId+'" applyFont="1" applyFill="1" applyBorder="1" xfId="0" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>';
                        $('cellXfs', styles).append(xfTh);
                        var thXfId = $('cellXfs xf', styles).length - 1;
                        $('cellXfs', styles).attr('count', $('cellXfs xf', styles).length);

                        // 4. Aplicar estilos en las celdas del Excel resultante
                        
                        // Fila 1: Título (por defecto fusionada, aplicamos el ID del XF)
                        $('row:eq(0) c', sheet).attr('s', titleXfId);
                        
                        // Fila 2: Subtítulo (por defecto fusionada)
                        $('row:eq(1) c', sheet).attr('s', subXfId);
                        
                        // Aumentar la altura de las filas un poco para que sea legible
                        $('row:eq(0)', sheet).attr('ht', '30');
                        $('row:eq(1)', sheet).attr('ht', '25');
                        $('row:eq(0)', sheet).attr('customHeight', '1');
                        $('row:eq(1)', sheet).attr('customHeight', '1');

                        // Fila 3: Las celdas de la tabla (Cabecera). Reemplaza el recuadro gris duro de DataTables por las letras limpias
                        $('row:eq(2) c', sheet).attr('s', thXfId);
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fa-solid fa-file-pdf mr-1"></i> PDF',
                    title: '',
                    className: '!bg-rose-50 !text-rose-600 hover:!bg-rose-100 hover:!text-rose-700 !font-bold !px-5 !py-2 !rounded-xl !shadow-sm !border !border-rose-200 transition-colors',
                    exportOptions: { 
                        columns: ':visible:not(.no-export)',
                        format: {
                            body: function ( data, row, column, node ) {
                                return $(node).text().replace(/\s+/g, ' ').trim();
                            }
                        }
                    },
                    customize: function(doc) {
                        doc.defaultStyle.fontSize = 10;
                        doc.styles.tableHeader.fontSize = 11;
                        doc.styles.tableHeader.fillColor = 'white'; // Fondo transparente/blanco
                        doc.styles.tableHeader.color = '#334155'; // Color gris oscuro, igual a imprimir
                        doc.styles.tableHeader.alignment = 'left';
                        doc.styles.tableHeader.bold = true;
                        
                        // Centrar la tabla y alinear anchos
                        doc.content[0].table.widths = Array(doc.content[0].table.body[0].length).fill('*');

                        // Estilos de bordes y padding para la tabla completos (layout estilo Imprimir sin verticales)
                        var objLayout = {};
                        objLayout['hLineWidth'] = function(i) { return 1; };
                        objLayout['vLineWidth'] = function(i) { return 0; };
                        objLayout['hLineColor'] = function(i, node) { 
                            // Línea más oscura debajo del header, más claras para el resto
                            return (i === 1) ? '#cbd5e1' : '#f1f5f9'; 
                        }; 
                        objLayout['paddingLeft'] = function(i) { return 8; };
                        objLayout['paddingRight'] = function(i) { return 8; };
                        objLayout['paddingTop'] = function(i) { return 12; };
                        objLayout['paddingBottom'] = function(i) { return 12; };
                        doc.content[0].layout = objLayout;

                        let headerBlocks = [];
                        
                        // Agregar Logo
                        if(logoBase64 !== '') {
                            headerBlocks.push({
                                image: logoBase64,
                                width: 80,
                                alignment: 'center',
                                margin: [0, 0, 0, 10]
                            });
                        }

                        // Textos descriptivos
                        headerBlocks.push({
                                text: "S.A.G (DORE'S)",
                                fontSize: 24,
                                bold: true,
                                color: '#dc2626',
                                alignment: 'center',
                                margin: [0, 0, 0, 5]
                            },
                            {
                                text: "Reporte Analítico del Sistema",
                                fontSize: 13,
                                bold: true,
                                color: '#64748b',
                                alignment: 'center',
                                margin: [0, 0, 0, 20]
                            });

                        // Línea divisora
                        headerBlocks.push({
                            canvas: [{ type: 'line', x1: 0, y1: 0, x2: 515, y2: 0, lineWidth: 1, lineColor: '#cbd5e1' }],
                            margin: [0, 0, 0, 20]
                        });

                        // Inyectamos todo al inicio del PDF (antes de la tabla)
                        doc.content.unshift(...headerBlocks);
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="fa-solid fa-print mr-1"></i> Imprimir',
                    title: '',
                    className: '!bg-slate-50 !text-slate-600 hover:!bg-slate-100 hover:!text-slate-700 !font-bold !px-5 !py-2 !rounded-xl !shadow-sm !border !border-slate-200 transition-colors',
                    exportOptions: { columns: ':visible:not(.no-export)' },
                    customize: function ( win ) {
                        $(win.document.body)
                            .css( 'font-family', 'Inter, system-ui, sans-serif' )
                            .prepend(
                                '<div style="text-align:center; padding-bottom:20px; border-bottom:2px solid #e2e8f0; margin-bottom:20px;">' +
                                '<img src="'+window.location.origin+'/login_register12/img/logo.png" style="max-height:80px; margin-bottom:10px; display:inline-block;" />' +
                                '<h2 style="margin:0; color:#dc2626; font-size: 28px; font-weight:800;">S.A.G (DORE\'S)</h2>' +
                                '<p style="margin:5px 0 0 0; color:#64748b; font-size:14px; font-weight:600;">Reporte Analítico del Sistema</p>' +
                                '</div>'
                            );
     
                        $(win.document.body).find( 'table' )
                            .addClass( 'compact' )
                            .css( 'font-size', '12px' )
                            .css( 'width', '100%')
                            .css( 'border-collapse', 'collapse');

                        $(win.document.body).find( 'table th' )
                            .css( 'background-color', '#f8fafc' )
                            .css( 'color', '#334155' )
                            .css( 'border-bottom', '2px solid #e2e8f0' )
                            .css( 'padding', '12px 8px' )
                            .css( 'text-align', 'left' );
                            
                        $(win.document.body).find( 'table td' )
                            .css( 'border-bottom', '1px solid #f1f5f9' )
                            .css( 'padding', '10px 8px' )
                            .css( 'color', '#475569' );
                    }
                }
            ],
            "language": {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla =(",
                "sInfo": "Registros del _START_ al _END_ de _TOTAL_ registros",
                "sInfoEmpty": "Registros del 0 al 0 de 0 registros",
                "sInfoFiltered": "-",
                "sInfoPostFix": "",
                "sSearch": "Buscar:",
                "sUrl": "",
                "sInfoThousands": ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast": "Último",
                    "sNext": "Siguiente",
                    "sPrevious": "Anterior"
                },
                "oAria": {
                    "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                },
                "buttons": {
                    "copy": "Copiar",
                    "colvis": "Visibilidad"
                }
            }
        });

        table.on('buttons-action', function (e, buttonApi, dataTable, node, config) {
            if (window.addNotification) {
                if (config.extend === 'excelHtml5') {
                    window.addNotification('info', 'Reporte Excel descargado exitosamente.');
                } else if (config.extend === 'pdfHtml5') {
                    window.addNotification('info', 'Reporte PDF generado exitosamente.');
                } else if (config.extend === 'print') {
                    window.addNotification('info', 'Preparando documento para impresión...');
                }
            }
        });
    });
</script>


<script type="text/javascript" src="../public/app/publico/js/lib/jqueryui/jquery-ui.min.js"></script>
<script type="text/javascript" src="../public/app/publico/js/lib/lobipanel/lobipanel.min.js"></script>
<script type="text/javascript" src="../public/app/publico/js/lib/match-height/jquery.matchHeight.min.js">
</script>
<script type="text/javascript" src="../public/loader/loader.js"></script>

<script>
    $(document).ready(function() {

        $('.panel').lobiPanel({
            sortable: true
        });
        $('.panel').on('dragged.lobiPanel', function(ev, lobiPanel) {
            $('.dahsboard-column').matchHeight();
        });

        google.charts.load('current', {
            'packages': ['corechart']
        });
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var dataTable = new google.visualization.DataTable();
            dataTable.addColumn('string', 'Day');
            dataTable.addColumn('number', 'Values');
            // A column for custom tooltip content
            dataTable.addColumn({
                type: 'string',
                role: 'tooltip',
                'p': {
                    'html': true
                }
            });
            dataTable.addRows([
                ['MON', 130, ' '],
                ['TUE', 130, '130'],
                ['WED', 180, '180'],
                ['THU', 175, '175'],
                ['FRI', 200, '200'],
                ['SAT', 170, '170'],
                ['SUN', 250, '250'],
                ['MON', 220, '220'],
                ['TUE', 220, ' ']
            ]);

            var options = {
                height: 314,
                legend: 'none',
                areaOpacity: 0.18,
                axisTitlesPosition: 'out',
                hAxis: {
                    title: '',
                    textStyle: {
                        color: '#fff',
                        fontName: 'Proxima Nova',
                        fontSize: 11,
                        bold: true,
                        italic: false
                    },
                    textPosition: 'out'
                },
                vAxis: {
                    minValue: 0,
                    textPosition: 'out',
                    textStyle: {
                        color: '#fff',
                        fontName: 'Proxima Nova',
                        fontSize: 11,
                        bold: true,
                        italic: false
                    },
                    baselineColor: '#16b4fc',
                    ticks: [0, 25, 50, 75, 100, 125, 150, 175, 200, 225, 250, 275, 300, 325, 350],
                    gridlines: {
                        color: '#1ba0fc',
                        count: 15
                    }
                },
                lineWidth: 2,
                colors: ['#fff'],
                curveType: 'function',
                pointSize: 5,
                pointShapeType: 'circle',
                pointFillColor: '#f00',
                backgroundColor: {
                    fill: '#008ffb',
                    strokeWidth: 0,
                },
                chartArea: {
                    left: 0,
                    top: 0,
                    width: '100%',
                    height: '100%'
                },
                fontSize: 11,
                fontName: 'Proxima Nova',
                tooltip: {
                    trigger: 'selection',
                    isHtml: true
                }
            };

            var chart = new google.visualization.AreaChart(document.getElementById('chart_div'));
            chart.draw(dataTable, options);
        }
        $(window).resize(function() {
            drawChart();
            setTimeout(function() {}, 1000);
        });
    });
</script>
<script src="../public/app/publico/js/app.js">
</script>

<script src="../public/app/publico/js/lib/jquery-flex-label/jquery.flex.label.js"></script>

<script type="application/javascript">
    (function($) {
        $(document).ready(function() {
            $('.fl-flex-label').flexLabel();
        });
    })(jQuery);
</script>

<!-- Flatpickr JS -->
<script src="/login_register12/public/vendor/dist/flatpickr/flatpickr.min.js"></script>
<script src="/login_register12/public/vendor/dist/flatpickr/es.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        /* Se desactiva Flatpickr para dejar que el navegador use su selector de fechas nativo 
           (mucho más robusto para saltos de meses/años sin conflictos de CSS)
        flatpickr("input[type='date']", {
            locale: "es",
            dateFormat: "Y-m-d",
            disableMobile: "true"
        });
        */
    });
</script>

<!-- Tom Select JS -->
<script src="/login_register12/public/vendor/dist/tom-select/tom-select.complete.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar Tom Select en todos los selects, SALVO los de DataTables (cantidad de registros)
        document.querySelectorAll('select').forEach((el) => {
            if (!el.closest('.dataTables_length') && !el.closest('.dataTables_filter')) {
                new TomSelect(el, {
                    create: false,
                    controlInput: null, // Desactiva la barra de escritura nativa si prefieres que actúe solo como Dropdown (puedes quitar esto si quieres buscador en los selects)
                });
            }
        });
    });
</script>

<!-- Notificaciones y Biométrico Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const notifBtn = document.getElementById('notification-btn');
        const notifDropdown = document.getElementById('notification-dropdown');
        const notifBadge = document.getElementById('notification-badge');
        const notifList = document.getElementById('notification-list');
        const notifCount = document.getElementById('notification-count');
        const bioLed = document.getElementById('biometric-led');
        const bioText = document.getElementById('biometric-text');
        
        if (!notifBtn || !bioLed) return; // Si no estamos en una página con el header

        let notifications = JSON.parse(localStorage.getItem('sys_notifications') || '[]');
        let wasDisconnected = false;
        let isFirstCheck = true;

        // Toggle dropdown
        notifBtn.addEventListener('click', () => {
            if (notifDropdown.classList.contains('hidden')) {
                notifDropdown.classList.remove('hidden');
                setTimeout(() => {
                    notifDropdown.classList.remove('opacity-0', 'scale-95');
                    notifDropdown.classList.add('opacity-100', 'scale-100');
                }, 10);
            } else {
                notifDropdown.classList.remove('opacity-100', 'scale-100');
                notifDropdown.classList.add('opacity-0', 'scale-95');
                setTimeout(() => {
                    notifDropdown.classList.add('hidden');
                }, 200);
            }
        });

        // Close dropdown when outside click
        document.addEventListener('click', (e) => {
            if (!notifBtn.contains(e.target) && !notifDropdown.contains(e.target)) {
                notifDropdown.classList.remove('opacity-100', 'scale-100');
                notifDropdown.classList.add('opacity-0', 'scale-95');
                setTimeout(() => {
                    notifDropdown.classList.add('hidden');
                }, 200);
            }
        });

        window.addNotification = function(type, message) {
            const time = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            const id = Date.now() + Math.random();
            
            let icon = '';
            let iconColor = '';
            let bgColor = '';
            
            if (type === 'error' || type === 'rojo') {
                icon = '<i class="fa-solid fa-triangle-exclamation"></i>';
                iconColor = 'text-rose-500';
                bgColor = 'bg-rose-50';
            } else if (type === 'success' || type === 'esmeralda') {
                icon = '<i class="fa-solid fa-check"></i>';
                iconColor = 'text-emerald-500';
                bgColor = 'bg-emerald-50';
            } else if (type === 'info' || type === 'azul') {
                icon = '<i class="fa-solid fa-info"></i>';
                iconColor = 'text-blue-500';
                bgColor = 'bg-blue-50';
            } else if (type === 'warning' || type === 'amarillo' || type === 'r_naranja') {
                icon = '<i class="fa-solid fa-bell"></i>';
                iconColor = 'text-amber-500';
                bgColor = 'bg-amber-50';
            }

            // Evitar duplicados seguidos (útil para eventos de red o recargas rápidas)
            const isDuplicate = notifications.some(n => n.message === message && n.time === time);
            if (!isDuplicate) {
                notifications.unshift({id, type, message, time, icon, iconColor, bgColor});
                if (notifications.length > 30) notifications.pop();
                
                localStorage.setItem('sys_notifications', JSON.stringify(notifications));
                renderNotifications();
            }
        }

        function renderNotifications() {
            if (notifications.length === 0) {
                notifList.innerHTML = '<div class="text-sm text-gray-400 p-4 text-center font-medium">No hay notificaciones nuevas</div>';
                notifBadge.classList.add('hidden');
                notifCount.textContent = '0';
                // Añadir botón para limpiar al final si hay notificaciones
                return;
            }

            notifBadge.classList.remove('hidden');
            notifCount.textContent = notifications.length;
            
            let listHTML = notifications.map(n => `
                <div class="flex gap-3 p-3 rounded-lg hover:bg-slate-50 transition-all border-b border-gray-50 last:border-0 items-start">
                    <div class="w-8 h-8 rounded-full ${n.bgColor} ${n.iconColor} flex items-center justify-center shrink-0">
                        ${n.icon}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-slate-700 font-medium leading-snug break-words">${n.message}</p>
                        <p class="text-xs text-slate-400 mt-1"><i class="fa-regular fa-clock"></i> ${n.time}</p>
                    </div>
                </div>
            `).join('');

            // Botón para limpiar notificaciones
            listHTML += `
                <div class="mt-2 pt-2 border-t border-slate-100 flex justify-center">
                    <button id="clear-notifications" class="text-xs text-slate-400 hover:text-red-500 font-semibold px-3 py-1 rounded-full hover:bg-red-50 transition-colors">
                        Limpiar notificaciones
                    </button>
                </div>
            `;
            
            notifList.innerHTML = listHTML;

            // Event listener para limpiar (usamos delegación de eventos o lo asignamos tras renderizar)
            const clearBtn = document.getElementById('clear-notifications');
            if (clearBtn) {
                clearBtn.addEventListener('click', (e) => {
                    e.stopPropagation(); // Evitar cerrar el dropdown si solo limpiamos
                    notifications = [];
                    localStorage.removeItem('sys_notifications');
                    renderNotifications();
                });
            }
        }

        function checkBiometricConnection() {
            fetch('/login_register12/controlador/check_biometric_status.php')
                .then(response => response.json())
                .then(data => {
                    const parent = bioLed.parentElement;
                    if (data.status === 'connected') {
                        // Verde / Conectado
                        bioLed.className = 'w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.7)] transition-colors duration-300';
                        parent.classList.remove('bg-slate-50', 'bg-rose-50', 'border-slate-200', 'border-rose-200');
                        parent.classList.add('bg-emerald-50', 'border-emerald-200');
                        bioText.textContent = 'En Línea';
                        bioText.className = 'text-xs font-bold text-emerald-700';

                        if (wasDisconnected || isFirstCheck) {
                            if(wasDisconnected) {
                                addNotification('success', 'El biométrico se ha conectado exitosamente.');
                            }
                            wasDisconnected = false;
                        }
                    } else {
                        // Rojo / Desconectado
                        bioLed.className = 'w-2.5 h-2.5 rounded-full bg-rose-500 shadow-[0_0_8px_rgba(244,63,94,0.7)] transition-colors duration-300 animate-pulse';
                        parent.classList.remove('bg-slate-50', 'bg-emerald-50', 'border-slate-200', 'border-emerald-200');
                        parent.classList.add('bg-rose-50', 'border-rose-200');
                        bioText.textContent = 'Desconectado';
                        bioText.className = 'text-xs font-bold text-rose-700';

                        if (!wasDisconnected || isFirstCheck) {
                            addNotification('error', 'Se ha perdido la conexión con el biométrico. Verifique la red.');
                            wasDisconnected = true;
                        }
                    }
                    isFirstCheck = false;
                })
                .catch(error => {
                    console.error('Error checking biometric:', error);
                    bioLed.className = 'w-2.5 h-2.5 rounded-full bg-slate-400 transition-colors duration-300';
                    bioText.textContent = 'Error de red';
                    bioText.className = 'text-xs font-semibold text-slate-600';
                });
        }

        function checkSmartNotifications() {
            fetch('/login_register12/controlador/api_notificaciones.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success' && data.data) {
                        data.data.forEach(nt => {
                            // Para no duplicar eventos estáticos o ya mostrados, verificamos por ID si podemos, o mensaje
                            const isDuplicate = notifications.some(n => n.message === nt.mensaje);
                            if (!isDuplicate) {
                                window.addNotification(nt.tipo, nt.mensaje);
                            }
                        });
                        
                        // Si nos trajimos transcientes, pedir a la API que las limpie de su sesion para no enviarlas otra vez.
                        const hasTransient = data.data.some(n => n.is_transient);
                        if (hasTransient) {
                            fetch('/login_register12/controlador/api_notificaciones.php?clear_transient=1');
                        }
                    }
                })
                .catch(error => console.error('Error checking smart notifications:', error));
        }

        // Renderear inicial
        renderNotifications();
        
        // Llamar inmediatamente y luego cada X segundos
        checkBiometricConnection();
        checkSmartNotifications();
        
        setInterval(checkBiometricConnection, 10000);
        setInterval(checkSmartNotifications, 30000); // 30 segundos
    });
</script>

</body>
</html>
