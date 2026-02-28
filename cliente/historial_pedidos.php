<?php
require __DIR__ . '/../db.php';
session_start();

if (!isset($_SESSION['cliente_nombre']) || empty($_SESSION['cliente_nombre'])) {
  header('Location: login.php'); // Redirige al login
  exit;
}

// Verificar que hay productos en el carrito
if (!isset($_SESSION['cliente_id']) || empty($_SESSION['cliente_id'])) {
  header("Location: carrito.php");
  exit;
}

$id_cliente = intval($_SESSION['cliente_id']);

$stmt = $pdo->prepare("
    SELECT 
        p.id_pedido,
        p.fecha_pedido,
        p.monto_total,
        p.id_estado,
        mp.nombre as metodo_pago,
        ep.descripcion as estado
    FROM pedidos p
    LEFT JOIN metodospago mp ON p.id_pago = mp.id_pago
    LEFT JOIN estadopedido ep ON p.id_estado = ep.id_estado
    WHERE p.id_cliente = ?
    ORDER BY p.fecha_pedido DESC
");
$stmt->execute([$id_cliente]);
$pedidos = $stmt->fetchAll();


if (!$pedidos) {
  header('Location: carrito.php');
  exit;
}

require __DIR__ . '/../header.php';

?>

<section class="container py-5">

  <div class="row justify-content-center">
    <div class="col-lg-10">

      <div class="card shadow-lg border-0 rounded-4">
        <div class="card-header bg-primary text-white rounded-top-4">
          <h4 class="mb-0">
            <i class="bi bi-receipt me-2"></i> Mis Pedidos
          </h4>
        </div>

        <div class="card-body p-4">

          <div class="table-responsive">
            <table class="table table-hover align-middle text-center">

              <thead class="table-light">
                <tr>
                  <th># Pedido</th>
                  <th>Fecha</th>
                  <th>Total</th>
                  <th>Estado</th>
                  <th>Método de Pago</th>
                  <th>Detalle</th>
                </tr>
              </thead>

              <tbody>
                <?php foreach ($pedidos as $pedido): ?>
                  <tr>

                    <td class="fw-bold">
                      #<?= htmlspecialchars($pedido['id_pedido']); ?>
                    </td>

                    <td>
                      <?= date("d/m/Y H:i", strtotime($pedido['fecha_pedido'])); ?>
                    </td>

                    <td class="fw-semibold text-success">
                      S/. <?= number_format($pedido['monto_total'], 2); ?>
                    </td>

                    <td>
                      <?php
                      $estado = $pedido['estado'] ?? 'Pendiente';

                      $badgeClass = 'bg-secondary';

                      if (stripos($estado, 'pendiente') !== false) {
                        $badgeClass = 'bg-warning text-dark';
                      } elseif (stripos($estado, 'completado') !== false) {
                        $badgeClass = 'bg-success';
                      } elseif (stripos($estado, 'cancelado') !== false) {
                        $badgeClass = 'bg-danger';
                      }
                      ?>
                      <span class="badge <?= $badgeClass ?> px-3 py-2 rounded-pill">
                        <?= htmlspecialchars($estado); ?>
                      </span>
                    </td>

                    <td>
                      <span class="badge bg-info text-dark px-3 py-2 rounded-pill">
                        <?= htmlspecialchars($pedido['metodo_pago']); ?>
                      </span>
                    </td>
                    <td>
                      <a href="detalle_pedido.php?id_pedido=<?= $pedido['id_pedido']; ?>"
                        class="btn btn-sm btn-outline-primary">
                        Ver Detalle
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>

            </table>
          </div>

        </div>
      </div>

    </div>
  </div>

</section>

<?php require __DIR__ . '/../footer.php'; ?>