<?php
require('FPDF/fpdf.php');
include '../conexion.php';

class PDF extends FPDF
{
    protected $colors = [
        'lightblue' => [173, 216, 230],
        'lightgreen' => [144, 238, 144],
        'lightcoral' => [240, 128, 128],
        'lightsalmon' => [255, 160, 122],
        'lightpink' => [255, 182, 193],
    ];

    function Header()
    {
        // Logo
        $this->Image('../imagenes/Imagen1.png', 10, 6, 180);

        // Título
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 30, utf8_decode('Reporte de Encuesta'), 0, 1, 'C');
        $this->Ln(0.1);
    }

    function Footer()
    {
        
        $this->SetY(-38);
        // Imagen del pie de página
        $this->Image('../imagenes/fooder.png', 1, $this->GetY() - 5, 200);
        // Fuente para el número de página
        $this->SetFont('Arial', 'I', 8);
        // Número de página
        $this->SetY(-10);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    function BarChart($labels, $data, $totalVotes, $x, $y)
    {
        // Configuración de la gráfica
        $maxWidth = 50; // Ancho máximo para las barras (ajustado)
        $maxHeight = 30; // Altura máxima para la gráfica
        $spacing = 5; // Espacio entre barras

        $barWidth = ($maxWidth - ($spacing * (count($data) - 1))) / count($data); // Ancho de cada barra

        // Normalizar los datos para que se ajusten al máximo de altura
        $maxDataValue = max($data);
        if ($maxDataValue > 0) {
            $scaleFactor = $maxHeight / $maxDataValue;
        } else {
            $scaleFactor = 1;
        }

        // Dibujar las barras
        for ($i = 0; $i < count($data); $i++) {
            $barHeight = $data[$i] * $scaleFactor;
            $this->SetFillColor(...$this->colors[array_keys($this->colors)[$i % count($this->colors)]]);
            $this->Rect($x + ($barWidth + $spacing) * $i, $y - $barHeight, $barWidth, $barHeight, 'F');
            $this->SetXY($x + ($barWidth + $spacing) * $i, $y + 2);
            $this->SetFont('Arial', '', 8);
            $this->Cell($barWidth, 10, utf8_decode($labels[$i]), 0, 0, 'C');

            // Mostrar el valor y el porcentaje
            $this->SetXY($x + ($barWidth + $spacing) * $i, $y - $barHeight - 10);
            $percentage = ($totalVotes > 0) ? round(($data[$i] / $totalVotes) * 100, 2) . '%' : '0%';
            $this->Cell($barWidth, 10, $data[$i] . ' (' . $percentage . ')', 0, 0, 'C');
        }
    }

    function CheckPageBreak($height)
    {
        // Si la altura requerida para el contenido actual más la gráfica excede la altura restante en la página, añadir una nueva página
        if ($this->GetY() + $height > $this->PageBreakTrigger) {
            $this->AddPage();
        }
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);

$pdf->Cell(0, 6, utf8_decode('Encuesta creada por:'), 0, 1, 'C');

$conexion = new mysqli('localhost', 'root', '', 'sistema_encuestas');

if ($conexion->connect_error) {
    die('Error de conexión: ' . $conexion->connect_error);
}

$conexion->set_charset("utf8");

// Obtener datos del usuario con id_tipo_usuario = 1
$query_usuario = "SELECT * FROM usuarios WHERE id_tipo_usuario = 1";
$resultado_usuario = $conexion->query($query_usuario);

if ($resultado_usuario->num_rows > 0) {
    while ($fila = $resultado_usuario->fetch_assoc()) {
        $pdf->Cell(0, 10, "ID: " . $fila['id_usuario'] . " - Nombre: " . utf8_decode($fila['nombres']) . " " . utf8_decode($fila['apellidos']), 0, 1);
    }
} else {
    $pdf->Cell(0, 10, utf8_decode('No hay datos disponibles'), 0, 1);
}

// Obtener datos de la encuesta
$query_encuesta = "SELECT * FROM encuestas WHERE id_encuesta = 1"; // Ajustar el id_encuesta según sea necesario
$resultado_encuesta = $conexion->query($query_encuesta);

if ($resultado_encuesta->num_rows > 0) {
    $encuesta = $resultado_encuesta->fetch_assoc();
    $pdf->Cell(0, 10, utf8_decode('Título: ') . utf8_decode($encuesta['titulo']), 0, 1);
    $pdf->Cell(0, 10, utf8_decode('Descripción: ') . utf8_decode($encuesta['descripcion']), 0, 1);
    $pdf->Cell(0, 10, utf8_decode('Fecha de Inicio: ') . $encuesta['fecha_inicio'], 0, 1);
    $pdf->Cell(0, 10, utf8_decode('Fecha Final: ') . $encuesta['fecha_final'], 0, 1);
}

$query_preguntas = "SELECT DISTINCT id_pregunta, titulo FROM preguntas WHERE id_encuesta = 1"; // Ajustar el id_encuesta según sea necesario
$resultados_preguntas = $conexion->query($query_preguntas);

if ($resultados_preguntas->num_rows > 0) {
    while ($row_pregunta = $resultados_preguntas->fetch_assoc()) {
        $id_pregunta = $row_pregunta['id_pregunta'];
        $pdf->Ln(10);
        $pdf->Cell(0, 10, utf8_decode('Pregunta: ') . utf8_decode($row_pregunta['titulo']), 0, 1);

        $labels = [];
        $data = [];
        $totalVotes = 0;

        // Obtener todas las opciones posibles para la pregunta
        $query_opciones = "SELECT valor FROM opciones WHERE id_pregunta = '$id_pregunta'";
        $resultados_opciones = $conexion->query($query_opciones);

        if ($resultados_opciones->num_rows > 0) {
            $opciones = [];
            while ($row_opcion = $resultados_opciones->fetch_assoc()) {
                $opciones[$row_opcion['valor']] = 0;
            }

            // Obtener los resultados de la encuesta para la pregunta
            $query_resultados = "
                SELECT preguntas.id_pregunta, preguntas.titulo, COUNT(preguntas.titulo) as count, opciones.valor 
                FROM opciones 
                INNER JOIN preguntas ON opciones.id_pregunta = preguntas.id_pregunta 
                INNER JOIN resultados ON opciones.id_opcion = resultados.id_opcion 
                WHERE preguntas.id_pregunta = '$id_pregunta' 
                GROUP BY opciones.valor 
                ORDER BY preguntas.id_pregunta";
            
            $resultados = $conexion->query($query_resultados);

            if ($resultados->num_rows > 0) {
                while ($row = $resultados->fetch_assoc()) {
                    $opciones[$row['valor']] = $row['count'];
                    $totalVotes += $row['count'];
                }
            }

            // Agregar las opciones y los valores (incluyendo los que no recibieron votos)
            foreach ($opciones as $opcion => $count) {
                $labels[] = utf8_decode($opcion);
                $data[] = $count;
            }

            // Graficar los resultados para la pregunta actual
            $pdf->Ln(5); // Añadir un poco de espacio antes de la gráfica
            $height = 40; // Altura estimada para la gráfica y el texto
            $pdf->CheckPageBreak($height);

            $y = $pdf->GetY() + 30; // Obtener la coordenada Y actual y añadir espacio (3 cm = 30 mm)

            $pdf->BarChart($labels, $data, $totalVotes, 10, $y);
            $pdf->Ln(40); // Añadir espacio después de cada gráfica
        }
    }
} else {
    $pdf->Cell(0, 10, utf8_decode('No hay preguntas disponibles'), 0, 1);
}

$pdf->Output();
?>
