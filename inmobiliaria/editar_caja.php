<?php
session_start();

// --- INICIO DEPURACIÓN ---
// Activa la muestra de errores en el navegador (solo para desarrollo)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Archivo de log para registrar el flujo de datos
$debug_log_file = 'debug_editar_caja.log';

// Limpiar el archivo de log al inicio de cada ejecución para que no se haga enorme
// NOTA: Para un uso en producción, esto no se haría así, o se rotaría el log.
// Para depuración, es útil para ver solo la última ejecución.
// file_put_contents($debug_log_file, "--- INICIO DE EJECUCIÓN: " . date('Y-m-d H:i:s') . " ---\n", FILE_APPEND);
// Si quieres ver solo la última ejecución, descomenta la línea de arriba y comenta la de abajo:
file_put_contents($debug_log_file, "\n--- INICIO DE EJECUCIÓN: " . date('Y-m-d H:i:s') . " ---\n", FILE_APPEND);

// Registra los datos recibidos por POST
file_put_contents($debug_log_file, "Datos POST recibidos:\n" . print_r($_POST, true) . "\n", FILE_APPEND);
// --- FIN DEPURACIÓN ---

include_once 'conexion.php'; // Incluye tu archivo de conexión a la base de datos

// Verifica la conexión a la base de datos
if ($conn->connect_error) {
    $_SESSION['mensaje'] = "Error de conexión a la base de datos: " . $conn->connect_error;
    file_put_contents($debug_log_file, "ERROR: Fallo de conexión a la DB: " . $conn->connect_error . "\n", FILE_APPEND);
    header("Location: contabilidad.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Intenta obtener el ID de caja. Usamos `?? null` para evitar errores si no está definido.
    $id_caja = $_POST['id_caja'] ?? null;

    // --- DEPURACIÓN: Verifica el ID de caja después de obtenerlo ---
    file_put_contents($debug_log_file, "Valor de \$id_caja después de recibirlo: " . var_export($id_caja, true) . "\n", FILE_APPEND);

    // Validación del ID: Es lo más importante. Debe ser numérico y no vacío.
    if (empty($id_caja) || !is_numeric($id_caja)) {
        $_SESSION['mensaje'] = "Error: ID de registro no válido o no proporcionado para la edición.";
        file_put_contents($debug_log_file, "ERROR: ID no válido o vacío. ID: " . var_export($id_caja, true) . "\n", FILE_APPEND);
        header("Location: contabilidad.php");
        exit();
    }

    // Recoger los demás datos del formulario
    $concepto = $_POST['Concepto'] ?? '';
    $tipo_movimiento = $_POST['TipoMovimiento'] ?? 'recibido'; // 'recibido' o 'enviado'
    $monto = floatval($_POST['Monto'] ?? 0); // Aseguramos que sea un número flotante
    $forma_pago = $_POST['FormaPago'] ?? '';
    $cliente_inmueble = $_POST['ClienteInmueble'] ?? '';
    $observaciones = $_POST['Observaciones'] ?? '';

    // Ajustar el monto a negativo si el tipo de movimiento es 'enviado'
    $recibido_enviado = $monto;
    if ($tipo_movimiento === 'enviado') {
        $recibido_enviado = -$monto; // Asegura que el monto sea negativo si es 'enviado'
    }

    // --- DEPURACIÓN: Muestra los valores de las variables antes de la consulta ---
    file_put_contents($debug_log_file, "Datos a actualizar:\n", FILE_APPEND);
    file_put_contents($debug_log_file, "ID: " . $id_caja . "\n", FILE_APPEND);
    file_put_contents($debug_log_file, "Concepto: " . $concepto . "\n", FILE_APPEND);
    file_put_contents($debug_log_file, "Recibido/Enviado: " . $recibido_enviado . "\n", FILE_APPEND);
    file_put_contents($debug_log_file, "Forma de Pago: " . $forma_pago . "\n", FILE_APPEND);
    file_put_contents($debug_log_file, "Cliente/Inmueble: " . $cliente_inmueble . "\n", FILE_APPEND);
    file_put_contents($debug_log_file, "Observaciones: " . $observaciones . "\n", FILE_APPEND);

    // Preparar la consulta SQL para actualizar los datos
    // La fecha no se edita en el formulario, así que no la incluimos en el UPDATE
    $sql = "UPDATE caja SET Concepto = ?, RecibidoEnviado = ?, FormaPago = ?, ClienteInmueble = ?, Observaciones = ? WHERE ID = ?";

    $stmt = $conn->prepare($sql);

    // Verificar si la preparación de la consulta fue exitosa
    if ($stmt === false) {
        $_SESSION['mensaje'] = "Error al preparar la consulta: " . $conn->error;
        file_put_contents($debug_log_file, "ERROR: Fallo al preparar la consulta: " . $conn->error . "\n", FILE_APPEND);
        header("Location: contabilidad.php");
        exit();
    }

    // Vincular los parámetros a la consulta preparada
    $stmt->bind_param("sdsssi", $concepto, $recibido_enviado, $forma_pago, $cliente_inmueble, $observaciones, $id_caja);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $_SESSION['mensaje'] = "Registro actualizado correctamente.";
            file_put_contents($debug_log_file, "ÉXITO: Registro ID " . $id_caja . " actualizado correctamente.\n", FILE_APPEND);
        } else {
            $_SESSION['mensaje'] = "No se realizaron cambios en el registro o el ID no existe.";
            file_put_contents($debug_log_file, "ADVERTENCIA: No se realizaron cambios en el registro ID " . $id_caja . " o no existe.\n", FILE_APPEND);
        }
    } else {
        $_SESSION['mensaje'] = "Error al actualizar el registro: " . $stmt->error;
        file_put_contents($debug_log_file, "ERROR: Fallo al ejecutar la consulta UPDATE para ID " . $id_caja . ": " . $stmt->error . "\n", FILE_APPEND);
    }

    // Cerrar la declaración y la conexión
    $stmt->close();
    $conn->close();

    // Redirigir de vuelta a la página principal de contabilidad
    header("Location: contabilidad.php");
    exit();

} else {
    // Si la solicitud no es POST, redirigir a la página principal
    $_SESSION['mensaje'] = "Acceso no autorizado.";
    file_put_contents($debug_log_file, "ADVERTENCIA: Acceso no autorizado (no es POST).\n", FILE_APPEND);
    header("Location: contabilidad.php");
    exit();
}
?>