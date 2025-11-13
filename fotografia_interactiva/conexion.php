<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "registro_usuarios";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "mensaje" => "Error de conexión a DB"]));
}
?>



