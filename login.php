<?php
// ==========================
// LOGIN.PHP
// ==========================

// Activar errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require 'db.php'; // Asegúrate de que $pdo esté definido aquí

$error = "";

// ==========================
// Procesar formulario POST
// ==========================
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);
    $contrasena = trim($_POST['contrasena']);

    if (empty($email) || empty($contrasena)) {
        $error = "Todos los campos son obligatorios.";
    } else {
        try {
            // Buscar usuario usando PDO, ignorando mayúsculas/minúsculas
            $stmt = $pdo->prepare("SELECT * FROM clientes WHERE LOWER(email) = LOWER(:email) LIMIT 1");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch();

            if ($user) {
                // Verificar contraseña
                if (password_verify($contrasena, $user['contrasena'])) {
                    session_regenerate_id(true);
                    $_SESSION['cliente_id'] = $user['id_cliente'];
                    $_SESSION['cliente_nombre'] = $user['nombre'];
                    $_SESSION['cliente_email'] = $user['email'];

                    // Redirigir al index
                    header("Location: index.php");
                    exit;
                } else {
                    $error = "La contraseña es incorrecta.";
                }
            } else {
                $error = "No existe una cuenta con ese correo.";
            }
        } catch (PDOException $e) {
            $error = "Error en la base de datos: " . $e->getMessage();
        }
    }
}
?>

<?php require 'header.php'; ?>
<section class="w-100">
    <div class="card mx-auto " style="width: 25rem; ">
        <h2>Iniciar Sesión</h2>

        <?php if (!empty($error)): ?>
            <p class="error" style="color:red;text-align:center;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form action="" method="POST">
            <label>Correo electrónico</label>
            <input type="email" name="email" required value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">

            <label>Contraseña</label>
            <input type="password" name="contrasena" required>

            <button type="submit" class="btn button">Ingresar</button>
        </form>

        <p style="text-align:center;margin-top:15px;">
            ¿No tienes una cuenta? <a href="registro.php">Regístrate aquí</a>
        </p>
    </div>
</section>

<?php require 'footer.php'; ?>