<?php
session_start();

// Display session messages (alerts) if they exist
if (isset($_SESSION['mensaje'])) {
    echo "<script>alert('" . $_SESSION['mensaje'] . "');</script>";
    unset($_SESSION['mensaje']); // Clear the message after displaying it
}

// Include the database connection
// Ensure 'conexion.php' properly establishes and returns a $conn object (e.g., new mysqli(...)).
include_once 'conexion.php';

// Check if $conn is set, if not, there was an issue with conexion.php
if (!isset($conn) || $conn->connect_error) {
    die("Error de conexión a la base de datos.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión Inmobiliaria - Detalle del Propietario</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=menu" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    </head>
<body>
    <header>
        <div class="header-content">
            <div class="dropdown">
                <button type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Menú de Navegación">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#000000">
                        <path d="M120-240v-80h720v80H120Zm0-200v-80h720v80H120Zm0-200v-80h720v80H120Z"/>
                    </svg>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">📆 Pagos del Mes</a></li>
                    </ul>
                </button>
            </div>

            <img src="logo2.png" alt="Logo Inmobiliaria" class="logo">

            <div>
                <div class="dropdown">
                    <button type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Opciones de Usuario">
                        👤
                        🔔
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">🔑 Cambiar Clave</a></li>
                            <li><a class="dropdown-item" href="logout.php">
                                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#000000">
                                    <path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h280v80H200v560h280v80H200Zm440-160-55-58 102-102H360v-80h327L585-622l55-58 200 200-200 200Z"/>
                                </svg> Cerrar sesión</a></li>
                        </ul>
                    </button>
                </div>
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
                $sql_propietario = "SELECT Nombre, Apellido, DNI, Telefono FROM clientes WHERE ClienteID = ?";
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
                                        <tr><th>Nombre</th><td><?php echo htmlspecialchars($fila_propietario['Nombre']); ?></td></tr>
                                        <tr><th>Apellido</th><td><?php echo htmlspecialchars($fila_propietario['Apellido']); ?></td></tr>
                                        <tr><th>DNI</th><td><?php echo htmlspecialchars($fila_propietario['DNI']); ?></td></tr>
                                        <tr><th>Teléfono</th><td><?php echo htmlspecialchars($fila_propietario['Telefono']); ?></td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php
                } else {
                    echo '<div class="alert bg-warning" role="alert">No se encontraron datos del propietario.</div>';
                }
                $stmt_propietario->close();

                // Get properties associated with the owner
                $sql_propiedades = "SELECT PropiedadID, Direccion, Ciudad, Barrio, Nro, Dominio, NroPartida, Estado
                                    FROM propiedades
                                    WHERE ClienteID = ?"; // Assuming ClienteID in properties refers to the owner
                $stmt_propiedades = $conn->prepare($sql_propiedades);
                if (!$stmt_propiedades) {
                    throw new Exception("Error al preparar la consulta de propiedades: " . $conn->error);
                }
                $stmt_propiedades->bind_param("i", $propietarioID);
                $stmt_propiedades->execute();
                $result_propiedades = $stmt_propiedades->get_result();

                if ($result_propiedades->num_rows > 0) {
                    ?>
                    <div class="card shadow-sm mb-4">
                        <div class="card-header text-white" style="background-color:rgba(233, 128, 0, 0.92);">
                            <h2 class="h5 mb-0">Propiedades del Propietario</h2>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="propiedadesPropietarioTable" class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Dirección</th>
                                            <th>Ciudad</th>
                                            <th>Barrio</th>
                                            <th>Nro</th>
                                            <th>Dominio</th>
                                            <th>Nro Partida</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($fila_propiedad = $result_propiedades->fetch_assoc()) { ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($fila_propiedad['Direccion']); ?></td>
                                                <td><?php echo htmlspecialchars($fila_propiedad['Ciudad']); ?></td>
                                                <td><?php echo htmlspecialchars($fila_propiedad['Barrio']); ?></td>
                                                <td><?php echo htmlspecialchars($fila_propiedad['Nro']); ?></td>
                                                <td><?php echo htmlspecialchars($fila_propiedad['Dominio']); ?></td>
                                                <td><?php echo htmlspecialchars($fila_propiedad['NroPartida']); ?></td>
                                                <td><?php echo htmlspecialchars($fila_propiedad['Estado']); ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-warning btn-sm editar-propiedad"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editPropertyModal"
                                                            data-id="<?php echo htmlspecialchars($fila_propiedad['PropiedadID']); ?>"
                                                            data-direccion="<?php echo htmlspecialchars($fila_propiedad['Direccion']); ?>"
                                                            data-ciudad="<?php echo htmlspecialchars($fila_propiedad['Ciudad']); ?>"
                                                            data-barrio="<?php echo htmlspecialchars($fila_propiedad['Barrio']); ?>"
                                                            data-nro="<?php echo htmlspecialchars($fila_propiedad['Nro']); ?>"
                                                            data-dominio="<?php echo htmlspecialchars($fila_propiedad['Dominio']); ?>"
                                                            data-nropartida="<?php echo htmlspecialchars($fila_propiedad['NroPartida']); ?>"
                                                            data-estado="<?php echo htmlspecialchars($fila_propiedad['Estado']); ?>">
                                                        <i class="fas fa-edit"></i> Editar
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php
                } else {
                    echo '<div class="alert  bg-warning" role="alert">No se encontraron propiedades para este propietario.</div>';
                }
                $stmt_propiedades->close();
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
                $('#propiedadesPropietarioTable').DataTable({
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
</body>
</html>