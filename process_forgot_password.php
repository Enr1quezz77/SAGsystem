<?php

session_start();
require 'config.php';
require 'vendor/autoload.php'; // PHPMailer por Composer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['email'])) {
    $email = trim($_POST['email']);
    // Buscar primero en usuario
    $stmt = $conn->prepare("SELECT id, usuario AS nombre FROM usuario WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($user = $result->fetch_assoc()) {
        $codigo = rand(100000, 999999);
        $_SESSION['reset_email'] = $email;
        $_SESSION['reset_code'] = $codigo;
        $_SESSION['reset_code_time'] = time();
        $_SESSION['reset_table'] = 'usuario';
        // Enviar correo con PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.tu-servidor.com'; // Cambia por tu servidor SMTP
            $mail->SMTPAuth = true;
            $mail->Username = 'tu-correo@dominio.com'; // Cambia por tu usuario SMTP
            $mail->Password = 'tu-password'; // Cambia por tu password SMTP
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->setFrom('no-reply@tudominio.com', 'Soporte');
            $mail->addAddress($email, $user['nombre']);
            $mail->Subject = 'Código de recuperación de contraseña';
            $mail->Body = "Hola {$user['nombre']},\n\nTu código de recuperación es: $codigo\n\nSi no solicitaste esto, ignora este mensaje.";
            $mail->send();
            header('Location: reset_password.php?step=code');
            exit;
        } catch (Exception $e) {
            $_SESSION['correo_error'] = 'No se pudo enviar el correo. Intenta más tarde.';
            $_SESSION['active_form'] = 'forgot';
            $_SESSION['active_tab'] = 'correo';
            header('Location: index.php');
            exit;
        }
    } else {
        // Buscar en users
        $stmt2 = $conn->prepare("SELECT id, usuario AS nombre FROM users WHERE email = ?");
        $stmt2->bind_param('s', $email);
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        if ($user2 = $result2->fetch_assoc()) {
            $codigo = rand(100000, 999999);
            $_SESSION['reset_email'] = $email;
            $_SESSION['reset_code'] = $codigo;
            $_SESSION['reset_code_time'] = time();
            $_SESSION['reset_table'] = 'users';
            // Enviar correo con PHPMailer
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.tu-servidor.com'; // Cambia por tu servidor SMTP
                $mail->SMTPAuth = true;
                $mail->Username = 'tu-correo@dominio.com'; // Cambia por tu usuario SMTP
                $mail->Password = 'tu-password'; // Cambia por tu password SMTP
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                $mail->setFrom('no-reply@tudominio.com', 'Soporte');
                $mail->addAddress($email, $user2['nombre']);
                $mail->Subject = 'Código de recuperación de contraseña';
                $mail->Body = "Hola {$user2['nombre']},\n\nTu código de recuperación es: $codigo\n\nSi no solicitaste esto, ignora este mensaje.";
                $mail->send();
                header('Location: reset_password.php?step=code');
                exit;
            } catch (Exception $e) {
                $_SESSION['correo_error'] = 'No se pudo enviar el correo. Intenta más tarde.';
                $_SESSION['active_form'] = 'forgot';
                $_SESSION['active_tab'] = 'correo';
                header('Location: index.php');
                exit;
            }
        } else {
            $_SESSION['correo_error'] = 'El correo no está registrado.';
            $_SESSION['active_form'] = 'forgot';
            $_SESSION['active_tab'] = 'correo';
            header('Location: index.php');
            exit;
        }
    }
}
?>
