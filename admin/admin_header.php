<?php
// header_admin.php
define("BASE_URL", "http://localhost:8080/");

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>
    <link href="<?= BASE_URL ?>assets/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <!-- using Bootstrap v5.3 -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        header {
            background: #007bff;
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            margin: 0;
            font-size: 22px;
        }

        header nav a {
            color: white;
            text-decoration: none;
            margin-left: 15px;
            font-weight: bold;
        }

        header nav a:hover {
            text-decoration: underline;
        }

        .container {
            min-height: 100vh;
            /* altura mínima igual a la ventana */
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .button {
            background: #007bff;
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
        }

        .button:hover {
            background: #005fa3;
        }
    </style>
</head>

<body>

    <header>
        <h1>Panel de Administración</h1>
        <nav>
            <a href="<?= BASE_URL ?>admin/admin_platos.php">Gestionar Platos</a>
            <a href="<?= BASE_URL ?>admin/admin_pedidos.php">Gestionar Pedidos</a>
            <a href="<?= BASE_URL ?>admin/admin.php" style="background:#6c757d; padding:5px 10px; border-radius:4px;">Inicio</a>
        </nav>
    </header>

    <main class="container">