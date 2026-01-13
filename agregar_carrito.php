<?php
session_start();
$id = intval($_POST['id_plato'] ?? 0);
$cant = max(1,intval($_POST['cantidad'] ?? 1));
if($id <= 0){ header('Location: menu.php'); exit; }

if(!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

if(isset($_SESSION['cart'][$id])){
    $_SESSION['cart'][$id] += $cant;
} else {
    $_SESSION['cart'][$id] = $cant;
}
header('Location: carrito.php');
exit;
