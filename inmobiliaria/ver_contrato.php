<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión Inmobiliaria</title>

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
               
               $hoy = date('Y-m-d');

$actualizar = "
    UPDATE contratos 
    SET estado = 'vencido' 
    WHERE fecha_fin < '$hoy' AND estado = 'activo'
";
mysqli_query($conn, $actualizar);

               
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