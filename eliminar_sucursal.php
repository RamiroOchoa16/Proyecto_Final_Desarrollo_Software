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
    $idSucursal = $_GET['id'];
    
    
    $query = "UPDATE sucursal SET Estatus = 'Inactivo' WHERE idSucursal = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $idSucursal, PDO::PARAM_INT);

    if ($stmt->execute()) {
        
        header("Location: ListaSucursales.php?mensaje=Sucursal Eliminada.");
        exit;
    } else {
        
        header("Location: ListaSucursales.php?mensaje=Error al eliminar la sucursal.");
        exit;
    }
} else {
    header("Location: ListaSucursales.php?mensaje=ID de Sucursal no válido o no especificado.");
    exit;
}
?>