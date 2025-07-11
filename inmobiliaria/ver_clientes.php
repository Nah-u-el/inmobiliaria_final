<?php
session_start();

// CABECERAS PARA EVITAR CACHÉ
header("Cache-Control: no-cache, no-store, must-revalidate, max-age=0"); // HTTP 1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies

// VERIFICACIÓN DE AUTENTICACIÓN
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: ../login/index.php");
    exit;
}

// MENSAJE DE ALERTA (opcional)
if (isset($_SESSION['mensaje'])) {
    echo "<script>alert('" . $_SESSION['mensaje'] . "');</script>";
    unset($_SESSION['mensaje']);
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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión Inmobiliaria - Detalle del Propietario</title>
    
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
    </head>
<body>
    <header>
        <div class="header-content">
            
            <div class="dropdown">
               <a href="../login/logout.php" class="btn btn-danger" title="Cerrar Sesión">
                <i class="fas fa-power-off"></i>
            </a>
            </div>
            
               <img src="../login/img_login/descarga.png" alt="SC Inmobiliaria" class="logo">

            <div class="notification-wrapper position-relative">
                <button class="notification-button-option1" aria-label="Notificaciones pendientes" id="btnNotificaciones">
                 <i class="fas fa-bell"></i>
                 <span class="notification-badge d-none" id="badgeNoti">0</span>
                </button>
            <div class="dropdown-notifications d-none" id="notiDropdown"></div>
        </div>
        </div>
        <nav>
            <ul>
                <li><a href="clientes.php"  class="active"><i class="fas fa-users"></i> Clientes</a></li>
                <li><a href="propietarios.php"><i class="fas fa-user-tie"></i> Propietarios</a></li>
                <li><a href="propiedades.php"><i class="fas fa-home"></i> Propiedades</a></li>
                <li><a href="contabilidad.php"><i class="fas fa-file-invoice-dollar"></i> Contabilidad</a></li>
            </ul>
        </nav>
    </header>
    <main class="container mt-4">
        <div class="mb-3">
            <a href="clientes.php" class="btn btn-secondary" style="background: rgba(233, 128, 0, 0.92);"><i class="fas fa-arrow-left"></i> Volver a Clientes</a>
        </div>

        <?php
        $propietarioID = null; // Initialize to null
        try {
            if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                $propietarioID = (int)$_GET['id'];

                // Verify if the PropietarioID exists in the 'clientes' table
                // Assuming 'clientes' stores both clients and owners, and a specific field
                // or type distinguishes them. If you have a separate 'propietarios' table,
                // adjust this query accordingly. For this example, I'll stick to 'clientes'
                // as per your original code.
                $sql_check = "SELECT ClienteID FROM clientes WHERE ClienteID = ?";
                $stmt_check = $conn->prepare($sql_check);
                if (!$stmt_check) {
                    throw new Exception("Error al preparar la consulta de verificación de propietario: " . $conn->error);
                }
                $stmt_check->bind_param("i", $propietarioID);
                $stmt_check->execute();
                $stmt_check->store_result();

                if ($stmt_check->num_rows === 0) {
                    echo '<div class="alert  bg-warning" role="alert">No se encontró un propietario con el ID proporcionado.</div>';
                    exit(); // Terminate script if owner does not exist
                }
                $stmt_check->close();

                // Get owner data
                $sql_propietario = "SELECT Fecha, Nombre, Apellido, Direccion, DNI, Telefono, Mail FROM clientes WHERE ClienteID = ?";
                $stmt_propietario = $conn->prepare($sql_propietario);
                if (!$stmt_propietario) {
                    throw new Exception("Error al preparar la consulta de datos del propietario: " . $conn->error);
                }
                $stmt_propietario->bind_param("i", $propietarioID);
                $stmt_propietario->execute();
                $result_propietario = $stmt_propietario->get_result();

                if ($result_propietario->num_rows > 0) {
                    $fila_propietario = $result_propietario->fetch_assoc();
                    ?>
                    <div class="card shadow-sm mb-4">
                        <div class="card-header text-white" style="background-color:rgba(233, 128, 0, 0.92);">
                            <h2 class="h5 mb-0">Datos del Propietario: <?php echo htmlspecialchars($fila_propietario['Nombre'] . ' ' . $fila_propietario['Apellido']); ?></h2>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <tbody>
                                        <tr><th>Fecha Ingreso</th>
                                        <td><?php echo htmlspecialchars($fila_propietario['Fecha']); ?></td></tr>
                                        <tr><th>Nombre</th><td><?php echo htmlspecialchars($fila_propietario['Nombre']); ?></td></tr>
                                        <tr><th>Apellido</th><td><?php echo htmlspecialchars($fila_propietario['Apellido']); ?></td></tr>
                                        <tr><th>Dirección</th><td><?php echo htmlspecialchars($fila_propietario['Direccion']); ?></td></tr>
                                        <tr><th>DNI</th><td><?php echo htmlspecialchars($fila_propietario['DNI']); ?></td></tr>
                                        <tr><th>Teléfono</th><td><?php echo htmlspecialchars($fila_propietario['Telefono']); ?></td></tr>
                                        <tr><th>Mail</th><td><?php echo htmlspecialchars($fila_propietario['Mail']); ?></td></tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php
// Obtener los garantes asociados al propietario
$sql_garantes = "SELECT Nombre, Apellido, DNI, Telefono, Mail, Fecha FROM garantes WHERE ClienteID = ?";
$stmt_garantes = $conn->prepare($sql_garantes);
if ($stmt_garantes) {
    $stmt_garantes->bind_param("i", $propietarioID);
    $stmt_garantes->execute();
    $result_garantes = $stmt_garantes->get_result();

    if ($result_garantes->num_rows > 0) {
        echo '<div class="card shadow-sm mb-4">';
        echo '<div class="card-header bg-secondary text-white">';
        echo '<h5 class="mb-0">Garantes Asociados</h5>';
        echo '</div>';
        echo '<div class="card-body">';
        echo '<div class="table-responsive">';
        echo '<table class="table table-bordered table-striped">';
        echo '<thead><tr><th>Fecha</th><th>Nombre</th><th>Apellido</th><th>DNI</th><th>Teléfono</th><th>Mail</th></tr></thead>';
        echo '<tbody>';
        while ($g = $result_garantes->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($g['Fecha']) . '</td>';
            echo '<td>' . htmlspecialchars($g['Nombre']) . '</td>';
            echo '<td>' . htmlspecialchars($g['Apellido']) . '</td>';
            echo '<td>' . htmlspecialchars($g['DNI']) . '</td>';
            echo '<td>' . htmlspecialchars($g['Telefono']) . '</td>';
            echo '<td>' . htmlspecialchars($g['Mail']) . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table></div></div></div>';
    } else {
        echo '<div class="alert alert-info">Este propietario no tiene garantes asociados.</div>';
    }
    $stmt_garantes->close();
} else {
    echo '<div class="alert alert-danger">Error al cargar los garantes: ' . $conn->error . '</div>';
}
?>

                    <?php
                } else {
                    echo '<div class="alert bg-warning" role="alert">No se encontraron datos del propietario.</div>';
                }
                $stmt_propietario->close();

                // Get properties associated with the owner
                // Obtener los garantes de contratos donde el propietario sea el cliente
$sql_inquilinos_vig = "
    SELECT 
        i.Nombre, i.Apellido, i.DNI, i.Telefono, i.Mail,
        p.Direccion AS DireccionPropiedad,
        c.fecha_inicio,
        c.fecha_fin,
        c.estado
    FROM contratos c
    JOIN inquilinos i ON c.InquilinoID = i.InquilinoID
    JOIN propiedades p ON c.PropiedadID = p.PropiedadID
    WHERE 
        c.ClienteID = ?
        AND c.estado = 'activo'
        AND c.fecha_fin >= CURDATE()
";

$stmt = $conn->prepare($sql_inquilinos_vig);
$stmt->bind_param("i", $propietarioID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    ?>
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-success text-white">
            <h2 class="h5 mb-0">Inquilinos con Contratos Vigentes</h2>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Inquilino</th>
                            <th>DNI</th>
                            <th>Teléfono</th>
                            <th>Mail</th>
                            <th>Propiedad</th>
                            <th>Inicio</th>
                            <th>Fin</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($i = $result->fetch_assoc()) { ?>
                            <tr>
                                <td><?= htmlspecialchars($i['Nombre'] . ' ' . $i['Apellido']) ?></td>
                                <td><?= htmlspecialchars($i['DNI']) ?></td>
                                <td><?= htmlspecialchars($i['Telefono']) ?></td>
                                <td><?= htmlspecialchars($i['Mail']) ?></td>
                                <td><?= htmlspecialchars($i['DireccionPropiedad']) ?></td>
                                <td><?= date('d/m/Y', strtotime($i['fecha_inicio'])) ?></td>
                                <td><?= date('d/m/Y', strtotime($i['fecha_fin'])) ?></td>
                                <td><?= ucfirst($i['estado']) ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php
} else {
    echo '<div class="alert alert-warning">No hay inquilinos con contratos vigentes.</div>';
}
$stmt->close();

$sql_inquilinos_hist = "
    SELECT 
        i.Nombre, i.Apellido, i.DNI, i.Telefono, i.Mail,
        p.Direccion AS DireccionPropiedad,
        c.fecha_inicio,
        c.fecha_fin,
        c.estado
    FROM contratos c
    JOIN inquilinos i ON c.InquilinoID = i.InquilinoID
    JOIN propiedades p ON c.PropiedadID = p.PropiedadID
    WHERE c.ClienteID = ?
";

$stmt = $conn->prepare($sql_inquilinos_hist);
$stmt->bind_param("i", $propietarioID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    ?>
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-secondary text-white">
            <h2 class="h5 mb-0">Historial Completo de Inquilinos</h2>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Inquilino</th>
                            <th>DNI</th>
                            <th>Teléfono</th>
                            <th>Mail</th>
                            <th>Propiedad</th>
                            <th>Inicio</th>
                            <th>Fin</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($i = $result->fetch_assoc()) { ?>
                            <tr>
                                <td><?= htmlspecialchars($i['Nombre'] . ' ' . $i['Apellido']) ?></td>
                                <td><?= htmlspecialchars($i['DNI']) ?></td>
                                <td><?= htmlspecialchars($i['Telefono']) ?></td>
                                <td><?= htmlspecialchars($i['Mail']) ?></td>
                                <td><?= htmlspecialchars($i['DireccionPropiedad']) ?></td>
                                <td><?= date('d/m/Y', strtotime($i['fecha_inicio'])) ?></td>
                                <td><?= date('d/m/Y', strtotime($i['fecha_fin'])) ?></td>
                                <td><?= ucfirst($i['estado']) ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php
} else {
    echo '<div class="alert alert-warning">No hay inquilinos históricos registrados.</div>';
}
$stmt->close();



            } else {
                echo '<div class="alert  bg-warning" role="alert">Error: El ID del propietario no es válido o no se proporcionó.</div>';
            }
        } catch (Exception $e) {
            echo '<div class="alert  bg-warning" role="alert">Ocurrió un error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        } finally {
            if (isset($conn) && $conn) {
                $conn->close(); // Close the connection here
            }
        }
        ?>
    </main>



    <div class="modal fade" id="editPropertyModal" tabindex="-1" aria-labelledby="editPropertyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPropertyModalLabel">Actualizar Propiedad</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="propiedadForm" action="actualizar_propiedad.php" method="POST">
                        <input type="hidden" id="propiedadID" name="propiedadID">
                        <div class="mb-3">
                            <label for="barrio" class="form-label">Barrio</label>
                            <input type="text" class="form-control" id="barrio" name="barrio" placeholder="Barrio" required>
                        </div>
                        <div class="mb-3">
                            <label for="ciudad" class="form-label">Ciudad</label>
                            <input type="text" class="form-control" id="ciudad" name="ciudad" placeholder="Ciudad" required>
                        </div>
                        <div class="mb-3">
                            <label for="direccion" class="form-label">Dirección</label>
                            <input type="text" class="form-control" id="direccion" name="direccion" placeholder="Dirección" required>
                        </div>
                        <div class="mb-3">
                            <label for="nro" class="form-label">Nro</label>
                            <input type="text" class="form-control" id="nro" name="nro" placeholder="Nro">
                        </div>
                        <div class="mb-3">
                            <label for="dominio" class="form-label">Dominio</label>
                            <input type="text" class="form-control" id="dominio" name="dominio" placeholder="Dominio" required>
                        </div>
                        <div class="mb-3">
                            <label for="nroPartida" class="form-label">Nro Partida</label>
                            <input type="text" class="form-control" id="nroPartida" name="nroPartida" placeholder="Nro Partida" required>
                        </div>
                        <div class="mb-3">
                            <label for="estado" class="form-label">Estado</label>
                            <select class="form-control" id="estado" name="estado">
                                <option value="alquilada">Alquilada</option>
                                <option value="en venta">En Venta</option>
                                <option value="disponible">Disponible</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary" style="background-color:rgba(233, 128, 0, 0.92); border-color:rgba(233, 128, 0, 0.92);">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Script to load data into the property edit modal
            document.querySelectorAll('.editar-propiedad').forEach(function (button) {
                button.addEventListener('click', function () {
                    const propiedadID = button.getAttribute('data-id');
                    const direccion = button.getAttribute('data-direccion');
                    const ciudad = button.getAttribute('data-ciudad');
                    const barrio = button.getAttribute('data-barrio');
                    const nro = button.getAttribute('data-nro');
                    const dominio = button.getAttribute('data-dominio');
                    const nroPartida = button.getAttribute('data-nropartida');
                    const estado = button.getAttribute('data-estado');

                    document.getElementById('propiedadID').value = propiedadID;
                    document.getElementById('direccion').value = direccion;
                    document.getElementById('ciudad').value = ciudad;
                    document.getElementById('barrio').value = barrio;
                    document.getElementById('nro').value = nro;
                    document.getElementById('dominio').value = dominio;
                    document.getElementById('nroPartida').value = nroPartida;
                    document.getElementById('estado').value = estado;

                    document.getElementById('editPropertyModalLabel').textContent = 'Editar Propiedad';
                });
            });

            // Clear the modal when it is closed
            document.getElementById('editPropertyModal').addEventListener('hidden.bs.modal', function () {
                document.getElementById('propiedadForm').reset(); // Clear the form
                document.getElementById('editPropertyModalLabel').textContent = 'Actualizar Propiedad'; // Restore the title
            });

            // If you want to initialize DataTables for the owner's properties table on this page,
            // uncomment the DataTables JS script in the head and the following code block:
            /*
            $(document).ready(function() {
                $('#garantesPropietarioTable').DataTable({
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/2.0.0/i18n/es-ES.json"
                    },
                    "paging": true,
                    "searching": true,
                    "ordering": true,
                    "info": true
                });
            });
            */
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
            console.log("Notificaciones:", data);

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
                    li.classList.add('p-2', 'border-bottom');

                    const contenido = document.createElement('div');
                    contenido.innerHTML = `
                        <strong>Contrato #${c.id}</strong><br>
                        ${c.direccion}<br>
                        <small>Vence: ${c.fecha_fin}</small>
                    `;

                    const acciones = document.createElement('div');
                    acciones.classList.add('d-flex', 'flex-column', 'align-items-end', 'mt-1');

                    const verBtn = document.createElement('a');
                    verBtn.href = `ver_contrato.php?id=${c.id}`;
                    verBtn.target = '_blank';
                    

                    const cerrarBtn = document.createElement('button');
                    cerrarBtn.className = 'btn btn-sm btn-outline-secondary';
                    cerrarBtn.textContent = 'Leído';
                    cerrarBtn.onclick = () => {
                        fetch('marcar_leido.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: 'contrato_id=' + c.id
                        }).then(res => res.json())
                          .then(resp => {
                              if (resp.success) {
                                  li.remove();
                                  const restantes = document.querySelectorAll('#notiDropdown li').length;
                                  badge.textContent = restantes;
                                  if (restantes === 0) {
                                      badge.classList.add('d-none');
                                      dropdown.innerHTML = '<div class="p-2 text-muted">No hay vencimientos próximos</div>';
                                  }
                              }
                          });
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

    btnNoti.addEventListener('click', () => {
        dropdown.classList.toggle('d-none');
    });
});
</script>


</body>
</html>