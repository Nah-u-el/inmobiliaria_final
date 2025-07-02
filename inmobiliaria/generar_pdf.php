<?php
require '../vendor/autoload.php';
use Dompdf\Dompdf;

include 'conexion.php';

$contratoID = isset($_GET['contrato_id']) ? intval($_GET['contrato_id']) : null;
$propiedadID = isset($_GET['id']) ? intval($_GET['id']) : null;

if (!$contratoID || !$propiedadID) {
    die("Datos incompletos.");
}

// Consulta
$sql = "
    SELECT p.Direccion, p.Ciudad, cl.Nombre AS Propietario,
           i.Nombre AS Inquilino, i.DNI,
           g1.Nombre AS Garante1, g2.Nombre AS Garante2,
           c.*
    FROM contratos c
    JOIN propiedades p ON c.PropiedadID = p.PropiedadID
    JOIN clientes cl ON p.ClienteID = cl.ClienteID
    JOIN inquilinos i ON c.InquilinoID = i.InquilinoID
    LEFT JOIN garantesinquilinos g1 ON c.GaranteinquilinoID = g1.GaranteInquilinoID
    LEFT JOIN garantesinquilinos g2 ON c.GaranteInquilinoID = g2.GaranteInquilinoID
    WHERE c.ContratoID = $contratoID AND c.PropiedadID = $propiedadID
";

$result = mysqli_query($conn, $sql);
if (!$result || mysqli_num_rows($result) === 0) {
    die("Contrato no encontrado.");
}

$data = mysqli_fetch_assoc($result);

// HTML con estilo y firma
$html = "
    <html>
    <head>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 10px;
                color: #333;
            }
            h1, h3 {
                color: rgba(233, 128, 0, 0.92);
                text-align: center;
            }
            .section {
                margin-bottom: 20px;
            }
            .firma {
                margin-top: 60px;
                text-align: center;
            }
            .firma div {
                display: inline-block;
                width: 40%;
                margin: 20px;
            }
            .linea {
                border-top: 1px solid #000;
                margin-top: 25px;
                width: 80%;
                margin-left: auto;
                margin-right: auto;
            }
            .logo {
                text-align: center;
                margin-bottom: 20px;
            }
            .logo img {
                width: 120px;
            }
        </style>
    </head>
    <body>
        <div class='section'>
            <h3>Propiedad</h3>
            <p><strong>Dirección:</strong> {$data['Direccion']}, {$data['Ciudad']}</p>
            <p><strong>Propietario:</strong> {$data['Propietario']}</p>
        </div>

        <div class='section'>
            <h3>Inquilino</h3>
            <p><strong>Nombre:</strong> {$data['Inquilino']}</p>
            <p><strong>DNI:</strong> {$data['DNI']}</p>
        </div>

        <div class='section'>
            <h3>Contrato</h3>
            <p><strong>Desde:</strong> {$data['fecha_inicio']}</p>
            <p><strong>Hasta:</strong> {$data['fecha_fin']}</p>
            <p><strong>Monto mensual:</strong> \$ {$data['canon_mensual']}</p>
            <p><strong>Depósito:</strong> \$ {$data['deposito']}</p>
        </div>

        <div class='section'>
            <h3>Garantes</h3>
            <p><strong>Garante 1:</strong> {$data['Garante1']}</p>
            <p><strong>Garante 2:</strong> {$data['Garante2']}</p>
        </div>

        <div class='firma'>
            <div>
                <div class='linea'></div>
                <p>Propietario</p>
            </div>
            <div>
                <div class='linea'></div>
                <p>Inquilino</p>
            </div>
        </div>

        <div class='firma'>
            <div>
                <div class='linea'></div>
                <p>Garante 1</p>
            </div>
            <div>
                <div class='linea'></div>
                <p>Garante 2</p>
            </div>
        </div>
    </body>
    </html>
";

// Crear PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Mostrar en el navegador
$dompdf->stream("contrato_{$contratoID}.pdf", ["Attachment" => false]);

mysqli_close($conn);
?>
