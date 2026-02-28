<?php
require __DIR__ . '/../db.php';

$id_plato = $_GET['id_plato'];

$stmt = $pdo->prepare("
    SELECT r.*, c.nombre
    FROM resenas_productos r
    JOIN clientes c ON r.id_cliente = c.id_cliente
    WHERE r.id_plato=? AND r.estado=1
    ORDER BY r.fecha DESC
");
$stmt->execute([$id_plato]);

$resenas = $stmt->fetchAll();

// Promedio
$prom = $pdo->prepare("
    SELECT AVG(calificacion) as promedio, COUNT(*) as total
    FROM resenas_productos
    WHERE id_plato=? AND estado=1
");
$prom->execute([$id_plato]);
$data = $prom->fetch();
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-4">

  <h3>Reseñas del Producto</h3>

  <div class="alert alert-info">
    ⭐ Promedio: <?= number_format($data['promedio'], 1) ?> / 5
    (<?= $data['total'] ?> reseñas)
  </div>

  <?php foreach ($resenas as $r): ?>

    <div class="card mb-3">
      <div class="card-body">
        <h6><?= $r['nombre'] ?></h6>
        <p>
          <?= str_repeat("⭐", $r['calificacion']) ?>
        </p>
        <p><?= htmlspecialchars($r['comentario']) ?></p>
        <small class="text-muted">
          <?= date('d/m/Y', strtotime($r['fecha'])) ?>
        </small>
      </div>
    </div>

  <?php endforeach; ?>

</div>