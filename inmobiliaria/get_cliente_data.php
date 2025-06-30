<?php
// get_cliente_data.php

include 'conexion.php'; // Your database connection file

header('Content-Type: application/json'); // Set header to indicate JSON response

$response = ['success' => false, 'message' => ''];

if (isset($_GET['clienteID'])) {
    $clienteID = $_GET['clienteID'];

    // --- Fetch Client Data ---
    $sql_cliente = "SELECT * FROM clientes WHERE ClienteID = ?";
    $stmt_cliente = mysqli_prepare($conn, $sql_cliente);
    mysqli_stmt_bind_param($stmt_cliente, "i", $clienteID);
    mysqli_stmt_execute($stmt_cliente);
    $result_cliente = mysqli_stmt_get_result($stmt_cliente);

    if ($cliente = mysqli_fetch_assoc($result_cliente)) {
        $response['cliente'] = $cliente;
        $response['success'] = true;

        // --- Fetch Garante 1 Data (assuming a relationship, e.g., via a join table or ClienteID in garantes) ---
        // This is a simplified example. You might need to adjust your database schema
        // or join queries based on how garantes are linked to clients.
        // For this example, let's assume 'garantes' table has a 'ClienteID' and 'TipoGarante' (1 or 2)
        // OR, you have a separate join table 'cliente_garante'
        
        // Option 1: Garantes linked directly to ClienteID with a 'tipo' column
        $sql_garantes = "SELECT * FROM garantes WHERE ClienteID = ? ORDER BY GaranteID LIMIT 2"; // Adjust as per your schema
        $stmt_garantes = mysqli_prepare($conn, $sql_garantes);
        mysqli_stmt_bind_param($stmt_garantes, "i", $clienteID);
        mysqli_stmt_execute($stmt_garantes);
        $result_garantes = mysqli_stmt_get_result($stmt_garantes);

        $garantes = [];
        while ($garante = mysqli_fetch_assoc($result_garantes)) {
            $garantes[] = $garante;
        }

        if (isset($garantes[0])) {
            $response['garante1'] = $garantes[0];
        }
        if (isset($garantes[1])) {
            $response['garante2'] = $garantes[1];
        }

        // IMPORTANT: If your garantes table is separate and linked differently,
        // you'll need to modify the queries above to fetch the correct garantes.
        // For instance, if a client has GaranteID1 and GaranteID2 columns directly in the clients table,
        // you would fetch those IDs and then query the garantes table for each ID.
        /*
        // Example for fetching garantes if ClienteID has GaranteID1 and GaranteID2 columns
        if (!empty($cliente['GaranteID1'])) {
            $sql_garante1 = "SELECT * FROM garantes WHERE GaranteID = ?";
            $stmt_garante1 = mysqli_prepare($conn, $sql_garante1);
            mysqli_stmt_bind_param($stmt_garante1, "i", $cliente['GaranteID1']);
            mysqli_stmt_execute($stmt_garante1);
            $result_garante1 = mysqli_stmt_get_result($stmt_garante1);
            $response['garante1'] = mysqli_fetch_assoc($result_garante1);
        }
        if (!empty($cliente['GaranteID2'])) {
            $sql_garante2 = "SELECT * FROM garantes WHERE GaranteID = ?";
            $stmt_garante2 = mysqli_prepare($conn, $sql_garante2);
            mysqli_stmt_bind_param($stmt_garante2, "i", $cliente['GaranteID2']);
            mysqli_stmt_execute($stmt_garante2);
            $result_garante2 = mysqli_stmt_get_result($stmt_garante2);
            $response['garante2'] = mysqli_fetch_assoc($result_garante2);
        }
        */

    } else {
        $response['message'] = 'Cliente no encontrado.';
    }

    mysqli_stmt_close($stmt_cliente);
    if (isset($stmt_garantes)) {
        mysqli_stmt_close($stmt_garantes);
    }
} else {
    $response['message'] = 'ID de cliente no proporcionado.';
}

mysqli_close($conn);
echo json_encode($response);
?>