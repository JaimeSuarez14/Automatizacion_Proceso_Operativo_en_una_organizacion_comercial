<?php
require __DIR__ . '/../db.php';
session_start();

$id_cliente = $_SESSION['cliente_id'];
$id_plato = $_GET['id_plato'];
$id_pedido = $_GET['id_pedido'];

// Verificar que el cliente compró el plato
$verificar = $pdo->prepare("
    SELECT dp.* 
    FROM detallepedido dp
    JOIN pedidos p ON dp.id_pedido = p.id_pedido
    WHERE p.id_cliente=? AND dp.id_plato=? AND dp.id_pedido=?
");
$verificar->execute([$id_cliente, $id_plato, $id_pedido]);

if (!$verificar->fetch()) {
  die("No puedes calificar este producto.");
}

if ($_POST) {
  $stmt = $pdo->prepare("
        INSERT INTO resenas_productos 
        (id_plato,id_cliente,id_pedido,calificacion,comentario)
        VALUES (?,?,?,?,?)
    ");
  $stmt->execute([
    $id_plato,
    $id_cliente,
    $id_pedido,
    $_POST['calificacion'],
    $_POST['comentario']
  ]);

  header("Location: historial_pedidos.php");
  exit;
}
require __DIR__ . '/../header.php';
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-4">
  <h4>Calificar Producto</h4>

  <form method="POST">
    <label>Calificación</label>
    <select name="calificacion" class="form-control mb-3" required>
      <option value="">Seleccione</option>
      <option value="5">⭐⭐⭐⭐⭐ Excelente</option>
      <option value="4">⭐⭐⭐⭐ Muy Bueno</option>
      <option value="3">⭐⭐⭐ Bueno</option>
      <option value="2">⭐⭐ Regular</option>
      <option value="1">⭐ Malo</option>
    </select>

    <label>Comentario</label>
    <textarea name="comentario" class="form-control mb-3"></textarea>

    <button class="btn btn-success">Enviar Reseña</button>

  </form>
</div>

<?php require __DIR__ . '/../footer.php'; ?>