<?php
include 'conexion.php';

$query = "SELECT YEAR(Fecha) as año, MONTH(Fecha) as mes, SUM(RecibidoEnviado) as total_mensual FROM caja GROUP BY año, mes ORDER BY año DESC, mes DESC";
$result = mysqli_query($conn, $query);

$meses = [];
while ($row = mysqli_fetch_assoc($result)) {
    $meses[] = $row;
}

header('Content-Type: application/json');
echo json_encode($meses);

mysqli_close($conn);
?>
