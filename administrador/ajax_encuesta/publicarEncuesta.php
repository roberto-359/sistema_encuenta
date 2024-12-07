<?php
include("../../conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_encuesta = $_POST['id_encuesta'];
    $tipo_usuario = $_POST['tipo_usuario'];
    
    // Actualizar el estado de la encuesta
    $query = "UPDATE encuestas SET estado = 1 WHERE id_encuesta = $id_encuesta";
    $con->query($query);
    
    // Asignar la encuesta a los usuarios del tipo seleccionado
    $queryUsuarios = "SELECT id_usuario FROM usuarios WHERE id_tipo_usuario = $tipo_usuario";
    $resultadoUsuarios = $con->query($queryUsuarios);
    
    while ($rowUsuario = $resultadoUsuarios->fetch_assoc()) {
        $id_usuario = $rowUsuario['id_usuario'];
        $queryAsignar = "INSERT INTO usuarios_encuestas (id_usuario, id_encuesta) VALUES ('$id_usuario', $id_encuesta)";
        $con->query($queryAsignar);
    }

    // Redireccionar de nuevo a la página de encuestas
    header("Location: ../index.php");
    exit();
}
?>
