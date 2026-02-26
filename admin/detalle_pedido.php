<?php
//no se esta usando 
require '../db.php';


if (isset($_POST['id_pedido']) && isset($_POST['estado'])) {
    $id = (int)$_POST['id_pedido'];
    $estado = $_POST['estado'];

    $stmt = $pdo->prepare("UPDATE pedidos SET estado=? WHERE id_pedido=?");
    $stmt->execute([$estado, $id]);

    header("Location: admin_panel.php");
    exit;
}

if (isset($_GET["id_pedido"])) {
    $id = (int) $_GET["id_pedido"];
    $sql = "SELECT 
    ped.id_pedido,
    c.nombre AS cliente_nombre,
    c.email AS cliente_email,
    ped.fecha_pedido,
    mp.nombre AS metodo_pago,
    ep.id_estado,
    ep.nombre_estado,
    d.id_detalle,
    p.nombre_plato,
    d.cantidad,
    d.precio_unitario,
    d.subtotal,
    ped.monto_total
        FROM pedidos ped
        LEFT JOIN clientes c ON ped.id_cliente = c.id_cliente
        LEFT JOIN metodospago mp ON ped.id_pago = mp.id_pago
        LEFT JOIN estadospedido ep ON ped.id_estado = ep.id_estado
        LEFT JOIN detallepedido d ON ped.id_pedido = d.id_pedido
        LEFT JOIN platos p ON d.id_plato = p.id_plato
        WHERE ped.id_pedido = ?
        ORDER BY ped.id_pedido DESC
        ";
    $query = $pdo->prepare($sql);
    $query->execute([$id]);
    $resultado = $query->fetchAll();
    //var_dump($resultado);
}



include __DIR__ . '../admin_header.php';
$subtitulo = "Información del Pedido";
include __DIR__ . '../cabecera_admin_pedidos.php' //para ver los titulos de la pagina
?>

<div class="container my-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Detalle del Pedido #<?= $resultado[0]['id_pedido'] ?></h2>
        <a href="admin_pedidos.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Regresar
        </a>
    </div>

    <div class="row g-4">

        <!-- Información del Cliente -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-dark text-white fw-bold">
                    Información del Cliente
                </div>
                <div class="card-body">
                    <p><strong>Nombre:</strong> <?= $resultado[0]['cliente_nombre'] ?></p>
                    <p><strong>Email:</strong> <?= $resultado[0]['cliente_email'] ?></p>
                    <p><strong>Fecha:</strong> <?= $resultado[0]['fecha_pedido'] ?></p>
                    <p><strong>Método de Pago:</strong> <?= $resultado[0]['metodo_pago'] ?></p>
                    <p>
                        <strong>Estado:</strong>
                        <span class="badge bg-primary">
                            <?= $resultado[0]['nombre_estado'] ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Lista de Productos -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-secondary text-white fw-bold">
                    Productos del Pedido
                </div>
                <div class="card-body">

                    <div class="row g-3">
                        <?php foreach ($resultado as $pedido): ?>
                            <div class="col-md-6 col-lg-6">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="fw-bold"><?= $pedido["nombre_plato"] ?></h5>
                                        <p class="mb-1">Cantidad: <?= $pedido["cantidad"] ?></p>
                                        <p class="mb-1">Precio: S/. <?= number_format($pedido["precio_unitario"], 2) ?></p>
                                        <p class="fw-bold text-primary">
                                            Subtotal: S/. <?= number_format($pedido["subtotal"], 2) ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>

                    <hr>

                    <div class="text-end">
                        <h4 class="fw-bold text-success">
                            Total: S/. <?= number_format($resultado[0]['monto_total'], 2) ?>
                        </h4>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<?php
include __DIR__ . '../admin_footer.php'
?>