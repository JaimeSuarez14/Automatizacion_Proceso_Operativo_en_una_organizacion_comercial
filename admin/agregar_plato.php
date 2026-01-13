<?php
require __DIR__ . '/../db.php';
require __DIR__ . '../admin_header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = $_POST['nombre_plato'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $categoria = $_POST['categoria'];

    // Manejo de imagen
    $nombreImagen = null;

    if (!empty($_FILES['imagen']['name'])) {
        $nombreImagen = time() . "_" . basename($_FILES['imagen']['name']);
        $rutaDestino = __DIR__ . "/../img/" . $nombreImagen;
        move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino);
    }

    $stmt = $pdo->prepare("INSERT INTO platos (nombre_plato, descripcion, precio, categoria, imagen) 
                           VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$nombre, $descripcion, $precio, $categoria, $nombreImagen]);

    header("Location: admin_platos.php"); // Redirige al listado de platos
    exit;
}
?>
<section class="d-flex justify-content-center w-100">
    <div style="width: fit-content;">
        <h1 class="text-center">Agregar Nuevo Plato</h1>

        <form class="w-auto p-4 bg-body-secondary" action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label>Nombre del plato:</label>
                <input type="text" class="form-control" name="nombre_plato" required>
            </div>

            <div class="mb-3">
                <label>Descripción:</label>
                <textarea class="form-control" name="descripcion" required></textarea>
            </div>

            <div class="mb-3">
                <label>Precio:</label>
                <input type="number" class="form-control" step="0.01" name="precio" required>
            </div>

            <div class="mb-3">
                <label>Categoría:</label>
                <input type="text" class="form-control" name="categoria" required>
            </div>

            <div class="mb-3">
                <label>Imagen del plato:</label>
                <input type="file" class="form-control" name="imagen" accept="image/*">
            </div>

            <button type="submit" class="btn btn-success w-100">Guardar Plato</button>
        </form>

        <a href="admin_platos.php" class="btn btn-secondary" style="margin-top:12px; display:inline-block;">⬅ Volver a Platos</a>
    </div>
</section>
<?php require __DIR__ . '../admin_footer.php'; ?>