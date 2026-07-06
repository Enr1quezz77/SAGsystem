<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1, user-scalable=no" name="viewport">
    <title>Dashboard</title>
    <link rel="icon" type="image/png" href="../img/logo.png">
    <link href="../public/bootstrap5/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../public/fontawesome/css/all.min.css">
    <script src="https://kit.fontawesome.com/8b02b9b95f.js" crossorigin="anonymous"></script>
    <style>
        body {
            font-family: 'Montserrat', 'Poppins', 'Roboto', Arial, sans-serif;
            background: rgb(240, 248, 255);
            margin: 0;
        }

        .dashboard-container {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-top: 20px;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .dashboard-header h1 {
            font-size: 1.8rem;
            font-weight: bold;
            color: #007bff;
            display: flex;
            align-items: center;
        }

        .dashboard-header h1 i {
            margin-right: 10px;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .card-title {
            font-size: 1.2rem;
            font-weight: bold;
        }

        .card-icon {
            font-size: 2rem;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <?php include '../vista/layout/sidebar.php'; ?>

    <!-- Contenido principal -->
    <div class="main-content">
        <div class="container">
            <div class="dashboard-container">
                <div class="dashboard-header">
                    <h1><i class="fas fa-tachometer-alt"></i> Bienvenido al Dashboard</h1>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card text-center">
                            <div class="card-body">
                                <div class="card-icon text-primary">
                                    <i class="fas fa-users"></i>
                                </div>
                                <h5 class="card-title">Gestión de Empleados</h5>
                                <a href="empleados.php" class="btn btn-primary">Ir</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center">
                            <div class="card-body">
                                <div class="card-icon text-success">
                                    <i class="fas fa-user"></i>
                                </div>
                                <h5 class="card-title">Gestión de Usuarios</h5>
                                <a href="usuarios.php" class="btn btn-success">Ir</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center">
                            <div class="card-body">
                                <div class="card-icon text-warning">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <h5 class="card-title">Reportes</h5>
                                <a href="reportes.php" class="btn btn-warning">Ir</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../public/bootstrap5/js/bootstrap.bundle.min.js"></script>
</body>
</html>