<?php
session_start();
require_once 'conexion.php';
header('Content-Type: application/json');

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

if (!isset($_GET['cliente_id']) || !is_numeric($_GET['cliente_id'])) {
    echo json_encode(['error' => 'ID de cliente no válido']);
    exit;
}

$clienteId = $_GET['cliente_id'];

$sql = "SELECT 
            c.ContratoID, 
            c.PropiedadID,
            c.canon_mensual,
            p.Direccion,
            i.Nombre AS InquilinoNombre,
            i.Apellido AS InquilinoApellido
        FROM contratos c
        JOIN propiedades p ON c.PropiedadID = p.PropiedadID
        JOIN inquilinos i ON c.InquilinoID = i.InquilinoID
        WHERE 
            c.ClienteID = ? 
            AND c.estado = 'activo'
            AND DATE(c.fecha_fin) >= CURDATE()";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $clienteId);
$stmt->execute();
$result = $stmt->get_result();

$contratos = [];

while ($row = $result->fetch_assoc()) {
    $nombreCompleto = $row['InquilinoNombre'] . ' ' . $row['InquilinoApellido'];
    $contratos[] = [
        'id' => $row['ContratoID'],
        'direccion' => $row['Direccion'],
        'canon_mensual' => $row['canon_mensual'],
        'propiedad_id' => $row['PropiedadID'],
        'inquilino' => $nombreCompleto,
        'descripcion' => "Contrato #{$row['ContratoID']} - {$row['Direccion']} - $nombreCompleto",
    ];
}

echo json_encode($contratos);
