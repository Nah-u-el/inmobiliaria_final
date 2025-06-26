<?php
session_start(); // Only one session_start() call

require_once 'conexion.php'; // Include conexion.php once

// Cabeceras anti-caché más estrictas
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Fecha en el pasado

// Verificación de sesión más robusta
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !isset($_SESSION['ultima_actividad'])) {
    header("location: ../login/index.php?reason=session_expired");
    exit; // Added exit;
}

// Control de tiempo de sesión (opcional pero recomendado)
if (isset($_SESSION['ultima_actividad']) && (time() - $_SESSION['ultima_actividad'] > 1800)) { // 30 minutos
    header("location: ../login/logout.php?reason=session_timeout");
    exit; // Added exit;
}
$_SESSION['ultima_actividad'] = time(); // Actualiza el tiempo de actividad

// --- Start of client detail fetching logic ---

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Ideally, redirect or show a user-friendly error page instead of just echoing
    echo "<div class='alert alert-danger'>ID de cliente inválido.</div>";
    exit;
}

$clienteID = intval($_GET['id']);

// Consulta para obtener los datos del cliente
$sql = "SELECT Fecha, Nombre, Apellido, Direccion, DNI, DireccionPersonal, Telefono, Mail
        FROM clientes
        WHERE ClienteID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $clienteID);
$stmt->execute();
$result = $stmt->get_result();
$cliente = $result->fetch_assoc();
$stmt->close();

// --- End of client detail fetching logic ---
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle del Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body class="bg-light">
<div class="container mt-4">
    <a href="clientes.php" class="btn btn-secondary mb-3"><i class="fas fa-arrow-left"></i> Volver al listado</a>

    <?php if ($cliente): ?>
        <div class="card shadow">
            <div class="card-header text-white bg-primary">
                <h5 class="mb-0">Detalle del Cliente: <?php echo htmlspecialchars($cliente['Nombre'] . ' ' . $cliente['Apellido']); ?></h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tbody>
                        <tr><th scope="row">Fecha</th><td><?php echo htmlspecialchars($cliente['Fecha']); ?></td></tr>
                        <tr><th scope="row">Nombre</th><td><?php echo htmlspecialchars($cliente['Nombre']); ?></td></tr>
                        <tr><th scope="row">Apellido</th><td><?php echo htmlspecialchars($cliente['Apellido']); ?></td></tr>
                        <tr><th scope="row">DNI</th><td><?php echo htmlspecialchars($cliente['DNI']); ?></td></tr>
                        <tr><th scope="row">Dirección</th><td><?php echo htmlspecialchars($cliente['Direccion']); ?></td></tr>
                        <tr><th scope="row">Dirección Personal</th><td><?php echo htmlspecialchars($cliente['DireccionPersonal']); ?></td></tr>
                        <tr><th scope="row">Teléfono</th><td><?php echo htmlspecialchars($cliente['Telefono']); ?></td></tr>
                        <tr><th scope="row">Email</th><td><?php echo htmlspecialchars($cliente['Mail']); ?></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-danger">No se encontraron datos para este cliente.</div>
    <?php endif; ?>
</div>
</body>
</html>