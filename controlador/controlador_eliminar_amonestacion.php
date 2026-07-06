<?php
if (!empty($_GET["id_amonestacion"])) {
    $id = $_GET["id_amonestacion"];
    $sql = $conexion->query("DELETE FROM amonestacion WHERE id_amonestacion=$id");
    if ($sql == true) { ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "CORRECTO",
                    icon: "success",
                    text: "Amonestación eliminada correctamente",
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
    ?>
    <script>
        setTimeout(() => {
            window.history.replaceState(null, null, window.location.pathname);
        }, 0);
    </script>
<?php }
