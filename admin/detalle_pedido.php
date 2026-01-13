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

if(isset($_GET["id_pedido"])){
    $id =(int) $_GET["id_pedido"];
}


$sql = "
SELECT 
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
    d.subtotal
FROM pedidos ped
LEFT JOIN clientes c ON ped.id_cliente = c.id_cliente
LEFT JOIN metodospago mp ON ped.id_pago = mp.id_pago
LEFT JOIN estadospedido ep ON ped.id_estado = ep.id_estado
LEFT JOIN detallepedido d ON ped.id_pedido = d.id_pedido
LEFT JOIN platos p ON d.id_plato = p.id_plato
ORDER BY ped.id_pedido DESC
";
include __DIR__ . '../admin_header.php';
$subtitulo = "Información del Pedido";
include __DIR__ . '../cabecera_admin_pedidos.php' //para ver los titulos de la pagina
?>

<div>
    <a href="admin_pedidos.php" class="btn btn-outline-primary"><i class="bi bi-arrow-return-left"></i> Regresar</a>
    <section class="row justify-content-center">
        <div class="row card my-2" style="width: 25rem;">
            <div class="card-body">
                <h5 class="card-title">Cliente: Jaime Suarez</h5>
                <h6 class="card-subtitle mb-2 text-body-secondary">Numero de Pedido: xxx555xx</h6>
                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card’s content.</p>
                <a href="#" class="card-link">Ver Perfil</a>
                <a href="#" class="card-link">Editar</a>
            </div>
        </div>
        <div class="container text-center bg-body-secondary">
            <div class="row align-items-start">
                <div class="col">
                    One of three columns
                </div>
                <div class="col">
                    One of three columns
                </div>
                <div class="col">
                    One of three columns
                </div>
            </div>
        </div>
    </section>
</div>

<?php
include __DIR__ . '../admin_footer.php'
?>