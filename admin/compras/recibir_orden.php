<?php
require __DIR__ . '/../../db.php';

if (!isset($_GET['id'])) {
  die("ID no válido");
}

$id_orden = (int)$_GET['id'];

// Verificar estado
$stmt = $pdo->prepare("SELECT estado FROM ordenes_compra WHERE id_orden=?");
$stmt->execute([$id_orden]);
$orden = $stmt->fetch();

if (!$orden || $orden['estado'] != 'aprobada') {
  die("La orden no puede ser recibida.");
}

// Obtener detalle
$stmt = $pdo->prepare("SELECT * FROM detalle_orden_compra WHERE id_orden=?");
$stmt->execute([$id_orden]);
$detalles = $stmt->fetchAll();

foreach ($detalles as $item) {

  // Verificar si insumo existe
  $stmt = $pdo->prepare("SELECT * FROM inventario WHERE nombre_insumo=?");
  $stmt->execute([$item['nombre_insumo']]);
  $insumo = $stmt->fetch();

  if ($insumo) {

    // Actualizar stock
    $nuevo_stock = $insumo['stock_actual'] + $item['cantidad'];

    $update = $pdo->prepare("UPDATE inventario SET stock_actual=?, fecha_actualizacion=NOW() WHERE id_inventario=?");
    $update->execute([$nuevo_stock, $insumo['id_inventario']]);

    $id_inventario = $insumo['id_inventario'];
  } else {

    // Crear insumo
    $insert = $pdo->prepare("
            INSERT INTO inventario (nombre_insumo, stock_actual, stock_minimo, unidad, estado)
            VALUES (?, ?, 5, 'unidad', 1)
        ");
    $insert->execute([$item['nombre_insumo'], $item['cantidad']]);

    $id_inventario = $pdo->lastInsertId();
  }

  // Registrar movimiento
  $mov = $pdo->prepare("
        INSERT INTO movimientos_inventario (id_inventario, tipo, cantidad, motivo)
        VALUES (?, 'entrada', ?, 'Ingreso por orden de compra #$id_orden')
    ");
  $mov->execute([$id_inventario, $item['cantidad']]);
}

// Cambiar estado a recibida
$updateEstado = $pdo->prepare("UPDATE ordenes_compra SET estado='recibida' WHERE id_orden=?");
$updateEstado->execute([$id_orden]);

header("Location: lista_ordenes.php?ok=1");
exit;
