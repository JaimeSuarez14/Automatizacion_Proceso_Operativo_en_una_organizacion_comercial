<?php
require __DIR__ . '/../../db.php';
require_once __DIR__ . '/../admin_header.php';

// Obtener últimas 10 ventas
$stmt = $pdo->query("
    SELECT v.id_venta, v.id_pedido, v.fecha_venta, v.monto_total, v.estado, 
           c.nombre
    FROM ventas v
    JOIN clientes c ON v.id_cliente = c.id_cliente
    ORDER BY v.fecha_venta DESC
    LIMIT 10
");
$ventas = $stmt->fetchAll();
?>

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
  :root {
    --primary: #e94560;
    --primary-soft: rgba(233, 69, 96, 0.12);
    --dark-bg: #0d1117;
    --card-bg: #161b27;
    --card-border: #1f2a3c;
    --text-main: #e2e8f0;
    --text-muted: #64748b;
    --accent: #38bdf8;
  }

  body {
    background: var(--dark-bg);
    font-family: 'Sora', sans-serif;
    color: var(--text-main);
  }

  /* ── Page wrapper ── */
  .page-wrapper {
    min-height: 100vh;
    padding: 2.5rem 1.5rem;
    background:
      radial-gradient(ellipse 80% 50% at 50% -10%, rgba(233, 69, 96, .15) 0%, transparent 70%),
      var(--dark-bg);
  }

  /* ── Header section ── */
  .page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 2rem;
  }

  .page-title {
    font-size: 1.6rem;
    font-weight: 700;
    letter-spacing: -.5px;
    margin: 0;
  }

  .page-title span {
    color: var(--primary);
  }

  .page-subtitle {
    color: var(--text-muted);
    font-size: .85rem;
    margin: .15rem 0 0;
  }

  /* ── Stat pills ── */
  .stat-pill {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: 14px;
    padding: .6rem 1.2rem;
    font-size: .82rem;
    color: var(--text-muted);
    display: flex;
    align-items: center;
    gap: .5rem;
  }

  .stat-pill strong {
    color: var(--text-main);
    font-size: 1rem;
    font-weight: 600;
  }

  /* ── Table card ── */
  .table-card {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 25px 60px rgba(0, 0, 0, .4);
  }

  .table-card-header {
    padding: 1.4rem 1.8rem;
    border-bottom: 1px solid var(--card-border);
    display: flex;
    align-items: center;
    gap: .75rem;
  }

  .table-card-header .icon-box {
    width: 38px;
    height: 38px;
    background: var(--primary-soft);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary);
    flex-shrink: 0;
  }

  .table-card-header h5 {
    margin: 0;
    font-weight: 600;
    font-size: 1rem;
  }

  .table-card-header small {
    color: var(--text-muted);
    font-size: .78rem;
    display: block;
  }

  /* ── Table ── */
  .ventas-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
  }

  .ventas-table thead th {
    background: rgba(255, 255, 255, .03);
    padding: .85rem 1.5rem;
    font-size: .72rem;
    font-weight: 600;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: var(--text-muted);
    border-bottom: 1px solid var(--card-border);
    white-space: nowrap;
  }

  .ventas-table tbody tr {
    transition: background .18s;
    cursor: default;
  }

  .ventas-table tbody tr:hover {
    background: rgba(255, 255, 255, .03);
  }

  .ventas-table tbody td {
    padding: 1rem 1.5rem;
    font-size: .875rem;
    border-bottom: 1px solid rgba(31, 42, 60, .7);
    vertical-align: middle;
    color: var(--text-main);
  }

  .ventas-table tbody tr:last-child td {
    border-bottom: none;
  }

  /* ID pill */
  .id-pill {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    background: rgba(56, 189, 248, .1);
    color: var(--accent);
    border: 1px solid rgba(56, 189, 248, .2);
    border-radius: 8px;
    padding: .2rem .7rem;
    font-size: .8rem;
    font-weight: 600;
    font-variant-numeric: tabular-nums;
  }

  /* Cliente avatar */
  .cliente-cell {
    display: flex;
    align-items: center;
    gap: .7rem;
  }

  .avatar {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: .8rem;
    flex-shrink: 0;
  }

  .cliente-name {
    font-weight: 500;
    font-size: .875rem;
  }

  /* Fecha */
  .fecha-main {
    font-size: .875rem;
    font-weight: 500;
  }

  .fecha-time {
    font-size: .75rem;
    color: var(--text-muted);
  }

  /* Monto */
  .monto {
    font-weight: 700;
    font-size: .95rem;
    color: #4ade80;
    font-variant-numeric: tabular-nums;
  }

  .monto-currency {
    font-size: .75rem;
    color: var(--text-muted);
    font-weight: 400;
  }

  /* Badges */
  .estado-badge {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .3rem .8rem;
    border-radius: 20px;
    font-size: .75rem;
    font-weight: 600;
    letter-spacing: .3px;
    white-space: nowrap;
  }

  .estado-badge .dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    display: inline-block;
  }

  /* Estado: pagado / completado */
  .estado-pagado,
  .estado-completado {
    background: rgba(74, 222, 128, .1);
    color: #4ade80;
    border: 1px solid rgba(74, 222, 128, .25);
  }

  .estado-pagado .dot,
  .estado-completado .dot {
    background: #4ade80;
    box-shadow: 0 0 6px #4ade80;
  }

  /* pendiente */
  .estado-pendiente {
    background: rgba(251, 191, 36, .1);
    color: #fbbf24;
    border: 1px solid rgba(251, 191, 36, .25);
  }

  .estado-pendiente .dot {
    background: #fbbf24;
    box-shadow: 0 0 6px #fbbf24;
  }

  /* cancelado / anulado */
  .estado-cancelado,
  .estado-anulado {
    background: rgba(233, 69, 96, .1);
    color: var(--primary);
    border: 1px solid rgba(233, 69, 96, .25);
  }

  .estado-cancelado .dot,
  .estado-anulado .dot {
    background: var(--primary);
    box-shadow: 0 0 6px var(--primary);
  }

  /* en-proceso */
  .estado-en-proceso {
    background: rgba(56, 189, 248, .1);
    color: var(--accent);
    border: 1px solid rgba(56, 189, 248, .25);
  }

  .estado-en-proceso .dot {
    background: var(--accent);
    box-shadow: 0 0 6px var(--accent);
  }

  /* Acción btn */
  .btn-ver {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    padding: .35rem .9rem;
    border-radius: 10px;
    font-size: .8rem;
    font-weight: 600;
    background: var(--primary-soft);
    color: var(--primary);
    border: 1px solid rgba(233, 69, 96, .3);
    text-decoration: none;
    transition: all .2s;
  }

  .btn-ver:hover {
    background: var(--primary);
    color: #fff;
    border-color: var(--primary);
    box-shadow: 0 4px 15px rgba(233, 69, 96, .35);
    transform: translateY(-1px);
  }

  /* Empty state */
  .empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: var(--text-muted);
  }

  .empty-state svg {
    opacity: .3;
    margin-bottom: 1rem;
  }

  /* Responsive scroll */
  .table-responsive-wrapper {
    overflow-x: auto;
  }
