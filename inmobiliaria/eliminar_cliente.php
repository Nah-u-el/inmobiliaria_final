<?php
session_start();

// 1. Verificación de sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login/index.php");
    exit;
}

// Incluir la conexión a la base de datos
include_once 'conexion.php';

// Verificar que se haya enviado un ID de cliente válido
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $cliente_id = mysqli_real_escape_string($conn, $_GET['id']);

    // Prepara la consulta SQL para actualizar el estado del cliente
    $sql = "UPDATE clientes SET estado = 'inactivo' WHERE ClienteID = '$cliente_id'";

    if (mysqli_query($conn, $sql)) {
        // Éxito: redirigir a la página de clientes con un mensaje
        $_SESSION['mensaje'] = "Cliente marcado como inactivo correctamente.";
    } else {
        // Error: redirigir con un mensaje de error
        $_SESSION['mensaje'] = "Error al marcar el cliente como inactivo: " . mysqli_error($conn);
    }
} else {
    // No se proporcionó un ID de cliente
    $_SESSION['mensaje'] = "ID de cliente no proporcionado.";
}

// Redirigir de vuelta a la página de clientes
header("location: clientes.php");
exit;

// Cierra la conexión a la base de datos
if (isset($conn) && $conn) {
    mysqli_close($conn);
}
?>