<?php
session_start();
include_once 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger datos del formulario
    $fecha = $_POST['Fecha'];
    $barrio = $_POST['Barrio'];
    $ciudad = $_POST['Ciudad'];
    $direccion = $_POST['Direccion'];
    $nro = $_POST['Nro'];
    $dominio = $_POST['Dominio'];
    $nro_partida = $_POST['NroPartida'];
    $estado = $_POST['Estado'];
    $propietarioID = $_POST['ClienteID']; // Nuevo campo
    
    // Validar datos (agrega tus validaciones aquí)
    
    // Insertar propiedad
    $sql = "INSERT INTO propiedades (Fecha, Barrio, Ciudad, Direccion, Nro, Dominio, NroPartida, Estado, ClienteID) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssssssssi", $fecha, $barrio, $ciudad, $direccion, $nro, $dominio, $nro_partida, $estado, $propietarioID);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['mensaje'] = "Propiedad agregada correctamente";
    } else {
        $_SESSION['mensaje'] = "Error al agregar la propiedad: " . mysqli_error($conn);
    }
    
    mysqli_stmt_close($stmt);
    header("Location: propiedades.php");
    exit;
}
?>