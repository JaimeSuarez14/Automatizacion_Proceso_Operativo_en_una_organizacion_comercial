<?php
require __DIR__ . '/../db.php';
require __DIR__ . '../admin_header.php';

// Asegurarnos que $pdo existe
if (!isset($pdo)) {
  die("Error: No se pudo conectar a la base de datos.");
}

// BUSCADOR Y FILTRO
$busqueda = $_GET['buscar'] ?? '';
$categoria = $_GET['categoria'] ?? '';

// Construir SQL con parámetros
$sql = "SELECT * FROM platos WHERE 1";
$params = [];

if ($busqueda !== '') {
  $sql .= " AND nombre_plato LIKE ?";
  $params[] = "%{$busqueda}%";
}
if ($categoria !== '') {
  $sql .= " AND categoria = ?";
  $params[] = $categoria;
}

$sql .= " ORDER BY id_plato ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$platos = $stmt->fetchAll();

// Obtener categorías para el select
$categorias = $pdo->query("SELECT DISTINCT categoria FROM platos ORDER BY categoria")->fetchAll();
?>

<h1 class="text-center fw-lighter">Administrar Platos</h1>

<form method="GET" style="margin-bottom:20px; display:flex; gap:10px; align-items:center; justify-content: center;">
  <input type="text" name="buscar" placeholder="Buscar plato..." class="form-control w-auto" value="<?php echo htmlspecialchars($busqueda); ?>">
  <select name="categoria" class="form-control w-auto">
    <option value="">Todas las categorías</option>
    <?php foreach ($categorias as $cat): ?>
      <option value="<?php echo htmlspecialchars($cat['categoria']); ?>" <?php echo ($categoria === $cat['categoria']) ? 'selected' : ''; ?>>
        <?php echo htmlspecialchars($cat['categoria']); ?>
      </option>
    <?php endforeach; ?>
  </select>
  <button type="submit" class="button">Filtrar</button>
</form>

<a href="agregar_plato.php" class="button" style="margin-bottom:12px; display:inline-block;">
  <i class="bi bi-plus-circle me-2"></i> Agregar nuevo plato
</a>

<table class="table-striped table" cellpadding="8" cellspacing="0" style="width:100%; margin-top:12px; background:white;">
  <tr style="background:#006064; color:white;">
    <th>ID</th>
    <th>Imagen</th>
    <th>Nombre</th>
    <th>Categoría</th>
    <th>Precio</th>
    <th style="width:200px;">Acciones</th>
  </tr>

  <?php if (count($platos) === 0): ?>
    <tr>
      <td colspan="6" style="text-align:center; padding:20px;">No se encontraron platos.</td>
    </tr>
  <?php endif; ?>

  <?php foreach ($platos as $p): ?>
    <tr>
      <td><?php echo (int)$p['id_plato']; ?></td>
      <td>
        <img src="../img/<?php echo htmlspecialchars($p['imagen'] ?: 'noimage.jpg'); ?>" width="70" alt="<?php echo htmlspecialchars($p['nombre_plato']); ?>">
      </td>
      <td><?php echo htmlspecialchars($p['nombre_plato']); ?></td>
      <td><?php echo htmlspecialchars($p['categoria']); ?></td>
      <td>S/ <?php echo number_format($p['precio'], 2); ?></td>
      <td>
        <a href="editar_plato.php?id=<?php echo (int)$p['id_plato']; ?>" class="button" style="margin-right:6px; margin-button:1px">✏️ Editar</a>

        <!-- FORMULARIO ELIMINAR -->
        <form action="eliminar_platos.php" method="post" style="display:inline-block; margin:0;" onsubmit="return confirm('¿Seguro que deseas eliminar este plato?');">
          <input type="hidden" name="id_plato" value="<?php echo (int)$p['id_plato']; ?>">
          <button type="submit" class="button" style="background:#c62828; border:0; color:white; padding:2px 10px; border-radius:6px; cursor:pointer;">
            🗑 Eliminar
          </button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
</table>
<?php require __DIR__ . '../admin_footer.php'; ?>