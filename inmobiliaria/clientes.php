<?php
session_start();

// --- INICIO DE LAS CABECERAS PARA EVITAR CACHÉ ---
// Estas cabeceras son fundamentales para prevenir el caché del navegador,
// especialmente el bfcache de Firefox.
header("Cache-Control: no-cache, no-store, must-revalidate, max-age=0"); // HTTP 1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies
// --- FIN DE LAS CABECERAS PARA EVITAR CACHÉ ---

// 1. Verificación de sesión al principio del script
// Esto es lo primero que debe ocurrir. Si no hay sesión válida, redirigir.
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login/index.php"); // Redirige al login
    exit; // Termina la ejecución del script
}

// Muestra mensajes de sesión (alerta) si existen
if (isset($_SESSION['mensaje'])) {
    echo "<script>alert('" . $_SESSION['mensaje'] . "');</script>";
    unset($_SESSION['mensaje']); // Elimina el mensaje después de mostrarlo
}

// Incluir la conexión a la base de datos una única vez
include_once 'conexion.php';

// **IMPORTANTE**: Asegúrate de que 'conexion.php' maneje la conexión correctamente
// y que la variable $conn esté disponible globalmente o sea devuelta por una función.
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <title>SC Inmobiliaria San Cristobal</title>

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

    <style>
        /* Oculta el body por defecto para evitar "flash" de contenido si la sesión no es válida */
        body {
            display: none;
        }
    </style>
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
        </div>
        <nav>
            <ul>
                <li><a href="clientes.php" class="active"><i class="fas fa-users"></i> Clientes</a></li>
                <li><a href="propietarios.php"><i class="fas fa-user-tie"></i> Propietarios</a></li>
                <li><a href="propiedades.php"><i class="fas fa-home"></i> Propiedades</a></li>
                <li><a href="contabilidad.php"><i class="fas fa-file-invoice-dollar"></i> Contabilidad</a></li>
            </ul>
        </nav>
    </header>
    <main class="container mt-4">

        <?php
// ... (código PHP anterior) ...

// Determinar la clase CSS del botón y el texto/ícono dinámicamente
// Si actualmente estamos mostrando 'inactivos', el botón debería ofrecer ver 'activos'.
// Si no estamos mostrando 'inactivos' (es decir, estamos mostrando 'activos'), el botón debería ofrecer ver 'inactivos'.
if (isset($_GET['mostrar']) && $_GET['mostrar'] == 'inactivos') {
    $button_class = 'btn btn-primary'; // Por ejemplo, azul para "Ver Clientes Activos"
    $button_text = 'Ver Clientes Activos';
    $button_icon = 'fas fa-user-check'; // Un ícono que sugiera "activo" o "revisar"
    $link_param = 'activos'; // El enlace dirigirá a la vista de activos
} else {
    $button_class = 'btn-warning'; // Por ejemplo, amarillo/naranja para "Ver Clientes Inactivos"
    $button_text = 'Ver Clientes Inactivos';
    $button_icon = 'fas fa-user-slash'; // Un ícono que sugiera "inactivo" o "oculto"
    $link_param = 'inactivos'; // El enlace dirigirá a la vista de inactivos
}

?>


        <div class="d-flex justify-content-start align-items-center mb-3">
    <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#addClienteModal">
        <i class="fas fa-user-plus"></i> Nuevo Cliente
    </button>

    <a href="clientes.php?mostrar=<?php echo $link_param; ?>"
       class="btn <?php echo $button_class; ?>"> <i class="<?php echo $button_icon; ?>"></i>
       <?php echo $button_text; ?>
    </a>
