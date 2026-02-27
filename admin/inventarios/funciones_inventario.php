<?php

function registrarMovimiento($pdo, $id_inventario, $tipo, $cantidad, $motivo)
{
  $stmt = $pdo->prepare("
        INSERT INTO movimientos_inventario 
        (id_inventario, tipo, cantidad, motivo) 
        VALUES (?, ?, ?, ?)
    ");
  $stmt->execute([$id_inventario, $tipo, $cantidad, $motivo]);
}

function actualizarStock($pdo, $id_inventario, $cantidad, $tipo)
{

  if ($tipo == 'entrada') {
    $sql = "UPDATE inventario SET stock_actual = stock_actual + ? WHERE id_inventario = ?";
  } else {
    $sql = "UPDATE inventario SET stock_actual = stock_actual - ? WHERE id_inventario = ?";
  }

  $stmt = $pdo->prepare($sql);
  $stmt->execute([$cantidad, $id_inventario]);
}

function obtenerInsumo($pdo, $id)
{
  $stmt = $pdo->prepare("SELECT * FROM inventario WHERE id_inventario = ?");
  $stmt->execute([$id]);
  return $stmt->fetch();
}