</style>

<div class="page-wrapper">
  <div class="container-xl">

    <!-- ── Page header ── -->
    <div class="page-header">
      <div>
        <h1 class="page-title">Historial de <span>Ventas</span></h1>
        <p class="page-subtitle">Últimas 10 transacciones registradas</p>
      </div>

      <div class="d-flex flex-wrap gap-2">
        <?php
        $total_ventas = count($ventas);
        $monto_total  = array_sum(array_column($ventas, 'monto_total'));
        ?>
        <div class="stat-pill">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="#38bdf8" viewBox="0 0 16 16">
            <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5z" />
          </svg>
          <span>Registros&nbsp;</span><strong><?= $total_ventas ?></strong>
        </div>
        <div class="stat-pill">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="#4ade80" viewBox="0 0 16 16">
            <path d="M8 10a2 2 0 1 0 0-4 2 2 0 0 0 0 4z" />
            <path d="M0 4a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1V4zm3 0a2 2 0 0 1-2 2v4a2 2 0 0 1 2 2h10a2 2 0 0 1 2-2V6a2 2 0 0 1-2-2H3z" />
          </svg>
          <span>Total&nbsp;</span><strong>S/. <?= number_format($monto_total, 2) ?></strong>
        </div>
      </div>
    </div>

    <!-- ── Table card ── -->
    <div class="table-card">
      <div class="table-card-header">
        <div class="icon-box">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
            <path d="M1 2.5A1.5 1.5 0 0 1 2.5 1h3A1.5 1.5 0 0 1 7 2.5v3A1.5 1.5 0 0 1 5.5 7h-3A1.5 1.5 0 0 1 1 5.5v-3zm8 0A1.5 1.5 0 0 1 10.5 1h3A1.5 1.5 0 0 1 15 2.5v3A1.5 1.5 0 0 1 13.5 7h-3A1.5 1.5 0 0 1 9 5.5v-3zm-8 8A1.5 1.5 0 0 1 2.5 9h3A1.5 1.5 0 0 1 7 10.5v3A1.5 1.5 0 0 1 5.5 15h-3A1.5 1.5 0 0 1 1 13.5v-3zm8 0A1.5 1.5 0 0 1 10.5 9h3a1.5 1.5 0 0 1 1.5 1.5v3a1.5 1.5 0 0 1-1.5 1.5h-3A1.5 1.5 0 0 1 9 13.5v-3z" />
          </svg>
        </div>
        <div>
          <h5>Ventas Recientes</h5>
          <small>Mostrando las últimas 10 ventas</small>
        </div>
        <div>
          <a href="exportar_pdf.php" class="stat-pill" style="text-decoration:none;">
            📄 Exportar PDF
          </a>
        </div>
      </div>

      <div class="table-responsive-wrapper">
        <table class="ventas-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Cliente</th>
              <th>Fecha</th>
              <th>Monto</th>
              <th>Estado</th>
              <th style="text-align:center;">Acción</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($ventas)): ?>
              <tr>
                <td colspan="6">
                  <div class="empty-state">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="#64748b" viewBox="0 0 16 16">
                      <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z" />
                      <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3.5a.5.5 0 0 1-.5-.5v-3.5A.5.5 0 0 1 8 4z" />
                    </svg>
                    <p>No hay ventas registradas aún.</p>
                  </div>
                </td>
              </tr>
            <?php else: ?>
              <?php
              // Paleta de colores para avatares
              $avatar_colors = [
                ['bg' => 'rgba(233,69,96,.18)',  'color' => '#e94560'],
                ['bg' => 'rgba(56,189,248,.18)', 'color' => '#38bdf8'],
                ['bg' => 'rgba(74,222,128,.18)', 'color' => '#4ade80'],
                ['bg' => 'rgba(251,191,36,.18)', 'color' => '#fbbf24'],
                ['bg' => 'rgba(167,139,250,.18)', 'color' => '#a78bfa'],
                ['bg' => 'rgba(251,113,133,.18)', 'color' => '#fb7185'],
              ];
              $i = 0;
              ?>
              <?php foreach ($ventas as $v):
                $color  = $avatar_colors[$i % count($avatar_colors)];
                $initials = mb_strtoupper(mb_substr($v['nombre'], 0, 1));
                $estado_class = 'estado-' . strtolower(str_replace([' ', '_'], '-', $v['estado']));
                $i++;
              ?>
                <tr>
                  <!-- ID -->
                  <td>
                    <span class="id-pill">
                      <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8 1a7 7 0 1 0 0 14A7 7 0 0 0 8 1zM0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8z" />
                      </svg>
                      <?= $v['id_venta'] ?>
                    </span>
                  </td>

                  <!-- Cliente -->
                  <td>
                    <div class="cliente-cell">
                      <div class="avatar" style="background:<?= $color['bg'] ?>; color:<?= $color['color'] ?>;">
                        <?= $initials ?>
                      </div>
                      <span class="cliente-name"><?= htmlspecialchars($v['nombre']) ?></span>
                    </div>
                  </td>

                  <!-- Fecha -->
                  <td>
                    <div class="fecha-main"><?= date('d/m/Y', strtotime($v['fecha_venta'])) ?></div>
                    <div class="fecha-time"><?= date('H:i', strtotime($v['fecha_venta'])) ?></div>
                  </td>

                  <!-- Monto -->
                  <td>
                    <span class="monto">
                      <span class="monto-currency">S/. </span><?= number_format($v['monto_total'], 2) ?>
                    </span>
                  </td>

                  <!-- Estado -->
                  <td>
                    <span class="estado-badge <?= $estado_class ?>">
                      <span class="dot"></span>
                      <?= htmlspecialchars($v['estado']) ?>
                    </span>
                  </td>

                  <!-- Acción -->
                  <td style="text-align:center;">
                    <a href="<?= BASE_URL ?>detalle_pedido.php?id_pedido=<?= $v['id_pedido'] ?>" class="btn-ver">
                      <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z" />
                        <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z" />
                      </svg>
                      Ver detalle
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div><!-- /table-card -->

  </div>
</div>

<?php require __DIR__ . '/../admin_footer.php'; ?>