<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S.A.G (DORE'S) - Lector Biométrico Simulado</title>
    <!-- Tailwind CSS -->
    <script src="/login_register12/public/vendor/dist/js/tailwindcss.js"></script>
    <!-- FontAwesome -->

    <style>
        .fingerprint-bg {
            background-image: radial-gradient(circle at center, rgba(16, 185, 129, 0.1) 0%, transparent 70%);
        }
        @keyframes scan {
            0% { transform: translateY(-100%); }
            50% { transform: translateY(100%); }
            100% { transform: translateY(-100%); }
        }
        .scanning-line {
            position: absolute;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, transparent, #10b981, transparent);
            box-shadow: 0 0 10px #10b981;
            top: 0;
            left: 0;
            animation: scan 2s linear infinite;
            display: none;
        }
        .animate-scan .scanning-line {
            display: block;
        }
        .animate-scan i {
            color: #10b981 !important;
            filter: drop-shadow(0 0 15px #10b981);
        }
    </style>
</head>
<body class="bg-slate-900 min-h-screen flex items-center justify-center p-4 selection:bg-emerald-500 selection:text-white">
    <div class="max-w-md w-full bg-slate-800 rounded-3xl shadow-2xl overflow-hidden border border-slate-700 relative">
        <!-- Decoration -->
        <div class="absolute top-0 right-0 -mt-16 -mr-16 w-48 h-48 bg-emerald-500/10 blur-3xl rounded-full"></div>
        <div class="absolute bottom-0 left-0 -mb-16 -ml-16 w-48 h-48 bg-blue-500/10 blur-3xl rounded-full"></div>

        <div class="relative z-10 p-8">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-emerald-500/10 text-emerald-400 mb-4 shadow-lg shadow-emerald-500/20">
                    <i class="fa-solid fa-fingerprint text-3xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-white tracking-wide">Terminal S.A.G</h1>
                <p class="text-slate-400 text-sm mt-1">Control de Asistencia Biométrico</p>
                <div class="mt-4 flex justify-center">
                    <div id="liveClock" class="bg-slate-900/50 backdrop-blur px-4 py-2 rounded-xl text-emerald-400 font-mono text-xl tracking-widest border border-slate-700/50 shadow-inner">
                        --:--:--
                    </div>
                </div>
            </div>

            <!-- Sensor Area -->
            <div class="mb-8 relative flex justify-center">
                <div id="sensorArea" class="w-40 h-40 rounded-full border-2 border-slate-600 border-dashed flex items-center justify-center cursor-pointer transition-all duration-300 hover:border-emerald-500/50 hover:bg-emerald-500/5 group fingerprint-bg relative overflow-hidden">
                    <i class="fa-solid fa-fingerprint text-6xl text-slate-500 group-hover:text-emerald-400/70 transition-colors duration-300 relative z-10"></i>
                    <div class="scanning-line z-20"></div>
                </div>
            </div>

            <div class="text-center mb-6 h-6">
                <!-- Instruction or status message -->
                <p id="statusMessage" class="text-slate-400 font-medium">Coloca tu huella en el sensor</p>
            </div>

            <!-- Simulation Input -->
            <div class="bg-slate-900 p-4 rounded-2xl border border-slate-700/50 relative">
                <div class="absolute -top-3 left-4 bg-slate-800 px-2 text-xs font-bold text-slate-500 tracking-wider">Simulador (DNI)</div>
                <label class="sr-only">Ingresar DNI de prueba</label>
                <div class="flex gap-2">
                    <input type="text" id="dniInput" placeholder="Ej. 12345678" class="w-full bg-slate-800 text-white placeholder-slate-500 px-4 py-3 rounded-xl border border-slate-600 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all font-mono text-center tracking-widest">
                    <button id="simulateBtn" class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 rounded-xl font-bold transition-all shadow-lg shadow-emerald-500/20 flex items-center justify-center hover:scale-105 active:scale-95">
                        <i class="fa-solid fa-expand"></i>
                    </button>
                </div>
                <p class="text-[10px] text-slate-500 mt-2 text-center">Ingresa un DNI registrado y presiona el botón para simular la lectura de la huella correspondiente.</p>
            </div>
            
            <div class="mt-6 text-center">
                <a href="inicio.php" onclick="window.close();" class="text-sm font-medium text-slate-500 hover:text-slate-300 transition-colors flex items-center justify-center gap-2">
                    <i class="fa-solid fa-arrow-left"></i> Cerrar Simulador
                </a>
            </div>
        </div>

        <!-- Success overlay -->
        <div id="successOverlay" class="absolute inset-0 bg-emerald-500/95 backdrop-blur flex items-center justify-center flex-col z-50 transform translate-y-full transition-transform duration-500 ease-in-out">
            <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mb-6 shadow-[0_0_40px_rgba(255,255,255,0.4)]">
                <!-- Checkmark based on type -->
                <i id="successIcon" class="fa-solid fa-check text-5xl text-emerald-500"></i>
            </div>
            <h2 id="successAction" class="text-3xl font-black text-white mb-2 uppercase tracking-wide">ENTRADA</h2>
            <p id="successName" class="text-emerald-50 text-xl font-bold mb-1">Juan Pérez</p>
            <p id="successTime" class="text-emerald-100/80 font-mono text-lg bg-black/10 px-4 py-1 rounded-lg">08:00 AM</p>
            <button id="closeOverlay" class="mt-8 px-6 py-2 border-2 border-white/30 hover:border-white/80 rounded-full text-white font-bold transition-colors">Volver</button>
        </div>

        <!-- Error overlay -->
        <div id="errorOverlay" class="absolute inset-0 bg-rose-500/95 backdrop-blur flex items-center justify-center flex-col z-50 transform translate-y-full transition-transform duration-500 ease-in-out">
            <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mb-6 shadow-[0_0_40px_rgba(255,255,255,0.4)]">
                <i class="fa-solid fa-xmark text-6xl text-rose-500"></i>
            </div>
            <h2 class="text-3xl font-black text-white mb-2 uppercase tracking-wide">ERROR</h2>
            <p id="errorMessage" class="text-rose-50 text-lg font-medium mb-1 text-center px-6">Huella no reconocida</p>
            <button id="closeErrorOverlay" class="mt-8 px-6 py-2 border-2 border-white/30 hover:border-white/80 rounded-full text-white font-bold transition-colors">Intentar de nuevo</button>
        </div>
    </div>

    <!-- Notification Sound (Optional, using browser primitive) -->
    <script>
        // Reloj en vivo
        function updateClock() {
            const now = new Date();
            let h = now.getHours();
            let m = now.getMinutes();
            let s = now.getSeconds();
            const ampm = h >= 12 ? 'PM' : 'AM';
            h = h % 12;
            h = h ? h : 12; // hora '0' debe ser '12'
            h = h < 10 ? '0' + h : h;
            m = m < 10 ? '0' + m : m;
            s = s < 10 ? '0' + s : s;
            document.getElementById('liveClock').innerText = h + ':' + m + ':' + s + ' ' + ampm;
        }
        setInterval(updateClock, 1000);
        updateClock(); // llamada inicial

        function playBeep(type) {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (!AudioContext) return;
            const ctx = new AudioContext();
            const osc = ctx.createOscillator();
            const gainNode = ctx.createGain();
            
            osc.connect(gainNode);
            gainNode.connect(ctx.destination);
            
            if (type === 'success') {
                osc.type = 'sine';
                osc.frequency.setValueAtTime(800, ctx.currentTime);
                osc.frequency.exponentialRampToValueAtTime(1200, ctx.currentTime + 0.1);
                gainNode.gain.setValueAtTime(0.5, ctx.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.3);
                osc.start(ctx.currentTime);
                osc.stop(ctx.currentTime + 0.3);
            } else {
                osc.type = 'sawtooth';
                osc.frequency.setValueAtTime(300, ctx.currentTime);
                osc.frequency.linearRampToValueAtTime(200, ctx.currentTime + 0.2);
                gainNode.gain.setValueAtTime(0.5, ctx.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.4);
                osc.start(ctx.currentTime);
                osc.stop(ctx.currentTime + 0.4);
            }
        }

        const dniInput = document.getElementById('dniInput');
        const simulateBtn = document.getElementById('simulateBtn');
        const sensorArea = document.getElementById('sensorArea');
        const statusMessage = document.getElementById('statusMessage');
        const successOverlay = document.getElementById('successOverlay');
        const errorOverlay = document.getElementById('errorOverlay');

        function processBiometric() {
            const dni = dniInput.value.trim();
            if (!dni) {
                statusMessage.className = "text-amber-400 font-bold";
                statusMessage.innerText = "Por favor, ingresa un DNI primero";
                setTimeout(() => {
                    statusMessage.className = "text-slate-400 font-medium";
                    statusMessage.innerText = "Coloca tu huella en el sensor";
                }, 2000);
                return;
            }

            // Iniciar animación de escaneo
            sensorArea.classList.add('animate-scan');
            statusMessage.className = "text-emerald-400 font-bold animate-pulse";
            statusMessage.innerText = "Procesando huella...";
            
            // Simular un pequeño retardo de procesamiento del lector (1.5s)
            setTimeout(() => {
                // Hacer la petición a la API
                fetch('../controlador/api_simulador_biometrico.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ dni: dni })
                })
                .then(response => response.json())
                .then(data => {
                    sensorArea.classList.remove('animate-scan');
                    statusMessage.className = "text-slate-400 font-medium";
                    statusMessage.innerText = "Coloca tu huella en el sensor";

                    if (data.status === 'success') {
                        // Success handling
                        playBeep('success');
                        
                        document.getElementById('successAction').innerText = data.accion;
                        document.getElementById('successName').innerText = data.empleado;
                        document.getElementById('successTime').innerText = data.hora;
                        
                        const icon = document.getElementById('successIcon');
                        if (data.accion === 'entrada') {
                            successOverlay.className = "absolute inset-0 bg-emerald-500/95 backdrop-blur flex items-center justify-center flex-col z-50 transform translate-y-0 transition-transform duration-500 ease-out";
                            icon.className = "fa-solid fa-arrow-right-to-bracket text-5xl text-emerald-500";
                            document.getElementById('successAction').className = "text-3xl font-black text-white mb-2 uppercase tracking-wide";
                        } else {
                            successOverlay.className = "absolute inset-0 bg-blue-500/95 backdrop-blur flex items-center justify-center flex-col z-50 transform translate-y-0 transition-transform duration-500 ease-out";
                            icon.className = "fa-solid fa-arrow-right-from-bracket text-5xl text-blue-500";
                            document.getElementById('successAction').className = "text-3xl font-black text-white mb-2 uppercase tracking-wide text-blue-100";
                            document.getElementById('successName').className = "text-blue-50 text-xl font-bold mb-1";
                            document.getElementById('successTime').className = "text-blue-100/80 font-mono text-lg bg-black/10 px-4 py-1 rounded-lg";
                        }
                        
                        // Auto close after 3 seconds
                        setTimeout(() => {
                            if (!successOverlay.classList.contains('translate-y-full')) {
                                closeOverlayFunc();
                            }
                        }, 3000);
                        
                    } else {
                        // Error handling
                        playBeep('error');
                        document.getElementById('errorMessage').innerText = data.message;
                        errorOverlay.className = "absolute inset-0 bg-rose-500/95 backdrop-blur flex items-center justify-center flex-col z-50 transform translate-y-0 transition-transform duration-500 ease-out";
                        
                        // Auto close after 3 seconds
                        setTimeout(() => {
                            if (!errorOverlay.classList.contains('translate-y-full')) {
                                closeErrorOverlayFunc();
                            }
                        }, 3000);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    sensorArea.classList.remove('animate-scan');
                    playBeep('error');
                    document.getElementById('errorMessage').innerText = "Error de conexión con el servidor";
                    errorOverlay.className = "absolute inset-0 bg-rose-500/95 backdrop-blur flex items-center justify-center flex-col z-50 transform translate-y-0 transition-transform duration-500 ease-out";
                });
            }, 1500);
        }

        function closeOverlayFunc() {
            successOverlay.classList.remove('translate-y-0');
            successOverlay.classList.add('translate-y-full');
            dniInput.value = ''; // Limpiar input para el siguiente
        }

        function closeErrorOverlayFunc() {
            errorOverlay.classList.remove('translate-y-0');
            errorOverlay.classList.add('translate-y-full');
            dniInput.select(); // Seleccionar texto para reemplazar fácil
        }

        simulateBtn.addEventListener('click', processBiometric);
        sensorArea.addEventListener('click', processBiometric);
        document.getElementById('closeOverlay').addEventListener('click', closeOverlayFunc);
        document.getElementById('closeErrorOverlay').addEventListener('click', closeErrorOverlayFunc);

        // Allow Enter key to trigger
        dniInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                processBiometric();
            }
        });
    </script>
</body>
</html>
