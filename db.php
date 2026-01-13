<?php
// ==========================
// CONFIGURACIÓN DE LA BASE DE DATOS
// ==========================
$host = "localhost";
$dbname = "cevichería";
$user = "root";
$pass = "";
$charset = "utf8mb4";

// ==========================
// CONEXIÓN MySQLi
// ==========================
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Conexión MySQLi fallida: " . $conn->connect_error);
}
$conn->set_charset($charset);

// ==========================
// CONEXIÓN PDO
// ==========================
$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Conexión PDO fallida: " . $e->getMessage());
}
