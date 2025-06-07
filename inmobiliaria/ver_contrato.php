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
</head>
<body>
    <header>
        <div class="header-content">
            <div class="dropdown">
                <button type="button" data-bs-toggle="dropdown" aria-expanded="false">
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
                    <button type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        👤
                        🔔
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#">🔑 Cambiar Clave</a></li>
                    <li><a class="dropdown-item" href="logout.php">
    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#000000">
        <path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h280v80H200v560h280v80H200Zm440-160-55-58 102-102H360v-80h327L585-622l55-58 200 200-200 200Z"/>
    </svg> Cerrar sesion</a></li>
                </ul>
                    </button>
            </div>
        </div>
    </div>
        <nav>
            <ul>
                <li><a href="clientes.php" ><i class="fas fa-users"></i> Clientes</a></li>
                <li><a href="propietarios.php"><i class="fas fa-user-tie"></i> Propietarios</a></li>
                <li><a href="propiedades.php" class="active"><i class="fas fa-home"></i> Propiedades</a></li>
                <li><a href="contabilidad.php"><i class="fas fa-file-invoice-dollar"></i> Contabilidad</a></li>
            </ul>
        </nav>
    </header>
    
       <div class="container mt-4">
            <a href="propiedades.php" class="btn text-white" style="background-color: rgba(233, 128, 0, 0.92);">
                <i class="fas fa-arrow-left"></i> Volver a Propiedades
            </a>
        </div>
    
    <div class="container mt-4">
        <?php
        include 'conexion.php';

        $propiedadID = isset($_GET['id']) ? intval($_GET['id']) : null;

        if (!$propiedadID) {
            echo '<div class="alert alert-danger">ID de propiedad no proporcionado.</div>';
            exit;
        }

        // Consulta con chequeo de error
        $propiedadQuery = "
            SELECT p.Direccion, p.Ciudad, cl.Nombre AS ClienteNombre
            FROM propiedades p
            JOIN clientes cl ON p.ClienteID = cl.ClienteID
            WHERE p.PropiedadID = $propiedadID
        ";

        $propiedadResult = mysqli_query($conn, $propiedadQuery);

        if (!$propiedadResult) {
            echo '<div class="alert alert-danger">Error en la consulta de propiedad: ' . mysqli_error($conn) . '</div>';
            exit;
        }

        if ($propiedadRow = mysqli_fetch_assoc($propiedadResult)) {
            $direccion = $propiedadRow['Direccion'];
            $ciudad = $propiedadRow['Ciudad'];
            $nombrePropietario = $propiedadRow['ClienteNombre'];
        } else {
            echo '<div class="alert alert-warning">Propiedad no encontrada.</div>';
            exit;
        }
        ?>

        <div class="card mb-4">
            <div class="card-header text-white" style="background-color:rgba(233, 128, 0, 0.92);">
                <h2 class="h4 mb-0">Contratos para la propiedad</h2>
            </div>
            <div class="card-body">
                <h3 class="h5"><?php echo "$direccion, $ciudad"; ?></h3>
                <p class="mb-4"><strong>Propietario:</strong> <?php echo $nombrePropietario; ?></p>

                <?php
               
               
                // Obtener contratos junto a garantes
                $sql = "
                    SELECT c.*, 
                           i.Nombre AS InquilinoNombre, 
                           i.DNI AS InquilinoDNI,
                           g1.Nombre AS Garante1Nombre,
                           g2.Nombre AS Garante2Nombre
                    FROM contratos c
                    JOIN inquilinos i ON c.InquilinoID = i.InquilinoID
                    LEFT JOIN garantesinquilinos g1 ON c.GaranteinquilinoID = g1.GaranteInquilinoID
                    LEFT JOIN garantesinquilinos g2 ON c.GaranteInquilinoID = g2.GaranteInquilinoID
                    WHERE c.PropiedadID = $propiedadID
                ";
                $result = mysqli_query($conn, $sql);

                if (!$result) {
                    echo '<div class="alert alert-danger">Error al obtener contratos: ' . mysqli_error($conn) . '</div>';
                    exit;
                }

                if (mysqli_num_rows($result) > 0) {
                ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Inquilino</th>
                                    <th>DNI</th>
                                    <th>Monto Mensual</th>
                                    <th>Depósito</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Fin</th>
                                    <th>Estado</th>
                                    <th>Garante 1</th>
                                    <th>Garante 2</th>
                                    <th>PDF</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php
                               while ($row = mysqli_fetch_assoc($result)) {
    $estado = '';
    $hoy = date('Y-m-d');
    $color = '';
    
    if ($row['fecha_fin'] < $hoy) {
        $estado = 'Vencido';
        $color = 'red';
    } elseif ($row['fecha_inicio'] <= $hoy && $row['fecha_fin'] >= $hoy) {
        $estado = 'Activo';
        $color = 'green';
    } else {
        $estado = 'Indefinido';
        $color = 'gray';
    }

                            echo "<tr>
                                <td>{$row['InquilinoNombre']}</td>
                                <td>{$row['InquilinoDNI']}</td>
                                <td>\${$row['canon_mensual']}</td>
                                <td>\${$row['deposito']}</td>
                                <td>{$row['fecha_inicio']}</td>
                                <td>{$row['fecha_fin']}</td>
                                <td><span style='color: $color;'>●</span> $estado</td>
                                <td>{$row['Garante1Nombre']}</td>
                                <td>{$row['Garante2Nombre']}</td>
                                <td>
                                     <form action='generar_pdf.php' method='get' target='_blank'>
                                     <input type='hidden' name='contrato_id' value='{$row['ContratoID']}'>
                                     <input type='hidden' name='id' value='$propiedadID'>
                                      <button type='submit' class='btn btn-outline-primary btn-sm' title='Generar PDF'>
                                        <i class='fas fa-file-pdf'></i>
                                      </button>
                                     </form>
                                 </td>
                            </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                <?php
                } else {
                    echo '<div class="alert alert-info">No hay contratos asociados a esta propiedad.</div>';
                }

                mysqli_close($conn);
                ?>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous" defer></script>
    <script src="//cdn.datatables.net/2.2.2/js/dataTables.min.js" defer></script>
</body>
</html>