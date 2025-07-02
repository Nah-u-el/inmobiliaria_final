<?php
require_once 'conexion.php';

// Verificar si llegaron todos los datos necesarios
if (
    isset($_POST['CajaID'], $_POST['Fecha'], $_POST['Concepto'], $_POST['TipoMovimiento'],
          $_POST['Monto'], $_POST['FormaPago'], $_POST['ClienteInmueble'])
) {
    // Sanitizar/recibir los datos del formulario
    $id_caja = intval($_POST['CajaID']);
    $fecha = $_POST['Fecha'];
    $concepto = trim($_POST['Concepto']);
    $tipo_movimiento = $_POST['TipoMovimiento'];
    $monto = floatval($_POST['Monto']);
    $forma_pago = trim($_POST['FormaPago']);
    $cliente_inmueble = trim($_POST['ClienteInmueble']);
    $observaciones = isset($_POST['Observaciones']) ? trim($_POST['Observaciones']) : '';

    // Preparar la consulta para actualizar
    $sql = "UPDATE caja 
            SET Fecha = ?, Concepto = ?, TipoMovimiento = ?, Monto = ?, FormaPago = ?, ClienteInmueble = ?, Observaciones = ?
            WHERE CajaID = ?";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("sssssssi", $fecha, $concepto, $tipo_movimiento, $monto, $forma_pago, $cliente_inmueble, $observaciones, $id_caja);

        if ($stmt->execute()) {
            echo "Registro actualizado correctamente";
        } else {
            echo "Error al ejecutar la actualización: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error al preparar la consulta: " . $conn->error;
    }

    $conn->close();
} else {
    echo "Faltan datos obligatorios para actualizar.";
}
