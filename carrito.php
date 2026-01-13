<?php
require 'db.php';
session_start();

// Inicializar carrito si no existe
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Eliminar producto
if (isset($_GET['accion']) && $_GET['accion']=="eliminar" && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if(isset($_SESSION['cart'][$id])){
        unset($_SESSION['cart'][$id]);
    }
    header("Location: carrito.php");
    exit;
}

// Vaciar carrito
if (isset($_GET['accion']) && $_GET['accion']=="vaciar") {
    $_SESSION['cart'] = [];
    header("Location: carrito.php");
    exit;
}

// Actualizar cantidades
if (isset($_POST['update'])) {
    foreach($_POST['cantidades'] as $id=>$cant){
        $id = (int)$id;
        $cant = max(1,(int)$cant);
        if(isset($_SESSION['cart'][$id])){
            $_SESSION['cart'][$id]=$cant;
        }
    }
    header("Location: carrito.php");
    exit;
}

// Obtener productos del carrito
$cart = $_SESSION['cart'];
$items = [];
$total = 0.0;

if($cart){
    $ids = implode(',', array_map('intval', array_keys($cart)));
    $stmt = $pdo->query("SELECT id_plato,nombre_plato,precio FROM platos WHERE id_plato IN ($ids)");
    $rows = $stmt->fetchAll();
    foreach($rows as $r){
        $qty = $cart[$r['id_plato']];
        $subtotal = $r['precio']*$qty;
        $items[] = [
            'id'=>$r['id_plato'],
            'nombre'=>$r['nombre_plato'],
            'precio'=>$r['precio'],
            'cantidad'=>$qty,
            'subtotal'=>$subtotal
        ];
        $total+=$subtotal;
    }
}

require 'header.php';
?>

<section class="card">
<h2>Carrito de pedidos</h2>

<?php if(!$items): ?>
<p>No hay productos en el carrito. <a href="menu.php">Ir al menú</a></p>
<?php else: ?>
<form method="post" action="carrito.php">
<?php foreach($items as $it): ?>
<div class="cart-line">
  <div>
    <strong><?=htmlspecialchars($it['nombre'])?></strong>
    <div class="small">S/. <?=number_format($it['precio'],2)?> x 
      <input type="number" name="cantidades[<?=$it['id']?>]" value="<?=$it['cantidad']?>" min="1" style="width:50px;">
    </div>
  </div>
  <div>
    S/. <?=number_format($it['subtotal'],2)?>
    <a class="btn-eliminar" href="carrito.php?accion=eliminar&id=<?=$it['id']?>">Eliminar</a>
  </div>
</div>
<?php endforeach; ?>

<p class="small">Total: <strong>S/. <?=number_format($total,2)?></strong></p>
<button type="submit" name="update" class="btn-actualizar">Actualizar cantidades</button>
<a class="btn-vaciar" href="carrito.php?accion=vaciar">Vaciar carrito</a>
<a class="btn-seguir" href="menu.php">Seguir comprando</a>
</form>

<!-- Formulario para enviar pedido por correo -->
<form method="post" action="send_order_email.php" style="margin-top:15px;">
<label>Correo del cliente:</label>
<input type="email" name="email_cliente" required placeholder="correo@ejemplo.com">
<input type="hidden" name="total" value="<?=number_format($total,2,".","")?>">
<?php foreach($items as $it): ?>
    <input type="hidden" name="productos[<?=$it['id']?>]" value="<?=$it['cantidad']?>">
<?php endforeach; ?>
<button type="submit" class="button">Pagar y Guardar pedido</button>
</form>

<?php endif; ?>
</section>

<?php require 'footer.php'; ?>
