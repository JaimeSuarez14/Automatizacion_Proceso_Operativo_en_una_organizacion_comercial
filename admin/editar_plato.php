<?php
require __DIR__ . '/../db.php';
require __DIR__ . '../admin_header.php';

// Verificar ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID inválido");
}

$id = (int) $_GET['id'];

// Obtener datos del plato
$stmt = $pdo->prepare("SELECT * FROM platos WHERE id_plato = ?");
$stmt->execute([$id]);
$plato = $stmt->fetch();

if (!$plato) {
    die("Plato no encontrado");
}

// Si envían el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = $_POST['nombre_plato'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $categoria = $_POST['categoria'];

    // Imagen existente
    $nombreImagen = $plato['imagen'];

    // Si subieron una nueva imagen
    if (!empty($_FILES['imagen']['name'])) {
        $nombreImagen = time() . "_" . basename($_FILES['imagen']['name']);
        move_uploaded_file($_FILES['imagen']['tmp_name'], __DIR__ . "/../img/" . $nombreImagen);
    }

    // Actualizar datos
    $update = $pdo->prepare("
        UPDATE platos 
        SET nombre_plato=?, descripcion=?, precio=?, categoria=?, imagen=? 
        WHERE id_plato=?
    ");

    $update->execute([
        $nombre,
        $descripcion,
        $precio,
        $categoria,
        $nombreImagen,
        $id
    ]);

    // Redirigir al panel de admin
    header("Location: admin_platos.php");
    exit;
}
?>
<section class="d-flex justify-content-center w-100">
    <div style="width: fit-content;">
        <h1 class="position-relative">
            <i class="bi bi-pencil-square me-2">
            </i>Editar Plato
            <a href="admin_platos.php" style="position: absolute; left:-100px" class="btn btn-outline-secondary">Regresar</a>
        </h1>

        <form class="w-auto bg-body-secondary p-4" method="post" enctype="multipart/form-data" >

            <label>Nombre:</label>
            <input type="text" name="nombre_plato" class="form-control mb-2" value="<?php echo htmlspecialchars($plato['nombre_plato']); ?>" required>

            <label>Descripción:</label>
            <textarea name="descripcion" class="form-control" required><?php echo htmlspecialchars($plato['descripcion']); ?></textarea>

            <label>Precio:</label>
            <input type="number" step="0.01" class="form-control" name="precio" value="<?php echo $plato['precio']; ?>" required>

            <label>Categoría:</label>
            <input type="text" name="categoria" class="form-control" value="<?php echo htmlspecialchars($plato['categoria']); ?>" required>

            <label>Imagen actual:</label><br>
            <img src="../img/<?php echo $plato['imagen'] ?: 'noimage.jpg'; ?>" width="150" style="border-radius:8px;"><br><br>

            <label>Subir nueva imagen (opcional):</label>
            <input type="file" name="imagen" class="form-control mb-4" accept="image/*">

            <button type="submit" class="btn btn-primary w-100">Actualizar</button>

        </form>
    </div>
</section>