<?php
session_start();


// Muestra mensajes de sesión (alertas) si existen
if (isset($_SESSION['mensaje'])) {
    echo "<script>alert('" . $_SESSION['mensaje'] . "');</script>";
    unset($_SESSION['mensaje']); // Elimina el mensaje después de mostrarlo
}

// Incluir la conexión a la base de datos una única vez
// Esto asegura que $conn esté disponible en todo el script sin reconexiones innecesarias.
include_once 'conexion.php'; 

// **IMPORTANTE**: Asegurate de que 'conexion.php' maneje la conexión correctamente
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
    <title>Sistema de Gestión Inmobiliaria</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css"> <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=menu" />
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
                <li><a href="propietarios.php"><i class="fas fa-user-tie"></i> Propietarios</a></li>
                <li><a href="propiedades.php" class="active"><i class="fas fa-home"></i> Propiedades</a></li>
                <li><a href="contabilidad.php"><i class="fas fa-file-invoice-dollar"></i> Contabilidad</a></li>
            </ul>
        </nav>
    </header>
    <main class="container mt-4">
        <div class="d-flex justify-content-start align-items-center mb-3">
            <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#exampleModal">
                <i class="fas fa-house-chimney-medical"></i> Agregar Propiedad
            </button>
            
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalContrato">
                <i class="fas fa-file-contract"></i> Agregar Contrato
            </button>
        </div>

        <div class="card shadow-sm">
            <div class="card-header text-white" style="background-color:rgba(233, 128, 0, 0.92);"> 
                <h2 class="h5 mb-0">Listado de Propiedades</h2>
            </div>
            <div class="card-body">
                <?php 
                // La conexión $conn ya debería estar disponible aquí por el include_once al principio.

                $sql = "SELECT PropiedadID, Direccion, Ciudad, Barrio FROM propiedades"; // Selecciona solo las columnas necesarias
                $result = mysqli_query($conn, $sql);

                // Verificar si hay resultados
                if (mysqli_num_rows($result) > 0) {
                    // Iniciar la tabla HTML con clases de Bootstrap para tablas
                    echo '<div class="table-responsive">
                            <table id="propiedadesTable" class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Dirección</th>
                                        <th>Ciudad</th>
                                        <th>Barrio</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>';

                    // Iterar sobre cada fila de resultados
                    while ($fila = mysqli_fetch_assoc($result)) {
                        // Mostrar cada fila en la tabla
                        echo '<tr>
                                <td>' . htmlspecialchars($fila['Direccion']) . '</td>
                                <td>' . htmlspecialchars($fila['Ciudad']) . '</td>
                                <td>' . htmlspecialchars($fila['Barrio']) . '</td>
                                <td>
                                    <a href="ver_contrato.php?id=' . htmlspecialchars($fila['PropiedadID']) . '" class="btn btn-sm btn-info text-white me-1"><i class="fas fa-eye"></i> Ver Contrato</a>
                                    
                                    <a href="ver_propiedad.php?id=' . htmlspecialchars($fila['PropiedadID']) . '" class="btn btn-sm btn-secondary">Ver Propiedad</a>
                                </td>
                            </tr>';
                    }

                    // Cerrar la tabla HTML
                    echo '          </tbody>
                                </table>
                            </div>'; // Cierre de .table-responsive
                } else {
                    // Si no hay resultados, mostrar un mensaje con estilo de Bootstrap
                    echo '<div class="alert alert-info" role="alert">No se encontraron propiedades.</div>';
                }
                ?>
            </div>
        </div>
    </main>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Agregar Propiedad</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="propiedad-tab" data-bs-toggle="tab" data-bs-target="#propiedad" type="button" role="tab" aria-controls="propiedad" aria-selected="true">Propiedad</button>
                        </li>
                    </ul>

                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="propiedad" role="tabpanel" aria-labelledby="propiedad-tab">
                            <br>
                            <form id="propiedadForm" action="agregar_propiedad.php" method="POST">
                                <div class="mb-3">
                                    <label for="fecha" class="form-label">Fecha de Ingreso</label>
                                    <input type="date" class="form-control" id="fecha" name="Fecha" required>
                                </div>
                                <div class="mb-3">
                                    <label for="barrio" class="form-label">Barrio</label>
                                    <input type="text" class="form-control" id="barrio" name="Barrio" placeholder="Barrio" required>
                                </div>
                                <div class="mb-3">
                                    <label for="ciudad" class="form-label">Ciudad</label>
                                    <input type="text" class="form-control" id="ciudad" name="Ciudad" placeholder="Ciudad" required>
                                </div>
                                <div class="mb-3">
                                    <label for="direccion" class="form-label">Dirección</label>
                                    <input type="text" class="form-control" id="direccion" name="Direccion" placeholder="Dirección" required>
                                </div>
                                <div class="mb-3">
                                    <label for="nro" class="form-label">Número</label>
                                    <input type="text" class="form-control" id="nro" name="Nro" placeholder="Nro" required>
                                </div>
                                <div class="mb-3">
                                    <label for="dominio" class="form-label">Dominio</label>
                                    <input type="text" class="form-control" id="dominio" name="Dominio" placeholder="Dominio" required>
                                </div>
                                <div class="mb-3">
                                    <label for="nro_partida" class="form-label">Número de Partida</label>
                                    <input type="tel" class="form-control" id="nro_partida" name="NroPartida" placeholder="Nro Partida" required>
                                </div>
                                <div class="mb-3">
                                    <label for="estado" class="form-label">Estado de la propiedad</label>
                                    <select class="form-select" id="estado" name="Estado">
                                        <option value="alquilada">Alquilada</option>
                                        <option value="en venta">En Venta</option>
                                    </select>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                    <button type="submit" class="btn btn-primary">Guardar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalContrato" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form action="guardar_contrato.php" method="POST" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nuevo Contrato</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="PropiedadID" class="form-label">Propiedad</label>
                        <select name="PropiedadID" id="PropiedadID" class="form-select" required>
                            <?php
                            // La conexión $conn ya debería estar disponible desde el include_once inicial.
                            $propiedades_contrato = mysqli_query($conn, "SELECT PropiedadID, Direccion, Ciudad FROM propiedades");
                            while ($row = mysqli_fetch_assoc($propiedades_contrato)) {
                                echo "<option value='{$row['PropiedadID']}'>" . htmlspecialchars($row['Direccion']) . " - " . htmlspecialchars($row['Ciudad']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="CanonMensual" class="form-label">Cuota Mensual</label>
                        <input type="number" step="0.01" name="CanonMensual" id="CanonMensual" placeholder="Cuota Mensual" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="Deposito" class="form-label">Depósito</label>
                        <input type="number" step="0.01" name="Deposito" id="Deposito" placeholder="Deposito inicial" class="form-control" required>
                    </div>

                    <h6 class="mt-4 mb-2">Inquilino</h6>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <input type="text" name="InquilinoNombre" placeholder="Nombre" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="InquilinoDNI" placeholder="DNI" class="form-control" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <input type="text" name="InquilinoTelefono" placeholder="Teléfono" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <input type="email" name="InquilinoMail" placeholder="Email" class="form-control">
                        </div>
                    </div>

                    <h6 class="mt-4 mb-2">Garante 1</h6>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <input type="text" name="Garante1Nombre" placeholder="Nombre" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="Garante1DNI" placeholder="DNI" class="form-control" required>
                        </div>
                    </div>

                    <h6 class="mt-4 mb-2">Garante 2 (Opcional)</h6>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <input type="text" name="Garante2Nombre" placeholder="Nombre" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="Garante2DNI" placeholder="DNI" class="form-control">
                        </div>
                    </div>

                    <h6 class="mt-4 mb-2">Fechas del Contrato</h6>
                    <div class="row">
                        <div class="col">
                            <label for="FechaInicio" class="form-label">Fecha Inicio</label>
                            <input type="date" name="FechaInicio" id="FechaInicio" class="form-control" required>
                        </div>
                        <div class="col">
                            <label for="FechaFin" class="form-label">Fecha Fin</label>
                            <input type="date" name="FechaFin" id="FechaFin" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-success">Guardar Contrato</button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="//cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>

    <script>
        // Inicializa DataTables
        $(document).ready(function() {
            console.log("jQuery y DataTables listos.");
            $('#propiedadesTable').DataTable({
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/2.0.2/i18n/es-ES.json" // URL correcta para DataTables 2.x
                },
                "paging": true,      // Habilita paginación
                "searching": true,   // Habilita el cuadro de búsqueda
                "ordering": true,    // Habilita ordenación de columnas
                "info": true         // Habilita información de la tabla
            });

            console.log("DataTables inicializado en #propiedadesTable.");
        });

        // Script para activar la pestaña de Propiedad al abrir el modal (exampleModal)
        document.addEventListener('DOMContentLoaded', function () {
            const exampleModal = document.getElementById('exampleModal');
            if (exampleModal) {
                exampleModal.addEventListener('show.bs.modal', function () {
                    const propiedadTab = new bootstrap.Tab(document.getElementById('propiedad-tab'));
                    propiedadTab.show();
                    console.log("Modal 'Agregar Propiedad' abierto, pestaña 'Propiedad' activada.");
                });
            } else {
                console.warn("Elemento 'exampleModal' no encontrado.");
            }
        });
    </script>
</body>
</html>

<?php
// Cierra la conexión a la base de datos al final del script si se abrió.
// Esto es una buena práctica para liberar recursos.
if (isset($conn) && $conn) {
    mysqli_close($conn);
}
?>