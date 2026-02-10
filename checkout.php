<?php
require 'db.php';
session_start();

// Verificar que hay productos en el carrito
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: carrito.php");
    exit;
}

// Obtener datos del carrito
$cart = $_SESSION['cart'];
$items = [];
$total = 0.0;

if ($cart) {
    $ids = implode(',', array_map('intval', array_keys($cart)));
    $stmt = $pdo->query("SELECT id_plato, nombre_plato, precio FROM platos WHERE id_plato IN ($ids)");
    $rows = $stmt->fetchAll();
    foreach ($rows as $r) {
        $qty = $cart[$r['id_plato']];
        $subtotal = $r['precio'] * $qty;
        $items[] = [
            'id' => $r['id_plato'],
            'nombre' => $r['nombre_plato'],
            'precio' => $r['precio'],
            'cantidad' => $qty,
            'subtotal' => $subtotal
        ];
        $total += $subtotal;
    }
}

// Obtener métodos de pago
$stmt = $pdo->query("SELECT id_pago, nombre FROM metodospago");
$metodos_pago = $stmt->fetchAll();

require 'header.php';
?>

<section class="card checkout-section">
    <h2>Checkout - Finalizar Pedido</h2>
    
    <div class="checkout-container">
        <!-- Resumen del Pedido -->
        <div class="checkout-resumen">
            <h3>Resumen de Pedido</h3>
            <table class="checkout-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['nombre']) ?></td>
                        <td>S/. <?= number_format($item['precio'], 2) ?></td>
                        <td><?= $item['cantidad'] ?></td>
                        <td>S/. <?= number_format($item['subtotal'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="checkout-total">
                <h4>Total: S/. <?= number_format($total, 2) ?></h4>
            </div>
        </div>

        <!-- Formulario de Cliente -->
        <div class="checkout-form">
            <h3>Información del Cliente</h3>
            <form id="formCheckout" method="POST" action="procesar_venta.php">
                
                <!-- Datos del Cliente -->
                <div class="form-group">
                    <label for="nombre_cliente">Nombre Completo: *</label>
                    <input type="text" id="nombre_cliente" name="nombre_cliente" 
                           placeholder="Ej: Juan Pérez" required>
                    <span class="error-msg" id="error_nombre"></span>
                </div>

                <div class="form-group">
                    <label for="email_cliente">Email: *</label>
                    <input type="email" id="email_cliente" name="email_cliente" 
                           placeholder="Ej: juan@email.com" required>
                    <span class="error-msg" id="error_email"></span>
                </div>

                <div class="form-group">
                    <label for="telefono_cliente">Teléfono: *</label>
                    <input type="tel" id="telefono_cliente" name="telefono_cliente" 
                           placeholder="Ej: 987654321" required>
                    <span class="error-msg" id="error_telefono"></span>
                </div>

                <div class="form-group">
                    <label for="direccion_cliente">Dirección de Entrega: *</label>
                    <textarea id="direccion_cliente" name="direccion_cliente" 
                              placeholder="Calle, número, distrito..." required></textarea>
                    <span class="error-msg" id="error_direccion"></span>
                </div>

                <!-- Método de Pago -->
                <div class="form-group">
                    <label for="id_pago">Método de Pago: *</label>
                    <select id="id_pago" name="id_pago" required>
                        <option value="">Selecciona un método de pago</option>
                        <?php foreach ($metodos_pago as $mp): ?>
                        <option value="<?= $mp['id_pago'] ?>">
                            <?= htmlspecialchars($mp['nombre']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <span class="error-msg" id="error_pago"></span>
                </div>

                <!-- Notas Especiales -->
                <div class="form-group">
                    <label for="notas">Notas / Especificaciones:</label>
                    <textarea id="notas" name="notas" 
                              placeholder="Ej: Sin picante, cambios en el plato..."></textarea>
                </div>

                <!-- Confirmación -->
                <div class="form-group checkbox">
                    <input type="checkbox" id="confirmacion" name="confirmacion" required>
                    <label for="confirmacion">Confirmo que los datos son correctos</label>
                    <span class="error-msg" id="error_confirmacion"></span>
                </div>

                <!-- Botones -->
                <div class="checkout-buttons">
                    <button type="submit" class="btn-primary" id="btnProcesar">
                        Procesar Pedido
                    </button>
                    <a href="carrito.php" class="btn-secondary">Volver al Carrito</a>
                </div>

                <!-- Campo oculto para enviar carrito -->
                <input type="hidden" name="productos" value="<?= htmlspecialchars(json_encode($cart)) ?>">
                <input type="hidden" name="total" value="<?= $total ?>">
            </form>
        </div>
    </div>
</section>

<style>
.checkout-section {
    max-width: 1200px;
    margin: 20px auto;
}

.checkout-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    margin-top: 30px;
}

.checkout-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

.checkout-table th, .checkout-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.checkout-table th {
    background-color: #f5f5f5;
    font-weight: bold;
}

.checkout-total {
    text-align: right;
    padding-top: 20px;
    border-top: 2px solid #333;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    color: #333;
}

.form-group input,
.form-group textarea,
.form-group select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-family: Arial, sans-serif;
    font-size: 14px;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
    outline: none;
    border-color: #2196F3;
    box-shadow: 0 0 5px rgba(33, 150, 243, 0.3);
}

.form-group.checkbox {
    display: flex;
    align-items: center;
}

.form-group.checkbox input {
    width: auto;
    margin-right: 10px;
}

.error-msg {
    color: #d32f2f;
    font-size: 12px;
    display: none;
}

.error-msg.show {
    display: block;
}

.checkout-buttons {
    display: flex;
    gap: 10px;
    margin-top: 30px;
}

.btn-primary, .btn-secondary {
    padding: 12px 24px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    font-weight: bold;
    text-decoration: none;
    text-align: center;
    transition: background-color 0.3s;
}

.btn-primary {
    background-color: #2196F3;
    color: white;
    flex: 1;
}

.btn-primary:hover {
    background-color: #1976D2;
}

.btn-secondary {
    background-color: #757575;
    color: white;
    flex: 1;
}

.btn-secondary:hover {
    background-color: #616161;
}

@media (max-width: 768px) {
    .checkout-container {
        grid-template-columns: 1fr;
    }
    
    .checkout-buttons {
        flex-direction: column;
    }
}
</style>

<script>
// Validación automática del formulario
document.getElementById('formCheckout').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (validarFormulario()) {
        // Mostrar loading
        document.getElementById('btnProcesar').disabled = true;
        document.getElementById('btnProcesar').textContent = 'Procesando...';
        
        // Enviar formulario
        this.submit();
    }
});

