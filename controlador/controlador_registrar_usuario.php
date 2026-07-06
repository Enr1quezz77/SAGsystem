<?php

if (!empty($_POST["btnregistrar"])) {
    if (!empty($_POST["txtusuario"]) && !empty($_POST["txtemail"]) && !empty($_POST["txtpassword"])) {
        $usuario = $_POST["txtusuario"];
        $email = $_POST["txtemail"];
        // ¡CRÍTICO!: Usar password_hash para compatibilidad con el index.php
        $password = password_hash($_POST["txtpassword"], PASSWORD_DEFAULT);
        $rol = 'user'; // Por defecto, se crea como user

        $sql = $conexion->query("SELECT count(*) as 'total' FROM users WHERE email='$email' OR usuario='$usuario'");

        if ($sql && $sql->fetch_object()->total > 0) {
            ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'USUARIO DUPLICADO',
                        text: "El usuario o correo electrónico ya existe",
                        confirmButtonColor: '#dc2626'
                    });
                });
            </script>
            <?php
        } else {
            $registro = $conexion->query("INSERT INTO users(usuario, email, password, role) VALUES ('$usuario', '$email', '$password', '$rol')");

            if ($registro === true) {
                ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'success',
                            title: '¡REGISTRADO!',
                            text: "El usuario se ha registrado correctamente",
                            confirmButtonColor: '#10b981',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = 'usuario.php';
                        });
                    });
                </script>
                <?php
            } else {
                ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'INCORRECTO',
                            text: "Error al registrar usuario: <?= htmlspecialchars($conexion->error, ENT_QUOTES, 'UTF-8') ?>",
                            confirmButtonColor: '#dc2626'
                        });
                    });
                </script>
                <?php
            }
        }
    } else {
        ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'warning',
                    title: 'ADVERTENCIA',
                    text: "Por favor, llena todos los campos",
                    confirmButtonColor: '#f59e0b'
                });
            });
        </script>
        <?php
    }
}

?>