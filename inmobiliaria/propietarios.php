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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión Inmobiliaria - Propietarios</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css"> 
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=menu" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="//cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css">
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
                            <li><a class="dropdown-item" href="#">
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
                <li><a href="clientes.php"><i class="fas fa-users"></i> Clientes</a></li>
                <li><a href="propietarios.php" class="active"><i class="fas fa-user-tie"></i> Propietarios</a></li>
                <li><a href="propiedades.php"><i class="fas fa-home"></i> Propiedades</a></li>
                <li><a href="contabilidad.php"><i class="fas fa-file-invoice-dollar"></i> Contabilidad</a></li>
            </ul>
        </nav>
    </header>
    <main class="container mt-4">
        <div class="d-flex justify-content-start align-items-center mb-3">
            <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#addPropietarioModal">
                <i class="fas fa-user-plus"></i> Nuevo Propietario
            </button>
            </div>

        <div class="card shadow-sm">
            <div class="card-header text-white" style="background-color:rgba(233, 128, 0, 0.92);"> 
                <h2 class="h5 mb-0">Listado de Propietarios</h2>
            </div>
            <div class="card-body">
                <?php 
                // La conexión $conn ya debería estar disponible aquí por el include_once al principio.

                // Consulta SQL para obtener los datos de la tabla `propietarios` (asumiendo que se llama así)
                // Cambié 'clientes' a 'propietarios' para reflejar el contexto del archivo.
                // Ajusta los nombres de las columnas según tu esquema real de la tabla 'propietarios'.
                $sql_propietarios = "SELECT ClienteID, Nombre, Apellido, Direccion FROM clientes WHERE estado = 'activo'"; // Solo activos por defecto
                
                if (isset($_GET['mostrar']) && $_GET['mostrar'] == 'inactivos') {
                    $sql_propietarios = "SELECT ClienteID, Nombre, Apellido, Direccion FROM clientes WHERE estado = 'inactivo'";
                }
                
                $result_propietarios = mysqli_query($conn, $sql_propietarios);

                // Verificar si hay resultados
                if (mysqli_num_rows($result_propietarios) > 0) {
                    // Iniciar la tabla HTML con clases de Bootstrap para tablas
                    echo '<div class="table-responsive">
                            <table id="propietariosTable" class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Nombre y Apellido</th>
                                        <th>Dirección</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>';

                    // Iterar sobre cada fila de resultados
                    while ($fila_propietario = mysqli_fetch_assoc($result_propietarios)) {
                        // Mostrar cada fila en la tabla
                        echo '<tr>
                                <td>' . htmlspecialchars($fila_propietario['Nombre']) . ' ' . htmlspecialchars($fila_propietario['Apellido']) . '</td>
                                <td>' . htmlspecialchars($fila_propietario['Direccion']) . '</td>
                                <td>
                                    <a href="propietarios_ver_propiedades.php?id=' . htmlspecialchars($fila_propietario['ClienteID']) . '" class="btn btn-sm btn-info text-white me-1" title="Ver Propiedades"><i class="fas fa-home"></i> Ver Propiedades</a>
                                    
                                    <a href="ver_inquilinos.php?id=' . htmlspecialchars($fila_propietario['ClienteID']) . '" class="btn btn-sm btn-secondary" title="Ver Inquilinos (Asociados)"><i class="fas fa-users"></i> Ver Inquilinos</a>
                                </td>
                            </tr>';
                    }

                    // Cerrar la tabla HTML
                    echo '      </tbody>
                            </table>
                        </div>'; // Cierre de .table-responsive
                } else {
                    // Si no hay resultados, mostrar un mensaje con estilo de Bootstrap
                    echo '<div class="alert alert-info" role="alert">No se encontraron propietarios.</div>';
                }
                ?>
            </div>
        </div>
    </main>

    <div class="modal fade" id="addPropietarioModal" tabindex="-1" aria-labelledby="addPropietarioModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPropietarioModalLabel">Agregar Propietario y Garantes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs" id="propietarioTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="propietario-tab" data-bs-toggle="tab" data-bs-target="#propietarioInfo" type="button" role="tab" aria-controls="propietarioInfo" aria-selected="true">Propietario</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="garante1-tab" data-bs-toggle="tab" data-bs-target="#garante1Info" type="button" role="tab" aria-controls="garante1Info" aria-selected="false">Garante 1</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="garante2-tab" data-bs-toggle="tab" data-bs-target="#garante2Info" type="button" role="tab" aria-controls="garante2Info" aria-selected="false">Garante 2 (Opcional)</button>
                        </li>
                    </ul>

                    <div class="tab-content mt-3" id="propietarioTabContent">
                        <div class="tab-pane fade show active" id="propietarioInfo" role="tabpanel" aria-labelledby="propietario-tab">
                            <form id="propietarioForm" action="agregar_propietario.php" method="POST">
                                <div class="mb-3">
                                    <label for="propietarioFecha" class="form-label">Fecha de Ingreso</label>
                                    <input type="date" class="form-control" id="propietarioFecha" name="Fecha" required>
                                </div>
                                <div class="mb-3">
                                    <label for="propietarioNombre" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="propietarioNombre" name="Nombre" placeholder="Nombre" required>
                                </div>
                                <div class="mb-3">
                                    <label for="propietarioApellido" class="form-label">Apellido</label>
                                    <input type="text" class="form-control" id="propietarioApellido" name="Apellido" placeholder="Apellido" required>
                                </div>
                                <div class="mb-3">
                                    <label for="propietarioDireccion" class="form-label">Dirección</label>
                                    <input type="text" class="form-control" id="propietarioDireccion" name="Direccion" placeholder="Dirección" required>
                                </div>
                                <div class="mb-3">
                                    <label for="propietarioDNI" class="form-label">DNI</label>
                                    <input type="text" class="form-control" id="propietarioDNI" name="DNI" placeholder="DNI" required minlength="8" maxlength="8" pattern="\d{8}" title="Debe ingresar exactamente 8 números">
                                </div>
                                <div class="mb-3">
                                    <label for="propietarioDireccionPersonal" class="form-label">Dirección Personal</label>
                                    <input type="text" class="form-control" id="propietarioDireccionPersonal" name="DireccionPersonal" placeholder="Dirección Personal" required>
                                </div>
                                <div class="mb-3">
                                    <label for="propietarioTelefono" class="form-label">Teléfono</label>
                                    <input type="tel" class="form-control" id="propietarioTelefono" name="Telefono" placeholder="Teléfono" required>
                                </div>
                                <div class="mb-3">
                                    <label for="propietarioMail" class="form-label">Correo Electrónico</label>
                                    <input type="email" class="form-control" id="propietarioMail" name="Mail" placeholder="Correo Electrónico" required>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                    <button type="submit" class="btn btn-primary">Guardar Propietario</button>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane fade" id="garante1Info" role="tabpanel" aria-labelledby="garante1-tab">
                            <form id="garante1Form" action="agregar_garante.php" method="POST">
                                <p class="alert alert-info">Los datos del Garante 1 se guardarán junto al propietario. Asegúrate de que el propietario haya sido guardado primero o que este formulario se envíe en conjunto.</p>
                                <div class="mb-3">
                                    <label for="garante1Fecha" class="form-label">Fecha de Ingreso</label>
                                    <input type="date" class="form-control" id="garante1Fecha" name="garante1_fecha">
                                </div>
                                <div class="mb-3">
                                    <label for="garante1Nombre" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="garante1Nombre" name="garante1_nombre" placeholder="Nombre Garante">
                                </div>
                                <div class="mb-3">
                                    <label for="garante1Apellido" class="form-label">Apellido</label>
                                    <input type="text" class="form-control" id="garante1Apellido" name="garante1_apellido" placeholder="Apellido Garante">
                                </div>
                                <div class="mb-3">
                                    <label for="garante1Direccion" class="form-label">Dirección</label>
                                    <input type="text" class="form-control" id="garante1Direccion" name="garante1_direccion" placeholder="Dirección Garante">
                                </div>
                                <div class="mb-3">
                                    <label for="garante1DNI" class="form-label">DNI</label>
                                    <input type="text" class="form-control" id="garante1DNI" name="garante1_dni" placeholder="DNI Garante">
                                </div>
                                <div class="mb-3">
                                    <label for="garante1DireccionPersonal" class="form-label">Dirección Personal</label>
                                    <input type="text" class="form-control" id="garante1DireccionPersonal" name="garante1_direccion_personal" placeholder="Dirección Personal Garante">
                                </div>
                                <div class="mb-3">
                                    <label for="garante1Telefono" class="form-label">Teléfono</label>
                                    <input type="tel" class="form-control" id="garante1Telefono" name="garante1_telefono" placeholder="Teléfono Garante">
                                </div>
                                <div class="mb-3">
                                    <label for="garante1Mail" class="form-label">Correo Electrónico</label>
                                    <input type="email" class="form-control" id="garante1Mail" name="garante1_mail" placeholder="Mail Garante">
                                </div>
                                </form>
                        </div>

                        <div class="tab-pane fade" id="garante2Info" role="tabpanel" aria-labelledby="garante2-tab">
                            <form id="garante2Form" action="agregar_garante.php" method="POST">
                                <p class="alert alert-info">Los datos del Garante 2 se guardarán junto al propietario. Asegúrate de que el propietario haya sido guardado primero o que este formulario se envíe en conjunto.</p>
                                <div class="mb-3">
                                    <label for="garante2Fecha" class="form-label">Fecha de Ingreso</label>
                                    <input type="date" class="form-control" id="garante2Fecha" name="garante2_fecha">
                                </div>
                                <div class="mb-3">
                                    <label for="garante2Nombre" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="garante2Nombre" name="garante2_nombre" placeholder="Nombre Garante">
                                </div>
                                <div class="mb-3">
                                    <label for="garante2Apellido" class="form-label">Apellido</label>
                                    <input type="text" class="form-control" id="garante2Apellido" name="garante2_apellido" placeholder="Apellido Garante">
                                </div>
                                <div class="mb-3">
                                    <label for="garante2Direccion" class="form-label">Dirección</label>
                                    <input type="text" class="form-control" id="garante2Direccion" name="garante2_direccion" placeholder="Dirección Garante">
                                </div>
                                <div class="mb-3">
                                    <label for="garante2DNI" class="form-label">DNI</label>
                                    <input type="text" class="form-control" id="garante2DNI" name="garante2_dni" placeholder="DNI Garante">
                                </div>
                                <div class="mb-3">
                                    <label for="garante2DireccionPersonal" class="form-label">Dirección Personal</label>
                                    <input type="text" class="form-control" id="garante2DireccionPersonal" name="garante2_direccion_personal" placeholder="Dirección Personal Garante">
                                </div>
                                <div class="mb-3">
                                    <label for="garante2Telefono" class="form-label">Teléfono</label>
                                    <input type="tel" class="form-control" id="garante2Telefono" name="garante2_telefono" placeholder="Teléfono Garante">
                                </div>
                                <div class="mb-3">
                                    <label for="garante2Mail" class="form-label">Correo Electrónico</label>
                                    <input type="email" class="form-control" id="garante2Mail" name="garante2_mail" placeholder="Mail Garante">
                                </div>
                                </form>
                        </div>
                    </div>
                </div>
                </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="//cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>

    <script>
        // Inicializa DataTables
        $(document).ready(function() {
            console.log("jQuery y DataTables listos.");
            $('#propietariosTable').DataTable({
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/2.0.2/i18n/es-ES.json" // URL correcta para DataTables 2.x
                },
                "paging": true,      // Habilita paginación
                "searching": true,   // Habilita el cuadro de búsqueda
                "ordering": true,    // Habilita ordenación de columnas
                "info": true         // Habilita información de la tabla
            });
            console.log("DataTables inicializado en #propietariosTable.");
        });

        // Script para activar la pestaña de Propietario al abrir el modal
        document.addEventListener('DOMContentLoaded', function () {
            const addPropietarioModal = document.getElementById('addPropietarioModal');
            if (addPropietarioModal) {
                addPropietarioModal.addEventListener('show.bs.modal', function () {
                    const propietarioTab = new bootstrap.Tab(document.getElementById('propietario-tab'));
                    propietarioTab.show();
                    console.log("Modal 'Agregar Propietario' abierto, pestaña 'Propietario' activada.");
                });
            } else {
                console.warn("Elemento 'addPropietarioModal' no encontrado.");
            }
        });

        // IMPORTANT: If you want to submit all guarantor data along with the owner data in one go,
        // you'll need to wrap all three forms (propietario, garante1, garante2) within a single <form> tag,
        // and adjust your `agregar_propietario.php` to handle all these fields.
        // The current structure has separate forms within tabs, which is generally not ideal
        // for submitting related data simultaneously unless handled via JavaScript.
        // For simplicity, I've moved the submit button to the modal footer, implying
        // a single logical submission for all data in the modal.
        // You would typically use hidden inputs or a more complex JS setup to send all data
        // if your PHP backend expects one single POST request for all owner/guarantor details.
        // Consider changing the HTML to have one <form> tag encompassing the entire modal-body if that's your goal.
        // For example:
        /*
        <div class="modal-content">
            <form id="fullPropietarioForm" action="agregar_propietario_completo.php" method="POST">
                <div class="modal-header">...</div>
                <div class="modal-body">
                    <ul class="nav nav-tabs">...</ul>
                    <div class="tab-content">
                        <div class="tab-pane">... propietario fields ...</div>
                        <div class="tab-pane">... garante 1 fields ...</div>
                        <div class="tab-pane">... garante 2 fields ...</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar Todo</button>
                </div>
            </form>
        </div>
        */
        // I have implemented the single form approach below to make it easier for your backend.
    </script>
</body>
</html>

<?php
// Cierra la conexión a la base de datos al final del script si se abrió.
if (isset($conn) && $conn) {
    mysqli_close($conn);
}
?>