<?php
require '../db.php';
session_start();

// Verificar autenticación de admin (ajusta según tu sistema)
// if (!isset($_SESSION['admin'])) {
//     header("Location: ../login.admin.php");
//     exit;
// }

// Obtener estadísticas
$stmt = $pdo->query("SELECT COUNT(*) as total FROM pedidos");
$total_pedidos = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COALESCE(SUM(monto_total), 0) as total FROM pedidos");
$ingresos_totales = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COALESCE(SUM(monto_total), 0) as total FROM pedidos WHERE DATE(fecha_pedido) = CURDATE()");
$ingresos_hoy = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM clientes");
$total_clientes = $stmt->fetch()['total'];

// Productos más vendidos
$stmt = $pdo->query("
    SELECT 
        pl.id_plato,
        pl.nombre_plato,
        SUM(dp.cantidad) as cantidad_vendida,
        ROUND(SUM(dp.subtotal), 2) as ingresos
    FROM detallepedido dp
    JOIN platos pl ON dp.id_plato = pl.id_plato
    GROUP BY pl.id_plato
    ORDER BY cantidad_vendida DESC
    LIMIT 5
");
$productos_top = $stmt->fetchAll();

// Clientes frecuentes
$stmt = $pdo->query("
    SELECT 
        c.id_cliente,
        c.nombre,
        COUNT(p.id_pedido) as numero_pedidos,
        ROUND(SUM(p.monto_total), 2) as gasto_total
    FROM clientes c
    LEFT JOIN pedidos p ON c.id_cliente = p.id_cliente
    GROUP BY c.id_cliente
    ORDER BY numero_pedidos DESC
    LIMIT 5
");
$clientes_frecuentes = $stmt->fetchAll();

// Últimas ventas
$stmt = $pdo->query("
    SELECT 
        p.id_pedido,
        c.nombre,
        p.fecha_pedido,
        p.monto_total,
        ep.descripcion as estado
    FROM pedidos p
    JOIN clientes c ON p.id_cliente = c.id_cliente
    LEFT JOIN estadopedido ep ON p.id_estado = ep.id_estado
    ORDER BY p.fecha_pedido DESC
    LIMIT 10
");
$ultimas_ventas = $stmt->fetchAll();

// Ingresos por mes
$stmt = $pdo->query("
    SELECT 
        DATE_FORMAT(fecha_pedido, '%Y-%m') as mes,
        COUNT(*) as pedidos,
        ROUND(SUM(monto_total), 2) as ingresos
    FROM pedidos
    WHERE fecha_pedido >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
    GROUP BY DATE_FORMAT(fecha_pedido, '%Y-%m')
    ORDER BY mes DESC
    LIMIT 12
");
$ingresos_mes = $stmt->fetchAll();

require '../header.php';
?>

<section class="dashboard-section">
    <h2>Dashboard de Ventas</h2>
    
    <!-- Tarjetas de Resumen -->
    <div class="dashboard-grid">
        <div class="card card-stat">
            <h3>Total de Pedidos</h3>
            <p class="stat-number"><?= number_format($total_pedidos) ?></p>
            <p class="stat-label">Todos los tiempos</p>
        </div>
        
        <div class="card card-stat">
            <h3>Ingresos Totales</h3>
            <p class="stat-number">S/. <?= number_format($ingresos_totales, 2) ?></p>
            <p class="stat-label">Total acumulado</p>
        </div>
        
        <div class="card card-stat">
            <h3>Ingresos Hoy</h3>
            <p class="stat-number">S/. <?= number_format($ingresos_hoy, 2) ?></p>
            <p class="stat-label"><?= date('d/m/Y') ?></p>
        </div>
        
        <div class="card card-stat">
            <h3>Total Clientes</h3>
            <p class="stat-number"><?= number_format($total_clientes) ?></p>
            <p class="stat-label">Clientes registrados</p>
        </div>
    </div>

    <!-- Sección de Gráficos y Datos -->
    <div class="dashboard-content">
        <!-- Productos Más Vendidos -->
        <div class="card content-block">
            <h3>Top 5 Productos Más Vendidos</h3>
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Ingresos</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos_top as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['nombre_plato']) ?></td>
                        <td><?= $p['cantidad_vendida'] ?></td>
                        <td>S/. <?= number_format($p['ingresos'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Clientes Frecuentes -->
        <div class="card content-block">
            <h3>Top 5 Clientes Frecuentes</h3>
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Pedidos</th>
                        <th>Total Gasto</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clientes_frecuentes as $c): ?>
                    <tr>
                        <td><?= htmlspecialchars($c['nombre']) ?></td>
                        <td><?= $c['numero_pedidos'] ?></td>
                        <td>S/. <?= number_format($c['gasto_total'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Últimas Ventas -->
        <div class="card content-block full-width">
            <h3>Últimas 10 Ventas</h3>
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Pedido</th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th>Monto</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ultimas_ventas as $v): ?>
                    <tr>
                        <td>#<?= $v['id_pedido'] ?></td>
                        <td><?= htmlspecialchars($v['nombre']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($v['fecha_pedido'])) ?></td>
                        <td>S/. <?= number_format($v['monto_total'], 2) ?></td>
                        <td><span class="badge badge-<?= strtolower(str_replace(' ', '-', $v['estado'])) ?>"><?= $v['estado'] ?></span></td>
                        <td>
                            <a href="detalle_pedido.php?id=<?= $v['id_pedido'] ?>" class="btn-small">Ver</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Canvas para gráficos (Chart.js) -->
    <div class="card content-block full-width chart-block">
        <h3>Ingresos por Mes (Últimos 12 Meses)</h3>
        <canvas id="ingresosMesChart"></canvas>
    </div>
</section>

<style>
.dashboard-section {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.card-stat {
    text-align: center;
    padding: 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.card-stat:nth-child(2) {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.card-stat:nth-child(3) {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.card-stat:nth-child(4) {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
}

.stat-number {
    font-size: 32px;
    font-weight: bold;
    margin: 10px 0;
}

.stat-label {
    font-size: 14px;
    opacity: 0.9;
}

.dashboard-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 30px;
}

.content-block {
    padding: 20px;
    border-radius: 8px;
    background-color: #fafafa;
}

.content-block.full-width {
    grid-column: 1 / -1;
}

.content-block h3 {
    margin-top: 0;
    color: #333;
    border-bottom: 2px solid #667eea;
    padding-bottom: 10px;
}

.dashboard-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

.dashboard-table th,
.dashboard-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.dashboard-table th {
    background-color: #f0f0f0;
    font-weight: bold;
    color: #333;
}

.dashboard-table tbody tr:hover {
    background-color: #f9f9f9;
}

.badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
    color: white;
}

.badge-pendiente {
    background-color: #ff9800;
}

.badge-completada {
    background-color: #4caf50;
}

.badge-cancelada {
    background-color: #f44336;
}

.btn-small {
    padding: 5px 10px;
    background-color: #667eea;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    font-size: 12px;
    transition: background-color 0.3s;
}

.btn-small:hover {
    background-color: #5568d3;
}

.chart-block {
    padding: 20px;
    min-height: 400px;
}

@media (max-width: 1200px) {
    .dashboard-content {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .dashboard-grid {
        grid-template-columns: 1fr;
    }
    
    .dashboard-table {
        font-size: 12px;
    }
    
    .dashboard-table th,
    .dashboard-table td {
        padding: 8px;
    }
}
</style>

<!-- Chart.js para gráficos -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Datos de ingresos por mes
const ingresosMes = <?= json_encode($ingresos_mes) ?>;

if (ingresosMes.length > 0) {
    const meses = ingresosMes.map(item => item.mes);
    const ingresos = ingresosMes.map(item => parseFloat(item.ingresos));
    const pedidos = ingresosMes.map(item => item.pedidos);
    
    const ctx = document.getElementById('ingresosMesChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: meses,
            datasets: [
                {
                    label: 'Ingresos (S/.)',
                    data: ingresos,
                    backgroundColor: 'rgba(102, 126, 234, 0.5)',
                    borderColor: 'rgba(102, 126, 234, 1)',
                    borderWidth: 2,
                    yAxisID: 'y'
                },
                {
                    label: 'Número de Pedidos',
                    data: pedidos,
                    backgroundColor: 'rgba(245, 87, 108, 0.5)',
                    borderColor: 'rgba(245, 87, 108, 1)',
                    borderWidth: 2,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Ingresos (S/.)'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Número de Pedidos'
                    },
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });
}
</script>

<?php require '../footer.php'; ?>
