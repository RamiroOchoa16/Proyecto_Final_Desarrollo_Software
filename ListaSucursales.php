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


function insertarSucursal($pdo, $nombre, $telefono, $direccion, $usuario, $clave) {
    try {
        
        $query = "INSERT INTO sucursal (Nombre, Telefono, Direccion, Usuario, Clave, Estatus) 
                  VALUES (:nombre, :telefono, :direccion, :usuario, :clave, 'Activo')";
        $stmt = $pdo->prepare($query);
        
        
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->bindParam(':usuario', $usuario);
        $stmt->bindParam(':clave', $clave);

        
        if ($stmt->execute()) {
            echo "<p>Sucursal agregada con éxito.</p>";
        } else {
            echo "<p>Error al agregar Sucursal.</p>";
        }
    } catch (PDOException $e) {
        echo "Error en la inserción: " . $e->getMessage();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $nombre = $_POST['Nombre'];
    $telefono = $_POST['Telefono'];
    $direccion = $_POST['Direccion'];
    $usuario = $_POST['Usuario'];
    $clave = $_POST['Clave']; 

    insertarSucursal($pdo, $nombre, $telefono, $direccion, $usuario, $clave);
}

function obtenerSucursales($pdo, $estatus) {
    $query = "
    SELECT 
        idSucursal, 
        COALESCE(Nombre, 'Sin Nombre') AS Nombre, 
        COALESCE(Telefono, 'No Disponible') AS Telefono, 
        COALESCE(Direccion, 'No Disponible') AS Direccion, 
        COALESCE(Usuario, 'No Asignado') AS Usuario, 
        COALESCE(Clave, 'No Asignada') AS Clave 
    FROM sucursal
    WHERE Estatus = :estatus
    ";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':estatus', $estatus);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$sucursales = obtenerSucursales($pdo, 'Activo');
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
    <title>Lista de Sucursales</title>
    <link rel="icon" type="image/x-icon" href="img/Logo1.png">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
            font-size: 14px;
        }

        th {
            background-color: #6c83d0;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        img {
            width: 24px;
            height: 24px;
        }

        a {
            text-decoration: none;
        }

        a img {
            vertical-align: middle;
            margin-right: 8px;
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
            transition: background-color 0.2s;
        }

        button:hover {
            background-color: #5a6dbd;
        }

        .acciones span {
            display: flex;
            gap: 10px;
        }
    </style>
</head>
<body>

<h1>Lista de Sucursales</h1>

<table>
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Teléfono</th>
            <th>Dirección</th>
            <th>Usuario</th>
            <th>Contraseña</th>
            <th>Ver Empleados</th>
            <th>Medicamento Vendido</th>
            <th>Medicamento Disponible</th>
            <th>Medicamento Caducado</th>
            <th>Medicamento Eliminado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($sucursales as $sucursal): ?>
            <tr>
                <td><?php echo htmlspecialchars($sucursal['Nombre']); ?></td>
                <td><?php echo htmlspecialchars($sucursal['Telefono']); ?></td>
                <td><?php echo htmlspecialchars($sucursal['Direccion']); ?></td>
                <td><?php echo htmlspecialchars($sucursal['Usuario']); ?></td>
                <td><?php echo htmlspecialchars($sucursal['Clave']); ?></td>
                <td>
                    <a href="ListaEmpleados.php?id=<?php echo $sucursal['idSucursal']; ?>">
                        <img src="img/ver.png" alt="Ver Empleados">Ver
                    </a>
                </td>
                <td>
                    <a href="Tabla_Vendidos.php?id=<?php echo $sucursal['idSucursal']; ?>">
                        <img src="img/vendido.png" alt="Medicamento Vendido">Ver
                    </a>
                </td>
                <td>
                    <a href="ListaMedicamentos.php?id=<?php echo $sucursal['idSucursal']; ?>">
                        <img src="img/disponible.png" alt="Medicamento Disponible">Ver
                    </a>
                </td>
                <td>
                    <a href="Tabla_Caducados.php?id=<?php echo $sucursal['idSucursal']; ?>">
                        <img src="img/caducado.png" alt="Medicamento Caducado">Ver
                    </a>
                </td>
                <td>
                    <a href="Tabla_Eliminados.php?id=<?php echo $sucursal['idSucursal']; ?>">
                        <img src="img/borrar.png" alt="Medicamento Eliminado">Ver
                    </a>
                </td>
                <td class="acciones">
                    <span>
                        <a href="editar_sucursal.php?id=<?php echo $sucursal['idSucursal']; ?>">
                            <img src="img/Editar.png" alt="Editar">Editar
                        </a>
                        <a href="eliminar_sucursal.php?id=<?php echo $sucursal['idSucursal']; ?>" 
                           onclick="return confirm('¿Estás seguro de que deseas eliminar esta sucursal?');">
                            <img src="img/Eliminar.png" alt="Eliminar">Eliminar
                        </a>
                    </span>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<button onclick="window.location.href='Pagina_Principal.html';">Regresar</button>

</body>
</html>
