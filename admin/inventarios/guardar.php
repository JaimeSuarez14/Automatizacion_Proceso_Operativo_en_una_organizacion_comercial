<?php
require __DIR__ . '/../../db.php';

$nombre = $_POST['nombre'];
$stock = $_POST['stock'];
$minimo = $_POST['minimo'];
$unidad = $_POST['unidad'];

$stmt = $pdo->prepare("
INSERT INTO inventario 
(nombre_insumo, stock_actual, stock_minimo, unidad) 
VALUES (?, ?, ?, ?)
");

$stmt->execute([$nombre, $stock, $minimo, $unidad]);

header("Location: index.php");
exit;
