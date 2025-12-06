<?php
require 'conexion.php';
session_start();

$result = $conn->query("SELECT ruta FROM imagenes ORDER BY id DESC");
$imagenes = [];
while($row = $result->fetch_assoc()){
    $imagenes[] = $row['ruta'];
}
$conn->close();

// Generar HTML de la galería
foreach($imagenes as $img){
    echo '<img src="'.htmlspecialchars($img).'" alt="Imagen subida">';
}
?>
