<?php
require 'header.php';
require 'db.php';
?>

<!-- Banner de bienvenida -->
<section class="card hero-banner">
  <h2>Bienvenido a La Choza Náutica</h2>
  <p class="small">Ordena en línea nuestros platos tradicionales y marinos. Inicia sesión para gestionar el menú o realizar pedidos.</p>
</section>

<!-- Sección de platos destacados -->
<section class="card">
  <h3>Platos destacados</h3>
  <div class="menu-grid">
    <?php
    // Consulta con MySQLi
    $result = $conn->query("SELECT id_plato, nombre_plato, descripcion, precio, imagen FROM platos ORDER BY id_plato LIMIT 6");

    // Obtener todos los registros en array asociativo
    $platos = $result->fetch_all(MYSQLI_ASSOC);

    foreach ($platos as $p):
      $img = (!empty($p['imagen'])) ? trim($p['imagen']) : "noimage.jpg";
      $ruta = "img/" . $img;
    ?>

      <div class="card plato-item">

        <img class="plato-img h-50"
          src="<?php echo $ruta; ?>"
          alt="<?php echo htmlspecialchars($p['nombre_plato']); ?>"
          onerror="this.onerror=null; this.src='img/noimage.jpg';">

        <h4><?php echo htmlspecialchars($p['nombre_plato']); ?></h4>
        <p class="small"><?php echo htmlspecialchars($p['descripcion']); ?></p>
        <p><strong>S/. <?php echo number_format($p['precio'], 2); ?></strong></p>

        <!-- Formulario para agregar al carrito -->
        <form action="agregar_carrito.php" method="post">
          <input type="hidden" name="id_plato" value="<?php echo $p['id_plato']; ?>">
          <input type="number" name="cantidad" value="1" min="1" style="width:70px">
          <button class="button" type="submit">Agregar al carrito</button>
        </form>

      </div>

    <?php endforeach; ?>
  </div>
</section>

<?php require 'footer.php'; ?>