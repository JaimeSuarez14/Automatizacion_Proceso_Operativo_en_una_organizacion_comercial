<?php
require 'db.php';
session_start();

$id_pedido = intval($_GET['id'] ?? 0);

if ($id_pedido <= 0) {
    header("Location: index.php");
    exit;
}

// Obtener detalles del pedido
$stmt = $pdo->prepare("
    SELECT 
        p.id_pedido,
        p.fecha_pedido,
        p.monto_total,
        p.id_estado,
        c.nombre,
        c.email,
        c.telefono,
        c.direccion,
        mp.nombre as metodo_pago,
        ep.descripcion as estado
    FROM pedidos p
    JOIN clientes c ON p.id_cliente = c.id_cliente
    LEFT JOIN metodospago mp ON p.id_pago = mp.id_pago
    LEFT JOIN estadopedido ep ON p.id_estado = ep.id_estado
    WHERE p.id_pedido = ?
    ORDER BY p.fecha_pedido DESC
");
$stmt->execute([$id_pedido]);
$pedido = $stmt->fetch();

if (!$pedido) {
    header("Location: index.php");
    exit;
}

// Obtener detalles de los platos
$stmt = $pdo->prepare("
    SELECT 
        dp.id_plato,
        p.nombre_plato,
        dp.cantidad,
        dp.precio_unitario,
        dp.subtotal
    FROM detallepedido dp
    JOIN platos p ON dp.id_plato = p.id_plato
    WHERE dp.id_pedido = ?
");
$stmt->execute([$id_pedido]);
$detalles = $stmt->fetchAll();

require 'header.php';
?>

<section class="card confirmacion-section">
    <div class="confirmacion-container">
        <div class="confirmacion-header success">
            <h2>✓ Pedido Registrado Exitosamente</h2>
            <p>Número de Pedido: <strong>#<?= $pedido['id_pedido'] ?></strong></p>
        </div>

        <div class="confirmacion-grid">
            <!-- Información del Cliente -->
            <div class="info-block">
                <h3>Información del Cliente</h3>
                <dl>
                    <dt>Nombre:</dt>
                    <dd><?= htmlspecialchars($pedido['nombre']) ?></dd>
                    
                    <dt>Email:</dt>
                    <dd><?= htmlspecialchars($pedido['email']) ?></dd>
                    
                    <dt>Teléfono:</dt>
                    <dd><?= htmlspecialchars($pedido['telefono']) ?></dd>
                    
                    <dt>Dirección:</dt>
                    <dd><?= htmlspecialchars($pedido['direccion']) ?></dd>
                </dl>
            </div>

            <!-- Detalles del Pedido -->
            <div class="info-block">
                <h3>Detalles del Pedido</h3>
                <table class="confirmacion-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($detalles as $det): ?>
                        <tr>
                            <td><?= htmlspecialchars($det['nombre_plato']) ?></td>
                            <td><?= $det['cantidad'] ?></td>
                            <td>S/. <?= number_format($det['precio_unitario'], 2) ?></td>
                            <td>S/. <?= number_format($det['subtotal'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Resumen Financiero -->
        <div class="resumen-block">
            <h3>Resumen de Pago</h3>
            <div class="resumen-items">
                <div class="resumen-item">
                    <span>Subtotal:</span>
                    <strong>S/. <?= number_format($pedido['monto_total'] * 0.8, 2) ?></strong>
                </div>
                <div class="resumen-item">
                    <span>IGV (18%):</span>
                    <strong>S/. <?= number_format($pedido['monto_total'] * 0.2, 2) ?></strong>
                </div>
                <div class="resumen-item total">
                    <span>Total:</span>
                    <strong>S/. <?= number_format($pedido['monto_total'], 2) ?></strong>
                </div>
                <div class="resumen-item">
                    <span>Método de Pago:</span>
                    <strong><?= htmlspecialchars($pedido['metodo_pago'] ?? 'N/A') ?></strong>
                </div>
                <div class="resumen-item">
                    <span>Estado:</span>
                    <strong><?= htmlspecialchars($pedido['estado']) ?></strong>
                </div>
            </div>
        </div>

        <!-- Acciones -->
        <div class="confirmacion-actions">
            <p>Hemos enviado una confirmación a <strong><?= htmlspecialchars($pedido['email']) ?></strong></p>
            <div class="action-buttons">
                <a href="menu.php" class="btn-primary">Continuar Comprando</a>
                <a href="index.php" class="btn-secondary">Volver al Inicio</a>
            </div>
        </div>
    </div>
</section>

<style>
.confirmacion-section {
    max-width: 900px;
    margin: 30px auto;
}

.confirmacion-container {
    padding: 20px;
}

.confirmacion-header {
    padding: 20px;
    margin-bottom: 30px;
    border-radius: 8px;
    text-align: center;
}

.confirmacion-header.success {
    background-color: #e8f5e9;
    border-left: 5px solid #4caf50;
    color: #2e7d32;
}

.confirmacion-header h2 {
    margin: 0 0 10px 0;
    font-size: 24px;
}

.confirmacion-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 30px;
}

.info-block {
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background-color: #fafafa;
}

.info-block h3 {
    margin-top: 0;
    color: #333;
    border-bottom: 2px solid #2196F3;
    padding-bottom: 10px;
}

.info-block dl {
    margin: 0;
}

.info-block dt {
    font-weight: bold;
    color: #555;
    margin-top: 10px;
}

.info-block dd {
    margin: 5px 0 0 0;
    color: #333;
    word-break: break-word;
}

.confirmacion-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 10px;
}

.confirmacion-table th,
.confirmacion-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.confirmacion-table th {
    background-color: #f0f0f0;
    font-weight: bold;
    color: #333;
}

.resumen-block {
    padding: 20px;
    border: 2px solid #2196F3;
    border-radius: 8px;
    margin-bottom: 30px;
    background-color: #f8f9fa;
}

.resumen-block h3 {
    margin-top: 0;
    color: #2196F3;
}

.resumen-items {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.resumen-item {
    display: flex;
    justify-content: space-between;
    padding: 10px;
    border-bottom: 1px solid #ddd;
}

.resumen-item.total {
    border-bottom: none;
    border-top: 2px solid #333;
    font-size: 18px;
    font-weight: bold;
    color: #2196F3;
    padding-top: 15px;
}

.confirmacion-actions {
    text-align: center;
    padding: 20px;
    background-color: #e3f2fd;
    border-radius: 8px;
}

.action-buttons {
    display: flex;
    gap: 10px;
    justify-content: center;
    margin-top: 15px;
}

.btn-primary,
.btn-secondary {
    padding: 12px 24px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    font-weight: bold;
    text-decoration: none;
    transition: background-color 0.3s;
}

.btn-primary {
    background-color: #2196F3;
    color: white;
}

.btn-primary:hover {
    background-color: #1976D2;
}

.btn-secondary {
    background-color: #757575;
    color: white;
}

.btn-secondary:hover {
    background-color: #616161;
}

@media (max-width: 768px) {
    .confirmacion-grid {
        grid-template-columns: 1fr;
    }
    
    .action-buttons {
        flex-direction: column;
    }
}
</style>

<?php require 'footer.php'; ?>
