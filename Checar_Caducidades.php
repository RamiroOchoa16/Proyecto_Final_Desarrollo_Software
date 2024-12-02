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

// Función para obtener todos los medicamentos

function obtenerMedicamentos($pdo) {
    $query = "
        SELECT 
            M.idMedicamento, M.Nombre, C.Tipo AS Clasificacion, M.Cantidad,
            E.MedRegresable, E.Detalle, P.Nombre AS Provedor, M.FechaCaducidad,
            M.DiasRestantes, M.EstadoCaducidad, 
            O.FechaInicio AS OfertaInicio, O.FechaFin AS OfertaFin, O.PorcentajeDescuento
        FROM medicamentos M
        JOIN clasificacion C ON M.idClasificacion = C.idClasificacion
        JOIN eliminacionmedicamento E ON M.idEliminacion = E.idEliminacion
        JOIN provedores P ON M.idProvedor = P.idProvedor
        LEFT JOIN ofertas O ON M.idMedicamento = O.idMedicamento
        WHERE M.Estatus IN ('Disponible', 'Caducado')";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-top: 20px;
        }

        table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #4e73df;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        img {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        button {
            background-color: #6c83d0;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        button:hover {
            background-color: #5a6dbd;
        }

        button:active {
            background-color: #4e73df;
        }
    </style>
</head>
<body>

<h1>Lista Caducidades</h1>

<!-- Tabla para mostrar los medicamentos -->
<table>
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Clasificación</th>
            <th>Cantidad</th>
            <th>Medicación Regresable</th>
            <th>Características</th>
            <th>Proveedor</th>
            <th>Fecha de Caducidad</th>
            <th>Días Restantes</th>
            <th>Estatus Caducidad</th>
            <th>Oferta (Inicio - Fin)</th>
            <th>Porcentaje Descuento</th>
            <th>Poner en Oferta</th>
            <th>Eliminar</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($medicamentos as $medicamento): ?>
            <tr>
                <td><?php echo htmlspecialchars($medicamento['Nombre']); ?></td>
                <td><?php echo htmlspecialchars($medicamento['Clasificacion']); ?></td>
                <td><?php echo htmlspecialchars($medicamento['Cantidad']); ?></td>
                <td><?php echo htmlspecialchars($medicamento['MedRegresable']); ?></td>
                <td><?php echo htmlspecialchars($medicamento['Detalle']); ?></td>
                <td><?php echo htmlspecialchars($medicamento['Provedor']); ?></td>
                <td><?php echo htmlspecialchars($medicamento['FechaCaducidad']); ?></td>
                <td><?php echo htmlspecialchars($medicamento['DiasRestantes']); ?></td>
                <td><?php echo htmlspecialchars($medicamento['EstadoCaducidad']); ?></td>
                <td>
                    <?php 
                        if ($medicamento['OfertaInicio'] && $medicamento['OfertaFin']) {
                            echo htmlspecialchars($medicamento['OfertaInicio'] . " - " . $medicamento['OfertaFin']);
                        } else {
                            echo "Sin oferta";
                        }
                    ?>
                </td>
                <td>
                    <?php echo $medicamento['PorcentajeDescuento'] ? htmlspecialchars($medicamento['PorcentajeDescuento'] . "%") : "N/A"; ?>
                </td>
                <td>
                    <a href="Agregar_Oferta.php?id=<?php echo htmlspecialchars($medicamento['idMedicamento']); ?>">
                        <img src="img/oferta-especial.png" alt="Poner en oferta">
                    </a>
                </td>
                <td>
                    <a href="eliminar_caducidad.php?id=<?php echo htmlspecialchars($medicamento['idMedicamento']); ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar este medicamento?');">
                        <img src="img/Eliminar.png" alt="Eliminar">
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>


<button onclick="window.location.href='Pagina_Principal.html';">
    Regresar
</button>

</body>
</html>
