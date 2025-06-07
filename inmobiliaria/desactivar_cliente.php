<?php
include 'conexion.php';

// Verificar si se recibió el ID del cliente
if (isset($_GET['id'])) {
    $clienteID = $_GET['id'];

// Consulta SQL para desactivar el cliente
    $sql = "UPDATE clientes SET estado = 'inactivo' WHERE ClienteID = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $clienteID);
    
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Cliente BORRADO correctamente'); window.location.href='clientes.php';</script>";
    } else {
        echo "<script>alert('ERROR al desactivar cliente'); window.location.href='clientes.php';</script>";
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
} else {
    echo "<script>alert('CLIENTE NO ENCONTRADO'); window.location.href='clientes.php';</script>";
}
?>
