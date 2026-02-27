<?php
session_start();
if (empty($_SESSION['rol']) && empty($_SESSION['nombre_usuario'])) {
    header("Location: ../index.php");
    exit;
}
define("BASE_URL", "http://localhost:8080/");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            padding: 20px;
            height: 100vh;
        }

        .admin-container {
            max-width: 90%;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 15px;
            height: 100%;
        }

        .admin-container div {
            flex: 1;
        }

        h1 {
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="admin-container">
        <div>
            <h1>Panel de Administración de la Choza Nautica</h1>
            <div class="row row-cols-1 row-cols-sm-2 g-2">
                <div class="col">
                    <a class="btn btn-success w-100" href="dashboard_ventas.php">
                        <i class="fa-solid fa-sliders"></i>Dashboard</a>
                </div>

                <div class="col">
                    <a class="btn btn-warning w-100" href="admin_platos.php"><i class="fa-solid fa-bowl-rice"></i>Gestionar Platos</a>
                </div>

                <div class="col">
                    <a class="btn btn-primary w-100" href="admin_pedidos.php"><i class="fa-solid fa-square-pen" style="margin-right: 5px;"></i>Gestionar Pedidos</a>
                </div>

                <div class="col">
                    <a class="btn btn-primary w-100" href="<?= 'inventarios/index.php' ?>" ><i class="fa-solid fa-jar-wheat" style="margin-right: 5px;"></i>Gestionar Inventarios</a>
                </div>

                <div class="col">
                    <a class="btn btn-secondary w-100" href="../index.php"><i class="fa-solid fa-rotate-left"></i>Ingresar a la página</a>
                </div>

            </div>
        </div>
        <div class="imagen-rigth">
            <img style="width:100%; border-radius: 25px;" src="../img/paradise.jpg" alt="">
        </div>
    </div>

    <script src="../assets/dist/js/bootstrap.bundle.min.js"
        class="astro-vvvwv3sm"></script>
</body>

</html>