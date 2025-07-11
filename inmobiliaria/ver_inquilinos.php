<?php
session_start();

// Muestra mensajes de sesión (alertas) si existen
if (isset($_SESSION['mensaje'])) {
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                var alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-info alert-dismissible fade show mt-3';
                alertDiv.setAttribute('role', 'alert');
                alertDiv.innerHTML = '" . htmlspecialchars($_SESSION['mensaje']) . "<button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"Close\"></button>';
                document.querySelector('main.container').prepend(alertDiv); // Añadir al principio del main
            });
          </script>";
    unset($_SESSION['mensaje']); // Elimina el mensaje después de mostrarlo
}

// Incluir la conexión a la base de datos
include_once 'conexion.php'; 

// Asegúrate de que $conn esté disponible globalmente o sea devuelta por 'conexion.php'.
// Por ejemplo, 'conexion.php' podría contener:
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
    <title>Sistema de Gestión Inmobiliaria - Detalle del Cliente</title>

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
                <li><a href="propietarios.php" class="active"><i class="fas fa-user-tie"></i> Propietarios</a></li>
                <li><a href="propiedades.php"><i class="fas fa-home"></i> Propiedades</a></li>
                <li><a href="contabilidad.php"><i class="fas fa-file-invoice-dollar"></i> Contabilidad</a></li>
            </ul>
        </nav>
    </header>
    <main class="container mt-4">
        <div class="mb-3">
            <a href="propietarios.php" class="btn btn-secondary" style="background-color: rgba(233, 128, 0, 0.92);"><i class="fas fa-arrow-left"></i> Volver a Propietarios</a>
        </div>
        
        <?php
        try {
            // Verifica si se recibió un ClienteID válido
            if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                $clienteID = (int)$_GET['id']; // Asegurar que es un número entero

                // Consulta para obtener los datos del cliente
                $sql_cliente = "SELECT Nombre, Apellido, DNI, Telefono, Mail FROM clientes WHERE ClienteID = ?";
                $stmt_cliente = $conn->prepare($sql_cliente);
                $stmt_cliente->bind_param("i", $clienteID);
                $stmt_cliente->execute();
                $result_cliente = $stmt_cliente->get_result();
                $cliente = $result_cliente->fetch_assoc();
                $stmt_cliente->close();

                // Consulta para obtener los inquilinos y sus propiedades asociadas a este cliente (propietario)
                $sql_inquilinos = "SELECT 
                                    i.Nombre AS InquilinoNombre,
                                    i.Apellido AS InquilinoApellido,
                                    i.DNI AS InquilinoDNI,
                                    i.Telefono AS InquilinoTelefono,
                                    i.Mail AS InquilinoMail,
                                    p.Direccion AS PropiedadDireccion,
                                    i.InquilinoID
                                FROM inquilinos i
                                JOIN propiedades p ON i.PropiedadID = p.PropiedadID
                                WHERE p.ClienteID = ?"; // Asume que ClienteID en `propiedades` es el propietario de la propiedad
                $stmt_inquilinos = $conn->prepare($sql_inquilinos);
                $stmt_inquilinos->bind_param("i", $clienteID);
                $stmt_inquilinos->execute();
                $result_inquilinos = $stmt_inquilinos->get_result();

                // Consulta para obtener los garantes de los inquilinos asociados a este cliente (propietario)
                $sql_garantes = "SELECT 
                                    g.Nombre AS GaranteNombre,
                                    g.Apellido AS GaranteApellido,
                                    g.DNI AS GaranteDNI,
                                    g.Direccion AS GaranteDireccion,
                                    g.Telefono AS GaranteTelefono,
                                    g.Mail AS GaranteMail,
                                    p.Direccion AS PropiedadDireccion,
                                    i.InquilinoID,
                                    CONCAT(i.Nombre, ' ', i.Apellido) AS NombreCompletoInquilino
                                FROM GarantesInquilinos g
                                JOIN inquilinos i ON g.InquilinoID = i.InquilinoID
                                JOIN propiedades p ON i.PropiedadID = p.PropiedadID
                                WHERE p.ClienteID = ?";
                $stmt_garantes = $conn->prepare($sql_garantes);
                $stmt_garantes->bind_param("i", $clienteID);
                $stmt_garantes->execute();
                $result_garantes = $stmt_garantes->get_result();

                // Mostrar los datos del cliente
                if ($cliente) {
                    ?>
                    <div class="card shadow-sm mb-4">
                        <div class="card-header text-white" style="background-color:rgba(233, 128, 0, 0.92);">
                            <h2 class="h5 mb-0">Datos del Cliente: <?php echo htmlspecialchars($cliente['Nombre'] . ' ' . $cliente['Apellido']); ?></h2>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <tbody>
                                        <tr><th>Nombre</th><td><?php echo htmlspecialchars($cliente['Nombre']); ?></td></tr>
                                        <tr><th>Apellido</th><td><?php echo htmlspecialchars($cliente['Apellido']); ?></td></tr>
                                        <tr><th>DNI</th><td><?php echo htmlspecialchars($cliente['DNI']); ?></td></tr>
                                        <tr><th>Teléfono</th><td><?php echo htmlspecialchars($cliente['Telefono']); ?></td></tr>
                                        <tr><th>Mail</th><td><?php echo htmlspecialchars($cliente['Mail']); ?></td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php
                } else {
                    echo '<div class="alert alert-danger" role="alert">No se encontraron datos de este cliente.</div>';
                }

                // Mostrar los datos de los inquilinos
                ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-header text-white" style="background-color:rgba(233, 128, 0, 0.92);">
                        <h2 class="h5 mb-0">Inquilinos Asociados</h2>
                    </div>
                    <div class="card-body">
                        <?php if ($result_inquilinos->num_rows > 0) { ?>
                            <div class="table-responsive">
                                <table id="inquilinosTable" class="table table-striped table-hover table-bordered">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Apellido</th>                                            
                                            <th>DNI</th>
                                            <th>Teléfono</th>
                                            <th>Mail</th>
                                            <th>Dirección (Propiedad)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($inquilino = $result_inquilinos->fetch_assoc()) { ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($inquilino['InquilinoNombre']); ?></td>
                                                <td><?php echo htmlspecialchars($inquilino['InquilinoApellido']); ?></td>                                               
                                                <td><?php echo htmlspecialchars($inquilino['InquilinoDNI']); ?></td>                                             
                                                <td><?php echo htmlspecialchars($inquilino['InquilinoTelefono']); ?></td>
                                                <td><?php echo htmlspecialchars($inquilino['InquilinoMail']); ?></td>
                                                <td><?php echo htmlspecialchars($inquilino['PropiedadDireccion']); ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } else {
                            echo '<div class="alert bg-warning" role="alert">No se encontraron inquilinos asociados a este cliente.</div>';
                        } ?>
                    </div>
                </div>

                <?php
                // Mostrar los datos de los garantes (puedes optar por cargarlos dinámicamente en el modal si hay muchos)
                // Para este ejemplo, los mantendré listados pero también se cargarán en el modal.
                ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-header text-white" style="background-color:rgba(233, 128, 0, 0.92);">
                        <h2 class="h5 mb-0">Garantes Asociados</h2>
                    </div>
                    <div class="card-body">
                        <?php if ($result_garantes->num_rows > 0) { ?>
                            <div class="table-responsive">
                                <table id="garantesTable" class="table table-striped table-hover table-bordered">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Apellido</th>
                                            <th>DNI</th>
                                            
                                            <th>Teléfono</th>
                                            <th>Mail</th>
                                            <th>Inquilino Asociado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($garante = $result_garantes->fetch_assoc()) { ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($garante['GaranteNombre']); ?></td>
                                                <td><?php echo htmlspecialchars($garante['GaranteApellido']); ?></td>
                                                <td><?php echo htmlspecialchars($garante['GaranteDNI']); ?></td>
                                              
                                                <td><?php echo htmlspecialchars($garante['GaranteTelefono']); ?></td>
                                                <td><?php echo htmlspecialchars($garante['GaranteMail']); ?></td>
                                                <td><?php echo htmlspecialchars($garante['NombreCompletoInquilino']); ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } else {
                            echo '<div class="alert bg-warning" role="alert">No se encontraron garantes para los inquilinos de este cliente.</div>';
                        } ?>
                    </div>
                </div>

                <?php
                $stmt_inquilinos->close();
                $stmt_garantes->close();
            } else {
                echo '<div class="alert alert-danger" role="alert">No se recibió un ID de cliente válido.</div>';
            }
        } catch (Exception $e) {
            echo '<div class="alert alert-danger" role="alert">Ocurrió un error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        } finally {
            if (isset($conn) && $conn) {
                $conn->close();
            }
        }
        ?>
    </main>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg"> <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="exampleModalLabel">Agregar Nuevo Cliente y Garantes</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs" id="addClientTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="add-cliente-tab" data-bs-toggle="tab" data-bs-target="#add-cliente" type="button" role="tab" aria-controls="add-cliente" aria-selected="true">Datos del Cliente</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="add-garante1-tab" data-bs-toggle="tab" data-bs-target="#add-garante1" type="button" role="tab" aria-controls="add-garante1" aria-selected="false">Garante 1</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="add-garante2-tab" data-bs-toggle="tab" data-bs-target="#add-garante2" type="button" role="tab" aria-controls="add-garante2" aria-selected="false">Garante 2</button>
                        </li>
                    </ul>

                    <div class="tab-content" id="addClientTabContent">
                        <div class="tab-pane fade show active" id="add-cliente" role="tabpanel" aria-labelledby="add-cliente-tab">
                            <br>
                            <form id="clienteForm" action="agregar_clientes.php" method="POST">
                                <div class="mb-3">
                                    <label for="fecha" class="form-label">Fecha de Ingreso</label>
                                    <input type="date" class="form-control" id="fecha" name="Fecha" required>
                                </div>
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="nombre" name="Nombre" placeholder="Nombre" required>
                                </div>
                                <div class="mb-3">
                                    <label for="apellido" class="form-label">Apellido</label>
                                    <input type="text" class="form-control" id="apellido" name="Apellido" placeholder="Apellido" required>
                                </div>
                                <div class="mb-3">
                                    <label for="direccion" class="form-label">Dirección (de la propiedad)</label>
                                    <input type="text" class="form-control" id="direccion" name="Direccion" placeholder="Dirección de la propiedad" required>
                                </div>
                                <div class="mb-3">
                                    <label for="dni" class="form-label">DNI</label>
                                    <input type="text" class="form-control" id="dni" name="DNI" placeholder="DNI" required minlength="8" maxlength="8" pattern="\d{8}" title="Debe ingresar exactamente 8 números">
                                </div>
                                <div class="mb-3">
                                    <label for="direccion_personal" class="form-label">Dirección Personal</label>
                                    <input type="text" class="form-control" id="direccion_personal" name="DireccionPersonal" placeholder="Dirección Personal" required>
                                </div>
                                <div class="mb-3">
                                    <label for="telefono" class="form-label">Teléfono</label>
                                    <input type="tel" class="form-control" id="telefono" name="Telefono" placeholder="Teléfono" required>
                                </div>
                                <div class="mb-3">
                                    <label for="mail" class="form-label">Correo Electrónico</label>
                                    <input type="email" class="form-control" id="mail" name="Mail" placeholder="Correo Electrónico" required>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                    <button type="submit" class="btn btn-primary">Guardar Cliente</button>
                                </div>
                            </form>
                        </div>
                        
                        <div class="tab-pane fade" id="add-garante1" role="tabpanel" aria-labelledby="add-garante1-tab">
                            <br>
                            <form id="garante1Form" action="agregar_garante.php" method="POST"> <input type="hidden" name="inquilino_id_garante1" id="inquilinoIdGarante1"> <div class="mb-3">
                                    <label for="garante1_fecha" class="form-label">Fecha Garante 1</label>
                                    <input type="date" class="form-control" id="garante1_fecha" name="garante1_fecha" placeholder="Fecha Garante ">
                                </div>
                                <div class="mb-3">
                                    <label for="garante1_nombre" class="form-label">Nombre Garante 1</label>
                                    <input type="text" class="form-control" id="garante1_nombre" name="garante1_nombre" placeholder="Nombre Garante ">
                                </div>
                                <div class="mb-3">
                                    <label for="garante1_apellido" class="form-label">Apellido Garante 1</label>
                                    <input type="text" class="form-control" id="garante1_apellido" name="garante1_apellido" placeholder="Apellido Garante ">
                                </div>
                                <div class="mb-3">
                                    <label for="garante1_direccion" class="form-label">Dirección Garante 1</label>
                                    <input type="text" class="form-control" id="garante1_direccion" name="garante1_direccion" placeholder="Direccion Garante ">
                                </div>
                                <div class="mb-3">
                                    <label for="garante1_dni" class="form-label">DNI Garante 1</label>
                                    <input type="text" class="form-control" id="garante1_dni" name="garante1_dni" placeholder="DNI Garante ">
                                </div>
                                <div class="mb-3">
                                    <label for="garante1_direccion_personal" class="form-label">Dirección Personal Garante 1</label>
                                    <input type="text" class="form-control" id="garante1_direccion_personal" name="garante1_direccion_personal" placeholder="Direccion Personal Garante ">
                                </div>
                                <div class="mb-3">
                                    <label for="garante1_telefono" class="form-label">Teléfono Garante 1</label>
                                    <input type="text" class="form-control" id="garante1_telefono" name="garante1_telefono" placeholder="Telefono Garante ">
                                </div>
                                <div class="mb-3">
                                    <label for="garante1_mail" class="form-label">Mail Garante 1</label>
                                    <input type="text" class="form-control" id="garante1_mail" name="garante1_mail" placeholder="Mail Garante ">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                    <button type="submit" class="btn btn-primary">Guardar Garante 1</button>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane fade" id="add-garante2" role="tabpanel" aria-labelledby="add-garante2-tab">
                            <br>
                            <form id="garante2Form" action="agregar_garante.php" method="POST"> <input type="hidden" name="inquilino_id_garante2" id="inquilinoIdGarante2"> <div class="mb-3">
                                    <label for="garante2_fecha" class="form-label">Fecha Garante 2</label>
                                    <input type="date" class="form-control" id="garante2_fecha" name="garante2_fecha" placeholder="Fecha Garante">
                                </div>
                                <div class="mb-3">
                                    <label for="garante2_nombre" class="form-label">Nombre Garante 2</label>
                                    <input type="text" class="form-control" id="garante2_nombre" name="garante2_nombre" placeholder="Nombre Garante ">
                                </div>
                                <div class="mb-3">
                                    <label for="garante2_apellido" class="form-label">Apellido Garante 2</label>
                                    <input type="text" class="form-control" id="garante2_apellido" name="garante2_apellido" placeholder="Apellido Garante ">
                                </div>
                                <div class="mb-3">
                                    <label for="garante2_direccion" class="form-label">Dirección Garante 2</label>
                                    <input type="text" class="form-control" id="garante2_direccion" name="garante2_direccion" placeholder="Direccion Garante ">
                                </div>
                                <div class="mb-3">
                                    <label for="garante2_dni" class="form-label">DNI Garante 2</label>
                                    <input type="text" class="form-control" id="garante2_dni" name="garante2_dni" placeholder="DNI Garante ">
                                </div>
                                <div class="mb-3">
                                    <label for="garante2_direccion_personal" class="form-label">Dirección Personal Garante 2</label>
                                    <input type="text" class="form-control" id="garante2_direccion_personal" name="garante2_direccion_personal" placeholder="Direccion Personal Garante ">
                                </div>
                                <div class="mb-3">
                                    <label for="garante2_telefono" class="form-label">Teléfono Garante 2</label>
                                    <input type="text" class="form-control" id="garante2_telefono" name="garante2_telefono" placeholder="Telefono Garante ">
                                </div>
                                <div class="mb-3">
                                    <label for="garante2_mail" class="form-label">Mail Garante 2</label>
                                    <input type="text" class="form-control" id="garante2_mail" name="garante2_mail" placeholder="Mail Garante ">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                    <button type="submit" class="btn btn-primary">Guardar Garante 2</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="garantesModal" tabindex="-1" aria-labelledby="garantesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="garantesModalLabel">Garantes de: </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div id="garantesContent">
                        <p>Cargando garantes...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Script para activar la pestaña del Cliente al abrir el modal "Agregar Nuevo Cliente"
            const addClientModal = document.getElementById('exampleModal');
            if (addClientModal) {
                addClientModal.addEventListener('show.bs.modal', function () {
                    const clienteTabButton = document.getElementById('add-cliente-tab');
                    if (clienteTabButton) {
                        const clienteTab = new bootstrap.Tab(clienteTabButton);
                        clienteTab.show();
                    }
                });
            }

            // Script para cargar garantes en el modal de "Ver Garantes"
            document.querySelectorAll('.ver-garantes').forEach(button => {
                button.addEventListener('click', function() {
                    const inquilinoId = this.getAttribute('data-inquilino-id');
                    const inquilinoNombre = this.getAttribute('data-inquilino-nombre');
                    const garantesModalLabel = document.getElementById('garantesModalLabel');
                    const garantesContent = document.getElementById('garantesContent');

                    if (garantesModalLabel) {
                        garantesModalLabel.textContent = `Garantes de: ${inquilinoNombre}`;
                    }
                    if (garantesContent) {
                        garantesContent.innerHTML = '<p>Cargando garantes...</p>'; // Mensaje de carga
                    }

                    // Realizar una solicitud AJAX para obtener los garantes de este inquilino
                    fetch(`obtener_garantes_por_inquilino.php?inquilino_id=${inquilinoId}`) // Asume un nuevo archivo PHP
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.text(); // O response.json() si tu PHP devuelve JSON
                        })
                        .then(data => {
                            if (garantesContent) {
                                garantesContent.innerHTML = data; // Asume que el PHP devuelve HTML formateado
                            }
                        })
                        .catch(error => {
                            console.error('Error al cargar los garantes:', error);
                            if (garantesContent) {
                                garantesContent.innerHTML = '<div class="alert alert-danger" role="alert">Error al cargar los garantes. Por favor, inténtelo de nuevo.</div>';
                            }
                        });
                });
            });

            // Lógica para habilitar/deshabilitar pestañas de garantes (si es que aún la quieres para el modal de añadir)
            // Originalmente, esto parecía depender de la validez del formulario del cliente.
            // Si el flujo es que se añade el cliente primero, y luego se edita para añadir garantes,
            // esta lógica podría necesitar ser ajustada o movida a la página de edición.
            // Para el modal "Agregar Nuevo Cliente", la lógica de habilitar tabs así no es común
            // ya que se envían todos los datos del formulario principal. Si son formularios separados
            // que se envían a diferentes scripts, entonces la lógica podría tener sentido.
            // Por simplicidad, la comentaré a menos que se aclare el flujo deseado.

            /*
            const clienteFormAdd = document.querySelector('#add-cliente form'); // Selecciona el formulario del cliente en el modal
            const garante1TabAdd = document.getElementById('add-garante1-tab');
            const garante2TabAdd = document.getElementById('add-garante2-tab');

            if (clienteFormAdd && garante1TabAdd && garante2TabAdd) {
                clienteFormAdd.addEventListener('input', function () {
                    if (clienteFormAdd.checkValidity()) {
                        garante1TabAdd.classList.remove('disabled');
                        garante2TabAdd.classList.remove('disabled');
                    } else {
                        garante1TabAdd.classList.add('disabled');
                        garante2TabAdd.classList.add('disabled');
                    }
                });
            }
            */

            // Si deseas inicializar DataTables para las tablas en esta página,
            // descomenta el script de DataTables JS en el head y los siguientes bloques de código:
            /*
            $('#clienteTable').DataTable({
                "language": { "url": "//cdn.datatables.net/plug-ins/2.0.0/i18n/es-ES.json" }
            });
            $('#inquilinosTable').DataTable({
                "language": { "url": "//cdn.datatables.net/plug-ins/2.0.0/i18n/es-ES.json" }
            });
            $('#garantesTable').DataTable({
                "language": { "url": "//cdn.datatables.net/plug-ins/2.0.0/i18n/es-ES.json" }
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