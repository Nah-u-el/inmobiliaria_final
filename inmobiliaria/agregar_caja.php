<?php
include 'conexion.php'; //

// Configurar el huso horario 
date_default_timezone_set('America/Argentina/Buenos_Aires');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recuperar los datos del formulario
    $fecha = date("Y-m-d"); // Obtener la fecha actual en formato YYYY-MM-DD
    $concepto = $_POST['Concepto'];
    $tipoMovimiento = $_POST['TipoMovimiento']; // Capturar tipo de movimiento
    $monto = number_format(floatval($_POST['Monto']), 2, '.', ''); // Formatear el monto
    $forma_pago = $_POST['FormaPago'];
    $cliente_inmueble = $_POST['ClienteInmueble'];
    $observaciones = $_POST['Observaciones'];

    // Validar que los datos no estén vacíos
    if (empty($concepto) || empty($monto) || empty($forma_pago) || empty($cliente_inmueble) || empty($observaciones)) {
        die("Todos los campos son obligatorios.");
    }

    // Validar que el monto sea un número
    if (!is_numeric($monto)) {
        die("El monto debe ser un número.");
    }

    // Ajustar el monto si es "Enviado"
    if ($tipoMovimiento === 'enviado' && $monto > 0) {
        $monto = -$monto;
    }

    // Preparar la consulta SQL con consultas preparadas
    $sql = "INSERT INTO caja (Fecha, Concepto, RecibidoEnviado, FormaPago, ClienteInmueble, Observaciones) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        // Vincular los parámetros
        mysqli_stmt_bind_param($stmt, "ssdsss", $fecha, $concepto, $monto, $forma_pago, $cliente_inmueble, $observaciones);

        // Ejecutar la consulta
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Saldo agregado correctamente'); window.location.href='contabilidad.php';</script>";
            exit(); // Detener el script después de la redirección
        } else {
            echo "Error al ejecutar la consulta: " . mysqli_stmt_error($stmt);
        }

        // Cerrar la declaración
        mysqli_stmt_close($stmt);
    } else {
        echo "Error al preparar la consulta: " . mysqli_error($conn);
    }

    // Cerrar la conexión
    mysqli_close($conn);
}
?>