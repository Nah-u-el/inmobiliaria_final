<?php
session_start();
if (isset($_SESSION['mensaje'])) {
    echo "<script>alert('" . $_SESSION['mensaje'] . "');</script>";
    unset($_SESSION['mensaje']); // Elimina el mensaje después de mostrarlo
}

include 'conexion.php';

// Obtener el ID de la propiedad desde la URL
if (isset($_GET['id'])) {
    $propiedadID = $_GET['id'];

    // Consulta SQL para obtener los detalles de la propiedad
    $sql = "SELECT * FROM propiedades WHERE PropiedadID = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $propiedadID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Verificar si se encontró la propiedad
    if (mysqli_num_rows($result) > 0) {
        $propiedad = mysqli_fetch_assoc($result);
    } else {
        echo "Propiedad no encontrada.";
        exit;
    }

    // Cerrar la consulta preparada
    mysqli_stmt_close($stmt);

    // Obtener los inquilinos asociados a la propiedad
    $sql_inquilinos = "SELECT i.* FROM inquilinos i WHERE i.PropiedadID = ?";
    $stmt_inquilinos = mysqli_prepare($conn, $sql_inquilinos);
    mysqli_stmt_bind_param($stmt_inquilinos, "i", $propiedadID);
    mysqli_stmt_execute($stmt_inquilinos);
    $result_inquilinos = mysqli_stmt_get_result($stmt_inquilinos);

    $inquilinos = [];
    if (mysqli_num_rows($result_inquilinos) > 0) {
        while ($inquilino = mysqli_fetch_assoc($result_inquilinos)) {
            $inquilinos[] = $inquilino;
        }
    }
    mysqli_stmt_close($stmt_inquilinos);

    // Obtener los garantes de cada inquilino
    foreach ($inquilinos as &$inquilino) {
        $sql_garantes = "SELECT g.* FROM garantesinquilinos g WHERE g.InquilinoID = ?";
        $stmt_garantes = mysqli_prepare($conn, $sql_garantes);
        mysqli_stmt_bind_param($stmt_garantes, "i", $inquilino['InquilinoID']);
        mysqli_stmt_execute($stmt_garantes);
        $result_garantes = mysqli_stmt_get_result($stmt_garantes);

        $garantes = [];
        if (mysqli_num_rows($result_garantes) > 0) {
            while ($garante = mysqli_fetch_assoc($result_garantes)) {
                $garantes[] = $garante;
            }
        }
        $inquilino['garantes'] = $garantes; // Asignar los garantes al inquilino
        mysqli_stmt_close($stmt_garantes);
    }
} else {
    echo "ID de propiedad no proporcionado.";
    exit;
}
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
        <li><a class="dropdown-item" href="#">
          <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#000000">
           <path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h280v80H200v560h280v80H200Zm440-160-55-58 102-102H360v-80h327L585-622l55-58 200 200-200 200Z"/>
          </svg> Cerrar sesion</a></li>
       </ul>
      </button>
     </div>
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
  
    <div class="container mt-4">
            <a href="propiedades.php" class="btn text-white" style="background-color: rgba(233, 128, 0, 0.92);">
                ← Volver a Propiedades
            </a>
        </div>
        <main class="container mt-4">
   <div class="card">
    <div class="card-header" >
     <h1 class="h5">Detalles de la Propiedad</h1>
    </div>
    <div class="card-body">
     <table class="table table-striped table-hover">
      <thead class="table-dark">
       <tr>
        <th>Fecha Ingreso</th>
        <th>Barrio</th>
        <th>Ciudad</th>
        <th>Dirección</th>
        <th>Nro</th>
        <th>Dominio</th>
        <th>Nro Partida</th>
        <th>Estado</th>
       </tr>
      </thead>
      <tbody>
       <tr>
        <td><?php echo $propiedad['Fecha']; ?></td>
        <td><?php echo $propiedad['Barrio']; ?></td>
        <td><?php echo $propiedad['Ciudad']; ?></td>
        <td><?php echo $propiedad['Direccion']; ?></td>
        <td><?php echo $propiedad['Nro']; ?></td>
        <td><?php echo $propiedad['Dominio']; ?></td>
        <td><?php echo $propiedad['NroPartida']; ?></td>
        <td><?php echo $propiedad['Estado']; ?></td>
       </tr>
      </tbody>
     </table>
    </div>
   </div>
   <div class="card mt-4">
    <div class="card-header">
     <h2 class="h5">Inquilinos</h2>
    </div>
    <div class="card-body">
     <?php if (!empty($inquilinos)): ?>
     <div class="table-responsive">
      <table class="table table-striped table-hover">
       <thead class="table-dark">
        <tr>
         <th>Fecha Ingreso</th>
         <th>Nombre</th>
         <th>Apellido</th>
         <th>Dirección</th>
         <th>DNI</th>
         <th>Dirección Personal</th>
         <th>Teléfono</th>
         <th>Mail</th>
        </tr>
       </thead>
       <tbody>
        <?php foreach ($inquilinos as $inquilino): ?>
        <tr>
         <td><?php echo $inquilino['Fecha']; ?></td>
         <td><?php echo $inquilino['Nombre']; ?></td>
         <td><?php echo $inquilino['Apellido']; ?></td>
         <td><?php echo $inquilino['Direccion']; ?></td>
         <td><?php echo $inquilino['DNI']; ?></td>
         <td><?php echo $inquilino['DireccionPersonal']; ?></td>
         <td><?php echo $inquilino['Telefono']; ?></td>
         <td><?php echo $inquilino['Mail']; ?></td>
        </tr>
        <?php endforeach; ?>
       </tbody>
      </table>
     </div>
     <?php else: ?>
     <div class="alert bg-warning">No hay inquilinos asociados</div>
     <?php endif; ?>
    </div>
   </div>
   <?php if (!empty($inquilinos)): ?>
   <?php foreach ($inquilinos as $inquilino): ?>
   <div class="card mt-4">
    <div class="card-header bg-secondary text-white">
     <h2 class="h5">Garantes de <?php echo $inquilino['Nombre'] . ' ' . $inquilino['Apellido']; ?></h2>
    </div>
    <div class="card-body">
     <?php if (!empty($inquilino['garantes'])): ?>
     <div class="table-responsive">
      <table class="table table-striped table-hover">
       <thead class="table-dark">
        <tr>
         <th>Fecha</th>
         <th>Nombre</th>
         <th>Apellido</th>
         <th>DNI</th>
         <th>Dirección</th>
         <th>Teléfono</th>
         <th>Mail</th>
        </tr>
       </thead>
       <tbody>
        <?php foreach ($inquilino['garantes'] as $garante): ?>
        <tr>
         <td><?php echo $garante['Fecha']; ?></td>
         <td><?php echo $garante['Nombre']; ?></td>
         <td><?php echo $garante['Apellido']; ?></td>
         <td><?php echo $garante['DNI']; ?></td>
         <td><?php echo $garante['Direccion']; ?></td>
         <td><?php echo $garante['Telefono']; ?></td>
         <td><?php echo $garante['Mail']; ?></td>
        </tr>
        <?php endforeach; ?>
       </tbody>
      </table>
     </div>
     <?php else: ?>
     <div class="alert alert-info">No hay garantes asociados</div>
     <?php endif; ?>
    </div>
   </div>
   <?php endforeach; ?>
   <?php endif; ?>
   <div class="modal fade" id="editarPropiedadModal" tabindex="-1" aria-labelledby="editarPropiedadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
     <div class="modal-content">
      <div class="modal-header">
       <h5 class="modal-title" id="exampleModalLabel">Actualizar Propiedad</h5>
       <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
       <form id="propiedadForm" action="actualizar_propiedad.php" method="POST">
        <input type="hidden" id="propiedadID" name="propiedadID" value="<?php echo $propiedad['PropiedadID']; ?>">
        <div class="mb-3">
         <label for="barrio">Barrio</label>
         <input type="text" class="form-control" id="barrio" name="barrio" value="<?php echo $propiedad['Barrio']; ?>" required>
        </div>
        <div class="mb-3">
         <label for="ciudad">Ciudad</label>
         <input type="text" class="form-control" id="ciudad" name="ciudad" value="<?php echo $propiedad['Ciudad']; ?>" required>
        </div>
        <div class="mb-3">
         <label for="direccion">Dirección</label>
         <input type="text" class="form-control" id="direccion" name="direccion" value="<?php echo $propiedad['Direccion']; ?>" required>
        </div>
        <div class="mb-3">
         <label for="nro">Nro</label>
         <input type="text" class="form-control" id="nro" name="nro" value="<?php echo $propiedad['Nro']; ?>">
        </div>
        <div class="mb-3">
         <label for="dominio">Dominio</label>
         <input type="text" class="form-control" id="dominio" name="dominio" value="<?php echo $propiedad['Dominio']; ?>" required>
        </div>
        <div class="mb-3">
         <label for="nroPartida">Nro Partida</label>
         <input type="text" class="form-control" id="nroPartida" name="nroPartida" value="<?php echo $propiedad['NroPartida']; ?>" required>
        </div>
        <div class="mb-3">
         <label for="estado">Estado</label>
         <select class="form-control" id="estado" name="estado">
          <option value="alquilada" <?php echo ($propiedad['Estado'] == 'alquilada') ? 'selected' : ''; ?>>Alquilada</option>
          <option value="en venta" <?php echo ($propiedad['Estado'] == 'en venta') ? 'selected' : ''; ?>>En Venta</option>
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
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous" defer></script>
   <script src="//cdn.datatables.net/2.2.2/js/dataTables.min.js" defer></script>
   <script>
    document.addEventListener("DOMContentLoaded", function () {
     document.querySelectorAll(".editar-propiedad").forEach(button => {
      button.addEventListener("click", function () {
       const modal = new bootstrap.Modal(document.getElementById("editarPropiedadModal"));
       document.getElementById("propiedadID").value = this.dataset.id;
       document.getElementById("barrio").value = this.dataset.barrio;
       document.getElementById("ciudad").value = this.dataset.ciudad;
       document.getElementById("direccion").value = this.dataset.direccion;
       document.getElementById("nro").value = this.dataset.nro;
       document.getElementById("dominio").value = this.dataset.dominio;
       document.getElementById("nroPartida").value = this.dataset.nropartida;
       document.getElementById("estado").value = this.dataset.estado;
       modal.show();
      });
     });
    });
   </script>
  </main>
 </body>
 </html>