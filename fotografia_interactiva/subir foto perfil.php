<?php
session_start();
require 'conexion.php';

// Verificar sesión
if (!isset($_SESSION['id'])) {
    $_SESSION['mensaje_error'] = "Debes iniciar sesión para subir una foto.";
    header("Location: login.php");
    exit;
}

$id = $_SESSION['id'];

// Verificar archivo
if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['mensaje_error'] = "Error al subir archivo. Intenta nuevamente.";
    header("Location: perfil_usuario.php");
    exit;
}

// Validar tipo y tamaño
$maxSize = 2 * 1024 * 1024; // 2MB
$allowedMime = ['image/jpeg', 'image/png', 'image/webp'];

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $_FILES['foto']['tmp_name']);
finfo_close($finfo);

if (!in_array($mime, $allowedMime)) {
    $_SESSION['mensaje_error'] = "Formato no permitido. Usa JPG, PNG o WEBP.";
    header("Location: perfil_usuario.php");
    exit;
}

if ($_FILES['foto']['size'] > $maxSize) {
    $_SESSION['mensaje_error'] = "El archivo es demasiado grande (máx 2MB).";
    header("Location: perfil_usuario.php");
    exit;
}

// Carpeta uploads
$uploadsDir = __DIR__ . '/uploads';
if (!is_dir($uploadsDir)) mkdir($uploadsDir, 0755, true);

// Nombre único
$ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
$nombreArchivo = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
$rutaDestino = $uploadsDir . '/' . $nombreArchivo;

// Mover archivo
if (!move_uploaded_file($_FILES['foto']['tmp_name'], $rutaDestino)) {
    $_SESSION['mensaje_error'] = "No se pudo guardar la foto en el servidor.";
    header("Location: perfil_usuario.php");
    exit;
}

// Borrar foto anterior
$stmt = $conexion->prepare("SELECT foto_perfil FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();

if (!empty($row['foto_perfil']) && file_exists($uploadsDir . '/' . $row['foto_perfil'])) {
    @unlink($uploadsDir . '/' . $row['foto_perfil']);
}

// Guardar en BD
$stmt = $conexion->prepare("UPDATE usuarios SET foto_perfil = ? WHERE id = ?");
$stmt->bind_param("si", $nombreArchivo, $id);
if ($stmt->execute()) {
    $_SESSION['mensaje_ok'] = "Foto subida correctamente.";
} else {
    $_SESSION['mensaje_error'] = "Error al actualizar la base de datos.";
    @unlink($rutaDestino);
}
$stmt->close();

// Redirigir al perfil
header("Location: perfil_usuario.php");
exit;
