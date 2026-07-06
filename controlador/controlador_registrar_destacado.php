<?php
if (!empty($_POST["btnregistrar"])) {
    if (!empty($_POST["empleado"]) and !empty($_POST["motivo"]) and !empty($_POST["nivel"])) {
        $empleado = $_POST["empleado"];
        $motivo = $_POST["motivo"];
        $nivel = $_POST["nivel"];

        $sql = $conexion->query("INSERT INTO destacado (id_empleado, motivo, nivel) VALUES ($empleado, '$motivo', '$nivel')");
        if ($sql == true) { ?>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        title: "CORRECTO",
                        icon: "success",
                        text: "Mérito registrado correctamente",
                        confirmButtonColor: "#10b981"
                    });
                    if(window.addNotification) window.addNotification("success", "Mérito asignado a empleado exitosamente");
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
