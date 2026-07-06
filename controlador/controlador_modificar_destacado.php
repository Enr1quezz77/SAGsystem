<?php
if (!empty($_POST["btnmodificar"])) {
    if (!empty($_POST["id_destacado"]) and !empty($_POST["empleado"]) and !empty($_POST["motivo"]) and !empty($_POST["nivel"])) {
        $id = $_POST["id_destacado"];
        $empleado = $_POST["empleado"];
        $motivo = $_POST["motivo"];
        $nivel = $_POST["nivel"];

        $sql = $conexion->query("UPDATE destacado SET id_empleado=$empleado, motivo='$motivo', nivel='$nivel' WHERE id_destacado=$id");
        if ($sql == true) { ?>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        title: "CORRECTO",
                        icon: "success",
                        text: "Mérito modificado correctamente",
                        confirmButtonColor: "#10b981"
                    });
                    if(window.addNotification) window.addNotification("success", "Mérito actualizado exitosamente");
                });
            </script>
        <?php } else { 
            $dbError = addslashes($conexion->error);
        ?>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        title: "ERROR",
                        icon: "error",
                        text: "Error MySQL: <?= $dbError ?>",
                        confirmButtonColor: "#ef4444"
                    });
                });
            </script>
        <?php }
    } else { ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "ERROR",
                    icon: "error",
                    text: "Todos los campos son obligatorios",
                    confirmButtonColor: "#ef4444"
                });
            });
        </script>
    <?php }
    ?>
    <script>
        setTimeout(() => {
            window.history.replaceState(null, null, window.location.pathname);
        }, 0);
    </script>
<?php }
