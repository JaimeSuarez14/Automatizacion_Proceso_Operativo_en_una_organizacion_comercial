<?php
require __DIR__ . '/../../db.php';
require_once __DIR__ . '/../admin_header.php';

$stmt = $pdo->query("
    SELECT o.*, p.nombre AS proveedor
    FROM ordenes_compra o
    JOIN proveedores p ON o.id_proveedor = p.id_proveedor
    ORDER BY o.fecha_orden DESC
");

$ordenes = $stmt->fetchAll();
?>
<section class="bg-light">
  <div class="container mt-4">

    <div class="d-flex justify-content-between mb-3">
      <h3>Órdenes de Compra</h3>
      <a href="crear_orden.php" class="btn btn-primary">Nueva Orden</a>
    </div>
    <div class="py-2">
      <a href="reporte_mensual.php" class="btn btn-primary">Ver Reporte Mensual</a>
    </div>

    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>#</th>
          <th>Proveedor</th>
          <th>Fecha</th>
          <th>Estado</th>
          <th>Total</th>
          <th>Acción</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($ordenes as $o): ?>
          <tr>
            <td><?= $o['id_orden'] ?></td>
            <td><?= $o['proveedor'] ?></td>
            <td><?= date('d/m/Y', strtotime($o['fecha_orden'])) ?></td>
            <td>
              <span class="badge bg-<?=
                                    $o['estado'] == 'pendiente' ? 'secondary' : ($o['estado'] == 'aprobada' ? 'warning' : ($o['estado'] == 'recibida' ? 'success' : 'danger'))
                                    ?>">
                <?= $o['estado'] ?>
              </span>
            </td>
            <td>S/. <?= number_format($o['monto_total'], 2) ?></td>
            <td>
              <?php if ($o['estado'] == 'aprobada'): ?>
                <a href="recibir_orden.php?id=<?= $o['id_orden'] ?>"
                  class="btn btn-success btn-sm">
                  Recibir
                </a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

  </div>
</section>

<?php require_once __DIR__ . '/../admin_footer.php';
?>