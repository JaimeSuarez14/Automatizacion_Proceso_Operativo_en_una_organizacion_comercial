<?php
session_start();
if (empty($_SESSION['rol']) && empty($_SESSION['nombre_usuario'])) {
    header("Location: ../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
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

        .btn {
            display: block;
            width: auto;
            margin: 10px 0;
            padding: 12px;
            text-align: center;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 8px;
        }

        .btn:hover {
            opacity: 0.9;
        }
    </style>
</head>

<body>

    <div class="admin-container">
        <div>
            <h1>Panel de Administración de la Choza Nautica</h1>

            <a class="btn" href="admin_platos.php"><i class="fa-solid fa-bowl-rice" style="margin-right: 5px;"></i>Gestionar Platos</a>
            <a class="btn" href="admin_pedidos.php"><i class="fa-solid fa-square-pen" style="margin-right: 5px;" style="margin-right: 5px;"></i>Gestionar Pedidos</a>
            <a class="btn" href="../index.php" style="background:#6c757d;"><i class="fa-solid fa-rotate-left" style="margin-right: 5px;"></i>Ingresar a la página</a>
        </div>
        <div class="imagen-rigth">
            <img style="width:100%; border-radius: 25px;" src="../img/paradise.jpg" alt="">
        </div>
    </div>

</body>

</html>