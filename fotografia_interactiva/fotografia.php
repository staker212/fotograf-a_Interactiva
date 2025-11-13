<?php
// Conexión a la base de datos
$host = "localhost";
$user = "root";
$pass = "";
$db = "registro_usuarios";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Capturar registro
$registroExitoso = false;
$errorRegistro = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nombre'])) {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $usuario = $_POST['usuario'];
    $contraseña = password_hash($_POST['contraseña'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (nombre, correo, usuario, contraseña) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nombre, $correo, $usuario, $contraseña);

    if ($stmt->execute()) {
        $registroExitoso = true;
    } else {
        $errorRegistro = $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Fotografía Interactiva</title>
<link rel="stylesheet" href="estilos.css">
</head>
<body>

<div id="login-container">
  <!-- LOGIN -->
  <div id="login-box">
    <h2>Iniciar Sesión</h2>
    <input type="text" id="usuario" placeholder="Usuario" required><br>
    <input type="password" id="contraseña" placeholder="Contraseña" required><br>
    <button id="btnEntrar">Entrar</button>
    <p id="mensaje" style="color:red; font-size: 0.9em;"></p>
    <p>¿No tienes una cuenta? <a href="#" id="mostrar-registro">Regístrate aquí</a></p>
  </div>

  <!-- REGISTRO -->
  <div id="registro-box" style="display:none;">
    <h2>Crear Cuenta</h2>
    <form method="POST" action="">
    <input type="text" id="nombre" placeholder="Nombre completo" required>
<input type="email" id="correo" placeholder="Correo electrónico" required>
<input type="text" id="nuevoUsuario" placeholder="Nombre de usuario" required>
<input type="password" id="nuevaContraseña" placeholder="Contraseña" required>
<button id="btnRegistrar">Registrarse</button>

    </form>
    <p id="mensajeRegistro" style="color:green; font-size: 0.9em;">
      <?php 
        if($registroExitoso) echo "✅ Registro exitoso. Ahora puedes iniciar sesión.";
        if($errorRegistro) echo "❌ Error: $errorRegistro";
      ?>
    </p>
    <p>¿Ya tienes cuenta? <a href="#" id="mostrar-login">Inicia sesión</a></p>
  </div>
</div>

<!-- Contenido principal -->
<div id="contenido" style="display:none;">
  <!-- ... Aquí va TODO tu contenido actual (tutorial, subir, galería, descubrir, footer) ... -->
</div>

<script>
  // --- LOGIN ---
  document.getElementById('btnEntrar').addEventListener('click', async function() {
    const usuario = document.getElementById('usuario').value.trim();
    const contraseña = document.getElementById('contraseña').value.trim();
    const mensaje = document.getElementById('mensaje');

    if(!usuario || !contraseña){
      mensaje.textContent = "⚠️ Ingresa usuario y contraseña.";
      return;
    }

    const datos = new FormData();
    datos.append("usuario", usuario);
    datos.append("contraseña", contraseña);

    const respuesta = await fetch("login.php", {
      method: "POST",
      body: datos
    });

    const resultado = await respuesta.text();

    if(resultado === "success"){
      document.getElementById('login-container').style.display = 'none';
      document.getElementById('contenido').style.display = 'block';
      mensaje.textContent = "";
    } else {
      mensaje.textContent = "❌ Usuario o contraseña incorrectos";
    }
  });

  // Alternar login/registro
  document.getElementById('mostrar-registro').addEventListener('click', () => {
    document.getElementById('login-box').style.display = 'none';
    document.getElementById('registro-box').style.display = 'block';
  });
  document.getElementById('mostrar-login').addEventListener('click', () => {
    document.getElementById('registro-box').style.display = 'none';
    document.getElementById('login-box').style.display = 'block';
  });

  // Cerrar sesión
  function cerrarSesion(){
    document.getElementById('login-container').style.display = 'flex';
    document.getElementById('contenido').style.display = 'none';
    document.getElementById('usuario').value = '';
    document.getElementById('contraseña').value = '';
  }

  // Mostrar secciones
  function mostrarSeccion(id){
    document.querySelectorAll('.seccion').forEach(sec => sec.style.display = 'none');
    document.getElementById(id).style.display = 'block';
  }

  // Subir y mostrar imagen
  function mostrarImagen(){
    const archivo = document.getElementById('archivo').files[0];
    const preview = document.getElementById('preview');
    const galeria = document.getElementById('galeria-contenido');

    if(archivo){
      const reader = new FileReader();
      reader.onload = function(e){
        const img = document.createElement('img');
        img.src = e.target.result;
        img.style.width = '200px';
        img.style.margin = '10px';
        img.style.borderRadius = '10px';
        preview.innerHTML = '';
        preview.appendChild(img);

        const imgGaleria = img.cloneNode();
        galeria.appendChild(imgGaleria);
      }
      reader.readAsDataURL(archivo);
    }
  }
</script>
</body>
</html>






