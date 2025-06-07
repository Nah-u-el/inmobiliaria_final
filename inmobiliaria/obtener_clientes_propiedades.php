<?php
include 'conexion.php';

$term = $_GET['term'] ?? '';

$sql = "SELECT id, nombre, 'cliente' AS tipo FROM clientes WHERE nombre LIKE '%$term%' 
        UNION 
        SELECT id, direccion, 'inmueble' AS tipo FROM inmuebles WHERE direccion LIKE '%$term%'";

$result = mysqli_query($conn, $sql);

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = [
        'label' => $row['nombre'] ?? $row['direccion'], // Mostrar nombre o dirección
        'value' => $row['id'], // ID del cliente o inmueble
        'tipo' => $row['tipo'] // 'cliente' o 'inmueble'
    ];
}

echo json_encode($data);
mysqli_close($conn);

?>