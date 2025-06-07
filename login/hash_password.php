<?php
$password = "inmobiliaria511"; // Cambiamos el texto, que no este separado
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
echo $hashed_password;
?>