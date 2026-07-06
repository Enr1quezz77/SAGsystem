<?php
include_once '../modelo/auditoria_helper.php';

if (!empty($_GET["id"])) {
    $id = $_GET["id"];
    
    // Obtener nombre del empleado antes de eliminarlo
    $sql_info = $conexion->query("SELECT nombre, apellido FROM empleado WHERE id_empleado=$id");
    $empleado_info = $sql_info->fetch_object();
    $nombre_completo = $empleado_info ? $empleado_info->nombre . " " . $empleado_info->apellido : "ID $id";

    $sql = $conexion->query(" delete from empleado where id_empleado=$id ");
    if ($sql == true) {
        registrar_auditoria($conexion, 'Eliminar', 'Empleados', "Eliminó al empleado: $nombre_completo");
        ?>
        <script>
             document.addEventListener("DOMContentLoaded", function() {
                 Swal.fire({
                 title: "CORRECTO",
                 icon: "success",
                 text: "Empleado eliminado correctamente",
                 confirmButtonColor: "#ef4444"
              });
         });
       </script>
     <?php } else { ?>
        <script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        title: "INCORRECTO",
                        icon: "error",
                        text: "Error al eliminar empleado",
                        confirmButtonColor: "#ef4444"
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