<?php
/**
 * Script de Prueba del Sistema de Automatización de Ventas
 * 
 * Este archivo verifica que todos los componentes estén correctamente instalados
 * y funcionando.
 * 
 * Uso: Abre http://localhost/choza2/test_sistema.php en el navegador
 */

require 'db.php';
session_start();

$pruebas = [];
$errores = [];

// ============================================
// PRUEBA 1: Conexión a BD
// ============================================
try {
    $stmt = $pdo->query("SELECT 1");
    $pruebas['Conexión a BD'] = '✓ PASSOU';
} catch (Exception $e) {
    $pruebas['Conexión a BD'] = '✗ FALLÓ';
    $errores[] = 'No se puede conectar a la BD: ' . $e->getMessage();
}

// ============================================
// PRUEBA 2: Tablas Necesarias
// ============================================
$tablas_requieridas = ['clientes', 'pedidos', 'detallepedido', 'platos', 'metodos_pago', 'estadopedido'];

foreach ($tablas_requieridas as $tabla) {
    try {
        $stmt = $pdo->query("SELECT 1 FROM $tabla LIMIT 1");
        $pruebas["Tabla: $tabla"] = '✓ EXISTE';
    } catch (Exception $e) {
        $pruebas["Tabla: $tabla"] = '✗ NO EXISTE';
        $errores[] = "Tabla $tabla no existe. Ejecuta setup_automatizacion_ventas.sql";
    }
}

// ============================================
// PRUEBA 3: Archivos Requeridos
// ============================================
$archivos_requieridos = 
[
    'checkout.php' => 'Formulario de checkout',
    'procesar_venta.php' => 'Procesamiento de ventas',
    'confirmacion_pedido.php' => 'Página de confirmación',
    'api_ventas.php' => 'API de consultas',
    'admin/dashboard_ventas.php' => 'Dashboard de reportes',
    'carrito.php' => 'Carrito mejorado'
];

foreach ($archivos_requieridos as $archivo => $descripcion) {
    if (file_exists(__DIR__ . '/' . $archivo)) {
        $pruebas["Archivo: $archivo"] = '✓ EXIST E';
    } else {
        $pruebas["Archivo: $archivo"] = '✗ FALTA';
        $errores[] = "Archivo $archivo no encontrado";
    }
}

// ============================================
// PRUEBA 4: Métodos de Pago
// ============================================
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM metodos_pago");
    $result = $stmt->fetch();
    $total = $result['total'];
    
    if ($total > 0) {
        $pruebas['Métodos de Pago'] = "✓ $total MÉTODOS CONFIGURADOS";
    } else {
        $pruebas['Métodos de Pago'] = '⚠ NINGUNO CONFIGURADO';
        $errores[] = 'No hay métodos de pago. Ejecuta setup_automatizacion_ventas.sql';
    }
} catch (Exception $e) {
    $pruebas['Métodos de Pago'] = '✗ ERROR';
    $errores[] = 'Error al consultar métodos de pago: ' . $e->getMessage();
}

// ============================================
// PRUEBA 5: Estados de Pedidos
// ============================================
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM estadopedido");
    $result = $stmt->fetch();
    $total = $result['total'];
    
    if ($total > 0) {
        $pruebas['Estados de Pedidos'] = "✓ $total ESTADOS CONFIGURADOS";
    } else {
        $pruebas['Estados de Pedidos'] = '⚠ NINGUNO CONFIGURADO';
        $errores[] = 'No hay estados de pedidos. Ejecuta setup_automatizacion_ventas.sql';
    }
} catch (Exception $e) {
    $pruebas['Estados de Pedidos'] = '✗ ERROR';
    $errores[] = 'Error al consultar estados: ' . $e->getMessage();
}

// ============================================
// PRUEBA 6: Datos de Prueba en Platos
// ============================================
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM platos");
    $result = $stmt->fetch();
    $total = $result['total'];
    
    if ($total > 0) {
        $pruebas['Platos Disponibles'] = "✓ $total PLATOS EN BD";
    } else {
        $pruebas['Platos Disponibles'] = '⚠ SIN PLATOS';
        $errores[] = 'No hay platos registrados. Agrega platos en admin/admin_platos.php';
    }
} catch (Exception $e) {
    $pruebas['Platos Disponibles'] = '✗ ERROR';
    $errores[] = 'Error al contar platos: ' . $e->getMessage();
}

