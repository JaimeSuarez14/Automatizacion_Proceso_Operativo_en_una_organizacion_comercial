<?php
// header_admin.php
define("BASE_URL", "http://localhost:8080/");

session_start();
if (empty($_SESSION['rol']) && empty($_SESSION['nombre_usuario'])) {
    header("Location: index.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>
    <link rel="icon" type="image/x-icon" href="<?= BASE_URL ?>icons/logo.png">
    <link href="<?= BASE_URL ?>assets/dist/css/bootstrap.min.css" rel="stylesheet" />
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

    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm sticky-top" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%); border-bottom: 2px solid #e94560;">
        <div class="container-fluid px-4">

            <!-- Brand / Logo -->
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="<?= BASE_URL ?>admin/admin.php">
                <div style="width:32px; height:32px; background:#e94560; border-radius:8px; display:flex; align-items:center; justify-content:center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="white" viewBox="0 0 16 16">
                        <path d="M8 1a7 7 0 1 0 0 14A7 7 0 0 0 8 1zM0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8z" />
                        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3.5a.5.5 0 0 1-.5-.5v-3.5A.5.5 0 0 1 8 4z" />
                    </svg>
                </div>
                <span style="letter-spacing:1px; font-size:1rem;">Panel Admin</span>
            </a>

            <!-- Botón hamburguesa (móvil) -->
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarAdmin">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Links -->
            <div class="collapse navbar-collapse" id="navbarAdmin">
                <ul class="navbar-nav ms-auto gap-1 py-2 py-lg-0">

                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center gap-2 px-3 py-2 rounded-2"
                            href="<?= BASE_URL ?>admin/admin.php"
                            style="color:#a0aec0; transition: all .2s;"
                            onmouseover="this.style.background='rgba(233,69,96,0.15)'; this.style.color='#fff';"
                            onmouseout="this.style.background='transparent'; this.style.color='#a0aec0';">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4.5v-4h3v4H14a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L8.354 1.146z" />
                            </svg>
                            Inicio
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center gap-2 px-3 py-2 rounded-2"
                            href="<?= BASE_URL ?>admin/dashboard_ventas.php"
                            style="color:#a0aec0; transition: all .2s;"
                            onmouseover="this.style.background='rgba(233,69,96,0.15)'; this.style.color='#fff';"
                            onmouseout="this.style.background='transparent'; this.style.color='#a0aec0';">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M8.5 6a.5.5 0 0 0-1 0v1.5H6a.5.5 0 0 0 0 1h1.5V10a.5.5 0 0 0 1 0V8.5H10a.5.5 0 0 0 0-1H8.5V6z" />
                                <path d="M.5 1h15a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-.5.5H.5a.5.5 0 0 1-.5-.5v-3A.5.5 0 0 1 .5 1zM1 4h14V2H1v2z" />
                                <path d="M0 8a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V8zm2-1a1 1 0 0 0-1 1v5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V8a1 1 0 0 0-1-1H2z" />
                            </svg>
                            Dashboard
                        </a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link d-flex align-items-center gap-2 px-3 py-2 rounded-2 dropdown-toggle"
                            href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"
                            style="color:#a0aec0; transition: all .2s;"
                            onmouseover="this.style.background='rgba(233,69,96,0.15)'; this.style.color='#fff';"
                            onmouseout="this.style.background='transparent'; this.style.color='#a0aec0';">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4z" />
                            </svg>
                            Gestionar...
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>admin/admin_pedidos.php">Pedidos</a></li>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>admin/admin_platos.php">Platos</a></li>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>admin/inventarios/index.php">Inventario</a></li>
                            <li>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>admin/compras/lista_ordenes.php">Compras</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>logout.php">Cerrar Sesion</a></li>
                        </ul>
                    </li>
                    <span class="badge rounded-pill ms-1" style="background:#e94560; font-size:.7rem;">!</span>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container">