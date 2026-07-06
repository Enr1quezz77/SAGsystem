<?php
if (!empty($_POST["btnmodificar"])) {
    if (!empty($_POST["id_amonestacion"]) and !empty($_POST["empleado"]) and !empty($_POST["motivo"]) and !empty($_POST["gravedad"])) {
        $id = $_POST["id_amonestacion"];
        $empleado = $_POST["empleado"];
        $motivo = $_POST["motivo"];
        $gravedad = $_POST["gravedad"];
        $observacion = !empty($_POST["observacion"]) ? $_POST["observacion"] : "";

        $sql = $conexion->query("UPDATE amonestacion SET id_empleado=$empleado, motivo='$motivo', gravedad='$gravedad', observacion='$observacion' WHERE id_amonestacion=$id");
        if ($sql == true) { ?>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        title: "CORRECTO",
                        icon: "success",
                        text: "Amonestación modificada correctamente",
                        confirmButtonColor: "#10b981"
                    });
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
                    text: "Revisa los campos obligatorios",
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
