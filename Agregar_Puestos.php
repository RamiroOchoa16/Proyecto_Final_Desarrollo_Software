<?php
// Configuración de la base de datos
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
    
    $puesto = $_POST['puesto'];
    $salario = $_POST['salario'];

    
    $stmt = $conexion->prepare("INSERT INTO puestos (puesto, salario) VALUES (?, ?)");
    if ($stmt) {
        // Vincular parámetros
        $stmt->bind_param("si", $puesto, $salario);
        
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
}

// Cerrar la conexión
$conexion->close();
?>
