<?php
require 'db.php';
session_start();

if (!isset($_SESSION['cliente_nombre']) || empty($_SESSION['cliente_nombre'])) {
  header('Location: login.php'); // Redirige al login
  exit;
}

// Verificar que hay productos en el carrito
if (!isset($_SESSION['cliente_id']) || empty($_SESSION['cliente_id'])) {
  header("Location: carrito.php");
  exit;
}

$id_cliente = intval($_SESSION['cliente_id']);

$stmt = $pdo->prepare("
    SELECT 
        p.id_pedido,
        p.fecha_pedido,
        p.monto_total,
        p.id_estado,
        mp.nombre as metodo_pago,
        ep.descripcion as estado
    FROM pedidos p
    LEFT JOIN metodospago mp ON p.id_pago = mp.id_pago
    LEFT JOIN estadopedido ep ON p.id_estado = ep.id_estado
    WHERE p.id_cliente = ?
    ORDER BY p.fecha_pedido DESC
");
$stmt->execute([$id_cliente]);
$pedidos = $stmt->fetchAll();


if (!$pedidos) {
  header('Location: carrito.php');
  exit;
}

require 'header.php';
?>
<section class="container">
  <table class="table">
    <thead>
      <tr>
        <th scope="col">Id</th>
        <th scope="col">Fecha</th>
        <th scope="col">Importe</th>
        <th scope="col">Estado</th>
        <th scope="col">Metodo de Pago</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($pedidos as $pedido): ?>
      <tr>
        <th scope="row"><?php echo htmlspecialchars($pedido['id_pedido']);?></th>
        <th scope="row"><?php echo htmlspecialchars($pedido['fecha_pedido']);?></th>
        <th scope="row"><?php echo htmlspecialchars($pedido['monto_total']);?></th>
        <th scope="row"><?=  $pedido['estado']!=""? htmlspecialchars($pedido['estado']) : "Vacio";?></th>
        <th scope="row"><?php echo htmlspecialchars($pedido['metodo_pago']);?></th>
        
      </tr>
      <?php endforeach; ?> 
     
    </tbody>
  </table>
</section>

<?php require __DIR__ . '/footer.php'; ?>
