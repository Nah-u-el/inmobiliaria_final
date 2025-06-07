<?php
// Configuración de la Base de Datos (igual que en login.php)
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'login');

$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($link === false) {
    error_log("Error de conexión a la base de datos en reset_password.php: " . mysqli_connect_error());
    die("Lo sentimos, no pudimos conectar con la base de datos en este momento.");
}

$token = filter_input(INPUT_GET, 'token', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$message = '';
$show_form = false;
$user_id_for_reset = 0;

if (empty($token)) {
    $message = "Token de restablecimiento no proporcionado.";
} else {
    // 1. Verificar el token en la base de datos
    $sql = "SELECT user_id, expires_at, used FROM password_resets WHERE token = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $param_token);
        $param_token = $token;

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);

            if (mysqli_stmt_num_rows($stmt) == 1) {
                mysqli_stmt_bind_result($stmt, $user_id, $expires_at_db, $used_db);
                mysqli_stmt_fetch($stmt);

                // 2. Validar token: No usado y no expirado
                if ($used_db) {
                    $message = "Este enlace de restablecimiento ya ha sido utilizado.";
                } elseif (strtotime($expires_at_db) < time()) {
                    $message = "Este enlace de restablecimiento ha expirado.";
                } else {
                    // Token válido, mostrar el formulario para nueva contraseña
                    $show_form = true;
                    $user_id_for_reset = $user_id; // Almacenamos el user_id para usarlo al actualizar la contraseña
                }
            } else {
                $message = "Token de restablecimiento inválido.";
            }
        } else {
            error_log("Error al ejecutar la sentencia de búsqueda de token: " . mysqli_stmt_error($stmt));
            $message = "Hubo un problema. Por favor, inténtalo de nuevo más tarde.";
        }
        mysqli_stmt_close($stmt);
    } else {
        error_log("Error al preparar la sentencia de búsqueda de token: " . mysqli_error($link));
        $message = "Hubo un problema interno. Por favor, inténtalo de nuevo más tarde.";
    }
}

// Procesar el envío del formulario de nueva contraseña
if ($show_form && $_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new_password'])) {
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($new_password) || empty($confirm_password)) {
        $message = "Por favor, ingresa y confirma tu nueva contraseña.";
    } elseif ($new_password !== $confirm_password) {
        $message = "Las contraseñas no coinciden.";
    } elseif (strlen($new_password) < 8) { // Ejemplo de política de contraseña
        $message = "La contraseña debe tener al menos 8 caracteres.";
    } else {
        // 3. Hashear la nueva contraseña y actualizar en la DB
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Iniciar transacción para asegurar atomicidad
        mysqli_begin_transaction($link);
        $success = true;

        // Actualizar la contraseña del usuario
        $update_password_sql = "UPDATE users SET password = ? WHERE id = ?";
        if ($update_password_stmt = mysqli_prepare($link, $update_password_sql)) {
            mysqli_stmt_bind_param($update_password_stmt, "si", $hashed_password, $user_id_for_reset);
            if (!mysqli_stmt_execute($update_password_stmt)) {
                $success = false;
                error_log("Error al actualizar contraseña: " . mysqli_stmt_error($update_password_stmt));
            }
            mysqli_stmt_close($update_password_stmt);
        } else {
            $success = false;
            error_log("Error al preparar actualización de contraseña: " . mysqli_error($link));
        }

        // Marcar el token como usado para evitar reuso
        if ($success) {
            $mark_token_used_sql = "UPDATE password_resets SET used = TRUE WHERE token = ?";
            if ($mark_token_used_stmt = mysqli_prepare($link, $mark_token_used_sql)) {
                mysqli_stmt_bind_param($mark_token_used_stmt, "s", $token);
                if (!mysqli_stmt_execute($mark_token_used_stmt)) {
                    $success = false;
                    error_log("Error al marcar token como usado: " . mysqli_stmt_error($mark_token_used_stmt));
                }
                mysqli_stmt_close($mark_token_used_stmt);
            } else {
                $success = false;
                error_log("Error al preparar actualización de token: " . mysqli_error($link));
            }
        }

        if ($success) {
            mysqli_commit($link);
            $message = "Tu contraseña ha sido restablecida con éxito. Ya puedes <a href='index.html'>iniciar sesión</a>.";
            $show_form = false; // Ocultar el formulario después del éxito
        } else {
            mysqli_rollback($link); // Revertir cambios si algo falló
            $message = "No se pudo restablecer la contraseña. Por favor, inténtalo de nuevo más tarde.";
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
    <title>Restablecer Contraseña</title>
    <link rel="stylesheet" href="styles.css"> <style>
        /* (Tus estilos CSS del login-container y body, etc.) */
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

        .login-container {
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
        .password-toggle { /* Mantener para la funcionalidad de mostrar/ocultar contraseña */
            position: absolute;
            right: 15px;
            top: 70%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #999;
            font-size: 0.9em;
            user-select: none;
            transition: color 0.3s ease;
        }

        .password-toggle:hover {
            color: #FF7F00;
        }

        .btn-submit { /* Reutilizamos el estilo del botón de enviar */
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
            color: #28A745;
            margin-top: 15px;
            font-size: 0.95em;
        }
        .message-error {
            color: #E74C3C;
            margin-top: 15px;
            font-size: 0.95em;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Restablecer Contraseña</h2>
        
        <?php if (!empty($message)): ?>
            <div class="<?php echo (strpos($message, 'éxito') !== false) ? 'message-success' : 'message-error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if ($show_form): ?>
            <p>Por favor, ingresa tu nueva contraseña.</p>
            <form action="reset_password.php?token=<?php echo htmlspecialchars($token); ?>" method="post">
                <div class="input-group">
                    <label for="new_password">Nueva Contraseña</label>
                    <input type="password" id="new_password" name="new_password" placeholder="Mínimo 8 caracteres" required>
                    <span class="password-toggle" id="toggleNewPassword">Mostrar</span>
                </div>
                <div class="input-group">
                    <label for="confirm_password">Confirmar Contraseña</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Repite la nueva contraseña" required>
                    <span class="password-toggle" id="toggleConfirmPassword">Mostrar</span>
                </div>
                <button type="submit" class="btn-submit">Restablecer Contraseña</button>
            </form>
        <?php endif; ?>
        <p style="margin-top: 20px;"><a href="index.html" style="color: #FF7F00; text-decoration: none;">Volver al inicio de sesión</a></p>
    </div>

    <script>
        // Funcionalidad para mostrar/ocultar contraseñas
        const toggleNewPassword = document.getElementById('toggleNewPassword');
        const newPasswordInput = document.getElementById('new_password');

        if (toggleNewPassword && newPasswordInput) {
            toggleNewPassword.addEventListener('click', function () {
                const type = newPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                newPasswordInput.setAttribute('type', type);
                this.textContent = type === 'password' ? 'Mostrar' : 'Ocultar';
            });
        }

        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const confirmPasswordInput = document.getElementById('confirm_password');
        
        if (toggleConfirmPassword && confirmPasswordInput) {
            toggleConfirmPassword.addEventListener('click', function () {
                const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                confirmPasswordInput.setAttribute('type', type);
                this.textContent = type === 'password' ? 'Mostrar' : 'Ocultar';
            });
        }
    </script>
</body>
</html>