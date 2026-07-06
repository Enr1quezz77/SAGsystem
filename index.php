<?php

session_start();

$errors = [
    'login' => $_SESSION['login_error'] ?? '',
    'register' => $_SESSION['register_error'] ?? '',
    'security' => $_SESSION['security_error'] ?? '',
    'correo' => $_SESSION['correo_error'] ?? ''
];
$success = $_SESSION['security_success'] ?? '';
$activeForm = 'login'; // Forzar siempre el login como formulario inicial
$activeTab = 'seguridad'; // Forzar que la pestaña activa sea la de seguridad
// Limpiar solo los mensajes después de usarlos
unset($_SESSION['login_error'], $_SESSION['register_error'], $_SESSION['security_error'], $_SESSION['correo_error'], $_SESSION['security_success']);

function showError($error) {
    return !empty($error) ? "<p class='error-message'>$error</p>" : '';
}

function isActiveForm($formName, $activeForm) {
    return $formName === $activeForm ? 'active' : '';
}

?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S.A.G (DORE'S)</title>
    <!-- Tailwind CSS -->
    <script src="/login_register12/public/vendor/dist/js/tailwindcss.js"></script>
    <link href="/login_register12/public/vendor/dist/boxicons/boxicons.min.css" rel="stylesheet">
    <link rel="icon" href="img/logo.png?v=<?= time() ?>">
    <link href="/login_register12/public/vendor/dist/fonts/poppins/index.css" rel="stylesheet">
    <link href="/login_register12/public/vendor/dist/fonts/inter/index.css" rel="stylesheet">
    <link href="/login_register12/public/bootstrap5/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, .font-poppins { font-family: 'Poppins', sans-serif; }
        .form-box { display: none; opacity: 0; transition: opacity 0.4s ease; }
        .form-box.active { display: block; opacity: 1; }
    </style>
</head>
<body class="bg-slate-50 h-screen flex overflow-hidden">

    <!-- Lado Izquierdo: Branding (Split Screen) -->
    <div class="hidden lg:flex lg:w-1/2 h-full relative bg-gradient-to-br from-red-700 to-red-900 justify-center items-center p-12 overflow-hidden shadow-[10px_0_30px_rgba(0,0,0,0.1)] z-10 w-full">
        <!-- Abstract blur shapes -->
        <div class="absolute top-0 left-0 w-full h-full opacity-30 pointer-events-none">
            <div class="absolute -top-[20%] -left-[10%] w-[500px] h-[500px] rounded-full bg-red-500 blur-3xl"></div>
            <div class="absolute bottom-[0%] -right-[10%] w-[600px] h-[600px] rounded-full bg-orange-500 blur-3xl"></div>
        </div>
        
        <div class="relative z-10 flex flex-col items-center text-center">
            <div class="w-48 h-48 bg-white/10 backdrop-blur-md rounded-full shadow-2xl p-4 border border-white/20 mb-8 flex items-center justify-center">
                <img src="img/logo.png?v=<?= time() ?>" alt="Dore's Logo" class="w-full h-full object-contain rounded-full">
            </div>
            <h1 class="text-5xl font-bold text-white mb-4 tracking-tight drop-shadow-md">S.A.G <span class="text-amber-300">(DORE'S)</span></h1>
            <p class="text-lg text-red-100 font-medium max-w-sm drop-shadow">Sistema Automatizado de Gestión</p>
        </div>
    </div>

    <!-- Lado Derecho: Formularios -->
    <div class="w-full lg:w-1/2 h-full flex justify-center items-center p-6 sm:p-12 bg-white relative overflow-y-auto">
        
        <!-- Botón de Manual de Usuario -->
        <a href="ver_manual.php" target="_blank" class="absolute top-6 right-6 lg:top-8 lg:right-8 w-12 h-12 bg-slate-100 text-slate-600 hover:text-white hover:bg-red-600 rounded-full flex items-center justify-center shadow-md hover:shadow-lg hover:-translate-y-1 transition-all duration-300 z-50 group" title="Ver Manual de Usuario">
            <i class='bx bx-question-mark text-2xl'></i>
            <span class="absolute right-14 bg-slate-800 text-white text-xs px-3 py-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                Manual de Usuario
            </span>
        </a>

        <div class="w-full max-w-md">
            
            <!-- Logo solo en pantallas móviles -->
            <div class="lg:hidden flex flex-col items-center text-center mb-10">
                <img src="img/logo.png?v=<?= time() ?>" alt="Dore's Logo" class="w-24 h-24 object-contain shadow-lg rounded-full mb-4">
                <h1 class="text-3xl font-bold text-slate-800">S.A.G <span class="text-red-600">(DORE'S)</span></h1>
            </div>

            <!-- LOGIN -->
            <div class="form-box active" id="login-form">
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-slate-800 tracking-tight">Bienvenido</h2>
                    <p class="text-slate-500 mt-2">Ingresa tus credenciales para continuar</p>
                </div>
                
                <?= !empty($errors['login']) ? '<div class="bg-rose-50 border border-rose-200 text-rose-600 px-4 py-3 rounded-xl mb-6 text-sm text-center font-medium shadow-sm"><i class="bx bxs-error-circle mr-1"></i> '.$errors['login'].'</div>' : '' ?>

                <form action="login_register.php" method="post" class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5 ml-1">Correo Electrónico</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                <i class='bx bx-envelope text-xl'></i>
                            </div>
                            <input type="email" name="email" placeholder="ejemplo@correo.com" class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-red-500/10 focus:border-red-500 text-slate-700 outline-none transition-all font-medium" required>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5 ml-1">Contraseña</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                <i class='bx bx-lock-alt text-xl'></i>
                            </div>
                            <input type="password" id="login_password" name="password" placeholder="••••••••" class="w-full pl-11 pr-12 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-red-500/10 focus:border-red-500 text-slate-700 outline-none transition-all font-medium" required>
                            <button type="button" onclick="togglePasswordVisibility('login_password', this)" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600 transition-colors focus:outline-none">
                                <i class='bx bx-show text-xl'></i>
                            </button>
                        </div>
                        <div class="flex justify-end mt-2">
                            <a href="#" onclick="showForm('forgot-form'); return false;" class="text-sm text-red-600 hover:text-red-700 hover:underline font-semibold transition-colors">¿Olvidaste tu contraseña?</a>
                        </div>
                    </div>

                    <button type="submit" name="login" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3.5 px-4 rounded-xl shadow-lg shadow-red-600/30 transition-all transform hover:-translate-y-0.5 active:translate-y-0 mt-2">
                        Iniciar Sesión
                    </button>
                </form>

                <p class="text-center mt-8 text-slate-500 text-sm font-medium">
                    ¿No tienes una cuenta? <a href="#" onclick="showForm('register-form'); return false;" class="text-red-600 hover:underline font-bold border-0">Regístrate</a>
                </p>
            </div>

            <!-- REGISTRO -->
            <div class="form-box" id="register-form">
                <button type="button" onclick="showForm('login-form')" class="mb-4 w-fit flex items-center gap-1.5 text-sm font-bold text-slate-400 hover:text-red-600 transition-colors">
                    <i class='bx bx-left-arrow-alt text-xl'></i> Volver
                </button>
                <div class="text-center mb-6">
                    <h2 class="text-3xl font-bold text-slate-800 tracking-tight">Crea una cuenta</h2>
                    <p class="text-slate-500 mt-2">Rellena los datos para comenzar</p>
                </div>
                
                <?= !empty($errors['register']) ? '<div class="bg-rose-50 border border-rose-200 text-rose-600 px-4 py-3 rounded-xl mb-6 text-sm text-center font-medium shadow-sm"><i class="bx bxs-error-circle mr-1"></i> '.$errors['register'].'</div>' : '' ?>

                <form action="login_register.php" method="post" class="space-y-4">
                    <div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                <i class='bx bx-user text-xl'></i>
                            </div>
                            <input type="text" name="usuario" placeholder="Nombre completo" class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-red-500/10 focus:border-red-500 text-slate-700 outline-none transition-all font-medium" required>
                        </div>
                    </div>
                    <div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                <i class='bx bx-envelope text-xl'></i>
                            </div>
                            <input type="email" name="email" placeholder="Correo Electrónico" class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-red-500/10 focus:border-red-500 text-slate-700 outline-none transition-all font-medium" required>
                        </div>
                    </div>
                    <div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                <i class='bx bx-lock-alt text-xl'></i>
                            </div>
                            <input type="password" id="register_password" name="password" placeholder="Contraseña" class="w-full pl-11 pr-12 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-red-500/10 focus:border-red-500 text-slate-700 outline-none transition-all font-medium" required>
                            <button type="button" onclick="togglePasswordVisibility('register_password', this)" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600 transition-colors focus:outline-none">
                                <i class='bx bx-show text-xl'></i>
                            </button>
                        </div>
                    </div>
                    <div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                <i class='bx bx-shield text-xl'></i>
                            </div>
                            <select name="role" id="role-select" class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-red-500/10 focus:border-red-500 text-slate-700 outline-none transition-all font-medium appearance-none" required>
                                <option value="user" selected>Nivel: Usuario Regular</option>
                                <option value="admin">Nivel: Administrador</option>
                            </select>
                        </div>
                    </div>

                    <div id="admin-key-container" class="hidden">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-red-500">
                                <i class='bx bx-key text-xl'></i>
                            </div>
                            <input type="password" name="admin_key" id="admin_key" placeholder="Clave secreta para Administrador" class="w-full pl-11 pr-4 py-3 bg-red-50 border border-red-200 rounded-xl focus:ring-4 focus:ring-red-500/10 focus:border-red-500 text-slate-700 outline-none transition-all font-medium">
                        </div>
                        <p class="text-xs text-red-600 mt-1 ml-1 font-medium"><i class='bx bx-info-circle mr-1'></i>Requerida por seguridad</p>
                    </div>

                    <button type="submit" name="register" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-bold py-3.5 px-4 rounded-xl shadow-lg shadow-slate-800/30 transition-all transform hover:-translate-y-0.5 active:translate-y-0 mt-4">
                        Completar Registro
                    </button>
                </form>
            </div>

            <!-- RECUPERAR CONTRASEÑA -->
            <div class="form-box" id="forgot-form">
                <button type="button" onclick="showForm('login-form')" class="mb-4 w-fit flex items-center gap-1.5 text-sm font-bold text-slate-400 hover:text-red-600 transition-colors">
                    <i class='bx bx-left-arrow-alt text-xl'></i> Volver
                </button>
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4 text-red-600 text-3xl">
                        <i class='bx bx-check-shield'></i>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Pregunta de Seguridad</h2>
                </div>

                <?= !empty($errors['security']) ? '<div class="bg-rose-50 border border-rose-200 text-rose-600 px-4 py-3 rounded-xl mb-6 text-sm text-center font-medium shadow-sm">'.$errors['security'].'</div>' : '' ?>

                <form action="process_security_question.php" method="post" class="space-y-4">
                    <div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400"><i class='bx bx-envelope text-xl'></i></div>
                            <input type="email" name="email" placeholder="Ingresa tu correo" class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-red-500/10 focus:border-red-500 text-slate-700 outline-none font-medium" required>
                        </div>
                    </div>
                    <div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400"><i class='bx bx-question-mark text-xl'></i></div>
                            <select name="pregunta" class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-red-500/10 focus:border-red-500 text-slate-700 outline-none font-medium appearance-none" required>
                                <option value="" disabled selected>Elige tu pregunta de seguridad...</option>
                                <option value="color">¿Cuál es tu color favorito?</option>
                                <option value="mascota">¿Nombre de tu primera mascota?</option>
                                <option value="ciudad">¿Ciudad donde naciste?</option>
                                <option value="madre">¿Segundo nombre de tu madre?</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400"><i class='bx bx-key text-xl'></i></div>
                            <input type="password" name="respuesta" placeholder="Tu respuesta secreta" class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 text-slate-700 outline-none font-medium" required>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3.5 px-4 rounded-xl shadow-lg shadow-red-600/30 mt-4 transition-all">
                        Verificar Identidad
                    </button>
                    <div class="mt-4 pt-4 border-t border-slate-100 flex flex-col gap-3">
                        <button type="button" class="w-full py-2.5 bg-slate-100 text-slate-600 font-semibold rounded-xl hover:bg-slate-200 transition" data-bs-toggle="modal" data-bs-target="#modalPregunta">
                            <i class='bx bx-cog mr-1'></i> Configurar pregunta
                        </button>
                    </div>
                </form>
            </div>
            
        </div>
    </div>

    <!-- Modal de Configurar Pregunta (Bootstrap) -->
    <div class="modal fade" id="modalPregunta" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl rounded-3xl overflow-hidden">
          <div class="bg-slate-800 p-6 flex flex-row items-center justify-between">
            <h5 class="flex items-center text-white font-bold text-lg m-0"><i class="bx bx-shield-quarter mr-2 text-2xl"></i> Mejorar Seguridad</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <form action="process_set_security_question.php" method="post" autocomplete="off">
            <div class="p-6 bg-white space-y-4">
              <?php if (!empty($success)): ?>
                <div class="bg-emerald-50 text-emerald-700 p-3 rounded-xl mb-4 text-sm font-semibold flex items-center shadow-sm">
                  <i class="bx bx-check-circle mr-2 text-lg"></i> <?= $success ?>
                </div>
                <script>setTimeout(function(){ window.location.href = 'index.php'; }, 2000);</script>
              <?php endif; ?>
              
              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Correo electrónico actual</label>
                <input type="email" name="email" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-slate-800" required>
              </div>
              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Elige una pregunta</label>
                <select name="pregunta" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-slate-800" required>
                  <option value="" disabled selected>Selecciona una opción...</option>
                  <option value="color">¿Cuál es tu color favorito?</option>
                  <option value="mascota">¿Nombre de tu primera mascota?</option>
                  <option value="ciudad">¿Ciudad donde naciste?</option>
                  <option value="madre">¿Segundo nombre de tu madre?</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Respuesta secreta</label>
                <input type="password" name="respuesta" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-slate-800" required>
              </div>
            </div>
            <div class="p-5 bg-slate-50 border-t border-slate-100 flex justify-end gap-3 rounded-b-3xl">
              <button type="button" class="px-5 py-2.5 rounded-xl text-slate-600 bg-white border border-slate-200 hover:bg-slate-100 font-semibold" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="px-5 py-2.5 rounded-xl text-white bg-slate-800 hover:bg-slate-900 font-semibold shadow-md active:scale-95 transition-transform">Guardar Cambios</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Scripts Bootstrap local -->
    <script src="/login_register12/public/bootstrap5/js/popper.min.js"></script>
    <script src="/login_register12/public/bootstrap5/js/bootstrap.min.js"></script>

    <script>
    // Mostrar formulario principal al cargar si no hay variables de servidor
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (!empty($errors['register'])): ?>
            showForm('register-form');
        <?php elseif (!empty($errors['security'])): ?>
            showForm('forgot-form');
            var myModal = new bootstrap.Modal(document.getElementById('modalPregunta'));
            myModal.show();
        <?php elseif (!empty($success)): ?>
            showForm('forgot-form');
            var myModal = new bootstrap.Modal(document.getElementById('modalPregunta'));
            myModal.show();
        <?php else: ?>
            showForm('login-form');
        <?php endif; ?>
    });

    function showForm(formId) {
        const forms = document.querySelectorAll('.form-box');
        forms.forEach(form => {
            if(form.classList.contains('active')) {
                form.classList.remove('active');
                setTimeout(() => { form.style.display = 'none'; }, 200); 
            }
        });

        setTimeout(() => {
            const target = document.getElementById(formId);
            target.style.display = 'block';
            setTimeout(() => { target.classList.add('active'); }, 20);
        }, 220);
    }
    
    // Si cierran el modal, volver a ocultarlo normal
    var modalPreguntaEl = document.getElementById('modalPregunta');
    if (modalPreguntaEl) {
      modalPreguntaEl.addEventListener('hidden.bs.modal', function () {
        // No forzamos regresar al login, los dejamos en el forgot-form o el form que estuvieran
      });
    }

    // Mostrar/ocultar campo de clave de admin en registro
    const roleSelect = document.getElementById('role-select');
    const adminKeyContainer = document.getElementById('admin-key-container');
    const adminKeyInput = document.getElementById('admin_key');

    if (roleSelect) {
        roleSelect.addEventListener('change', function() {
            if (this.value === 'admin') {
                adminKeyContainer.classList.remove('hidden');
                adminKeyInput.required = true;
            } else {
                adminKeyContainer.classList.add('hidden');
                adminKeyInput.required = false;
                adminKeyInput.value = '';
            }
        });
    }

    function togglePasswordVisibility(inputId, button) {
        const input = document.getElementById(inputId);
        const icon = button.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bx-show');
            icon.classList.add('bx-hide');
        } else {
            input.type = 'password';
            icon.classList.remove('bx-hide');
            icon.classList.add('bx-show');
        }
    }
    </script>
</body>
</html>