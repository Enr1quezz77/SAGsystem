<?php
if (!empty($_GET["id_destacado"])) {
    $id = $_GET["id_destacado"];
    $sql = $conexion->query("DELETE FROM destacado WHERE id_destacado=$id");
    if ($sql == true) { ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "CORRECTO",
                    icon: "success",
                    text: "Mérito eliminado correctamente",
                    confirmButtonColor: "#10b981"
                });
                if(window.addNotification) window.addNotification("success", "Mérito eliminado exitosamente");
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
