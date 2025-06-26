<?php

session_start();


define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'login');


header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);


if ($link === false) {
    die("ERROR: No se pudo conectar a la base de datos. " . mysqli_connect_error());
}


$login_err = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW); // Password will be hashed, so raw is fine for now

    
    if (empty($username) || empty($password)) {
        $login_err = "Por favor, ingresa tu usuario y contraseña.";
    } else {
        
        $sql = "SELECT id, username, password FROM users WHERE username = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            
            $param_username = $username;
            
            
            if (mysqli_stmt_execute($stmt)) {
                
                mysqli_stmt_store_result($stmt);
                
                
                if (mysqli_stmt_num_rows($stmt) == 1) {                    
                    
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                    if (mysqli_stmt_fetch($stmt)) {
                        
                        if (password_verify($password, $hashed_password)) {
                            
                            session_regenerate_id(true); // Regenro la session del id por seguridad
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;                            
                            
                            // Redirigimos a la pagina segura que seria el Home en este caso puedo dejarlo como cliente(despues lo vemos bien)
                            header("location: ../inmobiliaria/clientes.php");
                            exit; // Redirigimos al inicio del login
                        } else {
                            // La contraseña no es valida
                            $login_err = "La contraseña que ingresaste no es válida.";
                        }
                    }
                } else {
                    // El usuario no existe
                    $login_err = "No existe una cuenta con ese nombre de usuario.";
                }
            } else {
                echo "¡Ups! Algo salió mal. Por favor, inténtalo de nuevo más tarde.";
            }

            // cerramos la consulta
            mysqli_stmt_close($stmt);
        }
    }
    
    
    if (!empty($login_err)) {
        
        // en la aplicacion real lo podemos redirigir a, redirect: header("Location: index.html?error=" . urlencode($login_err));
    }
}

// Cerramos conexion
mysqli_close($link);


if (!empty($login_err)) {
    
    echo "<script>alert('$login_err'); window.history.back();</script>";
}
?>