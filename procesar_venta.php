<?php
require 'db.php';
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Header JSON para respuestas AJAX
header('Content-Type: application/json');

// Validar que es POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Obtener datos
$nombre = trim($_POST['nombre_cliente'] ?? '');
$email = trim($_POST['email_cliente'] ?? '');
$telefono = trim($_POST['telefono_cliente'] ?? '');
$direccion = trim($_POST['direccion_cliente'] ?? '');
$notas = trim($_POST['notas'] ?? '');
$id_pago = intval($_POST['id_pago'] ?? 0);
$total = floatval($_POST['total'] ?? 0);
$productos = json_decode($_POST['productos'] ?? '{}', true);

// Validaciones
$errores = [];

if (empty($nombre) || strlen($nombre) < 3) {
    $errores[] = 'Nombre inválido';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errores[] = 'Email inválido';
}

if (empty($telefono) || strlen(preg_replace('/\D/', '', $telefono)) < 7) {
    $errores[] = 'Teléfono inválido';
}

if (empty($direccion) || strlen($direccion) < 10) {
    $errores[] = 'Dirección inválida';
}

if ($id_pago <= 0) {
    $errores[] = 'Método de pago inválido';
}

if ($total <= 0) {
    $errores[] = 'Total de pedido inválido';
}

if (empty($productos)) {
    $errores[] = 'No hay productos en el pedido';
}

// Si hay errores, retornar
if (!empty($errores)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Error en validación',
        'errores' => $errores
    ]);
    exit;
}

try {
    // Iniciar transacción
    $pdo->beginTransaction();

    // 1. Buscar o crear cliente
    $stmt = $pdo->prepare("SELECT id_cliente FROM clientes WHERE email = ?");
    $stmt->execute([$email]);
    $id_cliente = $stmt->fetchColumn();

    if (!$id_cliente) {
        $stmt = $pdo->prepare("INSERT INTO clientes (nombre, email, telefono, direccion, fecha_registro) 
                               VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$nombre, $email, $telefono, $direccion]);
        $id_cliente = $pdo->lastInsertId();
    }

    // 2. Crear pedido
    $stmt = $pdo->prepare("INSERT INTO pedidos (id_cliente, fecha_pedido, id_pago, id_estado, monto_total, notas)
                           VALUES (?, NOW(), ?, 1, ?, ?)");
    $stmt->execute([$id_cliente, $id_pago, $total, $notas]);
    $id_pedido = $pdo->lastInsertId();

    // 3. Crear detalles del pedido
    foreach ($productos as $id_plato => $cantidad) {
        $stmt = $pdo->prepare("SELECT precio FROM platos WHERE id_plato = ?");
        $stmt->execute([$id_plato]);
        $precio = $stmt->fetchColumn();

        if ($precio === false) {
            throw new Exception("Plato no encontrado: $id_plato");
        }

        $subtotal = $precio * $cantidad;
        $stmt = $pdo->prepare("INSERT INTO detallepedido (id_pedido, id_plato, cantidad, precio_unitario, subtotal)
                               VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$id_pedido, $id_plato, $cantidad, $precio, $subtotal]);
    }

    // 4. Registrar venta en tabla de ventas (si existe)
    try {
        $stmt = $pdo->prepare("INSERT INTO ventas (id_pedido, id_cliente, fecha_venta, monto_total, estado)
                               VALUES (?, ?, NOW(), ?, 'completada')");
        $stmt->execute([$id_pedido, $id_cliente, $total]);
    } catch (Exception $e) {
        // Si la tabla no existe, continuar sin registrar
    }

    // Commit de la transacción
    $pdo->commit();

    // 5. Enviar email de confirmación
    enviarEmailConfirmacion($email, $nombre, $id_pedido, $total, $productos);

    // 6. Limpiar carrito
    $_SESSION['cart'] = [];

    // Retornar respuesta exitosa
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Pedido registrado exitosamente',
        'id_pedido' => $id_pedido,
        'redirect' => 'confirmacion_pedido.php?id=' . $id_pedido
    ]);

} catch (Exception $e) {
    // Rollback en caso de error
    $pdo->rollBack();
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al procesar el pedido: ' . $e->getMessage()
    ]);
}

/**
 * Enviar email de confirmación del pedido
 */
function enviarEmailConfirmacion($email, $nombre, $id_pedido, $total, $productos) {
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';
    require 'PHPMailer/src/Exception.php';

    try {
        global $pdo;
        
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'tu_email@gmail.com'; // Cambiar
        $mail->Password = 'tu_contraseña_app'; // Cambiar
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('tu_email@gmail.com', 'La Choza Náutica');
        $mail->addAddress($email, $nombre);

        // Construir cuerpo del email
        $body = "<h2>Confirmación de Pedido #$id_pedido</h2>";
        $body .= "<p>Hola <strong>$nombre</strong>,</p>";
        $body .= "<p>Tu pedido ha sido registrado exitosamente.</p>";
        $body .= "<h3>Detalle del Pedido:</h3>";
        $body .= "<table border='1' cellpadding='10'>";
        $body .= "<tr><th>Producto</th><th>Cantidad</th><th>Subtotal</th></tr>";

        foreach ($productos as $id => $cantidad) {
            $stmt = $pdo->prepare("SELECT nombre_plato, precio FROM platos WHERE id_plato = ?");
            $stmt->execute([$id]);
            $plato = $stmt->fetch();
            if ($plato) {
                $subtotal = $plato['precio'] * $cantidad;
                $body .= "<tr>";
                $body .= "<td>" . htmlspecialchars($plato['nombre_plato']) . "</td>";
                $body .= "<td>$cantidad</td>";
                $body .= "<td>S/. " . number_format($subtotal, 2) . "</td>";
                $body .= "</tr>";
            }
        }

        $body .= "</table>";
        $body .= "<h3>Total: S/. " . number_format($total, 2) . "</h3>";
        $body .= "<p>Tu pedido será procesado pronto. Gracias por tu compra.</p>";

        $mail->isHTML(true);
        $mail->Subject = "Confirmación de Pedido #$id_pedido";
        $mail->Body = $body;

        $mail->send();
    } catch (Exception $e) {
        // Log de error, pero no bloquear el proceso
        error_log("Error al enviar email: " . $e->getMessage());
    }
}

exit;
?>
