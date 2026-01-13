<?php
// Mostrar errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ==========================
// Conexión a la base de datos con PDO
// ==========================
$host = "localhost";
$db   = "cevichería";
$user = "root";
$pass = "";
$charset = "utf8mb4";

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Manejo de errores
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Error al conectar con la base de datos: " . $e->getMessage());
}

// ==========================
// Validar que sea POST
// ==========================
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: registro.php");
    exit;
}

// ==========================
// Recoger y limpiar datos
// ==========================
$nombre = trim($_POST['nombre']);
$telefono = trim($_POST['telefono']);
$direccion = trim($_POST['direccion']);
$email = trim($_POST['email']);
$contrasena = trim($_POST['contrasena']);

// ==========================
// Validaciones básicas
// ==========================
if (empty($nombre) || empty($telefono) || empty($direccion) || empty($email) || empty($contrasena)) {
    header("Location: registro.php?error=Todos los campos son obligatorios");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: registro.php?error=Email inválido");
    exit;
}

// ==========================
// Verificar si el correo ya existe
// ==========================
$stmt = $pdo->prepare("SELECT id_cliente FROM clientes WHERE email = :email LIMIT 1");
$stmt->bindParam(':email', $email);
$stmt->execute();

if ($stmt->fetch()) {
    header("Location: registro.php?error=El correo ya está registrado");
    exit;
}

// ==========================
// Encriptar contraseña
// ==========================
$hash = password_hash($contrasena, PASSWORD_DEFAULT);

// ==========================
// Insertar usuario en la base de datos
// ==========================
$stmt = $pdo->prepare("
    INSERT INTO clientes (nombre, telefono, direccion, email, contrasena)
    VALUES (:nombre, :telefono, :direccion, :email, :contrasena)
");

$stmt->bindParam(':nombre', $nombre);
$stmt->bindParam(':telefono', $telefono);
$stmt->bindParam(':direccion', $direccion);
$stmt->bindParam(':email', $email);
$stmt->bindParam(':contrasena', $hash);

if ($stmt->execute()) {
    header("Location: login.php?msg=Registro exitoso, ahora puedes iniciar sesión");
    exit;
} else {
    header("Location: registro.php?error=Ocurrió un error al registrar");
    exit;
}
?>
