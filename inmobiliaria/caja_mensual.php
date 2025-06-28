<?php
session_start();

// Muestra mensajes de sesión (alertas) si existen
if (isset($_SESSION['mensaje'])) {
    echo "<script>alert('" . $_SESSION['mensaje'] . "');</script>";
    unset($_SESSION['mensaje']); // Elimina el mensaje después de mostrarlo
}

// Incluir la conexión a la base de datos una única vez
include_once 'conexion.php';

// **IMPORTANTE**: Asegúrate de que 'conexion.php' maneje la conexión correctamente
// y que la variable $conn esté disponible globalmente o sea devuelta por una función.
// Idealmente, tu archivo 'conexion.php' debería lucir algo como:
/*
<?php
$servername = "localhost";
$username = "tu_usuario";
$password = "tu_clave";
$dbname = "tu_base_de_datos";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
*/

// Obtener el mes y año desde la URL
$mes = $_GET['mes'] ?? date('m'); // Mes actual si no se especifica
$año = $_GET['año'] ?? date('Y'); // Año actual si no se especifica

// Establecer el idioma a español para strftime
setlocale(LC_TIME, 'es_ES.UTF-8', 'spanish');
$nombre_mes_año = strftime("%B %Y", mktime(0, 0, 0, $mes, 1, $año));

// Consulta SQL para obtener los datos mensuales
$stmt = $conn->prepare("SELECT Fecha, Concepto, RecibidoEnviado, FormaPago, ClienteInmueble, Observaciones FROM caja WHERE MONTH(Fecha) = ? AND YEAR(Fecha) = ?");
$stmt->bind_param("ii", $mes, $año);
$stmt->execute();
$result = $stmt->get_result();

