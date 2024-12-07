<?php
session_start();   // Iniciar sesión

include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir los datos del formulario
    $id_usuario = $_POST['id_usuario'];
    $clave = $_POST['clave'];
    $nombres = $_POST['nombres'];
    $apellidos = $_POST['apellidos'];
    $email = $_POST['email'];
    $id_tipo_usuario = $_POST['id_tipo_usuario'];

    // Insertar los datos en la base de datos
    $query = "INSERT INTO usuarios (id_usuario, clave, nombres, apellidos, email, id_tipo_usuario) VALUES ('$id_usuario', '$clave', '$nombres', '$apellidos', '$email', '$id_tipo_usuario')";
    
    if ($con->query($query) === TRUE) {
        echo "Registro exitoso!";
        // Redirigir al usuario al inicio de sesión
        header("Location: index.php");
        exit();
    } else {
        echo "Error: " . $query . "<br>" . $con->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="shortcut icon" href="imagenes/Logo.png">
  <link rel="stylesheet" href="css/registro.css">
  <title>Sistema de encuestas - Registro</title>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #1B396A;">
    <a class="navbar-brand" href="index.php">Sistema de Encuestas</a>
</nav>

<div class="container">
    <div class="card card-container">
        <form class="form-signin" action="registro.php" method="POST">
            <input type="text" id="inputUsuario" class="form-control" placeholder="Usuario" required autofocus name="id_usuario">
            <input type="password" id="inputPassword" class="form-control" placeholder="Contraseña" required name="clave">
            <input type="text" id="inputNombres" class="form-control" placeholder="Nombres" required name="nombres">
            <input type="text" id="inputApellidos" class="form-control" placeholder="Apellidos" required name="apellidos">
            <input type="email" id="inputEmail" class="form-control" placeholder="Correo electrónico" required name="email">
            <select class="form-control" name="id_tipo_usuario" required>
                <option value="" disabled selected>Seleccione su carrera: </option>
                <option value="2">Ing. Gestión Empresarial</option>
                <option value="3">Ing. Logística</option>
                <option value="4">Ing. Química</option>
                <option value="5">Ing. Materiales</option>
                <option value="6">Ing. Electromecánica</option>
            </select>
            <button class="btn btn-lg btn-primary btn-block btn-signin" type="submit">Registrar</button>
        </form>
        <br>
        <a href="login.php" class="btn btn-secondary btn-block btn-signin">Iniciar Sesión</a>
    </div>
</div>

<footer class="page-footer font-small" style="background-color: #1B396A; margin-top: 150px">
    <div class="footer-copyright text-center py-3" style="color:white;">
        © 2024 Todos los derechos reservados
        <br>
        <a href="https://tlaxco.tecnm.mx/" target="_blank">Tec NM Campus Tlaxco | Tlaxco</a>
    </div>
</footer>

<script src="js/jquery-3.3.1.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
