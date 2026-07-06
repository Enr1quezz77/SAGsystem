<?php
// process_set_security_question.php
// Guarda la pregunta y respuesta de seguridad para el usuario
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['email']) && !empty($_POST['pregunta']) && !empty($_POST['respuesta'])) {
    $email = trim($_POST['email']);
    $pregunta = trim($_POST['pregunta']);
    $respuesta = trim($_POST['respuesta']);
    $respuesta_hash = password_hash($respuesta, PASSWORD_DEFAULT);
    // Verificar si el usuario existe en la tabla usuario
    $check = $conn->prepare("SELECT id_usuario FROM usuario WHERE email=?");
    $check->bind_param('s', $email);
    $check->execute();
    $result = $check->get_result();
    if ($result && $result->num_rows > 0) {
        $stmt = $conn->prepare("UPDATE usuario SET pregunta_seguridad=?, respuesta_seguridad=? WHERE email=?");
        $stmt->bind_param('sss', $pregunta, $respuesta_hash, $email);
        if ($stmt->execute()) {
            $_SESSION['security_success'] = 'Pregunta de seguridad guardada correctamente. Ahora puedes recuperar tu contraseña usando esta pregunta.';
        } else {
            $_SESSION['security_error'] = 'Error al guardar la pregunta de seguridad.';
        }
    } else {
        // Si no existe en usuario, buscar en users
        $check2 = $conn->prepare("SELECT id FROM users WHERE email=?");
        $check2->bind_param('s', $email);
        $check2->execute();
        $result2 = $check2->get_result();
        if ($result2 && $result2->num_rows > 0) {
            // Agregar columnas si no existen
            $conn->query("ALTER TABLE users ADD COLUMN IF NOT EXISTS pregunta_seguridad VARCHAR(255) NULL");
            $conn->query("ALTER TABLE users ADD COLUMN IF NOT EXISTS respuesta_seguridad VARCHAR(255) NULL");
            $stmt2 = $conn->prepare("UPDATE users SET pregunta_seguridad=?, respuesta_seguridad=? WHERE email=?");
            $stmt2->bind_param('sss', $pregunta, $respuesta_hash, $email);
            if ($stmt2->execute()) {
                $_SESSION['security_success'] = 'Pregunta de seguridad guardada correctamente. Ahora puedes recuperar tu contraseña usando esta pregunta.';
            } else {
                $_SESSION['security_error'] = 'Error al guardar la pregunta de seguridad.';
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
