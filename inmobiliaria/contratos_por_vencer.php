<?php
require_once 'conexion.php';

$sql = "
    SELECT c.ContratoID AS id, i.Nombre AS inquilino, p.Direccion AS direccion, c.fecha_fin
    FROM contratos c
    JOIN inquilinos i ON c.InquilinoID = i.InquilinoID
    JOIN propiedades p ON c.PropiedadID = p.PropiedadID
    WHERE c.estado = 'activo'
      AND c.fecha_fin BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
      AND c.ContratoID NOT IN (
          SELECT ContratoID FROM notificaciones_leidas
      )
    ORDER BY c.fecha_fin ASC
";

$result = $conn->query($sql);
$contratos = [];

while ($row = $result->fetch_assoc()) {
    $fecha_fin = new DateTime($row['fecha_fin']);
    $hoy = new DateTime();
    $dias_restantes = $hoy->diff($fecha_fin)->days;

    $urgencia = $dias_restantes <= 7 ? 'alta' : 'media';

    $contratos[] = [
        'id' => $row['id'],
        'inquilino' => $row['inquilino'],
        'direccion' => $row['direccion'],
        'fecha_fin' => $fecha_fin->format('d/m/Y'),
        'urgencia' => $urgencia
    ];
}

header('Content-Type: application/json');
echo json_encode($contratos);
