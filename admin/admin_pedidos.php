<?php
// Mostrar errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conexión
require '../db.php'; // Ajusta la ruta si es necesario
require __DIR__ . '../admin_header.php';
$subtitulo = "Listado de Pedidos ";
?>




<?php
// ==========================
// Cambiar estado del pedido
// ==========================
if (isset($_POST['cambiar_estado'])) {
    $id_pedido = intval($_POST['id_pedido']);
    $nuevo_estado = intval($_POST['nuevo_estado']);
    $stmt = $conn->prepare("UPDATE pedidos SET id_estado = ? WHERE id_pedido = ?");
    $stmt->bind_param("ii", $nuevo_estado, $id_pedido);
    $stmt->execute();
    header("Location: admin_pedidos.php");
    exit;
}

// ==========================
// Eliminar pedido
// ==========================
if (isset($_POST['eliminar_pedido'])) {
    $id_pedido = intval($_POST['id_pedido']);

    // Primero eliminar detallepedido
    $stmt1 = $conn->prepare("DELETE FROM detallepedido WHERE id_pedido = ?");
    $stmt1->bind_param("i", $id_pedido);
    $stmt1->execute();

    // Luego eliminar pedido
    $stmt2 = $conn->prepare("DELETE FROM pedidos WHERE id_pedido = ?");
    $stmt2->bind_param("i", $id_pedido);
    $stmt2->execute();

    header("Location: admin_pedidos.php");
    exit;
}

// ==========================
// Traer todos los pedidos con detalles
// ==========================
$sql = "
SELECT 
    ped.id_pedido,
    c.nombre AS cliente_nombre,
    c.email AS cliente_email,
    ped.fecha_pedido,
    mp.nombre AS metodo_pago,
    ep.id_estado,
    ep.nombre_estado,
    ped.monto_total
FROM pedidos ped
LEFT JOIN clientes c ON ped.id_cliente = c.id_cliente
LEFT JOIN metodospago mp ON ped.id_pago = mp.id_pago
LEFT JOIN estadospedido ep ON ped.id_estado = ep.id_estado
ORDER BY ped.id_pedido DESC
";

$result = $conn->query($sql);
if (!$result) die("Error en la consulta: " . $conn->error);

// ==========================
// Traer todos los estados para dropdown
// ==========================
$estados = $conn->query("SELECT * FROM estadospedido")->fetch_all(MYSQLI_ASSOC);
include"cabecera_admin_pedidos.php";
?>


