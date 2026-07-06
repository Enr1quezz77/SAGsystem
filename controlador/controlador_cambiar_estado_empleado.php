<?php
if (!empty($_GET["id_empleado_estado"]) && !empty($_GET["nuevo_estado"])) {
    $id = (int)$_GET["id_empleado_estado"];
    $nuevo_estado = $_GET["nuevo_estado"] === 'Activo' ? 'Activo' : 'Suspendido';
    $sql = $conexion->query("UPDATE empleado SET estado='$nuevo_estado' WHERE id_empleado=$id");
    if ($sql == true) { ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "ESTADO ACTUALIZADO",
                    icon: "success",
                    text: "El empleado ahora está <?= $nuevo_estado ?>",
                    confirmButtonColor: "#10b981"
                });
                if(window.addNotification) window.addNotification("info", "Estado del empleado cambiado a <?= $nuevo_estado ?>");
            });
        </script>
    <?php } else { ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "ERROR",
                    icon: "error",
                    text: "Error al cambiar estado",
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
