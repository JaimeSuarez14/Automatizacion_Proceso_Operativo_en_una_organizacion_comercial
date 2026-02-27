<?php
require __DIR__ . '/../../db.php';
require 'funciones_inventario.php';
require_once __DIR__ . '/../admin_header.php';


$id = $_GET['id'];
$insumo = obtenerInsumo($pdo, $id);
?>

<div class="container py-4">
  <div class="card shadow">
    <div class="card-header bg-primary text-white">
      <h4>Editar Insumo</h4>
    </div>

    <div class="card-body">
      <form action="update.php" method="POST">

        <input type="hidden" name="id" value="<?= $insumo['id_inventario'] ?>">

        <div class="mb-3">
          <label>Nombre</label>
          <input type="text" name="nombre" value="<?= $insumo['nombre_insumo'] ?>" class="form-control">
        </div>

        <div class="mb-3">
          <label>Stock Actual</label>
          <input type="number" step="0.01" name="stock_actual" value="<?= $insumo['stock_actual'] ?>" class="form-control">
        </div>

        <div class="mb-3">
          <label>Stock Mínimo</label>
          <input type="number" step="0.01" name="stock_minimo" value="<?= $insumo['stock_minimo'] ?>" class="form-control">
        </div>

        <div class="mb-3">
          <label>Unidad</label>
          <input type="text" name="unidad" value="<?= $insumo['unidad'] ?>" class="form-control">
        </div>

        <button class="btn btn-primary">Actualizar</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>

      </form>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../admin_footer.php';
 ?>