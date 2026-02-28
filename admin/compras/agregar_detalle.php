<?php
require __DIR__ . '/../../db.php';
require_once __DIR__ . '/../admin_header.php';

$id = $_GET['id'];

if ($_POST) {
  $subtotal = $_POST['cantidad'] * $_POST['precio_unitario'];
  $stmt = $pdo->prepare("
        INSERT INTO detalle_orden_compra
        (id_orden,nombre_insumo,cantidad,precio_unitario,subtotal)
        VALUES (?,?,?,?,?)
    ");
  $stmt->execute([
    $id,
    $_POST['nombre_insumo'],
    $_POST['cantidad'],
    $_POST['precio_unitario'],
    $subtotal
  ]);

  $pdo->prepare("
        UPDATE ordenes_compra
        SET monto_total = monto_total + ?
        WHERE id_orden = ?
    ")->execute([$subtotal, $id]);
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-4">
  <h3>Agregar Insumo</h3>

  <form method="POST" class="row g-3">
    <div class="col-md-4">
      <input name="nombre_insumo" class="form-control" placeholder="Insumo" required>
    </div>
    <div class="col-md-2">
      <input name="cantidad" type="number" step="0.01" class="form-control" placeholder="Cantidad" required>
    </div>
    <div class="col-md-2">
      <input name="precio_unitario" type="number" step="0.01" class="form-control" placeholder="Precio" required>
    </div>
    <div class="col-md-2">
      <button class="btn btn-success">Agregar</button>
    </div>
  </form>

  <a href="lista_ordenes.php" class="btn btn-secondary mt-3">Finalizar</a>

</div>