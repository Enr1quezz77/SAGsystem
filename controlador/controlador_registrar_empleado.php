<?php

if (!empty($_POST["btnregistrar"])) {
    if (!empty($_POST["txtnombre"]) and !empty($_POST["txtapellido"]) and !empty($_POST["txtcargo"]) and !empty($_POST["txtdni"])) {
        $nombre = $_POST["txtnombre"];
        $apellido = $_POST["txtapellido"];
        $cargo = $_POST["txtcargo"];
        $dni = $_POST["nacionalidad"] . $_POST["txtdni"];
        
        $foto_name = "";
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $foto_name = "emp_" . time() . "_" . uniqid() . "." . $ext;
            $destino = "../img/empleados/" . $foto_name;
            if (!is_dir("../img/empleados/")) {
                mkdir("../img/empleados/", 0777, true);
            }
            move_uploaded_file($_FILES['foto']['tmp_name'], $destino);
        }
        
        $sql = $conexion->query(" insert into empleado(nombre,apellido,cargo,dni,foto)values('$nombre','$apellido',$cargo,'$dni','$foto_name') ");
        if ($sql == true) { 
            
            // Obtener el ID numérico insertado que se usará en el reloj
            $id_empleado_nuevo = $conexion->insert_id;
            
            // Conectar al biométrico para añadirle el nombre y prepararlo para la huella
            require_once 'zklibrary.php';
            $zk = new ZKLibrary('192.168.1.200', 4370);
            if ($zk->connect()) {
                // zklibrary: setUser(uid, userid, name, password, role)
                // Usamos el ID autoincremental como "uid" interno y como "userid" (el que marcamos con el dedo)
                // Se corta el nombre para que quepa en la pantallita del reloj
                $zk->setUser($id_empleado_nuevo, $id_empleado_nuevo, substr(strtoupper($nombre), 0, 10), '', 0);
                $zk->disconnect();
            }
        ?>
       <script>
      document.addEventListener("DOMContentLoaded", function() {
             Swal.fire({
             title: "CORRECTO",
             icon: "success",
             text: "Empleado registrado correctamente",
             confirmButtonColor: "#10b981"
                });
             if(window.addNotification) window.addNotification("success", "Nuevo empleado registrado: <?= $nombre . " " . $apellido ?>");
            });
        </script>
       <?php } else { 
           $dbError = addslashes($conexion->error);
       ?>
      <script>
      document.addEventListener("DOMContentLoaded", function() {
             Swal.fire({
             title: "INCORRECTO",
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