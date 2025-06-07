<?php
require_once('../libs/tcpdf/tcpdf.php');
include 'conexion.php';

$contratoID = $_GET['contrato_id'] ?? null;
if (!$contratoID) {
    die("Contrato no especificado.");
}

// Consulta del contrato y relaciones
$sql = "SELECT 
            c.*, 
            p.Direccion, p.Ciudad,
            i.Nombre AS InquilinoNombre, i.DNI AS InquilinoDNI, i.Telefono, i.Mail,
            g1.Nombre AS Garante1Nombre,
            g2.Nombre AS Garante2Nombre
        FROM contratos c
        JOIN propiedades p ON p.PropiedadID = c.PropiedadID
        JOIN inquilinos i ON i.InquilinoID = c.InquilinoID
        LEFT JOIN garantesinquilinos g1 ON g1.GaranteInquilinoID = c.GaranteInquilinoID
        LEFT JOIN garantesinquilinos g2 ON g2.GaranteInquilinoID = c.GaranteInquilino2ID
        WHERE c.ContratoID = $contratoID";

$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    die("Contrato no encontrado.");
}

$row = mysqli_fetch_assoc($result);

// Crear el PDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Inmobiliaria');
$pdf->SetTitle('Contrato de Alquiler');
$pdf->SetMargins(20, 20, 20);
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 11);

// Contenido HTML del contrato
$html = "
<h2 style='text-align:center;'>Contrato de Alquiler</h2>
<hr>
<p><strong>Dirección del Inmueble:</strong> {$row['Direccion']}, {$row['Ciudad']}</p>

<h4>Datos del Inquilino</h4>
<ul>
    <li><strong>Nombre:</strong> {$row['InquilinoNombre']}</li>
    <li><strong>DNI:</strong> {$row['InquilinoDNI']}</li>
    <li><strong>Teléfono:</strong> {$row['Telefono']}</li>
    <li><strong>Email:</strong> {$row['Mail']}</li>
</ul>

<h4>Datos del Contrato</h4>
<ul>
    <li><strong>Canon mensual:</strong> \$ {$row['canon_mensual']}</li>
    <li><strong>Depósito:</strong> \$ {$row['deposito']}</li>
    <li><strong>Fecha de inicio:</strong> {$row['fecha_inicio']}</li>
    <li><strong>Fecha de fin:</strong> {$row['fecha_fin']}</li>
</ul>

<h4>Garantes</h4>
<ul>
    <li><strong>Garante 1:</strong> " . ($row['Garante1Nombre'] ?? 'No especificado') . "</li>
    <li><strong>Garante 2:</strong> " . ($row['Garante2Nombre'] ?? 'No especificado') . "</li>
</ul>

<br><br><br>
<p>__________________________<br>Firma del Inquilino</p>

<p style='text-align:right;'>Fecha de emisión: " . date('d/m/Y') . "</p>
";

// Agregar contenido al PDF
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('contrato_' . $contratoID . '.pdf', 'I');
