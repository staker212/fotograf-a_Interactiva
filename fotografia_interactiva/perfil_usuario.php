<?php
session_start();
require 'conexion.php';

// Verificar sesión
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

$id = $_SESSION['id'];

// Obtener datos del usuario
$stmt = $conexion->prepare("SELECT nombre, email, foto_perfil FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
$stmt->close();

// Foto por defecto
$foto_src = 'assets/default-avatar.png';
if (!empty($usuario['foto_perfil']) && file_exists('uploads/' . $usuario['foto_perfil'])) {
    $foto_src = 'uploads/' . $usuario['foto_perfil'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Mi Perfil</title>
<style>
body { font-family: Arial, sans-serif; background:#f5f5f5; padding:20px; }
.avatar { width:150px; height:150px; border-radius:50%; object-fit:cover; }
.btn { padding:8px 12px; cursor:pointer; margin-top:10px; }
.perfil { background:white; padding:20px; border-radius:10px; max-width:400px; margin:auto; text-align:center; }
</style>
</head>
<body>

<div class="perfil">
    <h1>Mi Perfil</h1>

    <img src="<?php echo htmlspecialchars($foto_src); ?>" alt="Avatar" class="avatar">
    <p><strong><?php echo htmlspecialchars($usuario['nombre']); ?></strong></p>
    <p><?php echo htmlspecialchars($usuario['email']); ?></p>

    <!-- Cambiar foto -->
    <form action="subir_foto_perfil.php" method="post" enctype="multipart/form-data">
        <label>
            Cambiar foto:
            <input type="file" name="foto" accept="image/*" required>
        </label>
        <button type="submit" class="btn">Subir</button>
