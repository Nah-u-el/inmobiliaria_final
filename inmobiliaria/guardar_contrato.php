<?php
include 'conexion.php';

$propiedadID = $_POST['PropiedadID'];

// Verificar si ya existe un contrato activo para esta propiedad
$sql_verificar = "SELECT COUNT(*) as total FROM contratos WHERE PropiedadID = $propiedadID AND estado = 'activo'";
$result_verificar = mysqli_query($conn, $sql_verificar);
$row_verificar = mysqli_fetch_assoc($result_verificar);

if ($row_verificar['total'] > 0) {
    echo "<script>alert('Ya existe un contrato activo para esta propiedad.'); window.location.href='propiedades.php';</script>";
    exit;
}

// Obtener el ClienteID (dueño) de la propiedad
$sql_cliente = "SELECT ClienteID FROM propiedades WHERE PropiedadID = $propiedadID LIMIT 1";
$result_cliente = mysqli_query($conn, $sql_cliente);
if ($row = mysqli_fetch_assoc($result_cliente)) {
    $clienteID = $row['ClienteID'];
} else {
    die("No se encontró el propietario de la propiedad seleccionada.");
}

// Datos del inquilino
$inquilinoFecha     = date('Y-m-d');
$inquilinoNombre    = $_POST['InquilinoNombre'];
$inquilinoApellido  = $_POST['InquilinoApellido'];
$inquilinoDNI       = $_POST['InquilinoDNI'];
$inquilinoTelefono  = $_POST['InquilinoTelefono'];
$inquilinoMail      = $_POST['InquilinoMail'];

// Insertar inquilino
$sql_inquilino = "INSERT INTO inquilinos (Fecha, Nombre, Apellido, DNI, Telefono, Mail, PropiedadID, ClienteID)
                  VALUES ('$inquilinoFecha', '$inquilinoNombre', '$inquilinoApellido', '$inquilinoDNI', '$inquilinoTelefono', '$inquilinoMail', '$propiedadID', '$clienteID')";
mysqli_query($conn, $sql_inquilino) or die("Error al insertar inquilino: " . mysqli_error($conn));
$inquilinoID = mysqli_insert_id($conn);

// Datos garante 1
$garante1Nombre    = $_POST['Garante1Nombre'];
$garante1Apellido  = $_POST['Garante1Apellido'];
$garante1DNI       = $_POST['Garante1DNI'];
$garante1Telefono  = $_POST['Garante1Telefono'];
$garante1Mail      = $_POST['Garante1Mail'];

// Insertar garante 1
$sql_g1 = "INSERT INTO garantesinquilinos (Nombre, Apellido, DNI, Telefono, Mail, InquilinoID)
           VALUES ('$garante1Nombre', '$garante1Apellido', '$garante1DNI', '$garante1Telefono', '$garante1Mail', $inquilinoID)";
mysqli_query($conn, $sql_g1) or die("Error al insertar garante 1: " . mysqli_error($conn));
$garante1ID = mysqli_insert_id($conn);

// Datos garante 2 (opcional)
$garante2Nombre    = $_POST['Garante2Nombre'] ?? '';
$garante2Apellido  = $_POST['Garante2Apellido'] ?? '';
$garante2DNI       = $_POST['Garante2DNI'] ?? '';
$garante2Telefono  = $_POST['Garante2Telefono'] ?? '';
$garante2Mail      = $_POST['Garante2Mail'] ?? '';

// Insertar garante 2 si tiene datos
if (!empty($garante2Nombre) && !empty($garante2Apellido) && !empty($garante2DNI)) {
    // Opcional: verificar que el DNI no esté duplicado
    $check_dni = mysqli_query($conn, "SELECT 1 FROM garantesinquilinos WHERE DNI = '$garante2DNI' LIMIT 1");
    if (mysqli_num_rows($check_dni) === 0) {
        $sql_g2 = "INSERT INTO garantesinquilinos (Nombre, Apellido, DNI, Telefono, Mail, InquilinoID)
                   VALUES ('$garante2Nombre', '$garante2Apellido', '$garante2DNI', '$garante2Telefono', '$garante2Mail', $inquilinoID)";
        mysqli_query($conn, $sql_g2) or die("Error al insertar garante 2: " . mysqli_error($conn));
        $garante2ID = mysqli_insert_id($conn);
    } else {
        echo "<script>alert('El DNI del garante 2 ya está registrado.'); history.back();</script>";
        exit;
    }
}

// Datos del contrato
$fechaInicio = $_POST['FechaInicio'];
$fechaFin    = $_POST['FechaFin'];
$canon       = $_POST['CanonMensual'];
$deposito    = $_POST['Deposito'];

// Insertar contrato
$sql_contrato = "INSERT INTO contratos (
                    ClienteID, InquilinoID, PropiedadID, GaranteInquilinoID,
                    fecha_inicio, fecha_fin, canon_mensual, deposito, estado
                ) VALUES (
                    $clienteID, $inquilinoID, $propiedadID, $garante1ID,
                    '$fechaInicio', '$fechaFin', $canon, '$deposito', 'activo'
                )";
mysqli_query($conn, $sql_contrato) or die("Error al insertar contrato: " . mysqli_error($conn));
$contratoID = mysqli_insert_id($conn);

// Redireccionar al PDF
echo "<script>
    alert('✅ Contrato guardado exitosamente.');
    window.location.href = 'propiedades.php';
</script>";

mysqli_close($conn);
?>
