<?php
session_start();
require_once '../vendor/autoload.php';
require_once 'conexion.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Verificar sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login/index.php");
    exit;
}

// Obtener datos del formulario
$datos = [
    'descuento' => is_numeric($_GET['descuento'] ?? null) ? (float)$_GET['descuento'] : 0,
    'monto_luz' => is_numeric($_GET['monto_luz'] ?? null) ? (float)$_GET['monto_luz'] : 0,
    'numero_recibo' => $_GET['numero_recibo'] ?? '',
    'fecha' => $_GET['fecha'] ?? date('Y-m-d'),
    'nombre_cliente' => $_GET['nombre_cliente'] ?? '',
    'inquilino_nombre' => $_GET['inquilino_nombre'] ?? '',
    'direccion_propiedad' => $_GET['direccion_propiedad'] ?? '',
    'concepto' => $_GET['concepto'] ?? 'Alquiler mensual',
    'monto' => $_GET['monto'] ?? 0,
    'periodo' => $_GET['periodo'] ?? '',
    'observaciones' => $_GET['observaciones'] ?? '',
    'generar_duplicado' => isset($_GET['generar_duplicado']),
    'contrato_id' => $_GET['contrato_id'] ?? null
];

// Si hay un contrato, obtener más detalles
if ($datos['contrato_id']) {
    $sql = "SELECT c.*, p.Direccion 
            FROM contratos c
            JOIN propiedades p ON c.PropiedadID = p.PropiedadID
            WHERE c.ContratoID = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $datos['contrato_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $contrato = mysqli_fetch_assoc($result);

    if ($contrato) {
        $datos['detalles_contrato'] = [
            'fecha_inicio' => $contrato['fecha_inicio'],
            'fecha_fin' => $contrato['fecha_fin'],
            'deposito' => $contrato['deposito'],
            'canon_mensual' => $contrato['canon_mensual']
        ];
    }
}

// Configurar DOMPDF
$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'Helvetica');
$dompdf = new Dompdf($options);

// HTML del recibo
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 12px; }
        .recibo { margin-bottom: 20px; }
        .header { text-align: center; margin-bottom: 15px; }
        .logo { height: 60px; }
        .title { font-size: 16px; font-weight: bold; margin: 5px 0; }
        .info { margin-bottom: 10px; }
        .info-label { font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 5px; border: 1px solid #ddd; }
        .total { font-weight: bold; }
        .footer { font-size: 10px; text-align: center; margin-top: 10px; }
        .observaciones { margin-top: 10px; font-size: 10px; }
        .separador { border-top: 1px dashed #000; margin: 15px 0; text-align: center; }
        .detalles-contrato { font-size: 10px; margin-top: 10px; }
    </style>
</head>
<body>';

// Función para generar un recibo individual
function generarReciboHTML($datos, $esDuplicado = false) {
    $monto = (float)$datos['monto'];
    $descuento = (float)$datos['descuento'];
    $monto_luz = (float)$datos['monto_luz'];
    $total_final = max(0, $monto + $monto_luz - $descuento);


    $html = '
    <div class="recibo">
        <div class="header">
            <div class="title">SC INMOBILIARIA SAN CRISTOBAL</div>
           
            <div>Tel: 381-1234567 - Email: info@scinmobiliaria.com</div>
        </div>
        
        <div style="text-align: center; font-weight: bold; margin: 10px 0; border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 5px;">
            RECIBO '.($esDuplicado ? '(DUPLICADO)' : '').'
        </div>

        <div style="margin: 10px 0; font-size: 13px;"><strong>Recibí de:</strong> '.htmlspecialchars($datos['inquilino_nombre']).'</div>
        
        <div class="info">
            <div><span class="info-label">N°:</span> '.htmlspecialchars($datos['numero_recibo']).'</div>
            <div><span class="info-label">Fecha:</span> '.date('d/m/Y', strtotime($datos['fecha'])).'</div>
            <div><span class="info-label">Cliente:</span> '.htmlspecialchars($datos['nombre_cliente']).'</div>
            <div><span class="info-label">Propiedad:</span> '.htmlspecialchars($datos['direccion_propiedad']).'</div>
            <div><span class="info-label">Período:</span> '.htmlspecialchars($datos['periodo']).'</div>
            '.($datos['contrato_id'] ? '<div><span class="info-label">Contrato:</span> #'.htmlspecialchars($datos['contrato_id']).'</div>' : '').'
        </div>
        
        <table>
            <tr>
                <th>Concepto</th>
                <th style="text-align: right;">Importe</th>
            </tr>
            <tr>
                <td>'.htmlspecialchars($datos['concepto']).'</td>
                <td style="text-align: right;">$'.number_format($monto, 2, ',', '.').'</td>
                </tr>';
            if ($datos['monto_luz'] > 0) {
             $html .= '
                <tr>
                <td>Luz</td>
            <td style="text-align: right;">$'.number_format($datos['monto_luz'], 2, ',', '.').'</td>
            </tr>';
            }

            
            

    if ($descuento > 0) {
        $html .= '
            <tr>
                <td>Descuento</td>
                <td style="text-align: right;">-$'.number_format($descuento, 2, ',', '.').'</td>
            </tr>';
    }

    $html .= '
            <tr class="total">
                <td>TOTAL</td>
                <td style="text-align: right;">$'.number_format($total_final, 2, ',', '.').'</td>
            </tr>
        </table>

        <div style="font-size: 11px; margin-top: 5px;">
            <strong>Total en letras:</strong> '.numeroALetras($total_final).' pesos.
        </div>';

    if (isset($datos['detalles_contrato'])) {
        $html .= '
        <div class="detalles-contrato">
            <div><strong>Detalles del contrato:</strong></div>
            <div>Inicio: '.date('d/m/Y', strtotime($datos['detalles_contrato']['fecha_inicio'])).'</div>
            <div>Fin: '.date('d/m/Y', strtotime($datos['detalles_contrato']['fecha_fin'])).'</div>
            
        </div>';
    }

    if (!empty($datos['observaciones'])) {
        $html .= '
        <div class="observaciones">
            <div><strong>Observaciones:</strong></div>
            <div>'.nl2br(htmlspecialchars($datos['observaciones'])).'</div>
        </div>';
    }

    $html .= '
        
        
        <div class="footer">
            Recibo válido como comprobante de pago<br>
            SC Inmobiliaria San Cristobal 
        </div>
    </div>';

    return $html;
}


function numeroALetras($numero) {
    $f = new NumberFormatter("es", NumberFormatter::SPELLOUT);
    return ucfirst($f->format($numero));
}


// Generar HTML
$html .= generarReciboHTML($datos);

if ($datos['generar_duplicado']) {
    $html .= '<div class="separador">CORTAR POR AQUÍ</div>';
    $html .= generarReciboHTML($datos, true);
}

$html .= '</body></html>';

// Renderizar PDF
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('recibo_'.$datos['numero_recibo'].'.pdf', ['Attachment' => false]);
exit;
