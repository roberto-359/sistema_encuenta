<?php
if (isset($_POST['id_encuesta']) && isset($_POST['titulo']) && isset($_POST['id_tipo_pregunta'])) {
    // Incluir archivo de conexión a base de datos
    include("../../conexion.php");

    // Obtener valores
    $id_encuesta     = $_POST['id_encuesta'];
    $titulo          = $_POST['titulo'];
    $id_tipo_pregunta = $_POST['id_tipo_pregunta'];

    // Escapar caracteres especiales para evitar inyección SQL
    $id_encuesta = $con->real_escape_string($id_encuesta);
    $titulo = $con->real_escape_string($titulo);
    $id_tipo_pregunta = $con->real_escape_string($id_tipo_pregunta);

    // Insertar en la base de datos
    $query = "INSERT INTO preguntas (id_encuesta, titulo, id_tipo_pregunta)
              VALUES ('$id_encuesta', '$titulo', '$id_tipo_pregunta')";

    if ($con->query($query) === TRUE) {
        echo "Pregunta agregada correctamente.";
    } else {
        echo "Error: " . $query . "<br>" . $con->error;
    }
}
?>
