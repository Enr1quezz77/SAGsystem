<?php
if (!empty($_POST["btnregistrar"])) {
    if (!empty($_POST["empleado"]) and !empty($_POST["motivo"]) and !empty($_POST["gravedad"])) {
        $empleado = $_POST["empleado"];
        $motivo = $_POST["motivo"];
        $gravedad = $_POST["gravedad"];
        $observacion = !empty($_POST["observacion"]) ? $_POST["observacion"] : "";
        $suspender = isset($_POST["suspender"]) ? $_POST["suspender"] : "0";

        $sql = $conexion->query("INSERT INTO amonestacion (id_empleado, motivo, gravedad, observacion) VALUES ($empleado, '$motivo', '$gravedad', '$observacion')");
        if ($sql == true) { 
            if ($suspender === "1") {
                $conexion->query("UPDATE empleado SET estado='Suspendido' WHERE id_empleado=$empleado");
            }
            ?>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        title: "CORRECTO",
                        icon: "success",
                        text: "Amonestación registrada correctamente",
                        confirmButtonColor: "#10b981"
                    });
                    if(window.addNotification) window.addNotification("warning", "Se registró una amonestación " + (<?= $suspender ?> == 1 ? "y se suspendió al empleado" : ""));
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
                    text: "Todos los campos principales son obligatorios",
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