// ============================================
// PRUEBA 7: API de Ventas
// ============================================
$url_api = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/choza2/api_ventas.php?accion=estadisticas';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url_api);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code === 200 && $response) {
    $data = json_decode($response, true);
    if ($data && isset($data['success']) && $data['success']) {
        $pruebas['API Ventas'] = '✓ FUNCIONANDO';
    } else {
        $pruebas['API Ventas'] = '⚠ RESPONDE PERO CON ERRORES';
        $errores[] = 'API no responde correctamente: ' . substr($response, 0, 100);
    }
} else {
    $pruebas['API Ventas'] = '✗ NO RESPONDE';
    $errores[] = "API retorna código HTTP $http_code";
}

// ============================================
// PRUEBA 8: Session y Carrito
// ============================================
if (isset($_SESSION)) {
    $pruebas['Sesiones PHP'] = '✓ FUNCIONANDO';
} else {
    $pruebas['Sesiones PHP'] = '✗ NO FUNCIONAN';
    $errores[] = 'Las sesiones de PHP no están habilitadas';
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Sistema de Ventas</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 28px;
            margin-bottom: 5px;
        }
        
        .header p {
            opacity: 0.9;
            font-size: 14px;
        }
        
        .content {
            padding: 30px;
        }
        
        .prueba-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #eee;
            background: #fafafa;
            margin-bottom: 5px;
            border-radius: 4px;
        }
        
        .prueba-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        
        .prueba-nombre {
            font-weight: 600;
            color: #333;
            flex: 1;
        }
        
        .prueba-resultado {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 13px;
        }
        
        .resultado-ok {
            background: #e8f5e9;
            color: #2e7d32;
        }
        
        .resultado-error {
            background: #ffebee;
            color: #c62828;
        }
        
        .resultado-warning {
            background: #fff3e0;
            color: #e65100;
        }
        
        .errores-section {
            margin-top: 30px;
            padding: 20px;
            background: #ffebee;
            border-left: 5px solid #f44336;
            border-radius: 4px;
        }
        
        .errores-section h2 {
            color: #c62828;
            margin-bottom: 15px;
            font-size: 18px;
        }
        
        .error-item {
            padding: 10px;
            margin-bottom: 10px;
            background: white;
            border-radius: 4px;
            border-left: 3px solid #f44336;
            color: #333;
        }
        
        .error-item:last-child {
            margin-bottom: 0;
        }
        
        .success-section {
            margin-top: 30px;
            padding: 20px;
            background: #e8f5e9;
            border-left: 5px solid #4caf50;
            border-radius: 4px;
        }
        
        .success-section h2 {
            color: #2e7d32;
            margin-bottom: 10px;
            font-size: 18px;
        }
        
        .actions {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 30px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            text-align: center;
            transition: background-color 0.3s;
        }
        
        .btn-primary {
            background: #2196F3;
            color: white;
        }
        
        .btn-primary:hover {
            background: #1976D2;
        }
        
        .btn-success {
            background: #4caf50;
            color: white;
        }
        
        .btn-success:hover {
            background: #388e3c;
        }
        
        @media (max-width: 600px) {
            .prueba-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🧪 Pruebas del Sistema de Automatización</h1>
            <p>Verifica que todos los componentes estén correctamente instalados</p>
        </div>
        
        <div class="content">
            <h2 style="margin-bottom: 20px; color: #333;">Resultados de las Pruebas</h2>
            
            <?php foreach ($pruebas as $nombre => $resultado): ?>
                <div class="prueba-item">
                    <span class="prueba-nombre"><?= htmlspecialchars($nombre) ?></span>
                    <span class="prueba-resultado <?= strpos($resultado, '✓') !== false ? 'resultado-ok' : (strpos($resultado, '✗') !== false ? 'resultado-error' : 'resultado-warning') ?>">
                        <?= htmlspecialchars($resultado) ?>
                    </span>
                </div>
            <?php endforeach; ?>
            
            <?php if ($errores): ?>
                <div class="errores-section">
                    <h2>⚠️ Problemas Detectados</h2>
                    <?php foreach ($errores as $error): ?>
                        <div class="error-item"><?= htmlspecialchars($error) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="success-section">
                    <h2>✓ ¡Sistema Completamente Configurado!</h2>
                    <p>Todas las pruebas han pasado correctamente. El sistema está listo para usar.</p>
                </div>
            <?php endif; ?>
            
            <div class="actions">
                <a href="index.php" class="btn btn-primary">Volver al Inicio</a>
                <a href="menu.php" class="btn btn-primary">Ver Menú</a>
                <a href="admin/dashboard_ventas.php" class="btn btn-success">Dashboard de Ventas</a>
            </div>
        </div>
    </div>
</body>
</html>
