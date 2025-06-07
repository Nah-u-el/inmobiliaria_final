<?php

session_start();

// actualizar_cliente.php

include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del cliente
    $clienteID = $_POST['ClienteID'];
    $nombre = $_POST['Nombre'];
    $apellido = $_POST['Apellido'];
    $direccion = $_POST['Direccion'];
    $dni = $_POST['DNI'];
    $direccionPersonal = $_POST['DireccionPersonal'];
    $telefono = $_POST['Telefono'];
    $mail = $_POST['Mail'];

    // Actualizar datos del cliente
    $sql_cliente = "UPDATE clientes SET Nombre = ?, Apellido = ?, Direccion = ?, DNI = ?, DireccionPersonal = ?, Telefono = ?, Mail = ? WHERE ClienteID = ?";
    $stmt_cliente = mysqli_prepare($conn, $sql_cliente);
    mysqli_stmt_bind_param($stmt_cliente, "sssssssi", $nombre, $apellido, $direccion, $dni, $direccionPersonal, $telefono, $mail, $clienteID);
    mysqli_stmt_execute($stmt_cliente);

    // Actualizar datos del garante 1
    if (isset($_POST['GaranteID1'])) {
        $garanteID1 = $_POST['GaranteID1'];
        $nombreGarante1 = $_POST['NombreGarante1'];
        $apellidoGarante1 = $_POST['ApellidoGarante1'];
        $direccionGarante1 = $_POST['DireccionGarante1'];
        $dniGarante1 = $_POST['DNIGarante1'];
        $direccionPersonalGarante1 = $_POST['DireccionPersonalGarante1'];
        $telefonoGarante1 = $_POST['TelefonoGarante1'];
        $mailGarante1 = $_POST['MailGarante1'];

        $sql_garante1 = "UPDATE garantes SET Nombre = ?, Apellido = ?, Direccion = ?, DNI = ?, DireccionPersonal = ?, Telefono = ?, Mail = ? WHERE GaranteID = ?";
        $stmt_garante1 = mysqli_prepare($conn, $sql_garante1);
        mysqli_stmt_bind_param($stmt_garante1, "sssssssi", $nombreGarante1, $apellidoGarante1, $direccionGarante1, $dniGarante1, $direccionPersonalGarante1, $telefonoGarante1, $mailGarante1, $garanteID1);
        mysqli_stmt_execute($stmt_garante1);
    }

    // Actualizar datos del garante 2
    if (isset($_POST['GaranteID2'])) {
        $garanteID2 = $_POST['GaranteID2'];
        $nombreGarante2 = $_POST['NombreGarante2'];
        $apellidoGarante2 = $_POST['ApellidoGarante2'];
        $direccionGarante2 = $_POST['DireccionGarante2'];
        $dniGarante2 = $_POST['DNIGarante2'];
        $direccionPersonalGarante2 = $_POST['DireccionPersonalGarante2'];
        $telefonoGarante2 = $_POST['TelefonoGarante2'];
        $mailGarante2 = $_POST['MailGarante2'];

        $sql_garante2 = "UPDATE garantes SET Nombre = ?, Apellido = ?, Direccion = ?, DNI = ?, DireccionPersonal = ?, Telefono = ?, Mail = ? WHERE GaranteID = ?";
        $stmt_garante2 = mysqli_prepare($conn, $sql_garante2);
        mysqli_stmt_bind_param($stmt_garante2, "sssssssi", $nombreGarante2, $apellidoGarante2, $direccionGarante2, $dniGarante2, $direccionPersonalGarante2, $telefonoGarante2, $mailGarante2, $garanteID2);
        mysqli_stmt_execute($stmt_garante2);
    }

    // Mensaje de actualizacion exitosa
    
    $_SESSION['mensaje'] = "Los datos se actualizaron correctamente.";

    // Redirigir de vuelta a la página de ver clientes
    
    header("Location: ver_clientes.php?id=$clienteID");
    exit;
}