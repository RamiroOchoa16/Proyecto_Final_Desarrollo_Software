<?php

$host = 'localhost'; 
$dbname = 'farmacia';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $s) {
    die("Error en la conexión: " . $s->getMessage());
}


function insertarProvedor($pdo, $nombre, $telefono, $direccion) {
    try {
        
        $query = "INSERT INTO provedores (Nombre, Telefono, Direccion) 
                  VALUES (:nombre, :telefono, :direccion)";
        $stmt = $pdo->prepare($query);
        
        
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':direccion', $direccion);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            echo "<p>Proveedor agregada con éxito.</p>";
        } else {
            echo "<p>Error al agregar Proveedor.</p>";
        }
    } catch (PDOException $e) {
        echo "Error en la inserción: " . $e->getMessage();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir datos del formulario
    $nombre = $_POST['Nombre'];
    $telefono = $_POST['Telefono'];
    $direccion = $_POST['Direccion'];

    insertarProvedor($pdo, $nombre, $telefono, $direccion);

    
    header("Location: ListaProvedores.php?mensaje=Proveedor agregado exitosamente");
    exit();
}

function obtenerProvedor($pdo) {
    $query = "SELECT idProvedor, Nombre, Telefono, Direccion FROM provedores";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    
    return $resultados ?: [];
}

$proveedores = obtenerProvedor($pdo);
?>


<?php
if (isset($_GET['mensaje'])) {
    echo "<div class='mensaje' style='color: green; font-weight: bold;'>" . htmlspecialchars($_GET['mensaje']) . "</div>";
}
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Proveedores</title>
    <link rel="icon" type="image/x-icon" href="imagenes/Logo1.jpg">
    <link rel="stylesheet" href="styleListas.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f6f8;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            min-height: 100vh;
        }

        h1 {
            color: #4e73df;
            margin: 20px 0;
        }

        table {
            width: 90%;
            max-width: 1000px;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        thead {
            background-color: #4e73df;
            color: white;
        }

        th, td {
            text-align: left;
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
        }

        tbody tr:hover {
            background-color: #f1f1f1;
        }

        td img {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        button {
            padding: 10px 20px;
            margin: 10px;
            font-size: 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            opacity: 0.9;
        }

        button:nth-child(1) {
            background-color: #e74a3b;
            color: white;
        }

        button:nth-child(2) {
            background-color: #4e73df;
            color: white;
        }

        @media (max-width: 768px) {
            table {
                width: 100%;
            }

            th, td {
                font-size: 12px;
                padding: 10px;
            }

            button {
                font-size: 12px;
                padding: 8px 16px;
            }
        }
    </style>
</head>
<body>

<h1>Lista de Proveedores</h1>

<table>
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Teléfono</th>
            <th>Dirección</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($proveedores as $proveedor): ?>
            <tr>
                <td><?= htmlspecialchars($proveedor['Nombre']) ?></td>
                <td><?= htmlspecialchars($proveedor['Telefono']) ?></td>
                <td><?= htmlspecialchars($proveedor['Direccion']) ?></td>
                <td>
                    <a href="editar_provedor.php?id=<?= $proveedor['idProvedor'] ?>">
                        <img src="img/Editar.png" alt="Editar" title="Editar">
                    </a>
                    <a href="eliminar_provedor.php?id=<?= $proveedor['idProvedor'] ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar este proveedor?');">
                        <img src="img/Eliminar.png" alt="Eliminar" title="Eliminar">
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div>
    <button onclick="window.location.href='Pagina_Principal.html';">Regresar</button>
    <button onclick="window.location.href='Agregar_Provedor.php';">Agregar Nuevo Proveedor</button>
</div>

</body>
</html>
