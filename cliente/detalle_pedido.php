<?php
require __DIR__ . '/../db.php';
session_start();

if (!isset($_SESSION['cliente_id'])) {
  header("Location: login.php");
  exit;
}

$id_cliente = intval($_SESSION['cliente_id']);
$id_pedido = intval($_GET['id_pedido'] ?? 0);

// Verificar que el pedido pertenece al cliente
$verificar = $pdo->prepare("
    SELECT * FROM pedidos 
    WHERE id_pedido=? AND id_cliente=?
");
$verificar->execute([$id_pedido, $id_cliente]);
$pedido = $verificar->fetch();

if (!$pedido) {
  die("Pedido no válido.");
}

// Obtener detalle
$stmt = $pdo->prepare("
    SELECT dp.*, pl.nombre_plato
    FROM detallepedido dp
    JOIN platos pl ON dp.id_plato = pl.id_plato
    WHERE dp.id_pedido=?
");
$stmt->execute([$id_pedido]);
$detalles = $stmt->fetchAll();

require __DIR__ . '/../header.php';
?>

<section class="container py-5">

  <div class="card shadow rounded-4">
    <div class="card-header bg-dark text-white">
      <h5>Detalle del Pedido #<?= $id_pedido ?></h5>
    </div>

    <div class="card-body">

      <table class="table text-center align-middle">
        <thead class="table-light">
          <tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio</th>
            <th>Subtotal</th>
            <th>Acción</th>
            <th>Reseñas</th>
          </tr>
        </thead>

        <tbody>
          <?php foreach ($detalles as $d): ?>

            <?php
            // Obtener promedio y total reseñas
            $prom = $pdo->prepare("
    SELECT 
        AVG(calificacion) as promedio,
        COUNT(*) as total
    FROM resenas_productos
    WHERE id_plato=? AND estado=1
");
            $prom->execute([$d['id_plato']]);
            $rating = $prom->fetch();

            // Obtener últimas 2 reseñas
            $ultimas = $pdo->prepare("
    SELECT comentario, calificacion
    FROM resenas_productos
    WHERE id_plato=? AND estado=1
    ORDER BY fecha DESC
    LIMIT 2
");
            $ultimas->execute([$d['id_plato']]);
            $comentarios = $ultimas->fetchAll();
            ?>

            <tr>

              <td class="fw-semibold">
                <?= htmlspecialchars($d['nombre_plato']) ?>
              </td>

              <td><?= $d['cantidad'] ?></td>

              <td>S/. <?= number_format($d['precio_unitario'], 2) ?></td>

              <td>S/. <?= number_format($d['subtotal'], 2) ?></td>

              <td>
                <?php if ($pedido['id_estado'] == 4): ?>
                  <a href="calificar_producto.php?id_plato=<?= $d['id_plato'] ?>&id_pedido=<?= $id_pedido ?>"
                    class="btn btn-sm btn-success">
                    Calificar
                  </a>
                <?php else: ?>
                  <span class="text-muted">No disponible</span>
                <?php endif; ?>
              </td>

              <!-- NUEVA COLUMNA RESEÑAS -->
              <td style="min-width:250px">
                <?php if ( $rating['total'] > 0 ): ?>
                  <div class="mb-2">
                    <strong>
                      <?= number_format($rating['promedio'], 1) ?>
                    </strong>
                    <?= str_repeat("⭐", round($rating['promedio'])) ?>
                    <small class="text-muted">
                      (<?= $rating['total'] ?> reseñas)
                    </small>
                  </div>

                  <?php foreach ($comentarios as $c): ?>
                    <div class="border rounded p-2 mb-1 bg-light">
                      <small>
                        <?= str_repeat("⭐", $c['calificacion']) ?>
                      </small>
                      <div style="font-size:13px;">
                        <?= htmlspecialchars($c['comentario']) ?>
                      </div>
                    </div>
                  <?php endforeach; ?>

                  <a href="ver_resenas.php?id_plato=<?= $d['id_plato'] ?>"
                    class="btn btn-sm btn-outline-primary mt-1">
                    Ver todas
                  </a>

                <?php else: ?>

                  <span class="text-muted">Sin reseñas</span>

                <?php endif; ?>

              </td>

            </tr>

          <?php endforeach; ?>
        </tbody>
      </table>

      <a href="historial_pedidos.php" class="btn btn-secondary mt-3">
        Volver
      </a>

    </div>
  </div>

</section>

<?php require __DIR__ . '/../footer.php'; ?>