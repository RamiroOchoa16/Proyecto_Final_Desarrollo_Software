<?php

$server = "localhost";
$user = "root";
$pass = "";
$db = "farmacia";

// Crear conexión
$conexion = mysqli_connect($server, $user, $pass, $db);

// Verificar conexión
if (!$conexion) {
    die("Conexión fallida: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    if (isset($_POST['MedRegresable']) && !empty(trim($_POST['MedRegresable']))) {
        $medregresable = trim($_POST['MedRegresable']); 

        
        $stmt = $conexion->prepare("INSERT INTO eliminacionmedicamento (MedRegresable) VALUES (?)");
        if ($stmt) {
            // Vincular parámetros
            $stmt->bind_param("s", $medregresable);
            
            // Ejecutar la consulta
            if ($stmt->execute()) {
                echo "Registro completo.";
            } else {
                echo "Error al registrar: " . $stmt->error;
            }
            
            // Cerrar la declaración
            $stmt->close();
        } else {
            echo "Error al preparar la consulta: " . $conexion->error;
        }
    } else {
        echo "Por favor, complete el campo 'Tipo'.";
    }
}

// Cerrar la conexión
$conexion->close();
?>
