<?php
session_start(); // Asegurarse de que la sesión esté iniciada
include ("../../conexion.php");

// Obtener el ID del usuario desde la sesión
$id_usuario = $_SESSION['id_usuario'];

// Consulta para obtener las encuestas asignadas al usuario que están activas (estado = '1')
$query = "
    SELECT e.*
    FROM encuestas e
    INNER JOIN usuarios_encuestas ue ON e.id_encuesta = ue.id_encuesta
    WHERE ue.id_usuario = '$id_usuario' AND e.estado = '1'";
$resultado = $con->query($query);
$tamaño = $resultado->num_rows;

$data = "";

if ($tamaño == 0) {
    $data .= "No hay encuestas disponibles";
} else {

// Diseñamos el encabezado de la tabla
$data = '
    <table class="table table-bordered table-hover table-condensed">
        <thead class="thead-dark">
            <tr>
                <th>Título</th>
                <th>Descripción</th>
                <th>Fecha Final</th>
                <th>Acciones</th>
            </tr>
        </thead>';

while ($row = $resultado->fetch_assoc()) {
    $data .= '
        <tbody>
            <tr>
                <td>' . $row['titulo'] . '</td>
                <td>' . $row["descripcion"] . '</td>
                <td>' . $row["fecha_final"] . '</td>
                <td>
                    <a class="btn btn-primary" href="responder.php?id_encuesta=' . $row['id_encuesta'] . '">Responder</a>
                </td>
            </tr>
        </tbody>';
}

$data .= '</table>';
}

echo $data;
?>
