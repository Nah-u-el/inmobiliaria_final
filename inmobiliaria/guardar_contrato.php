<?php
include 'conexion.php';

$propiedadID = $_POST['PropiedadID'];
$inquilinoNombre = $_POST['InquilinoNombre'];
$inquilinoDNI = $_POST['InquilinoDNI'];
$inquilinoTelefono = $_POST['InquilinoTelefono'];
$inquilinoMail = $_POST['InquilinoMail'];

$garante1Nombre = $_POST['Garante1Nombre'];
$garante1DNI = $_POST['Garante1DNI'];

$garante2Nombre = $_POST['Garante2Nombre'];
$garante2DNI = $_POST['Garante2DNI'];

$fechaInicio = $_POST['FechaInicio'];
$fechaFin = $_POST['FechaFin'];

$canon = $_POST['CanonMensual'];
$deposito = $_POST['Deposito'];


// Insertar inquilino
$sql_inquilino = "INSERT INTO inquilinos (Nombre, DNI, Telefono, Mail) VALUES ('$inquilinoNombre', '$inquilinoDNI', '$inquilinoTelefono', '$inquilinoMail')";
mysqli_query($conn, $sql_inquilino);
if (mysqli_error($conn)) {
    die("Error al insertar inquilino: " . mysqli_error($conn));
}
$inquilinoID = mysqli_insert_id($conn);

// Insertar garantes
$sql_g1 = "INSERT INTO garantesinquilinos (Nombre, DNI) VALUES ('$garante1Nombre', '$garante1DNI')";
mysqli_query($conn, $sql_g1);
$garante1ID = mysqli_insert_id($conn);

$sql_g2 = "INSERT INTO garantesinquilinos (Nombre, DNI) VALUES ('$garante2Nombre', '$garante2DNI')";
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
