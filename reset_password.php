<?php
// reset_password.php

// SQL para agregar campos de seguridad a la tabla usuario:
// ALTER TABLE usuario ADD COLUMN pregunta_seguridad VARCHAR(255) NULL, ADD COLUMN respuesta_seguridad VARCHAR(255) NULL;
//
// PHPMailer ya está integrado en process_forgot_password.php. Solo debes configurar:
// - $mail->Host = 'smtp.tu-servidor.com';
// - $mail->Username = 'tu-correo@dominio.com';
// - $mail->Password = 'tu-password';
// - $mail->setFrom('no-reply@tudominio.com', 'Soporte');
//
// Si usas Gmail, activa "acceso de apps menos seguras" o usa una contraseña de aplicación.
//
// El resto del flujo de recuperación y seguridad ya está implementado.

session_start();
require 'config.php';

// Paso 2: Mostrar formulario para nueva contraseña si verificado por pregunta de seguridad
if ((isset($_SESSION['reset_verified']) && $_SESSION['reset_verified']) && isset($_GET['step']) && $_GET['step'] === 'security') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nueva_password'])) {
        $email = $_SESSION['reset_email'] ?? '';
        $nueva = trim($_POST['nueva_password']);
        // Seguridad: Validar fortaleza de la contraseña (soporta UTF-8)
        if (mb_strlen($nueva, 'UTF-8') < 8 || !preg_match('/[A-Z]/u', $nueva) || !preg_match('/[a-z]/u', $nueva) || !preg_match('/[0-9]/u', $nueva)) {
            $error = 'La contraseña debe tener al menos 8 caracteres, incluir mayúsculas, minúsculas y números.';
        } else {
            $nueva_hash = password_hash($nueva, PASSWORD_DEFAULT);
            $email = strtolower(trim($email));
            // Buscar primero en users
            $stmt = $conn->prepare("SELECT id FROM users WHERE LOWER(email)=?");
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result && $result->num_rows > 0) {
                $stmt->close();
                $stmt = $conn->prepare("UPDATE users SET password=? WHERE LOWER(email)=?");
                $stmt->bind_param('ss', $nueva_hash, $email);
                if ($stmt->execute()) {
                    unset($_SESSION['reset_verified']);
                    unset($_SESSION['reset_email']);
                    unset($_SESSION['reset_table']);
                    $success = '¡Contraseña actualizada correctamente! Ahora puedes iniciar sesión.';
                } else {
                    $error = 'Error al actualizar la contraseña en users: ' . $stmt->error . ' | Email: ' . htmlspecialchars($email);
                }
            } else {
                $stmt->close();
                // CORREGIDO: usar id_usuario en vez de id
                $stmt = $conn->prepare("SELECT id_usuario FROM usuario WHERE LOWER(email)=?");
                $stmt->bind_param('s', $email);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result && $result->num_rows > 0) {
                    $stmt->close();
                    $stmt = $conn->prepare("UPDATE usuario SET password=? WHERE LOWER(email)=?");
                    $stmt->bind_param('ss', $nueva_hash, $email);
                    if ($stmt->execute()) {
                        unset($_SESSION['reset_verified']);
                        unset($_SESSION['reset_email']);
                        unset($_SESSION['reset_table']);
                        $success = '¡Contraseña actualizada correctamente! Ahora puedes iniciar sesión.';
                    } else {
                        $error = 'Error al actualizar la contraseña en usuario: ' . $stmt->error . ' | Email: ' . htmlspecialchars($email);
                    }
                } else {
                    $error = 'El usuario no existe. Email: ' . htmlspecialchars($email);
                }
            }
        }
    }
    // Formulario visual igual
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>S.A.G (DORE'S) - Nueva Contraseña</title>
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
        </style>
    </head>
    <body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-md bg-white rounded-3xl shadow-xl overflow-hidden border border-slate-100">
            <div class="bg-slate-800 p-6 text-center relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-white/5 rounded-full blur-2xl"></div>
                <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-red-500/20 rounded-full blur-2xl"></div>
                <div class="relative z-10">
                    <div class="w-16 h-16 bg-white/10 rounded-full flex items-center justify-center mx-auto mb-3 backdrop-blur-sm border border-white/20">
                        <i class='bx bx-lock-open-alt text-2xl text-white'></i>
                    </div>
                    <h2 class="text-2xl font-bold text-white tracking-tight">Nueva Contraseña</h2>
                    <p class="text-slate-300 mt-1 text-sm font-medium">Establece tu nueva credencial de acceso</p>
                </div>
            </div>
            
            <div class="p-8">
                <?php if (!empty($error)): ?>
                    <div class="bg-rose-50 border border-rose-200 text-rose-600 px-4 py-3 rounded-xl mb-6 text-sm text-center font-medium shadow-sm flex items-center justify-center">
                        <i class="bx bxs-error-circle mr-2 text-lg"></i> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($success)): ?>
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl mb-6 text-sm text-center font-medium shadow-sm flex items-center justify-center flex-col">
                        <div class="flex items-center mb-2">
                            <i class="bx bxs-check-circle mr-2 text-xl"></i> <span><?= htmlspecialchars($success) ?></span>
                        </div>
                    </div>
                    <a href="index.php" class="w-full flex items-center justify-center gap-2 bg-slate-800 hover:bg-slate-900 text-white font-bold py-3.5 px-4 rounded-xl shadow-lg shadow-slate-800/30 transition-all transform hover:-translate-y-0.5 active:translate-y-0">
                        <i class='bx bx-log-in-circle text-xl'></i> Volver al inicio de sesión
                    </a>
                <?php else: ?>
                    <form method="post" class="space-y-5">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5 ml-1">Nueva Contraseña</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                    <i class='bx bx-lock-alt text-xl'></i>
                                </div>
                                <input type="password" id="nueva_password" name="nueva_password" placeholder="Mínimo 8 caracteres, letras y números" class="w-full pl-11 pr-12 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-red-500/10 focus:border-red-500 text-slate-700 outline-none transition-all font-medium" required autofocus>
                                <button type="button" onclick="togglePasswordVisibility('nueva_password', this)" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600 transition-colors focus:outline-none">
                                    <i class='bx bx-show text-xl'></i>
                                </button>
                            </div>
                        </div>
                        
                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3.5 px-4 rounded-xl shadow-lg shadow-red-600/30 transition-all transform hover:-translate-y-0.5 active:translate-y-0 mt-2 flex justify-center items-center gap-2">
                            <i class='bx bx-save text-xl'></i> Guardar Contraseña
                        </button>
                        
                        <div class="text-center mt-6">
                            <a href="index.php" class="text-sm text-slate-500 hover:text-red-600 font-semibold transition-colors flex items-center justify-center gap-1">
                                <i class='bx bx-arrow-back'></i> Volver al inicio de sesión
                            </a>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
        
        <script>
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
    <?php
    exit;
}

// Si no, redirige al login y muestra el modal de login por defecto
header('Location: index.php?active_form=login');
exit;
?>