</div>


        <div class="card shadow-sm">
            <div class="card-header text-white" style="background-color:rgba(233, 128, 0, 0.92);">
                <h2 class="h5 mb-0">Listado de Clientes</h2>
            </div>
            <div class="card-body">
                <?php
                // La conexión $conn ya debería estar disponible aquí por el include_once al principio.

                // Consulta SQL para obtener los datos de la tabla `clientes`
                if (isset($_GET['mostrar']) && $_GET['mostrar'] == 'inactivos') {
                    $sql_clientes = "SELECT ClienteID, Nombre, Apellido, Direccion FROM clientes WHERE estado = 'inactivo'";
                } else {
                    $sql_clientes = "SELECT ClienteID, Nombre, Apellido, Direccion FROM clientes WHERE estado = 'activo'";
                }

                $result_clientes = mysqli_query($conn, $sql_clientes);

                // Verificar si hay resultados
                if (mysqli_num_rows($result_clientes) > 0) {
                    // Iniciar la tabla HTML con clases de Bootstrap para tablas
                    echo '<div class="table-responsive">
                                <table id="clientesTable" class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Nombre y Apellido</th>
                                            <th>Dirección</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>';

                    // Iterar sobre cada fila de resultados
                    while ($fila_cliente = mysqli_fetch_assoc($result_clientes)) {
                        // Determinar el texto y color del botón de estado
                        $btn_estado_text = (isset($_GET['mostrar']) && $_GET['mostrar'] == 'inactivos') ? 'Activar' : 'Eliminar';
                        $btn_estado_class = (isset($_GET['mostrar']) && $_GET['mostrar'] == 'inactivos') ? 'btn-warning' : 'btn-danger';
                        $btn_estado_icon = (isset($_GET['mostrar']) && $_GET['mostrar'] == 'inactivos') ? 'fas fa-user-check' : 'fas fa-trash-alt';
                        $action_file = (isset($_GET['mostrar']) && $_GET['mostrar'] == 'inactivos') ? 'activar_cliente.php' : 'eliminar_cliente.php';


                        echo '<tr>
                                    <td>' . htmlspecialchars($fila_cliente['Nombre']) . ' ' . htmlspecialchars($fila_cliente['Apellido']) . '</td>
                                    <td>' . htmlspecialchars($fila_cliente['Direccion']) . '</td>
                                    <td>
                                    
                                   <button type="button" class="btn btn-sm btn-success me-1" title="Generar Recibo" data-bs-toggle="modal" data-bs-target="#reciboModal" data-cliente-id="'.htmlspecialchars($fila_cliente['ClienteID']).'" data-cliente-nombre="'.htmlspecialchars($fila_cliente['Nombre']).' '.htmlspecialchars($fila_cliente['Apellido']).'">
                                   <i class="fas fa-file-invoice-dollar"></i> Recibo
                                   </button>
                                    
                                        <a href="ver_clientes.php?id=' . htmlspecialchars($fila_cliente['ClienteID']) . '" class="btn btn-sm btn-info text-white" title="Ver Detalles"><i class="fas fa-eye"></i> Ver</a>
                        
                                        <a href="' . $action_file . '?id=' . htmlspecialchars($fila_cliente['ClienteID']) . '" 
                                           class="btn btn-sm ' . $btn_estado_class . '" 
                                           title="' . $btn_estado_text . ' Cliente"
                                           onclick="return confirm(\'¿Estás seguro de que quieres ' . strtolower($btn_estado_text) . ' a ' . htmlspecialchars($fila_cliente['Nombre']) . ' ' . htmlspecialchars($fila_cliente['Apellido']) . '?\');">
                                           <i class="' . $btn_estado_icon . '"></i> ' . $btn_estado_text . '
                                        </a>
                                    </td>
                                </tr>';
                    }

                    // Cerrar la tabla HTML
                    echo '           </tbody>
                                </table>
                            </div>'; // Cierre de .table-responsive
                } else {
                    // Si no hay resultados, mostrar un mensaje con estilo de Bootstrap
                    echo '<div class="alert alert-info" role="alert">No se encontraron clientes.</div>';
                }
                ?>
            </div>
        </div>
    </main>

    <div class="modal fade" id="addClienteModal" tabindex="-1" aria-labelledby="addClienteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addClienteModalLabel">Agregar Cliente y Garantes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form id="fullClienteForm" action="agregar_clientes.php" method="POST">
                    <div class="modal-body">
                        <ul class="nav nav-tabs" id="clienteTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="cliente-tab" data-bs-toggle="tab" data-bs-target="#clienteInfo" type="button" role="tab" aria-controls="clienteInfo" aria-selected="true">Cliente</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="garante1-tab" data-bs-toggle="tab" data-bs-target="#garante1Info" type="button" role="tab" aria-controls="garante1Info" aria-selected="false">Garante 1</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="garante2-tab" data-bs-toggle="tab" data-bs-target="#garante2Info" type="button" role="tab" aria-controls="garante2Info" aria-selected="false">Garante 2 (Opcional)</button>
                            </li>
                        </ul>

                        <div class="tab-content mt-3" id="clienteTabContent">
                            <div class="tab-pane fade show active" id="clienteInfo" role="tabpanel" aria-labelledby="cliente-tab">
                                <div class="mb-3">
                                    <label for="clienteFecha" class="form-label">Fecha de Ingreso</label>
                                    <input type="date" class="form-control" id="clienteFecha" name="Fecha" required>
                                </div>
                                <div class="mb-3">
                                    <label for="clienteNombre" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="clienteNombre" name="Nombre" placeholder="Nombre" required>
                                </div>
                                <div class="mb-3">
                                    <label for="clienteApellido" class="form-label">Apellido</label>
                                    <input type="text" class="form-control" id="clienteApellido" name="Apellido" placeholder="Apellido" required>
                                </div>
                                <div class="mb-3">
                                    <label for="clienteDireccion" class="form-label">Dirección</label>
                                    <input type="text" class="form-control" id="clienteDireccion" name="Direccion" placeholder="Dirección" required>
                                </div>
                                <div class="mb-3">
                                    <label for="clienteDNI" class="form-label">DNI</label>
                                    <input type="number" class="form-control" id="clienteDNI" name="DNI" placeholder="DNI" required minlength="8" maxlength="8" pattern="\d{8}" title="Debe ingresar exactamente 8 números">
                                </div>
                                <div class="mb-3">
                                    <label for="clienteDireccionPersonal" class="form-label">Dirección Personal</label>
                                    <input type="text" class="form-control" id="clienteDireccionPersonal" name="DireccionPersonal" placeholder="Dirección Personal" required>
                                </div>
                                <div class="mb-3">
                                    <label for="clienteTelefono" class="form-label">Teléfono</label>
                                    <input type="tel" class="form-control" id="clienteTelefono" name="Telefono" placeholder="Teléfono" required>
                                </div>
                                <div class="mb-3">
                                    <label for="clienteMail" class="form-label">Correo Electrónico</label>
                                    <input type="email" class="form-control" id="clienteMail" name="Mail" placeholder="Correo Electrónico" required>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="garante1Info" role="tabpanel" aria-labelledby="garante1-tab">
                                <p class="alert alert-info">Ingresa los datos del primer garante. Estos se guardarán junto con el cliente.</p>
                                <div class="mb-3">
                                    <label for="garante1Fecha" class="form-label">Fecha de Ingreso</label>
                                    <input type="date" class="form-control" id="garante1Fecha" name="garante1_fecha">
                                </div>
                                <div class="mb-3">
                                    <label for="garante1Nombre" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="garante1Nombre" name="garante1_nombre" placeholder="Nombre Garante ">
                                </div>
                                <div class="mb-3">
                                    <label for="garante1Apellido" class="form-label">Apellido</label>
                                    <input type="text" class="form-control" id="garante1Apellido" name="garante1_apellido" placeholder="Apellido Garante ">
                                </div>
                                <div class="mb-3">
                                    <label for="garante1Direccion" class="form-label">Dirección</label>
                                    <input type="text" class="form-control" id="garante1Direccion" name="garante1_direccion" placeholder="Dirección Garante ">
                                </div>
                                <div class="mb-3">
                                    <label for="garante1DNI" class="form-label">DNI</label>
                                    <input type="number" class="form-control" id="garante1DNI" name="garante1_dni" placeholder="DNI Garante " minlength="8" maxlength="8">
                                </div>
                                <div class="mb-3">
                                    <label for="garante1DireccionPersonal" class="form-label">Dirección Personal</label>
                                    <input type="text" class="form-control" id="garante1DireccionPersonal" name="garante1_direccion_personal" placeholder="Dirección Personal Garante ">
                                </div>
                                <div class="mb-3">
                                    <label for="garante1Telefono" class="form-label">Teléfono</label>
                                    <input type="tel" class="form-control" id="garante1Telefono" name="garante1_telefono" placeholder="Teléfono Garante ">
                                </div>
                                <div class="mb-3">
                                    <label for="garante1Mail" class="form-label">Correo Electrónico</label>
                                    <input type="email" class="form-control" id="garante1Mail" name="garante1_mail" placeholder="Mail Garante ">
                                </div>
                            </div>

                            <div class="tab-pane fade" id="garante2Info" role="tabpanel" aria-labelledby="garante2-tab">
                                <p class="alert alert-info">Ingresa los datos del segundo garante, si es necesario.</p>
                                <div class="mb-3">
                                    <label for="garante2Fecha" class="form-label">Fecha de Ingreso</label>
                                    <input type="date" class="form-control" id="garante2Fecha" name="garante2_fecha">
                                </div>
                                <div class="mb-3">
                                    <label for="garante2Nombre" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="garante2Nombre" name="garante2_nombre" placeholder="Nombre Garante ">
                                </div>
                                <div class="mb-3">
                                    <label for="garante2Apellido" class="form-label">Apellido</label>
                                    <input type="text" class="form-control" id="garante2Apellido" name="garante2_apellido" placeholder="Apellido Garante ">
                                </div>
                                <div class="mb-3">
                                    <label for="garante2Direccion" class="form-label">Dirección</label>
                                    <input type="text" class="form-control" id="garante2Direccion" name="garante2_direccion" placeholder="Dirección Garante ">
                                </div>
                                <div class="mb-3">
                                    <label for="garante2DNI" class="form-label">DNI</label>
                                    <input type="number" class="form-control" id="garante2DNI" name="garante2_dni" placeholder="DNI Garante " minlength="8" maxlength="8">
                                </div>
                                <div class="mb-3">
                                    <label for="garante2DireccionPersonal" class="form-label">Dirección Personal</label>
                                    <input type="text" class="form-control" id="garante2DireccionPersonal" name="garante2_direccion_personal" placeholder="Dirección Personal Garante ">
                                </div>
                                <div class="mb-3">
                                    <label for="garante2Telefono" class="form-label">Teléfono</label>
                                    <input type="tel" class="form-control" id="garante2Telefono" name="garante2_telefono" placeholder="Teléfono Garante ">
                                </div>
                                <div class="mb-3">
                                    <label for="garante2Mail" class="form-label">Correo Electrónico</label>
                                    <input type="email" class="form-control" id="garante2Mail" name="garante2_mail" placeholder="Mail Garante ">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cliente</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
