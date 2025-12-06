<?php
header('Content-Type: application/json');
require 'conexion.php';
session_start();

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(["status" => "error", "mensaje" => "Debes iniciar sesión"]);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

$target_dir = "uploads/";
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

if (isset($_FILES["archivo"])) {
    $file = $_FILES["archivo"];
    $fileName = time() . "_" . basename($file["name"]); // Evita nombres repetidos
    $target_file = $target_dir . $fileName;

    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $allowedTypes = ["jpg", "jpeg", "png", "gif", "webp", "mp4", "mov", "avi", "mkv"];

    if (!in_array($fileType, $allowedTypes)) {
        echo json_encode(["status" => "error", "mensaje" => "Formato no permitido"]);
        exit;
    }

    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        // Guardar en la base de datos
        $stmt = $conn->prepare("INSERT INTO imagenes (ruta, usuario_id) VALUES (?, ?)");
        $stmt->bind_param("si", $target_file, $usuario_id);
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "ruta" => $target_file]);
        } else {
            echo json_encode(["status" => "error", "mensaje" => "Error al registrar en BD"]);
        }
        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "mensaje" => "No se pudo subir el archivo"]);
    }
} else {
    echo json_encode(["status" => "error", "mensaje" => "No se recibió ningún archivo"]);
}

$conn->close();
?>



