<?php
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contrato_id = $_POST['contrato_id'];

    $stmt = $conn->prepare("INSERT IGNORE INTO notificaciones_leidas (ContratoID) VALUES (?)");
    $stmt->bind_param("i", $contrato_id);
    $stmt->execute();

    echo json_encode(["success" => true]);
}
