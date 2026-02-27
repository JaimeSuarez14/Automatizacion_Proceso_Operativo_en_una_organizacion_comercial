<?php
require __DIR__ . '/../../db.php';
require_once __DIR__ . '/../admin_header.php';

$stmt = $pdo->query("SELECT * FROM inventario WHERE estado = 1 ORDER BY nombre_insumo");
$inventario = $stmt->fetchAll();
?>


<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
    .card { border: none; border-radius: 15px; }
    .table thead th { font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; border: none; }
    .btn-action { width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; transition: all 0.3s; }
    .btn-action:hover { transform: translateY(-2px); }
    .badge-soft-success { background-color: #d1e7dd; color: #0f5132; }
    .badge-soft-danger { background-color: #f8d7da; color: #842029; }
    .avatar-sm { width: 40px; height: 40px; background: #e9ecef; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #495057; }
</style>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-success">Gestión de Inventario</h2>
            <p class="text-muted">Control de insumos y existencias en tiempo real.</p>
        </div>
        <a href="crear.php" class="btn btn-primary px-4 py-2 shadow-sm" style="border-radius: 10px;">
            <i class="fas fa-plus me-2"></i> Nuevo Insumo
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Insumo</th>
                            <th>Stock</th>
                            <th>Unidad</th>
                            <th>Estado</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($inventario as $item): 
                            $esBajoStock = $item['stock_actual'] <= $item['stock_minimo'];
                        ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-3">
                                        <i class="fas fa-box-open"></i>
                                    </div>
                                    <div>
                                        <span class="d-block fw-bold text-dark"><?= htmlspecialchars($item['nombre_insumo']) ?></span>
                                        <small class="text-muted">Mín. requerido: <?= $item['stock_minimo'] ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="fw-bold <?= $esBajoStock ? 'text-danger' : 'text-dark' ?>">
                                    <?= $item['stock_actual'] ?>
                                </span>
                            </td>
                            <td><span class="text-muted"><?= $item['unidad'] ?></span></td>
                            <td>
                                <?php if ($esBajoStock): ?>
                                    <span class="badge rounded-pill badge-soft-danger">
                                        <i class="fas fa-exclamation-triangle me-1"></i> Reponer
                                    </span>
                                <?php else: ?>
                                    <span class="badge rounded-pill badge-soft-success">
                                        <i class="fas fa-check-circle me-1"></i> Stock OK
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-4">
                                <a href="editar.php?id=<?= $item['id_inventario'] ?>" class="btn btn-action btn-outline-primary me-1" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="movimientos.php?id=<?= $item['id_inventario'] ?>" class="btn btn-action btn-outline-info me-1" title="Ver Movimientos">
                                    <i class="fas fa-history"></i>
                                </a>
                                <a href="eliminar.php?id=<?= $item['id_inventario'] ?>" class="btn btn-action btn-outline-danger" title="Eliminar" onclick="return confirm('¿Eliminar este insumo?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../admin_footer.php'; ?>