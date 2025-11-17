<?php
header('Content-Type: application/json');

$target_dir = "uploads/";
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

if (isset($_FILES["archivo"])) {
    $file = $_FILES["archivo"];
    $fileName = basename($file["name"]);
    $target_file = $target_dir . $fileName;

    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Tipos permitidos (imágenes y videos)
    $allowedTypes = ["jpg", "jpeg", "png", "gif", "webp", "mp4", "mov", "avi", "mkv"];

    if (!in_array($fileType, $allowedTypes)) {
        echo json_encode(["status" => "error", "mensaje" => "Formato no permitido"]);
        exit;
    }

    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        echo json_encode(["status" => "success", "ruta" => $target_file]);
    } else {
        echo json_encode(["status" => "error", "mensaje" => "No se pudo subir el archivo"]);
    }
} else {
    echo json_encode(["status" => "error", "mensaje" => "No se recibió ningún archivo"]);
}
?>


