<?php
require __DIR__ . '/../../db.php';

if (isset($_POST['id'])) {
  $id = intval($_POST['id']);
  $sql = "UPDATE notificaciones 
            SET leido = 1 
            WHERE id_notificacion = $id";
  mysqli_query($conn, $sql);
}
