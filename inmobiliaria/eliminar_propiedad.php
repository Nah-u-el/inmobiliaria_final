<?php
session_start();
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['propiedad_id'], $_POST['propietario_id'])) {
    $propiedadID = intval($_POST['propiedad_id']);
    $propietarioID = intval($_POST['propietario_id']);

    $stmt = $conn->prepare("DELETE FROM propiedades WHERE PropiedadID = ?");
    $stmt->bind_param("i", $propiedadID);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Propiedad eliminada correctamente.";
    } else {
        $_SESSION['mensaje_error'] = "Error al eliminar la propiedad.";
    }

    $stmt->close();
    $conn->close();

    header("Location: propietarios_ver_propiedades.php?id=" . $propietarioID);
    exit;
}

header("Location: propietarios.php");
exit;
