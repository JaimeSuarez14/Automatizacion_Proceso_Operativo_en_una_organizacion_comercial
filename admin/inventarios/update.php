<?php
require __DIR__ . '/../../db.php';

if (isset($_POST['id'])) {

  $id = $_POST['id'];
  $nombre = $_POST['nombre'];
  $stock_actual = $_POST['stock_actual'];
  $unidad = $_POST['unidad'];
  $stock_minimo = $_POST['stock_minimo'];
  $stmt = $pdo->prepare("UPDATE inventario 
            SET nombre_insumo=?, 
                stock_actual=?, unidad=?,
                stock_minimo=?
            WHERE id_inventario=?");

  $stmt->execute([$nombre, $stock_actual, $unidad, $stock_minimo, $id]);

  header("Location: index.php");
  exit;
}
