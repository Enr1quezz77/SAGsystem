<?php
if (!empty($_GET["id"])) {
    $id = $_GET["id"];
    $sql = $conexion->query("DELETE FROM users WHERE id = $id");
    if ($sql == true) {?>
        <script>
             $(function notificacion() {
                 new PNotify({
                 title: "CORRECTO",
                 type: "success",
                 text: "Usuario eliminado correctamente",
                 styling: "bootstrap3"
              });
         });
       </script>
     <?php } else { ?>
        <script>
                $(function notificacion() {
                    new PNotify({
                        title: "INCORRECTO",
                        type: "error",
                        text: "Error al eliminar usuario",
                        styling: "bootstrap3"
                    });
                });
            </script>
    <?php } ?>
    <script>
   setTimeout(() => {
      window.history.replaceState(null,null,window.location.pathname); 
   }, 0);
</script>

<?php }
?>