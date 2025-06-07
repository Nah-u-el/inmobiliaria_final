<?php
session_start();

// Muestra mensajes de sesión (alertas) si existen
if (isset($_SESSION['mensaje'])) {
    echo "<script>alert('" . $_SESSION['mensaje'] . "');</script>";
    unset($_SESSION['mensaje']); // Elimina el mensaje después de mostrarlo
}

// Incluir la conexión a la base de datos una única vez
include_once 'conexion.php'; 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión Inmobiliaria</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css"> 
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=menu" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="//cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
</head>
<body>
    <header>
        <div class="header-content">
            <div class="dropdown">
                <button type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Menú de Navegación">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#000000">
                        <path d="M120-240v-80h720v80H120Zm0-200v-80h720v80H120Zm0-200v-80h720v80H120Z"/>
                    </svg>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#">📆 Pagos del Mes</a></li>
                </ul>
            </div>

            <img src="logo2.png" alt="Logo Inmobiliaria" class="logo">

            <div>
                <div class="dropdown">
                    <button type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Opciones de Usuario">
                        👤
                        🔔
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">🔑 Cambiar Clave</a></li>
                        <li><a class="dropdown-item" href="#">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#000000">
                                <path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h280v80H200v560h280v80H200Zm440-160-55-58 102-102H360v-80h327L585-622l55-58 200 200-200 200Z"/>
                            </svg> Cerrar sesión</a></li>
                    </ul>
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
    <main class="container mt-4"> <div class="d-flex justify-content-start align-items-center mb-3">
            <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#addCajaModal">
                <i class="fas fa-plus-circle"></i> Nuevo Saldo </button>
            <button type="button" class="btn btn-info" onclick="mostrarCajaMensual()">
                <i class="fas fa-box-open"></i> Ver Caja Mensual </button>
        </div>

        <div class="card shadow-sm">
            <div class="card-header text-white" style="background-color:rgba(233, 128, 0, 0.92);"> 
                <h2 class="h5 mb-0">Movimientos de Caja (Hoy)</h2>
            </div>
            <div class="card-body">
                <?php 
                // La conexión $conn ya debería estar disponible aquí por el include_once al principio.
                
                // Consulta SQL para obtener los datos de la tabla `caja`
                $stmt = $conn->prepare("SELECT * FROM caja WHERE DATE(Fecha) = CURDATE()");
                $stmt->execute();
                $result = $stmt->get_result();
                
                // Consulta para calcular el total recaudado en el día
                $stmtTotal = $conn->prepare("SELECT SUM(RecibidoEnviado) AS total_recaudado FROM caja WHERE DATE(Fecha) = CURDATE()");
                $stmtTotal->execute();
                $totalResult = $stmtTotal->get_result();
                $totalRow = $totalResult->fetch_assoc();
                $totalRecaudado = $totalRow['total_recaudado'] ?? 0; // Si no hay datos, el total es 0
                
                // Verificar si hay resultados
                if (mysqli_num_rows($result) > 0) {
                    // Iniciar la tabla HTML con clases de Bootstrap para tablas
                    echo '<div class="table-responsive">
                            <table id="cajaTable" class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Concepto</th>
                                        <th>Recibido/Enviado</th>
                                        <th>Forma de pago</th>
                                        <th>Cliente/Inmueble</th>
                                        <th>Observaciones</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>';
                
                    // Iterar sobre cada fila de resultados
                    while ($fila = mysqli_fetch_assoc($result)) {
                        echo '<tr>
                                <td>' . htmlspecialchars($fila['Fecha']) . '</td>
                                <td>' . htmlspecialchars($fila['Concepto']) . '</td>
                                <td>' . '$' . number_format($fila['RecibidoEnviado'], 2) . '</td> <td>' . htmlspecialchars($fila['FormaPago']) . '</td>
                                <td>' . htmlspecialchars($fila['ClienteInmueble']) . '</td>
                                <td>' . htmlspecialchars($fila['Observaciones']) . '</td>
                                <td>
                                    <button type="button" class="btn btn-info btn-sm" 
                                        onclick="editarCaja(' . htmlspecialchars(json_encode($fila)) . ')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>';
                    }
                
                    // Cerrar la tabla HTML
                    echo '          </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2"><strong>Total Recaudado:</strong></td>
                                        <td><strong>' . '$' . number_format($totalRecaudado, 2) . '</strong></td>
                                        <td colspan="4"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>'; // Cierre de .table-responsive
                } else {
                    // Si no hay resultados, mostrar un mensaje con estilo de Bootstrap
                    echo '<div class="alert alert-info" role="alert">No se encontraron movimientos de caja para hoy.</div>';
                }
                
                // Cerrar la conexión a la base de datos
                mysqli_close($conn);
                ?>
            </div>
        </div>
    </main>

    <div class="modal fade" id="addCajaModal" tabindex="-1" aria-labelledby="addCajaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCajaModalLabel">Agregar Saldo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="addCajaForm" action="agregar_caja.php" method="POST">
                        <div class="mb-3">
                            <label for="add_concepto" class="form-label">Concepto</label>
                            <input type="text" class="form-control" id="add_concepto" name="Concepto" placeholder="Concepto" required>
                        </div>
                        <div class="mb-3">
                            <label for="add_tipo_movimiento" class="form-label">Tipo de Movimiento</label>
                            <select class="form-select" id="add_tipo_movimiento" name="TipoMovimiento" required>
                                <option value="recibido">Recibido</option>
                                <option value="enviado">Enviado</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="add_monto" class="form-label">Monto</label>
                            <input type="number" step="0.01" class="form-control" id="add_monto" name="Monto" placeholder="Monto" required>
                        </div>
                        <div class="mb-3">
                            <label for="add_forma_pago" class="form-label">Forma de Pago</label>
                            <input type="text" class="form-control" id="add_forma_pago" name="FormaPago" placeholder="Forma de Pago" required>
                        </div>
                        <div class="mb-3">
                            <label for="add_cliente_inmueble" class="form-label">Cliente/Inmueble</label>
                            <input type="text" class="form-control" id="add_cliente_inmueble" name="ClienteInmueble" placeholder="Buscar cliente o inmueble" required>
                        </div>
                        <div class="mb-3">
                            <label for="add_observaciones" class="form-label">Observaciones</label>
                            <input type="text" class="form-control" id="add_observaciones" name="Observaciones" placeholder="Observaciones">
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

    <div class="modal fade" id="editCajaModal" tabindex="-1" aria-labelledby="editCajaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCajaModalLabel">Editar Saldo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="editCajaForm" action="editar_caja.php" method="POST">
                        <input type="hidden" id="edit_id_caja" name="id_caja">
                        <div class="mb-3">
                            <label for="edit_fecha" class="form-label">Fecha</label>
                            <input type="text" class="form-control" id="edit_fecha" name="Fecha" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="edit_concepto" class="form-label">Concepto</label>
                            <input type="text" class="form-control" id="edit_concepto" name="Concepto" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_tipo_movimiento" class="form-label">Tipo de Movimiento</label>
                            <select class="form-select" id="edit_tipo_movimiento" name="TipoMovimiento" required>
                                <option value="recibido">Recibido</option>
                                <option value="enviado">Enviado</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_monto" class="form-label">Monto</label>
                            <input type="number" step="0.01" class="form-control" id="edit_monto" name="Monto" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_forma_pago" class="form-label">Forma de Pago</label>
                            <input type="text" class="form-control" id="edit_forma_pago" name="FormaPago" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_cliente_inmueble" class="form-label">Cliente/Inmueble</label>
                            <input type="text" class="form-control" id="edit_cliente_inmueble" name="ClienteInmueble" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_observaciones" class="form-label">Observaciones</label>
                            <input type="text" class="form-control" id="edit_observaciones" name="Observaciones">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="//cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>


    <script>
        // Inicializa DataTables
        $(document).ready(function() {
            console.log("jQuery y DataTables listos.");
            $('#cajaTable').DataTable({ // Cambiado a #cajaTable para esta tabla específica
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/2.0.2/i18n/es-ES.json" 
                },
                "paging": true,      // Habilita paginación
                "searching": true,   // Habilita el cuadro de búsqueda
                "ordering": true,    // Habilita ordenación de columnas
                "info": true         // Habilita información de la tabla
            });

            console.log("DataTables inicializado en #cajaTable.");
        });

        // --- Ajustes para el formulario "Nuevo Saldo" ---
        // Ahora solo nos aseguramos que el monto sea un número positivo en el input
        // El signo (positivo/negativo) lo manejará el PHP.
        document.getElementById('add_tipo_movimiento').addEventListener('change', function() {
            adjustAddMontoInput();
        });

        document.getElementById('add_monto').addEventListener('input', function() {
            adjustAddMontoInput();
        });

        function adjustAddMontoInput() {
            const montoInput = document.getElementById('add_monto');
            let monto = parseFloat(montoInput.value) || 0;
            montoInput.value = Math.abs(monto); // Siempre mostrar el monto como positivo en el input
        }

        // --- Ajustes para el formulario "Editar Saldo" ---
        document.getElementById('edit_tipo_movimiento').addEventListener('change', function() {
            adjustEditMontoInput();
        });

        document.getElementById('edit_monto').addEventListener('input', function() {
            adjustEditMontoInput();
        });

        function adjustEditMontoInput() {
            const montoInput = document.getElementById('edit_monto');
            let monto = parseFloat(montoInput.value) || 0;
            montoInput.value = Math.abs(monto); // Siempre mostrar el monto como positivo en el input
        }

        // Function to open the edit modal and populate data
        function editarCaja(cajaData) {
            document.getElementById('edit_id_caja').value = cajaData.ID;
            document.getElementById('edit_fecha').value = cajaData.Fecha;
            document.getElementById('edit_concepto').value = cajaData.Concepto;
            
            // Mostrar el monto como positivo en el campo de edición, independientemente del signo en la DB
            document.getElementById('edit_monto').value = Math.abs(parseFloat(cajaData.RecibidoEnviado));
            
            document.getElementById('edit_forma_pago').value = cajaData.FormaPago;
            document.getElementById('edit_cliente_inmueble').value = cajaData.ClienteInmueble;
            document.getElementById('edit_observaciones').value = cajaData.Observaciones;

            // Set the correct selected option for TipoMovimiento based on the sign
            if (parseFloat(cajaData.RecibidoEnviado) < 0) {
                document.getElementById('edit_tipo_movimiento').value = 'enviado';
            } else {
                document.getElementById('edit_tipo_movimiento').value = 'recibido';
            }
            
            // Asegúrate de que el monto se muestre correctamente después de establecer el tipo
            adjustEditMontoInput();

            var editModal = new bootstrap.Modal(document.getElementById('editCajaModal'));
            editModal.show();
        }

        function mostrarCajaMensual() {
            window.location.href = 'caja_mensual.php';
        }

        // --- Manejo del formulario de edición (editCajaForm) con fetch ---
        // (Recomiendo que editar_caja.php también redirija después de éxito)
        document.getElementById('editCajaForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Evitar el envío normal del formulario

            const formData = new FormData(this);
            const tipoMovimiento = document.getElementById('edit_tipo_movimiento').value;
            let monto = parseFloat(document.getElementById('edit_monto').value) || 0;

            // Ajustar el monto en el formData antes de enviarlo, si la lógica PHP no lo hace
            // Si editar_caja.php ya maneja el signo, esta parte es redundante pero no dañina.
            if (tipoMovimiento === 'enviado') {
                monto = -Math.abs(monto);
            } else {
                monto = Math.abs(monto);
            }
            formData.set('Monto', monto); // Sobrescribir el valor del monto en formData

            fetch("editar_caja.php", { 
                method: "POST",
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                console.log("Respuesta del servidor (Editar):", data);
                if (data.includes("Registro actualizado correctamente")) {
                    alert("Saldo actualizado correctamente");
                    $('#editCajaModal').modal('hide');
                    location.reload(); // Recargar la página para ver los datos actualizados
                } else {
                    alert("Error al actualizar saldo. Verifica los datos: " + data); // Mostrar respuesta completa para depuración
                }
            })
            .catch(error => {
                console.error("Error al enviar solicitud de edición:", error);
                alert("Error en la solicitud de edición. Inténtalo de nuevo.");
            });
        });
    </script>
</body>
</html>