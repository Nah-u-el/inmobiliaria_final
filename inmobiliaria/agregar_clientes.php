<?php
require_once 'conexion.php'; // Conexión segura

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Iniciar transacción
    $conn->begin_transaction();

    try {
        // Insertar cliente
        $stmt = $conn->prepare("INSERT INTO clientes (Fecha, Nombre, Apellido, Direccion, Dni, DireccionPersonal, Telefono, Mail) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss",
            $_POST['Fecha'],
            $_POST['Nombre'],
            $_POST['Apellido'],
            $_POST['Direccion'],
            $_POST['DNI'],
            $_POST['DireccionPersonal'],
            $_POST['Telefono'],
            $_POST['Mail']
        );
        $stmt->execute();
        $cliente_id = $conn->insert_id;
        $stmt->close();

        // Función para insertar garante si existen los datos
        function insertar_garante($conn, $cliente_id, $prefix) {
            $campos = ['fecha', 'nombre', 'apellido', 'direccion', 'dni', 'direccion_personal', 'telefono', 'mail'];
            foreach ($campos as $campo) {
                if (empty($_POST[$prefix . $campo])) return;
            }

            $stmt = $conn->prepare("INSERT INTO garantes (ClienteID, Fecha, Nombre, Apellido, Direccion, Dni, DireccionPersonal, Telefono, Mail)
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issssssss",
                $cliente_id,
                $_POST[$prefix . 'fecha'],
                $_POST[$prefix . 'nombre'],
                $_POST[$prefix . 'apellido'],
                $_POST[$prefix . 'direccion'],
                $_POST[$prefix . 'dni'],
                $_POST[$prefix . 'direccion_personal'],
                $_POST[$prefix . 'telefono'],
                $_POST[$prefix . 'mail']
            );
            $stmt->execute();
            $stmt->close();
        }

        // Insertar garantes si existen
        insertar_garante($conn, $cliente_id, "garante1_");
        insertar_garante($conn, $cliente_id, "garante2_");

        $conn->commit();
        echo "<script>alert('Cliente y garantes agregados correctamente'); window.location.href='clientes.php';</script>";

    } catch (Exception $e) {
        $conn->rollback();
        error_log("Error al agregar cliente o garantes: " . $e->getMessage());
        echo "<script>alert('Ocurrió un error al agregar los datos. Intente nuevamente.'); window.location.href='clientes.php';</script>";
    }

    $conn->close();
}
?>
