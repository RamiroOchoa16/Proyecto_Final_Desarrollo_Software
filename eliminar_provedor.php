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

if (isset($_GET['id'])) {
    $idProveedor = $_GET['id'];
    
    $query =  "DELETE FROM provedores WHERE idProvedor = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $idProveedor, PDO::PARAM_INT);

    if ($stmt->execute()) {
        // Redirigir con mensaje de éxito
        header("Location: ListaProvedores.php?mensaje=Proveedor eliminado con éxito.");
        exit;
    } else {
        // Redirigir con mensaje de error
        header("Location: ListaProvedores.php?mensaje=Error al eliminar el Proveedor.");
        exit;
    }
} else {
    header("Location: ListaProvedores.php?mensaje=ID de Proveedor no especificado.");
        exit;
}


?>