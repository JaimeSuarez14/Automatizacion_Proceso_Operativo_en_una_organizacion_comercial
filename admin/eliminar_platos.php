<?php
require __DIR__ . '/../db.php';

// Asegurar que venga por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id_plato'])) {
    $id = (int) $_POST['id_plato'];

    // Obtener imagen actual para borrarla del disco
    $stmt = $pdo->prepare("SELECT imagen FROM platos WHERE id_plato = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    if ($row && !empty($row['imagen'])) {
        $path = __DIR__ . "/../img/" . $row['imagen'];
        if (is_file($path)) {
            @unlink($path);
        }
    }

    // Eliminar registro de la base de datos
    $del = $pdo->prepare("DELETE FROM platos WHERE id_plato = ?");
    $del->execute([$id]);
}

// Redirigir al panel de administración
header("Location: admin_platos.php");
exit;
