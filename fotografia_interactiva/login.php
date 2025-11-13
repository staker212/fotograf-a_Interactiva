<?php
header('Content-Type: application/json');
require 'conexion.php';
session_start();

$usuario    = $_POST['usuario'] ?? '';
$contraseña = $_POST['contraseña'] ?? '';

// Validar campos
if (!$usuario || !$contraseña) {
    echo json_encode(["status"=>"error","mensaje"=>"Completa todos los campos"]);
    exit;
}

// Buscar usuario
$stmt = $conn->prepare("SELECT id, nombre, contraseña FROM registro_usuarios WHERE usuario=?");
if (!$stmt) {
    echo json_encode(["status"=>"error","mensaje"=>"Error en consulta: " . $conn->error]);
    exit;
}

$stmt->bind_param("s", $usuario);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($id, $nombre, $hash);
$stmt->fetch();

if ($stmt->num_rows == 0) {
    echo json_encode(["status"=>"error","mensaje"=>"Usuario no encontrado"]);
    exit;
}

// Verificar contraseña
if (password_verify($contraseña, $hash)) {
    $_SESSION['usuario_id'] = $id;
    $_SESSION['usuario_nombre'] = $nombre;
    echo json_encode(["status"=>"success","mensaje"=>"Inicio de sesión correcto"]);
} else {
    echo json_encode(["status"=>"error","mensaje"=>"Contraseña incorrecta"]);
}

$stmt->close();
$conn->close();
?>



