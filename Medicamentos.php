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


function insertarMedicamento($pdo, $nombre, $idClasificacion, $cantidad, $precio_c, $precio_v, $idEliminacion, $idProveedor, $descripcion, $estatus,$fechaCaducidad) {
    $query = "
        INSERT INTO medicamentos (Nombre, idClasificacion, Cantidad, PrecioCompra, PrecioVenta, idEliminacion, idProvedor, Descripcion, Estatus,FechaCaducidad) 
        VALUES (:nombre, :idClasificacion, :cantidad, :precio_c, :precio_v, :idEliminacion, :idProveedor, :descripcion, :estatus, :fechacaducidad)";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':idClasificacion', $idClasificacion);
    $stmt->bindParam(':cantidad', $cantidad);
    $stmt->bindParam(':precio_c', $precio_c);
    $stmt->bindParam(':precio_v', $precio_v);
    $stmt->bindParam(':idEliminacion', $idEliminacion);
    $stmt->bindParam(':idProveedor', $idProveedor);
    $stmt->bindParam(':descripcion', $descripcion);
    $stmt->bindParam(':estatus', $estatus);
    $stmt->bindParam(':fechacaducidad', $fechaCaducidad);

    if ($stmt->execute()) {
        echo "<p>Medicamento agregado con éxito.</p>";
    } else {
        echo "<p>Error al agregar el medicamento.</p>";
    }

}



function obtenerMedicamentos($pdo) {
    $query = "
        SELECT 
            M.idMedicamento, M.Nombre, C.Tipo AS Clasificacion, M.Cantidad, M.PrecioCompra, M.PrecioVenta, 
            E.MedRegresable, P.Nombre AS Provedor, M.Descripcion, M.Estatus, M.fechaCaducidad
        FROM medicamentos M
        JOIN clasificacion C ON M.idClasificacion = C.idClasificacion
        JOIN eliminacionmedicamento E ON M.idEliminacion = E.idEliminacion
        JOIN provedores P ON M.idProvedor = P.idProvedor";
    
        $stmt = $pdo->prepare($query);  
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = strtoupper($_POST['nombre']);
    $idClasificacion = $_POST['idClasificacion'];
    $cantidad = $_POST['cantidad'];
    $precio_c = $_POST['precio_c'];
    $precio_v = $_POST['precio_v'];
    $idEliminacion = $_POST['idEliminacion'];
    $idProveedor = $_POST['idProvedor'];
    $descripcion = strtoupper($_POST['descripcion']);
    $estatus = 'Disponible'; 
    $fechaCaducidad = $_POST['fechaCaducidad'];

    insertarMedicamento($pdo, $nombre, $idClasificacion, $cantidad, $precio_c, $precio_v, $idEliminacion, $idProveedor, $descripcion, $estatus,$fechaCaducidad);
}

$medicamentos = obtenerMedicamentos($pdo);

?>

<?php

if (isset($_GET['mensaje'])) {
    echo "<div class='mensaje'>" . htmlspecialchars($_GET['mensaje']) . "</div>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Medicamentos</title>
    <link rel="icon" type="image/x-icon" href="img/inventario.png">
    <link rel="stylesheet" href="styleListas.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            color: #333;
            margin: 20px 0;
        }

        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #6c83d0;
            color: white;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        td {
            border-bottom: 1px solid #ddd;
        }

        button {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #6c83d0;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        button:hover {
            background-color: #5a6dbd;
        }
    </style>
</head>
<body>

<h1>Lista de Medicamentos</h1>

<!-- Tabla para mostrar los medicamentos -->
<table>
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Clasificación</th>
            <th>Cantidad</th>
            <th>Precio Compra</th>
            <th>Precio Venta</th>
            <th>Medicación Regresable</th>
            <th>Proveedor</th>
            <th>Descripción</th>
            <th>Estatus</th>
            <th>Fecha de Caducidad</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($medicamentos as $medicamento): ?>
            <tr>
                <td><?php echo htmlspecialchars($medicamento['Nombre']); ?></td>
                <td><?php echo htmlspecialchars($medicamento['Clasificacion']); ?></td>
                <td><?php echo htmlspecialchars($medicamento['Cantidad']); ?></td>
                <td><?php echo htmlspecialchars($medicamento['PrecioCompra']); ?></td>
                <td><?php echo htmlspecialchars($medicamento['PrecioVenta']); ?></td>
                <td><?php echo htmlspecialchars($medicamento['MedRegresable']); ?></td>
                <td><?php echo htmlspecialchars($medicamento['Provedor']); ?></td>
                <td><?php echo htmlspecialchars($medicamento['Descripcion']); ?></td>
                <td><?php echo htmlspecialchars($medicamento['Estatus']); ?></td>
                <td><?php echo htmlspecialchars($medicamento['fechaCaducidad']); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<button onclick="window.location.href='Pagina_Principal.html';">
    Regresar
</button>

</body>
</html>
