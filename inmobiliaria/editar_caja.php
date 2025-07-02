<?php
// editar_caja.php
require_once 'conexion.php';

// IMPORTANT: Keep these enabled for debugging, comment out in production
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// --- START DEBUGGING BLOCK ---
// Temporarily disable the JavaScript prevention to see PHP output directly
// In your JavaScript, make sure you have COMMENTED OUT this line:
// event.preventDefault();
// Once you've tested this PHP block, you can uncomment event.preventDefault() again.
echo "<pre>--- PHP Debug Info ---<br>";
echo "Received POST data:<br>";
print_r($_POST);

$received_caja_id = isset($_POST['CajaID']) ? $_POST['CajaID'] : 'NOT SET';
$parsed_caja_id = intval($received_caja_id);

echo "Raw CajaID from POST: " . $received_caja_id . "<br>";
echo "Parsed CajaID (intval): " . $parsed_caja_id . "<br>";
echo "----------------------<br>";
// --- END DEBUGGING BLOCK ---

// Original check for required data
if (
    isset($_POST['CajaID'], $_POST['Fecha'], $_POST['Concepto'],
          $_POST['Monto'], $_POST['FormaPago'], $_POST['ClienteInmueble'])
) {
    // Sanitizar/recibir los datos del formulario
    $id_caja = $parsed_caja_id; // Use the parsed ID for consistency in debugging
    $fecha = $_POST['Fecha'];
    $concepto = trim($_POST['Concepto']);
    $monto = floatval($_POST['Monto']); 
    $forma_pago = trim($_POST['FormaPago']);
    $cliente_inmueble = trim($_POST['ClienteInmueble']);
    $observaciones = isset($_POST['Observaciones']) ? trim($_POST['Observaciones']) : '';

    // Prepare the SQL query
    $sql = "UPDATE caja
            SET Fecha = ?, Concepto = ?, RecibidoEnviado = ?, FormaPago = ?, ClienteInmueble = ?, Observaciones = ?
            WHERE CajaID = ?";

    echo "SQL Query: " . htmlspecialchars($sql) . "<br>"; // Display the SQL query

    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // Corrected bind_param string: removed the space.
// Corrected bind_param string for 7 variables
$stmt->bind_param("ssssssi", $fecha, $concepto, $monto, $forma_pago, $cliente_inmueble, $observaciones, $id_caja);
        // Display the values being bound
        echo "Binding values: <br>";
        echo "1. Fecha: " . $fecha . "<br>";
        echo "2. Concepto: " . $concepto . "<br>";
        echo "3. Monto (RecibidoEnviado): " . $monto . "<br>";
        echo "4. FormaPago: " . $forma_pago . "<br>";
        echo "5. ClienteInmueble: " . $cliente_inmueble . "<br>";
        echo "6. Observaciones: " . $observaciones . "<br>";
        echo "7. CajaID (WHERE clause): " . $id_caja . "<br>";
        echo "----------------------<br>";

        if ($stmt->execute()) {
            $affected_rows = $stmt->affected_rows;
            if ($affected_rows > 0) {
                echo "Registro actualizado correctamente. Filas afectadas: " . $affected_rows;
            } else {
                echo "Registro no actualizado. No se encontraron filas coincidentes con el ID proporcionado o los datos no cambiaron.";
            }
        } else {
            echo "Error al ejecutar la actualización: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error al preparar la consulta: " . $conn->error;
    }

    $conn->close();
} else {
    echo "Faltan datos obligatorios para actualizar. Asegúrate de que todos los campos requeridos estén en el formulario POST. Datos recibidos: " . implode(', ', array_keys($_POST));
}
echo "</pre>";
?>