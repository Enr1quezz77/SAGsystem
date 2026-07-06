<?php
if (!empty($_POST["btnmodificar"])) {
    if (!empty($_POST["txtid"]) and !empty($_POST["txtnombre"]) and !empty($_POST["txtapellido"]) and !empty($_POST["txtdni"]) and !empty($_POST["txtcargo"])) {
        $id = $_POST["txtid"];
        $nombre = $_POST["txtnombre"];
        $apellido = $_POST["txtapellido"];
        $dni = $_POST["nacionalidad"] . $_POST["txtdni"];
        $cargo = $_POST["txtcargo"];
        
        $sqlActualizarFoto = "";
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $foto_name = "emp_" . time() . "_" . uniqid() . "." . $ext;
            $destino = "../img/empleados/" . $foto_name;
            if (!is_dir("../img/empleados/")) {
                mkdir("../img/empleados/", 0777, true);
            }
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $destino)) {
                $sqlActualizarFoto = ", foto='$foto_name'";
            }
        }
        
        $sql = $conexion->query(" update empleado set nombre='$nombre', apellido='$apellido', dni='$dni', cargo=$cargo $sqlActualizarFoto where id_empleado=$id ");
        if ($sql == true) { ?>
       <script>
      document.addEventListener("DOMContentLoaded", function() {
             Swal.fire({
             title: "CORRECTO",
             icon: "success",
             text: "El empleado se ha modificado correctamente",
             confirmButtonColor: "#f59e0b"
                });
            });
        </script>
       <?php } else { ?>
       <script>
       document.addEventListener("DOMContentLoaded", function() {
              Swal.fire({
              title: "INCORRECTO",
              icon: "error",
              text: "Error al modificar empleado",
              confirmButtonColor: "#ef4444"
                 });
             });
         </script>
       <?php }
    } else { ?>
       <script>
      document.addEventListener("DOMContentLoaded", function() {
             Swal.fire({
             title: "INCORRECTO",
             icon: "error",
             text: "Los campos estan vacios",
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