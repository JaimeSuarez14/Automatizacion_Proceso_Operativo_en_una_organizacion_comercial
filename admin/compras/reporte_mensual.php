<?php
require __DIR__ . '/../../db.php';
require_once __DIR__ . '/../admin_header.php';

$mes = $_GET['mes'] ?? date('m');
$anio = $_GET['anio'] ?? date('Y');

$stmt = $pdo->prepare("
    SELECT o.*, p.nombre AS proveedor
    FROM ordenes_compra o
    JOIN proveedores p ON o.id_proveedor = p.id_proveedor
    WHERE MONTH(o.fecha_orden)=? AND YEAR(o.fecha_orden)=?
    AND o.estado='recibida'
");
$stmt->execute([$mes, $anio]);

$ordenes = $stmt->fetchAll();
$total = array_sum(array_column($ordenes, 'monto_total'));
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-4">

  <h3>Reporte Mensual de Compras</h3>

  <form class="row mb-3">
    <div class="col-md-3">
      <input type="number" name="mes" value="<?= $mes ?>" class="form-control" min="1" max="12">
    </div>
    <div class="col-md-3">
      <input type="number" name="anio" value="<?= $anio ?>" class="form-control">
    </div>
    <div class="col-md-3">
      <button class="btn btn-primary">Filtrar</button>
    </div>
  </form>

  <table class="table table-bordered">
    <thead class="table-dark">
      <tr>
        <th>#</th>
        <th>Proveedor</th>
        <th>Fecha</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($ordenes as $o): ?>
        <tr>
          <td><?= $o['id_orden'] ?></td>
          <td><?= $o['proveedor'] ?></td>
          <td><?= date('d/m/Y', strtotime($o['fecha_orden'])) ?></td>
          <td>S/. <?= number_format($o['monto_total'], 2) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div class="alert alert-success">
    <strong>Total Comprado:</strong> S/. <?= number_format($total, 2) ?>
  </div>

</div>

<?php require_once __DIR__ . '/../admin_footer.php';
?>