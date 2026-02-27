<?php
require __DIR__ . '/../../db.php';
require 'funciones_inventario.php';

$id = $_GET['id'];

if ($_POST) {
  $tipo = $_POST['tipo'];
  $cantidad = $_POST['cantidad'];
  $motivo = $_POST['motivo'];

  actualizarStock($pdo, $id, $cantidad, $tipo);
  registrarMovimiento($pdo, $id, $tipo, $cantidad, $motivo);
  verificarStockMinimo($conn, $id);
  header("Location: movimientos.php?id=$id");
  exit;
}

$insumo = obtenerInsumo($pdo, $id);

$stmt = $pdo->prepare("
SELECT * FROM movimientos_inventario 
WHERE id_inventario = ?
ORDER BY fecha DESC
");
$stmt->execute([$id]);
$movimientos = $stmt->fetchAll();

require_once __DIR__ . '/../admin_header.php';
?>

<div class="container py-4">
  <div class="card shadow">
    <div class="card-header bg-info text-white">
      <h4>Movimientos - <?= $insumo['nombre_insumo'] ?></h4>
    </div>

    <div class="card-body">

      <form method="POST" class="row g-3 mb-4">

        <div class="col-md-3">
          <select name="tipo" class="form-select">
            <option value="entrada">Entrada</option>
            <option value="salida">Salida</option>
          </select>
        </div>

        <div class="col-md-3">
          <input type="number" step="0.01" name="cantidad" class="form-control" required>
        </div>

        <div class="col-md-4">
          <input type="text" name="motivo" class="form-control" placeholder="Motivo">
        </div>

        <div class="col-md-2">
          <button class="btn btn-success w-100">Registrar</button>
        </div>
      </form>

      <table class="table table-bordered text-center">
        <thead>
          <tr>
            <th>Tipo</th>
            <th>Cantidad</th>
            <th>Motivo</th>
            <th>Fecha</th>
          </tr>
        </thead>
        <tbody>

          <?php foreach ($movimientos as $mov): ?>
            <tr>
              <td><?= $mov['tipo'] ?></td>
              <td><?= $mov['cantidad'] ?></td>
              <td><?= $mov['motivo'] ?></td>
              <td><?= $mov['fecha'] ?></td>
            </tr>
          <?php endforeach; ?>

        </tbody>
      </table>

      <a href="index.php" class="btn btn-secondary">Volver</a>

    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../admin_footer.php';
?>