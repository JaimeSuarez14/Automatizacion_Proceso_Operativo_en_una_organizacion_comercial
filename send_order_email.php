<?php
require 'db.php';
session_start();

// PHPMailer
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre_cliente'] ?? '');
    $email = trim($_POST['email_cliente'] ?? '');
    $total = floatval($_POST['total']);
    $productos = $_POST['productos'] ?? [];
    $id_pago = intval($_POST['id_pago'] ?? 1); // 1 por defecto si no se envía

    if (empty($productos)) {
        die("No hay productos en el carrito.");
    }

    // Buscar o crear cliente
    $stmt = $pdo->prepare("SELECT id_cliente FROM clientes WHERE email=?");
    $stmt->execute([$email]);
    $id_cliente = $stmt->fetchColumn();

    if (!$id_cliente) {
        $stmt = $pdo->prepare("INSERT INTO clientes (nombre, email) VALUES (?, ?)");
        $stmt->execute([$nombre, $email]);
        $id_cliente = $pdo->lastInsertId();
    }

    // Construir mensaje de boleta
    $mensaje = "¡Gracias por su pedido, $nombre!\n\nDetalle del pedido:\n\n";
    foreach ($productos as $id => $cantidad) {
        $stmt = $pdo->prepare("SELECT nombre_plato, precio FROM platos WHERE id_plato=?");
        $stmt->execute([$id]);
        $plato = $stmt->fetch();
        if ($plato) {
            $subtotal = $plato['precio'] * $cantidad;
            $mensaje .= $plato['nombre_plato'] . " x $cantidad = S/. " . number_format($subtotal, 2) . "\n";
        }
    }
    $mensaje .= "\nTotal: S/. " . number_format($total, 2) . "\n";
    $mensaje .= "\n¡Gracias por su preferencia!";

    // Guardar pedido
    try {
        $pdo->beginTransaction();

        // Insertar pedido con id_cliente y id_pago
        $stmt = $pdo->prepare("INSERT INTO pedidos (id_cliente, fecha_pedido, id_pago, id_estado, monto_total)
                               VALUES (?, NOW(), ?, 1, ?)");
        $stmt->execute([$id_cliente, $id_pago, $total]);
        $id_pedido = $pdo->lastInsertId();

        // Insertar detallepedido
        foreach ($productos as $id => $cantidad) {
            $stmt2 = $pdo->prepare("SELECT precio FROM platos WHERE id_plato=?");
            $stmt2->execute([$id]);
            $precio = $stmt2->fetchColumn();
            $subtotal = $precio * $cantidad;

            $stmt = $pdo->prepare("INSERT INTO detallepedido (id_pedido, id_plato, cantidad, precio_unitario, subtotal)
                                   VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$id_pedido, $id, $cantidad, $precio, $subtotal]);
        }

        $pdo->commit();

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Error al guardar el pedido: " . $e->getMessage());
    }

    // Enviar correo
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'rcr72678438@gmail.com';
        $mail->Password = 'mpju gxkj xmiz xpdz';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('rcr72678438@gmail.com', 'La Choza Náutica');
        $mail->addAddress($email);

        $mail->isHTML(false);
        $mail->Subject = 'Boleta de su pedido';
        $mail->Body    = $mensaje;

        $mail->send();

        // Vaciar carrito
        $_SESSION['cart'] = [];
        header("Location: index.php");
        exit;

    } catch (Exception $e) {
        die("Error al enviar el correo: " . $mail->ErrorInfo);
    }
}
?>
