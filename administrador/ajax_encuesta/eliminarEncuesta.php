<?php
if (isset($_POST['id_encuesta']) && $_POST['id_encuesta'] != "") {
    include("../../conexion.php");
    $id_encuesta = $_POST['id_encuesta'];

    // Eliminar todas las dependencias
    $con->query("DELETE FROM resultados WHERE id_opcion IN (SELECT id_opcion FROM opciones WHERE id_pregunta IN (SELECT id_pregunta FROM preguntas WHERE id_encuesta = $id_encuesta))");
    $con->query("DELETE FROM opciones WHERE id_pregunta IN (SELECT id_pregunta FROM preguntas WHERE id_encuesta = $id_encuesta)");
    $con->query("DELETE FROM preguntas WHERE id_encuesta = $id_encuesta");
    $con->query("DELETE FROM usuarios_encuestas WHERE id_encuesta = $id_encuesta");

    // Finalmente eliminar la encuesta
    $query = $con->prepare("DELETE FROM encuestas WHERE id_encuesta = ?");
    $query->bind_param("i", $id_encuesta);

    if ($query->execute()) {
        echo "Encuesta eliminada con éxito";
    } else {
        echo "Error al eliminar la encuesta: " . $con->error;
    }

    $query->close();
    $con->close();
} else {
    echo "ID de encuesta no proporcionado o está vacío";
}