<!-- Modal para generación de recibo -->
<div class="modal fade" id="reciboModal" tabindex="-1" aria-labelledby="reciboModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reciboModalLabel">Generar Recibo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formRecibo" action="generar_recibo_dompdf.php" method="GET" target="_blank">
                <input type="hidden" name="id" id="clienteIdInput">
                <div class="modal-body">
                    <!-- Sección de selección de contrato -->
                    
                    <div class="mb-3">
                        <label for="selectContrato" class="form-label">Contrato de Alquiler</label>
                        <select class="form-select" id="selectContrato" name="contrato_id" required>
                            <option value="">Cargando contratos...</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="inquilinoNombre" class="form-label">Inquilino</label>
                        <input type="text" class="form-control" id="inquilinoNombre" name="inquilino_nombre" readonly>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="numeroRecibo" class="form-label">Número de Recibo</label>
                            <input type="text" class="form-control" id="numeroRecibo" name="numero_recibo" required>
                        </div>
                        <div class="col-md-6">
                            <label for="fechaRecibo" class="form-label">Fecha</label>
                            <input type="date" class="form-control" id="fechaRecibo" name="fecha" required>
                        </div>
                    </div>
                    
                    <!-- Datos del cliente (se autocompletan) -->
                    <div class="mb-3">
                        <label for="nombreCliente" class="form-label">Cliente</label>
                        <input type="text" class="form-control" id="nombreCliente" name="nombre_cliente" readonly>
                    </div>
                    
                    <!-- Datos de la propiedad (se autocompletan al seleccionar contrato) -->
                    <div class="mb-3">
                        <label for="direccionPropiedad" class="form-label">Propiedad</label>
                        <input type="text" class="form-control" id="direccionPropiedad" name="direccion_propiedad" readonly>
                    </div>
                    
                    <!-- Monto y período -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="monto" class="form-label">Monto</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="monto" name="monto" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="periodo" class="form-label">Período</label>
                            <input type="text" class="form-control" id="periodo" name="periodo" required>
                        </div>
                    </div>
                    
                    <!-- Concepto (se autocompleta pero es editable) -->
                    <div class="mb-3">
                        <label for="concepto" class="form-label">Concepto</label>
                        <input type="text" class="form-control" id="concepto" name="concepto" value="Alquiler mensual" required>
                    </div>
                    
                    <!-- Observaciones -->
                    <div class="mb-3">
                        <label for="observaciones" class="form-label">Observaciones (opcional)</label>
                        <textarea class="form-control" id="observaciones" name="observaciones" rows="2"></textarea>
                    </div>

                    <div class="form-group">
                      <label for="monto_luz">Monto Luz ($):</label>
                      <input type="number" step="0.01" name="monto_luz" id="monto_luz" class="form-control" placeholder="0.00">
                    </div>

                    <div class="form-group">
                         <label for="descuento">Descuento ($):</label>
                         <input type="number" step="0.01" name="descuento" id="descuento" class="form-control" placeholder="0.00">
                    </div>

                    
                    <!-- Opción de duplicado -->
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="generarDuplicado" name="generar_duplicado" checked>
                        <label class="form-check-label" for="generarDuplicado">
                            Incluir recibo duplicado
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Generar Recibo</button>
                </div>
            </form>
        </div>
    </div>
