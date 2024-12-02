<?php
$host = 'localhost'; 
$dbname = 'farmacia';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error en la conexión: " . $e->getMessage());
}


if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $idEmpleado = $_GET['id'];

    
    $query = "DELETE FROM empleados WHERE idEmpleado = :idEmpleado";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':idEmpleado', $idEmpleado);

    if ($stmt->execute()) {
        
        header("Location: ListaEmpleados.php?mensaje=Empleado eliminado con éxito.");
        exit;
    } else {
        
        header("Location: ListaEmpleados.php?mensaje=Error al eliminar el empleado.");
        exit;
    }
} else {
    
    header("Location: ListaEmpleados.php?mensaje=ID de empleado no válido.");
    exit;
}
?>