<?php
// process_security_question.php
// Verifica la pregunta y respuesta de seguridad y permite cambiar la contraseña si es correcta
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['email']) && !empty($_POST['pregunta']) && !empty($_POST['respuesta'])) {
    $email = trim($_POST['email']);
    $pregunta = trim($_POST['pregunta']);
    $respuesta = trim($_POST['respuesta']);
    // Buscar primero en usuario
    $stmt = $conn->prepare("SELECT pregunta_seguridad, respuesta_seguridad FROM usuario WHERE email=?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $row = $result->fetch_assoc()) {
        if ($row['pregunta_seguridad'] === $pregunta && password_verify($respuesta, $row['respuesta_seguridad'])) {
            $_SESSION['reset_email'] = $email;
            $_SESSION['reset_verified'] = true;
            $_SESSION['reset_table'] = 'usuario';
            header('Location: reset_password.php?step=security');
            exit;
        } else {
            $_SESSION['security_error'] = 'Pregunta o respuesta incorrecta. Intenta nuevamente.';
        }
    } else {
        // Buscar en users
        $stmt2 = $conn->prepare("SELECT pregunta_seguridad, respuesta_seguridad FROM users WHERE email=?");
        $stmt2->bind_param('s', $email);
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        if ($result2 && $row2 = $result2->fetch_assoc()) {
            if ($row2['pregunta_seguridad'] === $pregunta && password_verify($respuesta, $row2['respuesta_seguridad'])) {
                $_SESSION['reset_email'] = $email;
                $_SESSION['reset_verified'] = true;
                $_SESSION['reset_table'] = 'users';
                header('Location: reset_password.php?step=security');
                exit;
            } else {
                $_SESSION['security_error'] = 'Pregunta o respuesta incorrecta. Intenta nuevamente.';
            }
        } else {
            $_SESSION['security_error'] = 'El correo no existe en el sistema.';
        }
    }
    $_SESSION['active_form'] = 'forgot';
    $_SESSION['active_tab'] = 'seguridad';
    header('Location: index.php');
    exit;
}
?>
