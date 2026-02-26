<?php
/**
 * API de Ventas - Endpoint para consultar datos de ventas
 * 
 * Uso:
 * GET /api_ventas.php?accion=listar_ventas - Listar todas las ventas
 * GET /api_ventas.php?accion=venta_detalle&id=1 - Detalles de una venta
 * GET /api_ventas.php?accion=ventas_por_fechas&desde=2025-01-01&hasta=2025-02-10 - Ventas por rango de fechas
 * GET /api_ventas.php?accion=estadisticas - Estadísticas generales
 * GET /api_ventas.php?accion=productos_masvendidos - Top 5 productos más vendidos
 */

require 'db.php';
session_start();

// Header JSON
header('Content-Type: application/json; charset=utf-8');

// Verificar autenticación (opcional - descomentar si se requiere)
// if (!isset($_SESSION['user_id'])) {
//     http_response_code(401);
//     die(json_encode(['error' => 'No autorizado']));
// }

$accion = $_GET['accion'] ?? '';

try {
    switch ($accion) {
        case 'listar_ventas':
            listarVentas();
            break;
            
        case 'venta_detalle':
            detalleVenta();
            break;
            
        case 'ventas_por_fechas':
            ventasPorFechas();
            break;
            
        case 'estadisticas':
            estadisticas();
            break;
            
        case 'productos_masvendidos':
            productosMasVendidos();
            break;
            
        case 'clientes_frecuentes':
            clientesFrecuentes();
            break;
            
        case 'ingresos_por_mes':
            ingresosPorMes();
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Acción no válida']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error del servidor: ' . $e->getMessage()]);
}

/**
 * Listar todas las ventas con paginación
 */
function listarVentas() {
    global $pdo;
    
    $pagina = intval($_GET['pagina'] ?? 1);
    $limite = intval($_GET['limite'] ?? 10);
    $offset = ($pagina - 1) * $limite;
    
    // Validar límite
    if ($limite > 100) $limite = 100;
    if ($limite < 1) $limite = 10;
    
    // Contar total
    $stmtCount = $pdo->query("SELECT COUNT(*) as total FROM pedidos");
    $total = $stmtCount->fetch()['total'];
    
    // Obtener ventas
    $stmt = $pdo->prepare("
        SELECT 
            p.id_pedido,
            p.id_cliente,
            c.nombre as cliente_nombre,
            c.email as cliente_email,
            p.fecha_pedido,
            p.monto_total,
            mp.nombre as metodo_pago,
            ep.descripcion as estado,
            COUNT(dp.id_detalle) as cantidad_items
        FROM pedidos p
        JOIN clientes c ON p.id_cliente = c.id_cliente
        LEFT JOIN metodos_pago mp ON p.id_pago = mp.id_pago
        LEFT JOIN estadopedido ep ON p.id_estado = ep.id_estado
        LEFT JOIN detallepedido dp ON p.id_pedido = dp.id_pedido
        GROUP BY p.id_pedido
        ORDER BY p.fecha_pedido DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->execute([$limite, $offset]);
    $ventas = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'total' => $total,
        'pagina' => $pagina,
        'limite' => $limite,
        'paginas_totales' => ceil($total / $limite),
        'data' => $ventas
    ], JSON_UNESCAPED_UNICODE);
}

/**
 * Obtener detalles de una venta específica
 */
function detalleVenta() {
    global $pdo;
    
    $id_pedido = intval($_GET['id'] ?? 0);
    
    if ($id_pedido <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'ID de pedido inválido']);
        return;
    }
    
    // Información principal del pedido
    $stmt = $pdo->prepare("
        SELECT 
            p.id_pedido,
            p.id_cliente,
            c.nombre,
            c.email,
            c.telefono,
            c.direccion,
            p.fecha_pedido,
            p.monto_total,
            mp.nombre as metodo_pago,
            ep.descripcion as estado,
            p.notas
        FROM pedidos p
        JOIN clientes c ON p.id_cliente = c.id_cliente
        LEFT JOIN metodos_pago mp ON p.id_pago = mp.id_pago
        LEFT JOIN estadopedido ep ON p.id_estado = ep.id_estado
        WHERE p.id_pedido = ?
    ");
    $stmt->execute([$id_pedido]);
    $pedido = $stmt->fetch();
    
    if (!$pedido) {
        http_response_code(404);
        echo json_encode(['error' => 'Pedido no encontrado']);
        return;
    }
    
    // Detalles de los productos
    $stmt = $pdo->prepare("
        SELECT 
            dp.id_detalle,
            dp.id_plato,
            pl.nombre_plato,
            pl.descripcion,
            dp.cantidad,
            dp.precio_unitario,
            dp.subtotal
        FROM detallepedido dp
        JOIN platos pl ON dp.id_plato = pl.id_plato
        WHERE dp.id_pedido = ?
    ");
    $stmt->execute([$id_pedido]);
    $detalles = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'pedido' => $pedido,
        'detalles' => $detalles
    ], JSON_UNESCAPED_UNICODE);
}

/**
 * Ventas en un rango de fechas
 */
