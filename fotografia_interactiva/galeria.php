<?php
$archivos = glob('uploads/*.{jpg,jpeg,png,gif,webp,mp4,mov,avi,mkv}', GLOB_BRACE);

foreach($archivos as $archivo){
    $ext = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
    if(in_array($ext, ['mp4','mov','avi','mkv'])){
        echo '<video src="'.$archivo.'" controls width="250"></video>';
    } else {
        echo '<img src="'.$archivo.'" alt="Archivo subido">';
    }
}
?>