</div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="//cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            // Inicializa DataTables
            $('#clientesTable').DataTable({
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/2.0.2/i18n/es-ES.json" // URL correcta para DataTables 2.x
                },
                "paging": true,      // Habilita paginación
                "searching": true,   // Habilita el cuadro de búsqueda
                "ordering": true,    // Habilita ordenación de columnas
                "info": true         // Habilita información de la tabla
            });

            // Script para activar la pestaña de Cliente al abrir el modal
            const addClienteModal = document.getElementById('addClienteModal');
            if (addClienteModal) {
                addClienteModal.addEventListener('show.bs.modal', function () {
                    const clienteTab = new bootstrap.Tab(document.getElementById('cliente-tab'));
                    clienteTab.show();
                });
            }

            // JavaScript para manejar la prevención del historial y el bfcache
            (function () {
            // Reemplaza el historial con la URL actual para evitar el botón de retroceso
            // Esto evita que, al hacer clic en "atrás" una vez, se regrese a la página anterior real.
            history.replaceState(null, "", location.href);
            // Empuja una entrada falsa en el historial para detectar el intento de ir atrás.
            history.pushState(null, "", location.href);

            window.addEventListener("popstate", function () {
                // Cuando el usuario intenta ir hacia atrás (presiona la flecha del navegador),
                // redirige directamente a index.php. Usamos replace para evitar que la página actual
                // permanezca en el historial.
                window.location.replace("../login/logout.php");
            });

            window.addEventListener("pageshow", function (event) {
                // Si la página se está restaurando desde el bfcache de Firefox (o similar),
                // también redirige a index.php para asegurar la validación de la sesión.
                if (event.persisted) {
                    window.location.replace("../login/logout.php");
                }
            });

            // Finalmente, muestra el cuerpo del documento una vez que los scripts de seguridad se han ejecutado.
            document.body.style.display = "block";
            })();
        });
    </script>