function ventasPorFechas() {
    global $pdo;
    
    $desde = $_GET['desde'] ?? date('Y-m-d', strtotime('-30 days'));
    $hasta = $_GET['hasta'] ?? date('Y-m-d');
    
    // Validar fechas
    if (!strtotime($desde) || !strtotime($hasta)) {
        http_response_code(400);
        echo json_encode(['error' => 'Fechas inválidas']);
        return;
    }
    
    $stmt = $pdo->prepare("
        SELECT 
            p.id_pedido,
            c.nombre as cliente_nombre,
            p.fecha_pedido,
            p.monto_total,
            ep.descripcion as estado
        FROM pedidos p
        JOIN clientes c ON p.id_cliente = c.id_cliente
        LEFT JOIN estadopedido ep ON p.id_estado = ep.id_estado
        WHERE DATE(p.fecha_pedido) BETWEEN ? AND ?
        ORDER BY p.fecha_pedido DESC
    ");
    $stmt->execute([$desde, $hasta]);
    $ventas = $stmt->fetchAll();
    
    // Calcular totales
    $total_ventas = count($ventas);
    $monto_total = array_sum(array_column($ventas, 'monto_total'));
    $promedio = $total_ventas > 0 ? $monto_total / $total_ventas : 0;
    
    echo json_encode([
        'success' => true,
        'rango' => "$desde a $hasta",
        'total_ventas' => $total_ventas,
        'monto_total' => round($monto_total, 2),
        'promedio_venta' => round($promedio, 2),
        'ventas' => $ventas
    ], JSON_UNESCAPED_UNICODE);
}

/**
 * Estadísticas generales de ventas
 */
function estadisticas() {
    global $pdo;
    
    // Total de ventas
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM pedidos");
    $total_pedidos = $stmt->fetch()['total'];
    
    // Total de ingresos
    $stmt = $pdo->query("SELECT COALESCE(SUM(monto_total), 0) as total FROM pedidos");
    $ingresos_totales = $stmt->fetch()['total'];
    
    // Total de clientes
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM clientes");
    $total_clientes = $stmt->fetch()['total'];
    
    // Pedidos pendientes
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM pedidos WHERE id_estado = 1");
    $pedidos_pendientes = $stmt->fetch()['total'];
    
    // Promedio de venta
    $promedio = $total_pedidos > 0 ? $ingresos_totales / $total_pedidos : 0;
    
    // Hoy
    $stmt = $pdo->query("SELECT COUNT(*) as total, COALESCE(SUM(monto_total), 0) as monto FROM pedidos WHERE DATE(fecha_pedido) = CURDATE()");
    $hoy = $stmt->fetch();
    
    echo json_encode([
        'success' => true,
        'resumen' => [
            'total_pedidos' => $total_pedidos,
            'total_ingresos' => round($ingresos_totales, 2),
            'ingresos_hoy' => round($hoy['monto'], 2),
            'pedidos_hoy' => $hoy['total'],
            'total_clientes' => $total_clientes,
            'pedidos_pendientes' => $pedidos_pendientes,
            'promedio_venta' => round($promedio, 2)
        ]
    ], JSON_UNESCAPED_UNICODE);
}

/**
 * Top 5 productos más vendidos
 */
function productosMasVendidos() {
    global $pdo;
    
    $stmt = $pdo->query("
        SELECT 
            pl.id_plato,
            pl.nombre_plato,
            pl.precio,
            SUM(dp.cantidad) as cantidad_vendida,
            COUNT(DISTINCT dp.id_pedido) as numero_pedidos,
            ROUND(SUM(dp.cantidad * dp.precio_unitario), 2) as ingresos_totales
        FROM detallepedido dp
        JOIN platos pl ON dp.id_plato = pl.id_plato
        GROUP BY pl.id_plato
        ORDER BY cantidad_vendida DESC
        LIMIT 5
    ");
    $productos = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'productos' => $productos
    ], JSON_UNESCAPED_UNICODE);
}

/**
 * Top clientes más frecuentes
 */
function clientesFrecuentes() {
    global $pdo;
    
    $stmt = $pdo->query("
        SELECT 
            c.id_cliente,
            c.nombre,
            c.email,
            COUNT(p.id_pedido) as numero_pedidos,
            ROUND(SUM(p.monto_total), 2) as gasto_total,
            ROUND(AVG(p.monto_total), 2) as gasto_promedio,
            MAX(p.fecha_pedido) as ultima_compra
        FROM clientes c
        LEFT JOIN pedidos p ON c.id_cliente = p.id_cliente
        GROUP BY c.id_cliente
        HAVING numero_pedidos > 0
        ORDER BY numero_pedidos DESC
        LIMIT 10
    ");
    $clientes = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'clientes' => $clientes
    ], JSON_UNESCAPED_UNICODE);
}

/**
 * Ingresos por mes (últimos 12 meses)
 */
function ingresosPorMes() {
    global $pdo;
    
    $stmt = $pdo->query("
        SELECT 
            DATE_TRUNC(p.fecha_pedido, MONTH) as mes,
            COUNT(*) as numero_pedidos,
            ROUND(SUM(p.monto_total), 2) as ingresos,
            ROUND(AVG(p.monto_total), 2) as promedio
        FROM pedidos p
        WHERE p.fecha_pedido >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
        GROUP BY DATE_TRUNC(p.fecha_pedido, MONTH)
        ORDER BY mes DESC
    ");
    
    try {
        $ingresos = $stmt->fetchAll();
    } catch (Exception $e) {
        // Si DATE_TRUNC no funciona, usar DATE_FORMAT
        $stmt = $pdo->query("
            SELECT 
                DATE_FORMAT(p.fecha_pedido, '%Y-%m') as mes,
                COUNT(*) as numero_pedidos,
                ROUND(SUM(p.monto_total), 2) as ingresos,
                ROUND(AVG(p.monto_total), 2) as promedio
            FROM pedidos p
            WHERE p.fecha_pedido >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(p.fecha_pedido, '%Y-%m')
            ORDER BY mes DESC
        ");
        $ingresos = $stmt->fetchAll();
    }
    
    echo json_encode([
        'success' => true,
        'ingresos_por_mes' => $ingresos
    ], JSON_UNESCAPED_UNICODE);
}

exit;
?>
