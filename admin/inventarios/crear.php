<?php
require __DIR__ . '/../../db.php';
require __DIR__ . '/../admin_header.php';

?>

<div class="container py-4">
  <div class="card shadow">
    <div class="card-header bg-success text-white">
      <h4>Nuevo Insumo</h4>
    </div>

    <div class="card-body">
      <form action="guardar.php" method="POST">

        <div class="mb-3">
          <label>Nombre</label>
          <input type="text" name="nombre" class="form-control" required>
        </div>

        <div class="mb-3">
          <label>Stock Inicial</label>
          <input type="number" step="0.01" name="stock" class="form-control" required>
        </div>

        <div class="mb-3">
          <label>Stock Mínimo</label>
          <input type="number" step="0.01" name="minimo" class="form-control" required>
        </div>

        <div class="mb-3">
          <label>Unidad (kg, unidad, litro)</label>
          <input type="text" name="unidad" class="form-control" required>
        </div>

        <button class="btn btn-success">Guardar</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>

      </form>
    </div>
  </div>
</div>

<?php require __DIR__ . '../admin_footer.php'; ?>