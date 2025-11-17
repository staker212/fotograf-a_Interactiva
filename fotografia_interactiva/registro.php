<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
require 'conexion.php';

// Recibir datos
$nombre     = $_POST['nombre'] ?? '';
$correo     = $_POST['correo'] ?? '';
$usuario    = $_POST['usuario'] ?? '';
$contraseña = $_POST['contraseña'] ?? '';

// Validar campos
if (!$nombre || !$correo || !$usuario || !$contraseña) {
    echo json_encode(["status"=>"error","mensaje"=>"Completa todos los campos"]);
    exit;
}

// Verificar si el usuario existe
$stmt = $conn->prepare("SELECT id FROM registro_usuarios WHERE usuario=?");
if (!$stmt) {
    echo json_encode(["status"=>"error","mensaje"=>"Error en consulta: " . $conn->error]);
    exit;
}

$stmt->bind_param("s", $usuario);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(["status"=>"error","mensaje"=>"El usuario ya existe"]);
    exit;
}
$stmt->close();

// Insertar usuario
$hash = password_hash($contraseña, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO registro_usuarios (nombre, correo, usuario, contraseña) VALUES (?,?,?,?)");

if (!$stmt) {
    echo json_encode(["status"=>"error","mensaje"=>"Error en consulta: " . $conn->error]);
    exit;
}

$stmt->bind_param("ssss", $nombre, $correo, $usuario, $hash);

if ($stmt->execute()) {
    echo json_encode(["status"=>"success","mensaje"=>"Usuario registrado correctamente"]);
} else {
    echo json_encode(["status"=>"error","mensaje"=>"Error al registrar usuario: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>





