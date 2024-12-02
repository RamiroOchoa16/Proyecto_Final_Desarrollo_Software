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
    $idMedicamento = $_GET['id'];

    
    $query = "UPDATE medicamentos SET Estatus = 'Eliminado' WHERE idMedicamento = :idMedicamento";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':idMedicamento', $idMedicamento, PDO::PARAM_INT);

    if ($stmt->execute()) {
       
        header("Location: Checar_Caducidades.php?mensaje=Medicamento eliminado con éxito.");
        exit;
    } else {
       
        header("Location: Checar_Caducidades.php?mensaje=Error al eliminar el Medicamento.");
        exit;
    }
} else {
    
    header("Location: Checar_Caducidades.php?mensaje=ID no especificado");
    exit();
}
?>