// Consulta para calcular el total mensual
$stmtTotal = $conn->prepare("SELECT SUM(RecibidoEnviado) AS total_mensual FROM caja WHERE MONTH(Fecha) = ? AND YEAR(Fecha) = ?");
$stmtTotal->bind_param("ii", $mes, $año);
$stmtTotal->execute();
$totalResult = $stmtTotal->get_result();
$totalRow = $totalResult->fetch_assoc();
$totalMensual = $totalRow['total_mensual'] ?? 0; // Si no hay datos, el total es 0

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión Inmobiliaria - Caja Mensual</title>

    <link rel="icon" type="image/x-icon" href="../login/img/favicon.ico">
    <link rel="icon" type="image/png" href="../login/img/favicon-16x16.png">
    <link rel="icon" type="image/png" href="../login/img/android-chrome-192x192.png">
    <link rel="icon" type="image/png" href="../login/img/android-chrome-512x512.png">
    <link rel="icon" type="image/png" href="../login/img/apple-touch-icon.png">
    <link rel="icon" type="image/png" href="../login/img/favicon-32x32.png">


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="notificacion.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=menu" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="//cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" defer></script>
</head>
<body>
    <header>
        <div class="header-content">
          <div class="dropdown">
               <a href="../login/logout.php" class="btn btn-danger" title="Cerrar Sesión">
                <i class="fas fa-power-off"></i>
            </a>
            </div>

            <img src="../login/img_login/descarga.png" alt="Logo Inmobiliaria" class="logo">

            <div>
                <div class="notification-wrapper position-relative">
                <button class="notification-button-option1" aria-label="Notificaciones pendientes" id="btnNotificaciones">
                 <i class="fas fa-bell"></i>
                 <span class="notification-badge d-none" id="badgeNoti">0</span>
                </button>
            <div class="dropdown-notifications d-none" id="notiDropdown"></div>
                </div>
            </div>
        </div>
        <nav>
            <ul>
                <li><a href="clientes.php"><i class="fas fa-users"></i> Clientes</a></li>
                <li><a href="propietarios.php"><i class="fas fa-user-tie"></i> Propietarios</a></li>
                <li><a href="propiedades.php"><i class="fas fa-home"></i> Propiedades</a></li>
                <li><a href="contabilidad.php" class="active"><i class="fas fa-file-invoice-dollar"></i> Contabilidad</a></li>
            </ul>
        </nav>
    </header>
    <main class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
            <button type="button" class="btn btn-secondary" style="background-color: rgba(233, 128, 0, 0.92);" onclick="volverCaja()">
                <i class="fas fa-arrow-left"></i> Volver a Contabilidad
            </button>
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                    Seleccionar Mes y Año
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton" id="mesesDropdown">
                    </ul>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header text-white" style="background-color:rgba(233, 128, 0, 0.92);">
                <h2 class="h5 mb-0">Detalles de Caja - <?php echo htmlspecialchars(ucfirst($nombre_mes_año)); ?></h2>
            </div>
            <div class="card-body">
                <?php
                // Verificar si hay resultados
                if ($result->num_rows > 0) {
                    echo '<div class="table-responsive">
                            <table id="cajaMensualTable" class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Concepto</th>
                                        <th>Monto</th>
                                        <th>Forma de Pago</th>
                                        <th>Cliente/Inmueble</th>
                                        <th>Observaciones</th>
                                    </tr>
                                </thead>
                                <tbody>';

                    // Iterar sobre cada fila de resultados
                    while ($fila = $result->fetch_assoc()) {
                        $monto_clase = $fila['RecibidoEnviado'] >= 0 ? 'text-success' : 'text-danger';
                        echo '<tr>
                                <td>' . htmlspecialchars($fila['Fecha']) . '</td>
                                <td>' . htmlspecialchars($fila['Concepto']) . '</td>
                                <td class="' . $monto_clase . '">$' . number_format($fila['RecibidoEnviado'], 2, ',', '.') . '</td>
                                <td>' . htmlspecialchars($fila['FormaPago']) . '</td>
                                <td>' . htmlspecialchars($fila['ClienteInmueble']) . '</td>
                                <td>' . htmlspecialchars($fila['Observaciones']) . '</td>
                            </tr>';
                    }

                    // Cerrar la tabla HTML
                    echo '      </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2"><strong>Total Mensual:</strong></td>
                                        <td class="fs-5 fw-bold ' . ($totalMensual >= 0 ? 'text-success' : 'text-danger') . '">$' . number_format($totalMensual, 2, ',', '.') . '</td>
                                        <td colspan="3"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>'; // Cierre de .table-responsive
                } else {
                    // Si no hay resultados, mostrar un mensaje con estilo de Bootstrap
                    echo '<div class="alert alert-info" role="alert">No se encontraron movimientos para el mes y año seleccionados.</div>';
                }
                ?>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="//cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>

    <script>
        // Función para volver a la página de contabilidad
        function volverCaja() {
            window.location.href = 'contabilidad.php';
        }

        // Inicializa DataTables
        $(document).ready(function() {
            console.log("jQuery y DataTables listos.");
            $('#cajaMensualTable').DataTable({
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/2.0.2/i18n/es-ES.json" // URL correcta para DataTables 2.x
                },
                "paging": true,      // Habilita paginación
                "searching": true,   // Habilita el cuadro de búsqueda
                "ordering": true,    // Habilita ordenación de columnas
                "info": true         // Habilita información de la tabla
            });
            console.log("DataTables inicializado en #cajaMensualTable.");
        });

        document.addEventListener("DOMContentLoaded", function() {
            fetch('obtener_meses_con_datos.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log("Datos recibidos para el dropdown:", data); // Para depuración

                    const dropdown = document.getElementById('mesesDropdown');
                    dropdown.innerHTML = ''; // Limpiar opciones existentes

                    if (data.length === 0) {
                        dropdown.innerHTML = '<li><a class="dropdown-item" href="#">No hay datos disponibles</a></li>';
                        return;
                    }

                    // Ordenar los datos por año y luego por mes (descendente para el más reciente primero)
                    data.sort((a, b) => {
                        if (b.año !== a.año) {
                            return b.año - a.año;
                        }
                        return b.mes - a.mes;
                    });

                    data.forEach(item => {
                        const año = item.año;
                        const mes = item.mes;
                        // Usar toLocaleString para obtener el nombre del mes correctamente
                        const nombreMes = new Date(año, mes - 1).toLocaleString('es-ES', { month: 'long' });
                        const totalMensual = parseFloat(item.total_mensual).toFixed(2); // Asegurar formato de 2 decimales

                        const li = document.createElement('li');
                        const a = document.createElement('a');
                        a.classList.add('dropdown-item');
                        a.href = `caja_mensual.php?mes=${mes}&año=${año}`;
                        a.textContent = `${nombreMes} ${año} (Total: $${totalMensual})`;

                        li.appendChild(a);
                        dropdown.appendChild(li);
                    });
                })
                .catch(error => {
                    console.error("Error al cargar los meses:", error);
                    // Considera mostrar un mensaje de error más amigable al usuario
                });
        });
    </script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const badge = document.getElementById('badgeNoti');
    const dropdown = document.getElementById('notiDropdown');
    const btnNoti = document.getElementById('btnNotificaciones');

    fetch('contratos_por_vencer.php')
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                badge.textContent = data.length;
                badge.classList.remove('d-none');

                const ul = document.createElement('ul');
                ul.style.listStyle = 'none';
                ul.style.margin = 0;
                ul.style.padding = 0;

                data.forEach(c => {
                    const li = document.createElement('li');
                    li.classList.add(c.urgencia === 'alta' ? 'notificacion-alta' : 'notificacion-media');

                    const contenido = document.createElement('div');
                    contenido.innerHTML = `
                        <strong>Contrato #${c.id}</strong><br>
                        ${c.direccion}<br>
                        <small>Vence: ${c.fecha_fin}</small>
                    `;

                    const acciones = document.createElement('div');
                    acciones.classList.add('d-flex', 'flex-column', 'align-items-end');

                    const verBtn = document.createElement('a');
                    verBtn.href = `ver_contrato.php?id=${c.id}`;
                    verBtn.target = '_blank';
                   
                   

                    const cerrarBtn = document.createElement('button');
                    cerrarBtn.className = 'btn btn-sm btn-outline-secondary';
                    cerrarBtn.textContent = 'Leído';
                    cerrarBtn.onclick = () => {
                        li.remove();
                        const restantes = document.querySelectorAll('#notiDropdown li').length;
                        badge.textContent = restantes;
                        if (restantes === 0) {
                            badge.classList.add('d-none');
                            dropdown.innerHTML = '<div class="p-2 text-muted">No hay vencimientos próximos</div>';
                        }
                    };

                    acciones.appendChild(verBtn);
                    acciones.appendChild(cerrarBtn);

                    li.appendChild(contenido);
                    li.appendChild(acciones);
                    ul.appendChild(li);
                });

                dropdown.innerHTML = '';
                dropdown.appendChild(ul);
            } else {
                badge.classList.add('d-none');
                dropdown.innerHTML = '<div class="p-2 text-muted">No hay vencimientos próximos</div>';
            }
        })
        .catch(error => {
            console.error('Error al cargar notificaciones:', error);
        });

    // Mostrar/ocultar dropdown al hacer clic en la campanita
    btnNoti.addEventListener('click', () => {
        dropdown.classList.toggle('d-none');
    });
});
</script>

</body>
</html>

<?php
// Cierra la conexión a la base de datos al final del script si se abrió.
if (isset($conn) && $conn) {
    mysqli_close($conn);
}
?>