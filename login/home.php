<?php
// home.php

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
    // Es crucial usar window.location.replace() en el cliente si es posible,
    // o un encabezado de Location con un exit para el servidor.
    // Aquí, estamos del lado del servidor.
    header("location: index.php"); // Redirige al login
    exit; // Termina la ejecución del script
}

// Opcional: Regenerar ID de sesión periódicamente por seguridad (no estrictamente necesario para este problema, pero buena práctica)
// if (!isset($_SESSION['LAST_ACTIVITY']) || (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) { // 30 minutos
//     session_regenerate_id(true); // Regenera el ID de sesión
//     $_SESSION['LAST_ACTIVITY'] = time(); // Actualiza el tiempo de la última actividad
// }

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <title>Dashboard - Inmobiliaria</title>
    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    <link rel="icon" type="image/png" href="img/favicon-16x16.png">
    <link rel="icon" type="image/png" href="img/android-chrome-192x192.png">
    <link rel="icon" type="image/png" href="img/android-chrome-512x512.png">
    <link rel="icon" type="image/png" href="img/apple-touch-icon.png">
    <link rel="icon" type="image/png" href="img/favicon-32x32.png">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #F8F8F8;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
            text-align: center;
        }
        .dashboard-container {
            background-color: #FFFFFF;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
        }
        h2 {
            color: #FF7F00;
            margin-bottom: 20px;
            font-weight: 600;
            font-size: 2.2em;
        }
        p {
            color: #333;
            font-size: 1.1em;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .btn-logout {
            background-color: #FF7F00;
            color: #FFFFFF;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1em;
            font-weight: 600;
            transition: background-color 0.3s ease, transform 0.2s ease;
            text-decoration: none; /* Asegúrate de que los enlaces parezcan botones */
            display: inline-block; /* Para que padding funcione correctamente en <a> */
        }
        .btn-logout:hover {
            background-color: #E66A00;
            transform: translateY(-2px);
        }
    </style>
</head>
<body style="display: none;">
    <div class="dashboard-container">
        <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h2>
        <p>Has iniciado sesión correctamente. Aquí puedes gestionar tus propiedades.</p>
        <a href="logout.php" class="btn-logout">Cerrar Sesión</a>
    </div>

    <script>
        // Este script es CRUCIAL para Firefox y el bfcache.
        // Se ejecuta cuando la página se carga o se restaura desde el bfcache.
        window.addEventListener('pageshow', function(event) {
            // event.persisted es true si la página se está restaurando desde el bfcache
            if (event.persisted) {
                console.log('Página cargada desde bfcache. Forzando recarga para verificar sesión...');
                // Fuerza una recarga completa de la página, lo que obligará al servidor
                // a revalidar la sesión. Si no hay sesión, home.php redirigirá.
                window.location.reload(); 
            }
        });
    </script>
    <script>
    // Borra el historial y bloquea navegación hacia atrás
    (function () {
        // Reemplaza el historial con esta página para evitar ir hacia atrás
        history.replaceState(null, "", location.href);
        history.pushState(null, "", location.href);

        // Si se intenta volver atrás, destruye sesión
        window.addEventListener("popstate", function () {
            // Cierra sesión automáticamente
            window.location.href = "logout.php";
        });

        // Extra: al restaurar desde cache (como en Firefox bfcache)
        window.addEventListener("pageshow", function (event) {
            if (event.persisted) {
                // Si fue restaurada desde bfcache, forzar recarga y logout
                window.location.href = "logout.php";
            }
        });
    })();
    document.body.style.display = "block";
    </script>
    <script>
    // Seguridad adicional: Verifica la sesión en JS (solo si puedes exponer una variable mínima)
    const isLoggedIn = <?php echo json_encode(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true); ?>;
    if (!isLoggedIn) {
        window.location.replace("index.php");
    }
    </script>


</body>
</html>