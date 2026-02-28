<?php
require __DIR__ . '/../../db.php';

$proveedores = $pdo->query("SELECT * FROM proveedores WHERE estado=1")->fetchAll();

if ($_POST) {
  $stmt = $pdo->prepare("INSERT INTO ordenes_compra (id_proveedor, estado) VALUES (?, 'pendiente')");
  $stmt->execute([$_POST['id_proveedor']]);
  $id = $pdo->lastInsertId();
  header("Location: agregar_detalle.php?id=$id");
  exit;
}
require_once __DIR__ . '/../admin_header.php';
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-4">
  <h3>Nueva Orden</h3>

  <form method="POST">
    <div class="mb-3">
      <label>Proveedor</label>
      <select name="id_proveedor" class="form-control" required>
        <?php foreach ($proveedores as $p): ?>
          <option value="<?= $p['id_proveedor'] ?>">
            <?= $p['nombre'] ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <button class="btn btn-primary">Crear Orden</button>
  </form>
</div>

<?php require_once __DIR__ . '/../admin_footer.php';
?>