<?php
session_start();
include 'config.php';

if (!$conn) {
    die("Error de conexión: " . mysqli_connect_error());
}

if(isset($_POST['token']) && isset($_POST['password'])){
    $token = $_POST['token'];
    $new_password = $_POST['password'];
    
    // Verificar token
    $stmt = $conn->prepare("SELECT email, expires_at FROM usuario.password_resets WHERE token=? LIMIT 1");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($row = $result->fetch_assoc()){
        if(strtotime($row['expires_at']) < time()){
            echo "El enlace ha expirado.";
            exit;
        } else {
            $email = $row['email'];
            // Cifrar la nueva contraseña
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            // Actualizar la contraseña en la tabla 'usuario'
            $stmt->close();
            $stmt = $conn->prepare("UPDATE usuario SET password=? WHERE email=?");
            $stmt->bind_param("ss", $hashed_password, $email);
            
            if($stmt->execute()){
                // Eliminar el token para evitar que se reutilice
                $stmt->close();
                $stmt = $conn->prepare("DELETE FROM usuario.password_resets WHERE token=?");
                $stmt->bind_param("s", $token);
                $stmt->execute();
                echo "Tu contraseña ha sido actualizada correctamente.";
            } else {
                echo "Hubo un error al actualizar la contraseña.";
            }
        }
    } else {
        echo "Enlace inválido.";
    }
} else {
    echo "Datos inválidos.";
}
?>
