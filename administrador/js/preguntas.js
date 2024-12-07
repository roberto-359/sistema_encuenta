$(document).ready(function() {
    // Mostrar encuestas al cargar la página
    var id_encuesta = $("#id_encuesta").val();
    mostrarPreguntas(id_encuesta);

    // Cargar modal de Bootstrap para agregar nueva pregunta
    $("#boton_agregar").click(function() {
        $("#modal_agregar").modal("show");
    });
});

// Mostrar preguntas
function mostrarPreguntas(id_encuesta) {
    $.post("ajax_pregunta/mostrarPreguntas.php", { id_encuesta: id_encuesta }, function(data, status) {
        $("#tabla_preguntas").html(data);
    });
}

// Agregar nueva pregunta
function agregarPregunta() {
    var id_encuesta = $("#id_encuesta").val();
    var titulo = $("#titulo").val();
    var id_tipo_pregunta = $("#tipo_pregunta").val(); // Asegúrate de que el ID del select sea "tipo_pregunta"

    // Debugging: Verifica los valores antes de enviarlos
    console.log("id_encuesta:", id_encuesta);
    console.log("titulo:", titulo);
    console.log("id_tipo_pregunta:", id_tipo_pregunta);

    if (titulo && id_tipo_pregunta) {
        $.post("ajax_pregunta/agregarPregunta.php", {
            id_encuesta: id_encuesta,
            titulo: titulo,
            id_tipo_pregunta: id_tipo_pregunta
        }, function(data, status) {
            $("#modal_agregar").modal("hide");
            mostrarPreguntas(id_encuesta);
            $("#titulo").val("");
            $("#tipo_pregunta").val(""); // Limpia el campo select después de agregar
        });
    } else {
        alert("Por favor, complete todos los campos.");
    }
}

// Eliminar pregunta
function eliminarPregunta(id_pregunta) {
    if (confirm("¿Estás seguro de eliminar la pregunta?")) {
        $.post("ajax_pregunta/eliminarPregunta.php", { id_pregunta: id_pregunta }, function(data, status) {
            var id_encuesta = $("#id_encuesta").val();
            mostrarPreguntas(id_encuesta);
        });
    }
}

// Obtener detalles de la pregunta
function obtenerDetallesPregunta(id_pregunta) {
    $("#hidden_id_pregunta").val(id_pregunta);

    $.post("ajax_pregunta/mostrarDetallesPregunta.php", { id_pregunta: id_pregunta }, function(data, status) {
        var pregunta = JSON.parse(data);
        $("#modificar_titulo").val(pregunta.titulo);
        $("#tipo_pregunta").val(pregunta.id_tipo_pregunta); // Asegúrate de que este campo esté presente en el JSON
    });

    $("#modal_modificar").modal("show");
}

// Modificar detalles de la pregunta
function modificarDetallesPregunta() {
    var titulo = $("#modificar_titulo").val();
    var id_pregunta = $("#hidden_id_pregunta").val();
    var id_encuesta = $("#id_encuesta").val();

    if (titulo) {
        $.post("ajax_pregunta/modificarDetallesPregunta.php", {
            id_pregunta: id_pregunta,
            titulo: titulo
        }, function(data, status) {
            $("#modal_modificar").modal("hide");
            mostrarPreguntas(id_encuesta);
        });
    } else {
        alert("Por favor, complete todos los campos.");
    }
}
