<?php
// Incluir la conexión a la base de datos
include 'conexion.php';

// Verificar si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger los datos del formulario
    $fecha = $_POST['Fecha'];
    $barrio = $_POST['Barrio'];
    $ciudad = $_POST['Ciudad'];
    $direccion = $_POST['Direccion'];
    $nro = $_POST['Nro'];
    $dominio = $_POST['Dominio'];
    $nro_partida = $_POST['NroPartida'];
    $estado = $_POST['Estado'];

    // Validar y sanitizar los datos (opcional, pero recomendado)
    $fecha = mysqli_real_escape_string($conn, $fecha);
    $barrio = mysqli_real_escape_string($conn, $barrio);
    $ciudad = mysqli_real_escape_string($conn, $ciudad);
    $direccion = mysqli_real_escape_string($conn, $direccion);
    $nro = mysqli_real_escape_string($conn, $nro);
    $dominio = mysqli_real_escape_string($conn, $dominio);
    $nro_partida = mysqli_real_escape_string($conn, $nro_partida);
    $estado = mysqli_real_escape_string($conn, $estado);

    // Consulta SQL para insertar la propiedad
    $sql = "INSERT INTO propiedades (Fecha, Barrio, Ciudad, Direccion, Nro, Dominio, NroPartida, Estado)
            VALUES ('$fecha', '$barrio', '$ciudad', '$direccion', '$nro', '$dominio', '$nro_partida', '$estado')";

    // Ejecutar la consulta
    if (mysqli_query($conn, $sql)) {
        // Redirigir a la página de propiedades con un mensaje de éxito
        header("Location: propiedades.php?mensaje=Propiedad+agregada+correctamente");
        exit();
    } else {
        // Mostrar un mensaje de error si la consulta falla
        echo "Error al agregar la propiedad: " . mysqli_error($conn);
    }

    // Cerrar la conexión a la base de datos
    mysqli_close($conn);
} else {
    // Si no se envió el formulario, redirigir a la página de propiedades
    header("Location: propiedades.php");
    exit();
}
?>
