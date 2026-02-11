<?php
// Mostrar errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluimos conexión y header
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/header.php';

// Obtener todas las categorías usando MySQLi
$resultCategorias = $conn->query("SELECT DISTINCT categoria FROM platos ORDER BY categoria");
$categorias = $resultCategorias->fetch_all(MYSQLI_ASSOC);
?>

<div class="container-fluid px-4">
    <h1 class="text-center fw-bold mb-5">🍽️ Nuestro Menú</h1>
    <?php foreach ($categorias as $cat): ?>
        <div class="mb-5">
            <h2 class="mb-4 my-2">
                <span class="badge bg-primary fs-5 px-4 py-2">
                    <?php echo htmlspecialchars($cat['categoria']); ?>
                </span>
            </h2>

            <?php
            // Obtener platos de esa categoría usando MySQLi
            $categoriaActual = $cat['categoria'];

            // Preparar la consulta
            $stmt = $conn->prepare("SELECT * FROM platos WHERE categoria = ? ORDER BY nombre_plato");
            $stmt->bind_param("s", $categoriaActual);
            $stmt->execute();
            $resultadoPlatos = $stmt->get_result();
            $listaPlatos = $resultadoPlatos->fetch_all(MYSQLI_ASSOC);
            ?>

            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3">
                <?php foreach ($listaPlatos as $plato): ?>
                    <div class="col p-0">
                        <div class="card h-100 shadow-sm border-0 card-hover">
                            <?php
                            $img = (!empty($plato['imagen'])) ? trim($plato['imagen']) : "noimage.jpg";
                            $ruta = "img/" . $img;
                            ?>
                            <img
                                src="<?php echo $ruta; ?>"
                                class="card-img-top"
                                alt="<?php echo htmlspecialchars($plato['nombre_plato']); ?>"
                                onerror="this.onerror=null; this.src='img/noimage.jpg';"
                                style="height:200px; object-fit:cover;">

                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title fw-semibold">
                                    <?php echo htmlspecialchars($plato['nombre_plato']); ?>
                                </h5>

                                <p class="card-text text-muted small flex-grow-1">
                                    <?php echo htmlspecialchars($plato['descripcion']); ?>
                                </p>

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="fw-bold text-success fs-5">
                                        S/ <?php echo number_format($plato['precio'], 2); ?>
                                    </span>
                                </div>

                                <form action="agregar_carrito.php" method="post">
                                    <input type="hidden" name="id_plato" value="<?php echo $plato['id_plato']; ?>">

                                    <div class="input-group mb-2">
                                        <span class="input-group-text">Cant.</span>
                                        <input
                                            type="number"
                                            name="cantidad"
                                            class="form-control"
                                            value="1"
                                            min="1">
                                    </div>

                                    <button class="btn btn-primary w-100">
                                        🛒 Agregar al carrito
                                    </button>
                                </form>

                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php require_once __DIR__ . '/footer.php'; ?>