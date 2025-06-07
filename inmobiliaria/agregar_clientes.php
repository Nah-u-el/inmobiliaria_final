<?php
include 'conexion.php'; // Conexión a la base de datos

// Verifica si los datos fueron enviados
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fecha = mysqli_real_escape_string($conn, $_POST['Fecha']);
    $nombre = mysqli_real_escape_string($conn, $_POST['Nombre']);
    $apellido = mysqli_real_escape_string($conn, $_POST['Apellido']);
    $direccion = mysqli_real_escape_string($conn, $_POST['Direccion']);
    $dni = mysqli_real_escape_string($conn, $_POST['DNI']);
    $direccion_personal = mysqli_real_escape_string($conn, $_POST['DireccionPersonal']);
    $telefono = mysqli_real_escape_string($conn, $_POST['Telefono']);
    $mail = mysqli_real_escape_string($conn, $_POST['Mail']);

    // Inserta los datos en la tabla `clientes`
    $sql = "INSERT INTO clientes (Fecha, Nombre, Apellido, Direccion, Dni, DireccionPersonal, Telefono, Mail) 
            VALUES ('$fecha', '$nombre', '$apellido', '$direccion', '$dni', '$direccion_personal', '$telefono', '$mail')";

    if (mysqli_query($conn, $sql)) {
        // Obtener el ID del cliente recién creado
        $cliente_id = mysqli_insert_id($conn);

        // Insertar garante 1 si tiene datos
        if (!empty($_POST['garante1_fecha']) && !empty($_POST['garante1_nombre']) && !empty($_POST['garante1_apellido'])
            && !empty($_POST['garante1_direccion']) && !empty($_POST['garante1_dni']) && !empty($_POST['garante1_direccion_personal']) 
            && !empty($_POST['garante1_telefono']) && !empty($_POST['garante1_mail'])) {

            $fecha_garante1 = mysqli_real_escape_string($conn, $_POST['garante1_fecha']);
            $nombre_garante1 = mysqli_real_escape_string($conn, $_POST['garante1_nombre']);
            $apellido_garante1 = mysqli_real_escape_string($conn, $_POST['garante1_apellido']);
            $direccion_garante1 = mysqli_real_escape_string($conn, $_POST['garante1_direccion']);
            $dni_garante1 = mysqli_real_escape_string($conn, $_POST['garante1_dni']);
            $direccion_personal_garante1 = mysqli_real_escape_string($conn, $_POST['garante1_direccion_personal']);
            $telefono_garante1 = mysqli_real_escape_string($conn, $_POST['garante1_telefono']);
            $mail_garante1 = mysqli_real_escape_string($conn, $_POST['garante1_mail']);

            $sql_garante1 = "INSERT INTO garantes (ClienteID, Fecha, Nombre, Apellido, Direccion, Dni, DireccionPersonal, Telefono, Mail) 
                            VALUES ('$cliente_id', '$fecha_garante1', '$nombre_garante1', '$apellido_garante1',
                            '$direccion_garante1', '$dni_garante1', '$direccion_personal_garante1', '$telefono_garante1', '$mail_garante1')";

            if (!mysqli_query($conn, $sql_garante1)) {
                echo "Error al insertar garante 1: " . mysqli_error($conn);
            }
        }
        
        // Insertar garante 2 si tiene datos
        if (!empty($_POST['garante2_fecha']) && !empty($_POST['garante2_nombre']) && !empty($_POST['garante2_apellido'])
            && !empty($_POST['garante2_direccion']) && !empty($_POST['garante2_dni']) && !empty($_POST['garante2_direccion_personal']) 
            && !empty($_POST['garante2_telefono']) && !empty($_POST['garante2_mail'])) {

            $fecha_garante2 = mysqli_real_escape_string($conn, $_POST['garante2_fecha']);
            $nombre_garante2 = mysqli_real_escape_string($conn, $_POST['garante2_nombre']);
            $apellido_garante2 = mysqli_real_escape_string($conn, $_POST['garante2_apellido']);
            $direccion_garante2 = mysqli_real_escape_string($conn, $_POST['garante2_direccion']);
            $dni_garante2 = mysqli_real_escape_string($conn, $_POST['garante2_dni']);
            $direccion_personal_garante2 = mysqli_real_escape_string($conn, $_POST['garante2_direccion_personal']);
            $telefono_garante2 = mysqli_real_escape_string($conn, $_POST['garante2_telefono']);
            $mail_garante2 = mysqli_real_escape_string($conn, $_POST['garante2_mail']);

            $sql_garante2 = "INSERT INTO garantes (ClienteID, Fecha, Nombre, Apellido, Direccion, Dni, DireccionPersonal, Telefono, Mail) 
                            VALUES ('$cliente_id', '$fecha_garante2', '$nombre_garante2', '$apellido_garante2',
                            '$direccion_garante2', '$dni_garante2', '$direccion_personal_garante2', '$telefono_garante2', '$mail_garante2')";

            if (!mysqli_query($conn, $sql_garante2)) {
                echo "Error al insertar garante 2: " . mysqli_error($conn);
            }
        }

        // Redirigir con mensaje de éxito
        echo "<script>alert('Cliente y garantes agregados correctamente'); window.location.href='clientes.php';</script>";
    } else {
        echo "Error al agregar cliente: " . mysqli_error($conn);
    }

    // Cerrar la conexión
    mysqli_close($conn);
}
?>
