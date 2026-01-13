<?php
// ==========================
// LOGIN.PHP
// ==========================
session_start();
require 'db.php'; // Asegúrate de que $pdo esté definido aquí
if (isset($_SESSION['rol']) && isset($_SESSION['nombre_usuario']) && $_SESSION['rol'] =="admin"){
    header("Location: admin/admin.php");
    exit;
}   


$error = "";

// ==========================
// Procesar formulario POST
// ==========================
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $usuario = trim($_POST['usuario']);
    $contrasena = trim($_POST['contrasena']);

    if (empty($usuario) || empty($contrasena)) {
        $error = "Todos los campos son obligatorios.";
    } else {
        try {
            // Buscar usuario usando PDO, ignorando mayúsculas/minúsculas
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE LOWER(usuario) = LOWER(:usuario) LIMIT 1");
            $stmt->bindParam(':usuario', $usuario);
            $stmt->execute();
            $user = $stmt->fetch();

            if ($user) {
                // Verificar contraseña
                if (password_verify($contrasena, $user['contrasena'])) {
                    session_regenerate_id(true);
                    $_SESSION['admin_id'] = $user['id_usuario'];
                    $_SESSION['nombre_usuario'] = $user['usuario'];
                    $_SESSION['rol'] = $user['rol'];

                    // Redirigir al index
                    header("Location: admin/admin.php");
                    exit;
                } else {
                    $error = "La contraseña es incorrecta.";
                }
            } else {
                $error = "No existe una cuenta con ese usuario.";
            }
        } catch (PDOException $e) {
            $error = "Error en la base de datos: " . $e->getMessage();
        }
    }
}
?>

<?php require 'header.php'; ?>

<section class="card">
    <h2>Iniciar Sesión</h2>

    <?php if (!empty($error)): ?>
        <p class="error" style="color:red;text-align:center;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form action="" method="POST">
        <label>Nombre de usuario</label>
        <input type="text" name="usuario" required value="<?= isset($usuario) ? htmlspecialchars($usuario) : '' ?>">

        <label>Contraseña</label>
        <input type="password" name="contrasena" required>

        <button type="submit" class="btn button">Ingresar</button>
    </form>

    <p style="text-align:center;margin-top:15px;">
        Solo disponible para Jefatura. <a href="">Contacta con Administrador</a>
    </p>
</section>

<?php require 'footer.php'; ?>