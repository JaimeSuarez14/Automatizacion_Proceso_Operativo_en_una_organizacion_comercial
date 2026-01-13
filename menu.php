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

<h1 class="titulo-menu">Menú</h1>

<div class="contenedor-menu">

<?php foreach ($categorias as $cat): ?>
    <section class="categoria">
        
        <!-- Título de categoría -->
        <h2 class="categoria-titulo">
            <?php echo htmlspecialchars($cat['categoria']); ?>
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

        <div class="platos-grid">

            <?php foreach ($listaPlatos as $plato): ?>
                <div class="plato-item">

                    <?php 
                    // Usar la imagen guardada o mostrar una por defecto
                    $img = (!empty($plato['imagen'])) ? trim($plato['imagen']) : "noimage.jpg";
                    $ruta = "img/" . $img;
                    ?>

                    <!-- Imagen -->
                    <img class="plato-img"
                         src="<?php echo $ruta; ?>"
                         alt="<?php echo htmlspecialchars($plato['nombre_plato']); ?>"
                         onerror="this.onerror=null; this.src='img/noimage.jpg';">

                    <!-- Nombre del plato -->
                    <h3><?php echo htmlspecialchars($plato['nombre_plato']); ?></h3>

                    <!-- Descripción -->
                    <p><?php echo htmlspecialchars($plato['descripcion']); ?></p>

                    <!-- Precio -->
                    <span class="precio">S/ <?php echo number_format($plato['precio'], 2); ?></span>

                    <!-- FORMULARIO PARA AGREGAR AL CARRITO -->
                    <form action="agregar_carrito.php" method="post" style="margin-top:10px;">
                        <input type="hidden" name="id_plato" value="<?php echo $plato['id_plato']; ?>">

                        <label class="small" style="display:block; margin-bottom:5px;">Cantidad:</label>
                        <input 
                            type="number" 
                            name="cantidad" 
                            value="1" 
                            min="1"
                            class="input-cantidad"
                        >

                        <button class="button" type="submit" style="width:100%;">Agregar</button>
                    </form>

                </div>
            <?php endforeach; ?>

        </div>
    </section>
<?php endforeach; ?>

</div>

<?php require_once __DIR__ . '/footer.php'; ?>
