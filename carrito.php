<?php
require 'db.php';
session_start();

// Inicializar carrito si no existe
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Eliminar producto
if (isset($_GET['accion']) && $_GET['accion'] == "eliminar" && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if (isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]);
    }
    header("Location: carrito.php");
    exit;
}

// Vaciar carrito
if (isset($_GET['accion']) && $_GET['accion'] == "vaciar") {
    $_SESSION['cart'] = [];
    header("Location: carrito.php");
    exit;
}

// Actualizar cantidades
if (isset($_POST['update'])) {
    foreach ($_POST['cantidades'] as $id => $cant) {
        $id = (int)$id;
        $cant = max(1, (int)$cant);
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id] = $cant;
        }
    }
    header("Location: carrito.php");
    exit;
}

// Obtener productos del carrito
$cart = $_SESSION['cart'];
$items = [];
$total = 0.0;

if ($cart) {
    $ids = implode(',', array_map('intval', array_keys($cart)));
    $stmt = $pdo->query("SELECT id_plato,nombre_plato,precio,imagen FROM platos WHERE id_plato IN ($ids)");
    $rows = $stmt->fetchAll();
    foreach ($rows as $r) {
        $qty = $cart[$r['id_plato']];
        $subtotal = $r['precio'] * $qty;
        $items[] = [
            'id' => $r['id_plato'],
            'nombre' => $r['nombre_plato'],
            'precio' => $r['precio'],
            'imagen' => $r['imagen'],
            'cantidad' => $qty,
            'subtotal' => $subtotal
        ];
        $total += $subtotal;
    }

    
}

require 'header.php';
?>

<style>
    .cart-line {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px;
        border-bottom: 1px solid #ddd;
        background-color: #fafafa;
        margin-bottom: 10px;
        border-radius: 4px;
    }

    .cart-line:hover {
        background-color: #f0f0f0;
    }

    .cart-summary {
        margin-top: 20px;
        padding: 15px;
        border-top: 2px solid #333;
        background-color: #f9f9f9;
        border-radius: 4px;
    }

    .total-text {
        font-size: 18px;
        margin: 0 0 15px 0;
    }

    .cart-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn-actualizar,
    .btn-vaciar,
    .btn-seguir,
    .btn-checkout {
        padding: 10px 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        font-weight: bold;
        text-decoration: none;
        text-align: center;
    }

    .btn-actualizar {
        background-color: #2196F3;
        color: white;
        transition: background-color 0.3s;
    }

    .btn-actualizar:hover {
        background-color: #1976D2;
    }

    .btn-vaciar {
        background-color: #f44336;
        color: white;
        transition: background-color 0.3s;
    }

    .btn-vaciar:hover {
        background-color: #da190b;
    }

    .btn-seguir {
        background-color: #757575;
        color: white;
        transition: background-color 0.3s;
    }

    .btn-seguir:hover {
        background-color: #616161;
    }

    .btn-checkout {
        background-color: #4caf50;
        color: white;
        padding: 15px 30px;
        font-size: 16px;
        width: 100%;
        display: block;
        transition: background-color 0.3s;
    }

    .btn-checkout:hover {
        background-color: #388e3c;
    }

    .btn-eliminar {
        background-color: #f44336;
        color: white;
        padding: 5px 10px;
        border-radius: 3px;
        text-decoration: none;
        font-size: 12px;
        margin-left: 10px;
    }

    .btn-eliminar:hover {
        background-color: #da190b;
    }

    @media (max-width: 768px) {
        .cart-actions {
            flex-direction: column;
        }

        .cart-line {
            flex-direction: column;
            gap: 10px;
        }
    }
</style>


<h2>Carrito de pedidos</h2>

<?php if (!$items): ?>
    <div style="padding: 15px 15px; border: 2px solid #b0b0b0; background-color: #f9f9f9; border-radius: 4px;">
        <p>No hay productos en el carrito. <a href="menu.php">Ir al menú</a></p>
    </div>
<?php else: ?>
    <form method="post" action="carrito.php">
        <?php foreach ($items as $it): ?>
            <div class="cart-line">

                <div style="display:flex; gap:5px;">
                    <div style="display: inline;">
                        <img style="width: 60px;" src="img/<?php echo htmlspecialchars($it['imagen'] ?: 'noimage.jpg'); ?>">
                    </div>
                    <div style="display: inline;">
                        <strong><?= htmlspecialchars($it['nombre']) ?></strong>
                        <div class="small">S/. <?= number_format($it['precio'], 2) ?> x
                            <input type="number" name="cantidades[<?= $it['id'] ?>]" value="<?= $it['cantidad'] ?>" min="1" style="width:50px;">
                        </div>
                    </div>
                </div>
                <div>
                    S/. <?= number_format($it['subtotal'], 2) ?>
                    <a class="btn-eliminar" href="carrito.php?accion=eliminar&id=<?= $it['id'] ?>">Eliminar</a>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="cart-summary">
            <p class="total-text">Total: <strong>S/. <?= number_format($total, 2) ?></strong></p>
            <div class="cart-actions">
                <button type="submit" name="update" class="btn-actualizar">Actualizar cantidades</button>
                <a class="btn-vaciar" href="carrito.php?accion=vaciar">Vaciar carrito</a>
                <a class="btn-seguir" href="menu.php">Seguir comprando</a>
            </div>
        </div>
    </form>

    <!-- Botón para proceder al checkout -->
    <div class="checkout-section" style="margin-top:20px;">
        <a href="checkout.php" class="btn-checkout">Proceder a Checkout</a>
    </div>

<?php endif; ?>
</section>

<?php require 'footer.php'; ?>