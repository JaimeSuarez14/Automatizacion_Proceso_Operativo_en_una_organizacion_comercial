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

<div>
    <a href="admin_pedidos.php" class="btn btn-outline-primary"><i class="bi bi-arrow-return-left"></i> Regresar</a>
    <section class="row justify-content-center">
        <div class="row card my-2" style="width: 25rem;">
            <div class="card-body">
                <h5 class="card-title">Cliente: <?= $resultado[0]['cliente_nombre'] ?> </h5>
                <hr>
                <h6 class="card-subtitle mb-2 text-body-secondary">Numero de Pedido: xxx555xx</h6>
                <p class="card-text">Email: <?= $resultado[0]['cliente_email'] ?> </p>
                <p class="card-text">Fecha: <?= $resultado[0]['fecha_pedido'] ?> </p>
                <p class="card-text">Metodo de Pago: <?= $resultado[0]['metodo_pago'] ?></p>
                <p class="card-text">Estado: <?= $resultado[0]['nombre_estado'] ?></p>
                <a href="#" class="card-link">Ver Perfil</a>
                <a href="#" class="card-link">Editar</a>
            </div>
        </div>

        <div class="container text-center bg-body-secondary">
            <p class="text-start">Lista de productos del Pedido:</p>
            <div class="row align-items-start justify-content-center gap-2">
                <?php foreach ($resultado as $pedido): ?>
                    <div class="col-4">
                        <div class="card text-center">
                            <div class="card-header fw-bold">
                                <?= $pedido["nombre_plato"] ?>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">Cantidad: <?= $pedido["cantidad"] ?> </h5>
                                <p class="card-text">Precio: <?= $pedido["precio_unitario"] ?> </p>
                                <a href="#" class="btn btn-primary">Subtotal: <?= $pedido["subtotal"] ?> </a>
                            </div>
                            <div class="card-footer text-body-secondary">
                                2 days ago
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>

            </div>
            <p class="text-start display-6 text-success fst-italic my-3 ">Importe total del Pedido: S/. <?= $resultado[0]['monto_total'] ?> </p>

        </div>
    </section>
</div>

<?php
include __DIR__ . '../admin_footer.php'
?>