<table class="table table-striped table-hover table-light ">
    <thead class="bg-black text-white">
        <tr>
            <th class="bg-primary text-white">ID Pedido</th>
            <th class="bg-primary text-white">Cliente</th>
            <th class="bg-primary text-white">Email</th>
            <th class="bg-primary text-white">Fecha</th>
            <th class="bg-primary text-white">Método de Pago</th>
            <th class="bg-primary text-white">Estado</th>

            <th class="bg-primary text-white">Total</th>
            <th class="bg-primary text-white">Acciones</th>
        </tr>
    </thead>
    <tbody class="table-group-divider" style="font-size: 13px;">
        <?php
        $ultimo_pedido = 0;
        $platos = [];
        while ($row = $result->fetch_assoc()) {

            // Si es un pedido nuevo, reseteamos el array de platos
            if ($ultimo_pedido != $row['id_pedido']) {
                $ultimo_pedido = $row['id_pedido'];
                $platos = [];
            }

        ?>
            <tr>
                <td><?= $row['id_pedido'] ?></td>
                <td><?= htmlspecialchars($row['cliente_nombre'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['cliente_email'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['fecha_pedido'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['metodo_pago'] ?? '') ?></td>
                <td>
                    <div class="d-flex gap-2 align-items-center">
                        <?php foreach ($estados as $e) : ?>
                            <?php if ($e['id_estado'] == ($row['id_estado'])): ?>
                                <span class="badge <?php
                                                    echo ($e['nombre_estado'] == 'Pendiente' ? ' text-bg-secondary' : '');
                                                    echo $e['nombre_estado'] == 'Enviado' ? ' text-bg-primary' : '';
                                                    echo $e['nombre_estado'] == 'En preparacion' ? ' text-bg-warning' : '';
                                                    echo $e['nombre_estado'] == 'Entregado' ? ' text-bg-success' : '';
                                                    echo $e['nombre_estado'] == 'Cancelado' ? ' text-bg-danger' : '';
                                                    ?> " style="font-size: 13px;"> <?= htmlspecialchars($e['nombre_estado'] ?? '') ?> </span>
                            <?php endif ?>
                        <?php endforeach ?>
                        <button type="button" class="btn btn-info p-1 modalEdit" data-bs-toggle="modal" data-bs-target="#modalEdit" data-id="<?= $row['id_pedido'] ?>" data-estado="<?= $row['id_estado'] ?>">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                    </div>
                </td>

                <td><?= isset($row['monto_total']) ? number_format($row['monto_total'], 2) : '-' ?></td>
                <td>
                    <div class="d-flex gap-2">
                        <a href="detalle_pedido.php?id_pedido=<?php echo $row['id_pedido'] ?>" class="btn btn-outline-secondary p-2" title="detalle"><i class="bi bi-list-ul "></i></a>
                        <form method="POST" onsubmit="return confirm('¿Eliminar pedido <?= $row['id_pedido'] ?>?');">
                            <input type="hidden" name="id_pedido" value="<?= $row['id_pedido'] ?>">
                            <button title="eliminar" type="submit" name="eliminar_pedido" class="btn btn-outline-danger p-2"><i class="bi bi-trash3"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<div class="modal fade " tabindex="-1" role="dialog" id="modalEdit">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 shadow">
            <div class="modal-header p-5 pb-4 border-bottom-0">
                <h1 class="fw-bold mb-0 fs-2 display-5">Actualizar el Estado del Pedido</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-5 pt-0">
                <form class="" method="post">
                    <input type="hidden" id="modal_id_pedido" name="id_pedido" value="">
                    <div class="mb-2">
                        <label for="">Selecione un estado:</label>
                        <select id="estado_select" name="nuevo_estado" class="form-control">
                            <!-- se construye de manera dinamica-->
                        </select>
                    </div>
                    <button class="w-100 mb-2 btn btn-lg rounded-3 btn-primary" type="submit" name="cambiar_estado">
                        Guardar
                    </button>

                    <small class="text-body-secondary">** Una vez actualizado a Entregado o Cancelado no se podrá hacer mas cambios.</small>
                    <hr class="my-4">
                    <h2 class="fs-5 fw-bold mb-3">Volver a :</h2>
                    <button class="w-100 py-2 mb-2 btn btn-outline-secondary rounded-3" type="button">
                        <svg class="bi me-1" width="16" height="16" aria-hidden="true">
                            <use xlink:href="#google"></use>
                        </svg>
                        Sign up with Google
                    </button>
                    <button class="w-100 py-2 mb-2 btn btn-outline-primary rounded-3" type="button">
                        <svg class="bi me-1" width="16" height="16" aria-hidden="true">
                            <use xlink:href="#facebook"></use>
                        </svg>
                        Sign up with Facebook
                    </button>
                    <button class="w-100 py-2 mb-2 btn btn-outline-secondary rounded-3" type="button">
                        <svg class="bi me-1" width="16" height="16" aria-hidden="true">
                            <use xlink:href="#github"></use>
                        </svg>
                        Sign up with GitHub
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    const botones = document.querySelectorAll(".modalEdit");
    const estados = <?php echo json_encode($estados); ?>;

    botones.forEach(boton => {
        boton.addEventListener("click", (e) => {
            // Dejar que Bootstrap abra el modal; no prevenir default
            const id = boton.getAttribute("data-id");
            const id_estado = boton.getAttribute("data-estado");
            console.log(id, id_estado);

            const select = document.getElementById("estado_select");
            select.innerHTML = "";
            estados.forEach(est => {
                const option = document.createElement("option");
                option.value = est.id_estado;
                option.selected = est.id_estado == id_estado;
                option.textContent = est.nombre_estado;
                select.appendChild(option);
            });

            // Rellenar input oculto con el id del pedido seleccionado
            const hidden = document.getElementById("modal_id_pedido");
            if (hidden) hidden.value = id;
        });
    });
</script>
<?php require __DIR__ . '../admin_footer.php'; ?>