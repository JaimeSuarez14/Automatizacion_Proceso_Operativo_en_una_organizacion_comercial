<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>La Choza Náutica</title>
  <link rel="stylesheet" href="style.css">
  <link rel="icon" type="image/x-icon" href="icons/logo.png">
  <link href="assets/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />


</head>

<body>
  <header class="site-header bg-info-subtle">
    <div class="container header-inner">
      <h1 class="brand">
        <img src="icons/logo.png" width="60" alt="">
        La Choza Náutica
      </h1>
      <nav class="main-nav">
        <a class="flex align-content-center gap-2" href="index.php">
          <i class="fa-solid fa-house"></i><span>Inicio</span>
        </a>
        <a href="nosotros.php" class="flex align-content-center gap-4">
          <i class="fa-solid fa-users me-2"></i><span>Nosotros</span>
        </a>
        <a href="menu.php" class="flex align-content-center gap-2"><i class="fa-solid fa-plate-wheat">
          </i><span>Menú</span>
        </a>


        <?php if (isset($_SESSION['nombre_usuario'])): ?>
          <a href="admin/admin.php" style="color:green; font-size: bold; font-weight: bold; padding: 5px ;background-color: #bde3af;" class="flex align-items-center gap-5"><i class="fa-solid fa-user-shield"></i><span>Gestionar</span></a>
        <?php endif ?>

        <?php if (isset($_SESSION['cliente_nombre']) || isset($_SESSION['nombre_usuario'])): ?>
          <a class="flex align-items-center gap-2" href="carrito.php">
            <i class="fa-solid fa-cart-arrow-down "></i>
            <span>Pedidos (Carrito)</span>
          </a>

          <!-- Dropdown de usuario -->
          <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">

              <i class="fa-solid fa-user"></i>
              <span id="userButton" style="cursor:pointer; margin-right: 5px;">
                <?= $_SESSION['cliente_nombre'] ??  $_SESSION['nombre_usuario'] ?>
              </span>

            </button>
            <ul class="dropdown-menu">
              <li>
                <div class="bg-danger text-white flex align-items-center dropdown-item">
                  <i class="fa-solid fa-arrow-right-from-bracket"></i>
                  <a href="logout.php" style="text-decoration:none; color:white;">Cerrar sesión</a>
                </div>
              </li>
              <li><a class="dropdown-item" href="historial_pedidos.php">Historial Pedidos</a></li>
              <li><a class="dropdown-item" href="#">Comentarios</a></li>
            </ul>
          </div>

       
        <?php else: ?>
          <a href="login.php" class="flex align-content-center gap-2">
          <i class="fa-solid fa-arrow-right-to-bracket"></i>
          <span>Iniciar sesión</span>
        </a>
        <?php endif;  ?>
      </nav>
  <button class="menu-toggle" aria-label="Menu">☰</button>
  </div>
  </header>

  <main class="container">

    <script>
      // Dropdown del usuario
      document.addEventListener('DOMContentLoaded', function() {
        const userButton = document.getElementById('userButton');
        const userDropdown = document.getElementById('userDropdown');

        if (userButton) {
          userButton.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdown.style.display = (userDropdown.style.display === 'none') ? 'block' : 'none';
          });

          document.addEventListener('click', function(e) {
            if (!userButton.contains(e.target) && !userDropdown.contains(e.target)) {
              userDropdown.style.display = 'none';
            }
          });
        }
      });
    </script>