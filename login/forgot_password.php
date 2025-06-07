<?php
// Configuración de la Base de Datos (igual que en login.php)
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'login');

$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($link === false) {
    error_log("Error de conexión a la base de datos en forgot_password.php: " . mysqli_connect_error());
    die("Lo sentimos, no pudimos conectar con la base de datos en este momento.");
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Por favor, introduce una dirección de correo electrónico válida.";
    } else {
        // 1. Verificar si el correo existe en la base de datos
        $sql = "SELECT id FROM users WHERE email = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            $param_email = $email;

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 1) {
                    mysqli_stmt_bind_result($stmt, $user_id);
                    mysqli_stmt_fetch($stmt);

                    // 2. Generar un token único y seguro
                    $token = bin2hex(random_bytes(32)); // Genera un token de 64 caracteres hexadecimales
                    $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token válido por 1 hora

                    // 3. Almacenar el token en la base de datos
                    $insert_sql = "INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)";
                    if ($insert_stmt = mysqli_prepare($link, $insert_sql)) {
                        mysqli_stmt_bind_param($insert_stmt, "iss", $user_id, $token, $expires_at);
                        if (mysqli_stmt_execute($insert_stmt)) {
                            // 4. Enviar el correo electrónico al usuario
                            // ¡IMPORTANTE!: Configura tu servidor de correo para que funcione (ej. PHPMailer, Mailgun, SendGrid)
                            $reset_link = "http://" . $_SERVER['HTTP_HOST'] . "/reset_password.php?token=" . $token; // CAMBIA A HTTPS EN PRODUCCIÓN

                            $subject = "Restablecimiento de Contraseña para tu Inmobiliaria";
                            $body = "Hola,\n\n";
                            $body .= "Hemos recibido una solicitud para restablecer la contraseña de tu cuenta. ";
                            $body .= "Si no fuiste tú, por favor ignora este correo.\n\n";
                            $body .= "Para restablecer tu contraseña, haz clic en el siguiente enlace:\n";
                            $body .= $reset_link . "\n\n";
                            $body .= "Este enlace expirará en 1 hora.\n\n";
                            $body .= "Gracias,\nTu equipo de Inmobiliaria";

                            // Aquí iría el código para enviar el correo.
                            // Para fines de ejemplo, solo mostramos el enlace.
                            // En un entorno real, usarías una librería como PHPMailer.
                            
                            // Ejemplo simple (NO USAR EN PRODUCCIÓN para envío real):
                            // mail($email, $subject, $body, "From: no-reply@tudominio.com"); 
                            
                            $message = "Se ha enviado un enlace para restablecer tu contraseña a tu dirección de correo electrónico. " . 
                                       "**Para pruebas, el enlace es:** <a href='" . htmlspecialchars($reset_link) . "'>Haz clic aquí</a>";

                        } else {
                            error_log("Error al insertar token de restablecimiento: " . mysqli_stmt_error($insert_stmt));
                            $message = "Hubo un problema al procesar tu solicitud. Por favor, inténtalo de nuevo.";
                        }
                        mysqli_stmt_close($insert_stmt);
                    } else {
                        error_log("Error al preparar la sentencia de inserción de token: " . mysqli_error($link));
                        $message = "Hubo un problema interno. Por favor, inténtalo de nuevo más tarde.";
                    }
                } else {
                    // No dar pistas si el correo existe o no para evitar enumeración de usuarios
                    $message = "Si tu dirección de correo electrónico está registrada, recibirás un enlace para restablecer tu contraseña.";
                }
            } else {
                error_log("Error al ejecutar la sentencia de búsqueda de usuario: " . mysqli_stmt_error($stmt));
                $message = "Hubo un problema. Por favor, inténtalo de nuevo más tarde.";
            }
            mysqli_stmt_close($stmt);
        } else {
            error_log("Error al preparar la sentencia de búsqueda de usuario: " . mysqli_error($link));
            $message = "Hubo un problema interno. Por favor, inténtalo de nuevo más tarde.";
        }
    }
}
mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <link rel="stylesheet" href="styles.css"> <style>
        /* (Tus estilos CSS del login-container y body, etc., o importados de styles.css) */
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
        }

        .login-container { /* Reutilizamos esta clase para el formulario de recuperación */
            background-color: #FFFFFF;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .login-container h2 {
            color: #FF7F00;
            margin-bottom: 10px;
            font-weight: 600;
            font-size: 2.2em;
        }
        .login-container p {
            color: #666;
            margin-bottom: 30px;
            font-size: 1.1em;
            line-height: 1.6;
        }
        .input-group {
            margin-bottom: 25px;
            text-align: left;
            position: relative;
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
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }

        .input-group input:focus {
            border-color: #FF7F00;
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 127, 0, 0.1);
        }
        .btn-submit { /* Nuevo estilo para el botón de enviar email */
            background-color: #FF7F00;
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

        .btn-submit:hover {
            background-color: #E66A00;
            transform: translateY(-2px);
        }
        .message-success {
            color: #28A745; /* Verde para éxito */
            margin-top: 15px;
            font-size: 0.95em;
        }
        .message-error {
            color: #E74C3C; /* Rojo para errores */
            margin-top: 15px;
            font-size: 0.95em;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Recuperar Contraseña</h2>
        <p>Introduce tu dirección de correo electrónico y te enviaremos un enlace para restablecer tu contraseña.</p>

        <?php if (!empty($message)): ?>
            <div class="<?php echo (strpos($message, 'enviado') !== false || strpos($message, 'registrada') !== false) ? 'message-success' : 'message-error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form action="forgot_password.php" method="post">
            <div class="input-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" placeholder="Tu correo electrónico" required>
            </div>
            <button type="submit" class="btn-submit">Enviar Enlace de Restablecimiento</button>
        </form>
        <p style="margin-top: 20px;"><a href="index.html" style="color: #FF7F00; text-decoration: none;">Volver al inicio de sesión</a></p>
    </div>
</body>
</html>