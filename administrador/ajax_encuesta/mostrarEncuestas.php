<?php
// Incluimos el archivo de conexión a base de datos
include ("../../conexion.php");

// Diseñamos el encabezado de la tabla
$data = '
    <table class="table table-bordered table-hover table-condensed">
        <thead style="background-color: #1B396A;color:white" >
            <tr>
                <th>ID encuesta</th>
                <th>Título</th>
                <th width="100">Descripción</th>
                <th>Estado</th>
                <th>Fecha Inicio</th>
                <th>Fecha Final</th>
                <th>Acciones</th>
            </tr>
        </thead>';

$query = "SELECT * FROM encuestas ORDER BY id_encuesta DESC";
$resultado = $con->query($query);

while ($row = $resultado->fetch_assoc()) {
    $data .= '
        <tbody>
            <tr>
                <td>' . $row["id_encuesta"] . '</td>
                <td><a href="mostrar_preguntas.php?id_encuesta=' . $row['id_encuesta'] . '">' . $row['titulo'] . '</a></td>
                <td width="100">' . mb_strimwidth($row["descripcion"], 0, 30, "...") . '</td>
                <td>' . $row["estado"] . '</td>
                <td>' . $row["fecha_inicio"] . '</td>
                <td>' . $row["fecha_final"] . '</td>
                <td>
                    <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      Acciones
                    </button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                        <button onclick="obtenerDetallesEncuesta(' . $row['id_encuesta'] . ')" class="dropdown-item btn btn-warning">Modificar</button>
                        <button onclick="eliminarEncuesta(' . $row['id_encuesta'] . ')" class="dropdown-item btn btn-danger">Eliminar</button>
                        <button onclick="mostrarModalPublicar(' . $row['id_encuesta'] . ')" class="dropdown-item btn btn-primary">Publicar</button>
                        <button onclick="finalizarEncuesta(' . $row['id_encuesta'] . ')" class="dropdown-item btn btn-secondary">Finalizar</button>
                        <a class="dropdown-item btn btn-secondary" href="vista_previa.php?id_encuesta=' . $row['id_encuesta'] . '">Vista Previa</a>
                        <a class="dropdown-item btn btn-secondary" href="resultados.php?id_encuesta=' . $row['id_encuesta'] . '">Resultados</a>
                    </div>
                </td>
            </tr>
        </tbody>';
}

$data .= '</table>';

echo $data;

// Modal para publicar encuesta
echo '
<div id="modalPublicar" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Publicar Encuesta</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formPublicar" method="POST" action="ajax_encuesta/publicarEncuesta.php">
                    <input type="hidden" name="id_encuesta" id="id_encuesta">
                    <div class="form-group">
                        <label for="tipo_usuario">Selecciona la carrera deseada para publicar:</label>
                        <select name="tipo_usuario" id="tipo_usuario" class="form-control">
                             <option value="2">Ing. Gestión Empresarial</option>
                             <option value="3">Ing. Logística</option>
                             <option value="4">Ing. Química</option>
                             <option value="5">Ing. Materiales</option>
                             <option value="6">Ing. Electromecánica</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Enviar</button>
                </form>
            </div>
        </div>
    </div>
</div>';
?>

<script>
function mostrarModalPublicar(id_encuesta) {
    $('#id_encuesta').val(id_encuesta);
    $('#modalPublicar').modal('show');
}
</script>
