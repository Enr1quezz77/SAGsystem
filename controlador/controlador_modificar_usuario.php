<?php

if (!empty($_POST["btnmodificar"])) {
   if (!empty($_POST["txtnombre"]) and !empty($_POST["txtapellido"]) and !empty($_POST["txtusuario"])) {
    $nombre = $_POST["txtnombre"];
    $apellido = $_POST["txtapellido"];
    $usuario = $_POST["txtusuario"];

    $id = $_POST["txtid"];

    $sql = $conexion->query(" SELECT count(*) as 'total' FROM usuario WHERE usuario='$usuario'=$id ");

    if ($sql && $sql->fetch_object()->total > 0) {
        ?>
        <script>
            $(function notificacion() {
                new PNotify({
                    title: "ERROR",
                    type: "error",
                    text: "El usuario <?= htmlspecialchars($usuario, ENT_QUOTES, 'UTF-8') ?> ya existe", // Usar htmlspecialchars para prevenir XSS
                    styling: "bootstrap3"
                });
            });
        </script>
        <?php
    } else {
        $updateStmt = $conexion->prepare("UPDATE usuario SET nombre='$nombre', apellido='$apellido', usuario='$usuario' WHERE id_usuario=$id ");
        $success = $updateStmt->execute();
        if ($success) {
            ?>
            <script>
                $(function notificacion() {
                    new PNotify({
                        title: "CORRECTO",
                        type: "success",
                        text: "El usuario se ha modificado correctamente",
                        styling: "bootstrap3"
                    });
                });
            </script>
            <?php
        } else {
            ?>
            <script>
                $(function notificacion() {
                    new PNotify({
                        title: "INCORRECTO",
                        type: "error",
                        text: "Error al modificar usuario. Detalles: <?= htmlspecialchars($conexion->error, ENT_QUOTES, 'UTF-8') ?>", // Opcional: mostrar error de DB (con cuidado en producción)
                        styling: "bootstrap3"
                    });
                });
            </script>
            <?php
        }
    } 

} else {
    ?>
    <script>
        $(function notificacion() {
            new PNotify({
                title: "ERROR",
                type: "error",
                text: "Los campos estan vacios",
                styling: "bootstrap3"
            });
        });
    </script>
    <?php
} 
} 

?>