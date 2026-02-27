<?php
require __DIR__ . '/../../db.php';

$sql = "SELECT * FROM notificaciones 
        WHERE leido = 0 
        ORDER BY fecha DESC";

$resultado = mysqli_query($conn, $sql);
?>

<div class="position-fixed top-0 end-0 p-3" style="z-index: 1050; width: 350px;">

  <?php while ($noti = mysqli_fetch_assoc($resultado)) { ?>

    <div id="noti-<?php echo $noti['id_notificacion']; ?>"
      class="alert alert-danger alert-dismissible fade show shadow-sm mb-2"
      role="alert">

      <div class="d-flex justify-content-between align-items-start">
        <div>
          <strong>⚠ Alerta</strong><br>
          <?php echo $noti['mensaje']; ?>
          <div class="small text-muted">
            <?php echo $noti['fecha']; ?>
          </div>
        </div>
      </div>

      <button type="button"
        class="btn-close"
        onclick="marcarLeido(<?php echo $noti['id_notificacion']; ?>)">
      </button>

    </div>

  <?php } ?>

</div>