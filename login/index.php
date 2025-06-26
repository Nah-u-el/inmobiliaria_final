<?php
session_start();
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("Location: ../inmobiliaria/clientes.php");
    exit;
}
?>
<script>
window.addEventListener("pageshow", function (event) {
  if (event.persisted) {
    // La página fue cargada desde el bfcache (botón atrás o adelante)
    window.location.reload(); // Fuerza recarga y vuelve a verificar la sesión
  }
});
</script>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SC Inmobiliaria San Cristobal</title>

    <link rel="icon" type="image/ico" href="img/favicon.ico">
    <link rel="icon" type="image/png" href="img/favicon-16x16.png">
    <link rel="icon" type="image/png" href="img/android-chrome-192x192.png">
    <link rel="icon" type="image/png" href="img/android-chrome-512x512.png">
    <link rel="icon" type="image/png" href="img/apple-touch-icon.png">
    <link rel="icon" type="image/png" href="img/favicon-32x32.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="css/style.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');
        /* probar hacerlo un archivo css, si no queda lo dejamos como un style y lo modificamos*/
        
        /* fijarse cual queda mejor y elegir al final*/

        /*body {
            font-family: 'Poppins', sans-serif;
            background-color: #F8F8F8; 
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            /* Eliminamos padding del body para que no empuje el contenido fuera de la pantalla */
            /* padding: 20px; */ 
          /*  box-sizing: border-box; /* Importante para que padding y border no aumenten el tamaño */
          /*  overflow: hidden; /* <-- AÑADIDO: Evita barras de desplazamiento en el body */
        /*}

        .login-container {
            background-color: #FFFFFF; /* Blanco puro para el formulario */
          /*  padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
            /* AÑADIDO: Limitar altura y permitir desplazamiento solo si es necesario dentro del contenedor */
           /* max-height: 90vh; /* <-- Ajusta esto. El 90% del viewport height para dejar un margen */
            /* overflow-y: auto; /* <-- AÑADIDO: Permite desplazamiento vertical SOLO dentro del contenedor si su contenido excede max-height */
            /*box-sizing: border-box; /* Asegura que el padding se incluya en el max-width/height */
        /*} */
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #F8F8F8; /* Fondo blanco muy suave */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
        }

        .login-container {
            background-color: #FFFFFF; /* Blanco puro para el formulario */
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        /* ESTILOS PARA EL EFECTO DE REVELACIÓN */
        .login-form-content {
            /* Por defecto, oculto */
            opacity: 0;
            transform: translateY(20px); /* Un poco hacia abajo */
            transition: opacity 1s ease-out, transform 1s ease-out; /* Transición suave */
            pointer-events: none; /* Deshabilita interacciones mientras está oculto */
        }

        .login-form-content.visible {
            /* Visible y en su posición final */
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto; /* Habilita interacciones */
        }
        /* --- FIN DE ESTILOS PARA EL EFECTO DE REVELACIÓN --- */

        .login-container h2 {
            color: #FF7F00; /* Naranja vibrante para el título */
            margin-bottom: 10px;
            font-weight: 600;
            font-size: 2.2em;
        }

        .login-container p {
            color: #666;
            margin-bottom: 20px;
            font-size: 1.1em;
            line-height: 1.6;
        }

        .input-group {
            margin-bottom: 25px;
            text-align: left;
            position: relative; /* Añadido para posicionar el icono, probar que extencion le queda mejor */
        }

        .input-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 400;
            font-size: 0.95em;
        }

        .input-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #E0E0E0;
            border-radius: 8px;
            font-size: 1em;
            box-sizing: border-box; /* Asegura que el padding no aumente el ancho */
            transition: border-color 0.3s ease;
        }

        .input-group input:focus {
            border-color: #FF7F00; /* Borde naranja al enfocar */
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 127, 0, 0.1); /* Sutil sombra al enfocar */
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 70%; /* Ajusta esto si el input o label tienen diferente altura */
            transform: translateY(-50%);
            cursor: pointer;
            color: #999;
            font-size: 0.9em;
            user-select: none; /* Evita que el texto se seleccione */
            transition: color 0.3s ease;
        }

        .password-toggle:hover {
            color: #FF7F00;
        }

        .btn-login {
            background-color: #FF7F00; /* Naranja vibrante para el botón */
            color: #FFFFFF;
            padding: 15px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1em;
            font-weight: 600;
            width: 100%;
            transition: background-color 0.3s ease, transform 0.2s ease;
            box-shadow: 0 4px 10px rgba(255, 127, 0, 0.2);
        }

        .btn-login:hover {
            background-color: #E66A00; /* Naranja ligeramente más oscuro al pasar el mouse */
            transform: translateY(-2px); /* Pequeño efecto de elevación */
        }

        .forgot-password {
            margin-top: 25px;
            font-size: 0.9em;
        }

        .forgot-password a {
            color: #FF7F00;
            text-decoration: none;
            transition: color 0.3s ease;
            font-weight: 500;
        }

        .forgot-password a:hover {
            color: #E66A00;
            text-decoration: underline;
        }

        .error-message {
            color: #E74C3C; /* Rojo para errores */
            font-size: 0.85em;
            margin-top: 5px;
            text-align: left;
            display: block;
        }

        .login-logo {
    width: 150px; /* Ajusta el tamaño según prefieras */
    height: auto; /* Mantiene la proporción */
    margin-bottom: 1px; /* Espacio debajo del logo */
}
    </style>
</head>
<body>
    <div class="login-container">
        <img src="img_login/descarga.png" alt="SC Inmobiliaria" class="login-logo">
    
        <p>Accede a tu panel de administración.</p>

        <form action="login.php" method="post">
            <div class="input-group">
                <label for="username">Usuario</label>
                <input type="text" id="username" name="username" placeholder="Tu nombre de usuario" required>
            </div>
            <div class="input-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="Tu contraseña" required>
                <span class="password-toggle" id="togglePassword">Mostrar</span>
            </div>
            <button type="submit" class="btn-login">Ingresar</button>
        </form>
        
    </div>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');

        togglePassword.addEventListener('click', function (e) {
            // Alternar el tipo de atributo
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            // Cambiar el texto del span(que oculte o muestre la contraseña)
            this.textContent = type === 'password' ? 'Mostrar' : 'Ocultar';
        });
    </script>
    <script>
        // JS para el efecto de revelación
        document.addEventListener('DOMContentLoaded', function() {
            const loginFormContent = document.getElementById('loginFormContent');
            
            // Espera 1000 milisegundos (1 segundo) antes de mostrar el formulario
            setTimeout(() => {
                loginFormContent.classList.add('visible');
            }, 2000); // 1000 ms = 1 segundo
        });

        // JS para mostrar/ocultar contraseña (ya lo tenías)
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');

        togglePassword.addEventListener('click', function (e) {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.textContent = type === 'password' ? 'Mostrar' : 'Ocultar';
        });
    </script>
</body>
</html>