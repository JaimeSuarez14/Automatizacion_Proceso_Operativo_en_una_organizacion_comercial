<?php
require __DIR__ . '/../../db.php';

$id = $_GET['id'];

$stmt = $pdo->prepare("UPDATE inventario SET estado = 0 WHERE id_inventario = ?");
$stmt->execute([$id]);

header("Location: index.php");
exit;