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
    <link rel="icon" type="image/x-icon" href="icons/person-fill.svg">
  <link href="assets/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />


</head>

<body>
  <header class="site-header">
    <div class="container header-inner">
      <h1 class="brand">La Choza Náutica</h1>
      <nav class="main-nav">
        <a class="flex align-content-center gap-2" href="index.php">
          <i class="fa-solid fa-house"></i><span>Inicio</span>
        </a>
        <a href="menu.php" class="flex align-content-center gap-2" ><i class="fa-solid fa-plate-wheat"></i><span>Menú</span></a>

        <?php if (isset($_SESSION['nombre_usuario'])): ?>
          <a href="admin/admin.php" style="color:green; font-size: bold; font-weight: bold; padding: 5px ;background-color: #afe09d;">Gestionar</a>
        <?php endif ?>

        <?php if (isset($_SESSION['cliente_nombre']) || isset($_SESSION['nombre_usuario'])): ?>
          <a href="carrito.php">Pedidos (Carrito)</a>

          <!-- Dropdown de usuario -->
          <div class="user-menu" style="display:inline-block; position:relative;">
            <div style="display:flex; align-items: center; gap:5px">
              <img style="width: 24px; color:blue" src="icons/person-fill.svg" alt="">
              <span id="userButton" style="cursor:pointer; margin-right: 5px;">
                <?= $_SESSION['cliente_nombre'] ??  $_SESSION['nombre_usuario'] ?>
              </span>
            </div>
            <div id="userDropdown" style="display:none; margin-top:10px; background-color: red; position:absolute; width: 150px; top:100%; right:0;  border:1px solid #ddd; padding:5px; z-index:100; text-align: right">
              <a href="logout.php" style="text-decoration:none; color:white;">Cerrar sesión</a>
            </div>
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