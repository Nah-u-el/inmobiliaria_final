<?php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar la conexión a la base de datos
    if (!$conn) {
        die('Error de conexión: ' . mysqli_connect_error());
    }

    // Recuperar los datos del formulario
    $propiedadID = $_POST['propiedadID'] ?? null;
    $direccion = $_POST['direccion'] ?? '';
    $ciudad = $_POST['ciudad'] ?? '';
    $barrio = $_POST['barrio'] ?? '';
    $nro = $_POST['nro'] ?? '';
    $dominio = $_POST['dominio'] ?? '';
    $nroPartida = $_POST['nroPartida'] ?? '';
    $estado = $_POST['estado'] ?? '';

    // Validar que el PropiedadID esté presente y sea válido
    if (empty($propiedadID)) {
        die('Error: El ID de la propiedad es obligatorio para actualizar.');
    }

    // Validar campos obligatorios
    if (empty($direccion) || empty($ciudad) || empty($barrio) || empty($nro) || empty($dominio) || empty($nroPartida) || empty($estado)) {
        die('Todos los campos son obligatorios.');
    }

    // Preparar la consulta de actualización
    $sql = "UPDATE propiedades 
            SET Direccion = ?, Ciudad = ?, Barrio = ?, Nro = ?, Dominio = ?, NroPartida = ?, Estado = ?
            WHERE PropiedadID = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die('Error en la preparación de la consulta: ' . $conn->error);
    }

    // Vincular los parámetros
    $stmt->bind_param("sssssssi", $direccion, $ciudad, $barrio, $nro, $dominio, $nroPartida, $estado, $propiedadID);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        header("Location: propiedades.php"); // Redireccionar a la página de propiedades
        exit();
    } else {
        die('Error al actualizar la propiedad: ' . $stmt->error); // Detener la ejecución y mostrar el error
    }

    // Cerrar la conexión
    $stmt->close();
    $conn->close();
}
?>