<script>
$(document).ready(function () {
    $('#reciboModal').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        const clienteId = button.data('cliente-id');
        const clienteNombre = button.data('cliente-nombre');

        $('#clienteIdInput').val(clienteId);
        $('#nombreCliente').val(clienteNombre);

        const $selectContrato = $('#selectContrato');
        $selectContrato.html('<option>Cargando contratos...</option>');

        $.getJSON('obtener_contratos.php', { cliente_id: clienteId }, function (data) {
            $selectContrato.empty();

            if (Array.isArray(data) && data.length > 0) {
                $selectContrato.append('<option value="">Seleccione un contrato</option>');
                data.forEach(function (contrato) {
                    $selectContrato.append(`
                        <option value="${contrato.id}"
                            data-direccion="${contrato.direccion}"
                            data-monto="${contrato.canon_mensual}"
                            data-inquilino="${contrato.inquilino}">
                            Contrato #${contrato.id} - ${contrato.direccion} - ${contrato.inquilino}
                        </option>`);
                });
            } else {
                $selectContrato.append('<option value="">No hay contratos activos</option>');
            }
        });

        // Setea automáticamente el período actual
        const hoy = new Date();
        const meses = [
            'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
        ];
        const periodo = `${meses[hoy.getMonth()]} ${hoy.getFullYear()}`;
        $('#periodo').val(periodo);
    });

    $('#selectContrato').on('change', function () {
        const selected = $(this).find(':selected');
        $('#direccionPropiedad').val(selected.data('direccion') || '');
        $('#monto').val(selected.data('monto') || '');
        $('#inquilinoNombre').val(selected.data('inquilino') || '');
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
                    verBtn.textContent = 'Ver';
                    verBtn.className = 'btn btn-sm btn-primary mb-1';

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

<?php
// Cierra la conexión a la base de datos al final del script si se abrió.
if (isset($conn) && $conn) {
    mysqli_close($conn);
}
?>