function validarFormulario() {
    let valido = true;
    
    // Limpiar errores previos
    document.querySelectorAll('.error-msg').forEach(el => el.classList.remove('show'));
    
    // Validar nombre
    const nombre = document.getElementById('nombre_cliente').value.trim();
    if (!nombre || nombre.length < 3) {
        mostrarError('error_nombre', 'El nombre debe tener al menos 3 caracteres');
        valido = false;
    }
    
    // Validar email
    const email = document.getElementById('email_cliente').value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        mostrarError('error_email', 'Email inválido');
        valido = false;
    }
    
    // Validar teléfono
    const telefono = document.getElementById('telefono_cliente').value.trim();
    const telefonoRegex = /^\d{7,}$/;
    if (!telefonoRegex.test(telefono.replace(/\D/g, ''))) {
        mostrarError('error_telefono', 'Teléfono inválido (mínimo 7 dígitos)');
        valido = false;
    }
    
    // Validar dirección
    const direccion = document.getElementById('direccion_cliente').value.trim();
    if (!direccion || direccion.length < 10) {
        mostrarError('error_direccion', 'La dirección debe tener al menos 10 caracteres');
        valido = false;
    }
    
    // Validar método de pago
    const pago = document.getElementById('id_pago').value;
    if (!pago) {
        mostrarError('error_pago', 'Debes seleccionar un método de pago');
        valido = false;
    }
    
    // Validar confirmación
    if (!document.getElementById('confirmacion').checked) {
        mostrarError('error_confirmacion', 'Debes confirmar que los datos son correctos');
        valido = false;
    }
    
    return valido;
}

function mostrarError(elementId, mensaje) {
    const element = document.getElementById(elementId);
    element.textContent = mensaje;
    element.classList.add('show');
}
</script>

<?php require 'footer.php'; ?>
