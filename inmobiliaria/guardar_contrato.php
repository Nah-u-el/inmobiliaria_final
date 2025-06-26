<?php
include 'conexion.php';

$propiedadID = $_POST['PropiedadID'];

// Obtener el ClienteID (dueño) de la propiedad seleccionada
$sql_cliente = "SELECT ClienteID FROM propiedades WHERE PropiedadID = $propiedadID LIMIT 1";
$result_cliente = mysqli_query($conn, $sql_cliente);

if ($row = mysqli_fetch_assoc($result_cliente)) {
    $clienteID = $row['ClienteID'];
} else {
    die("No se encontró el propietario de la propiedad seleccionada.");
}

$inquilinoFecha = $_POST['InquilinoFecha'];
$inquilinoNombre = $_POST['InquilinoNombre'];
$inquilinoApellido = $_POST['InquilinoApellido'];
$inquilinoDNI = $_POST['InquilinoDNI'];
$inquilinoTelefono = $_POST['InquilinoTelefono'];
$inquilinoMail = $_POST['InquilinoMail'];


$garante1Nombre = $_POST['Garante1Nombre'];
$garante1Apellido = $_POST['Garante1Apellido'];
$garante1DNI = $_POST['Garante1DNI'];
$garante1Telefono = $_POST['Garante1Telefono'];
$garante1Mail = $_POST['Garante1Mail'];

$garante2Nombre = $_POST['Garante2Nombre'];
$garante2Apellido = $_POST['Garante2Apellido'];
$garante2DNI = $_POST['Garante2DNI'];
$garante2Telefono = $_POST['Garante2Telfono'];
$garante2Mail = $_POST['Garante2Mail'];

$fechaInicio = $_POST['FechaInicio'];
$fechaFin = $_POST['FechaFin'];

$canon = $_POST['CanonMensual'];
$deposito = $_POST['Deposito'];


// Insertar inquilino
$sql_inquilino = "INSERT INTO inquilinos (Fecha, Nombre, Apellido, DNI, Telefono, Mail, PropiedadID, ClienteID) VALUES ('$inquilinoFecha', '$inquilinoNombre', '$inquilinoApellido', '$inquilinoDNI', '$inquilinoTelefono', '$inquilinoMail', '$propiedadID', '$clienteID')";
mysqli_query($conn, $sql_inquilino);
if (mysqli_error($conn)) {
    die("Error al insertar inquilino: " . mysqli_error($conn));
}
$inquilinoID = mysqli_insert_id($conn);

// Insertar garantes
$sql_g1 = "INSERT INTO garantesinquilinos (Nombre, Apellido, DNI, Telefono, Mail, InquilinoID) VALUES ('$garante1Nombre', '$garante1Apellido', '$garante1DNI', '$garante1Telefono', '$garante1Mail', '$inquilinoID')";
mysqli_query($conn, $sql_g1);
$garante1ID = mysqli_insert_id($conn);

$sql_g2 = "INSERT INTO garantesinquilinos (Nombre, Apellido, DNI, Telefono, Mail, InquilinoID) VALUES ('$garante2Nombre', '$garante2Apellido', '$garante2DNI', '$garante2Telefono', '$garante2Mail')";
mysqli_query($conn, $sql_g2);
$garante2ID = mysqli_insert_id($conn);

// (Opcional) Obtener ClienteID desde propiedad
$sql_cliente = "SELECT ClienteID FROM propiedades WHERE PropiedadID = $propiedadID LIMIT 1";
$result_cliente = mysqli_query($conn, $sql_cliente);
$clienteID = 0;
if ($row = mysqli_fetch_assoc($result_cliente)) {
    $clienteID = $row['ClienteID'];
}


// Insertar contrato (solo UNA VEZ)
$sql_contrato = "INSERT INTO contratos (
    ClienteID, InquilinoID, PropiedadID, GaranteInquilinoID,
    fecha_inicio, fecha_fin, canon_mensual, deposito
) VALUES (
    $clienteID, $inquilinoID, $propiedadID, $garante1ID,
    '$fechaInicio', '$fechaFin', $canon, '$deposito'
)";
mysqli_query($conn, $sql_contrato) or die("Error SQL Contrato: " . mysqli_error($conn));

$contratoID = mysqli_insert_id($conn);

echo "<script>window.location.href='generar_pdf.php?contrato_id=$contratoID';</script>";


if (mysqli_affected_rows($conn) > 0) {
    echo "<script>alert('Contrato guardado con éxito'); window.location.href='propiedades.php';</script>";
} else {
    echo "<script>alert('Error al guardar el contrato'); history.back();</script>";
}

mysqli_close($conn);



?>
