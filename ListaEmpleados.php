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


error_reporting(E_ALL);
ini_set('display_errors', 1);


// Función para insertar un empleado
function insertarEmpleado($pdo, $nombre, $apellido_p, $apellido_m, $telefono, $curp, $rfc, $idPuesto, $idSucursal) {
    // Consulta para obtener el salario basado en el puesto
    $query = "SELECT Salario FROM puestos WHERE idPuesto = :idPuesto";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':idPuesto', $idPuesto);
    $stmt->execute();
    $puesto = $stmt->fetch(PDO::FETCH_ASSOC);
    $salario = $puesto['Salario'];


    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nombre = $_POST['Nombre'];
        $apellido_p = $_POST['apellido_p'];
        $apellido_m = $_POST['apellido_m'];
        $telefono = $_POST['telefono'];
        $curp = $_POST['curp'];
        $rfc = $_POST['rfc'];
        $idPuesto = $_POST['idPuesto'];
        $salario = $_POST['salario-hidden'];  
        $idSucursal = $_POST['idSucursal'];
    
        
        $query = "INSERT INTO empleados (Nombre, ApellidoP, ApellidoM, Telefono, Curp, Rfc, idPuesto, Salario, idSucursal) 
                  VALUES (:nombre, :apellido_p, :apellido_m, :telefono, :curp, :rfc, :idPuesto, :salario, :idSucursal)";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellido_p', $apellido_p);
        $stmt->bindParam(':apellido_m', $apellido_m);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':curp', $curp);
        $stmt->bindParam(':rfc', $rfc);
        $stmt->bindParam(':idPuesto', $idPuesto);
        $stmt->bindParam(':salario', $salario);
        $stmt->bindParam(':idSucursal', $idSucursal);
    
        if ($stmt->execute()) {
            // Redirigir con mensaje de éxito
            header("Location: ListaEmpleados.php?mensaje=Empleado agregado con éxito.");
            exit;
        } else {
            // Redirigir con mensaje de error
            header("Location: ListaEmpleados.php?mensaje=Error al agregar el empleado.");
            exit;
        }
    }
    
}

function obtenerEmpleados($pdo, $estatus) {
    $query = "
    SELECT 
        e.idEmpleado, e.Nombre, e.ApellidoP, e.ApellidoM, e.Telefono, e.CURP, e.RFC, e.Salario, 
        s.Nombre AS sucursal, p.Puesto AS puestos
    FROM empleados e
    JOIN sucursal s ON e.idSucursal = s.idSucursal
    JOIN puestos p ON e.idPuesto = p.idPuesto
    WHERE e.Estatus = :estatus";
    
    
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $query .= " AND e.idSucursal = :idSucursal"; 
    }
    
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':estatus', $estatus);
    
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $stmt->bindParam(':idSucursal', $_GET['id'], PDO::PARAM_INT);
    }
    
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = strtoupper($_POST['Nombre']);
    $apellido_p = strtoupper($_POST['apellido_p']);
    $apellido_m = strtoupper($_POST['apellido_m']);
    $telefono = $_POST['telefono'];
    $curp = strtoupper($_POST['curp']);
    $rfc = strtoupper($_POST['rfc']);
    $idPuesto = $_POST['idPuesto'];
    $idSucursal = $_POST['idSucursal'];

    insertarEmpleado($pdo, $nombre, $apellido_p, $apellido_m, $telefono, $curp, $rfc, $idPuesto, $idSucursal);
}


$empleados = obtenerEmpleados($pdo,'Activo');
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
    <title>Lista de Empleados</title>
    <link rel="icon" type="image/x-icon" href="imagenes/Logo1.jpg">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f6f8;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        h1 {
            color: #4e73df;
            margin-bottom: 20px;
        }

        table {
            width: 90%;
            max-width: 1200px;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        table thead tr {
            background-color: #4e73df;
            color: white;
        }

        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        table tbody tr:hover {
            background-color: #f1f5f9;
        }

        img {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        button {
            margin: 10px;
            padding: 10px 20px;
            background-color: #4e73df;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #3b62c3;
        }

        button.exit {
            background-color: #d9534f;
        }

        button.exit:hover {
            background-color: #c9302c;
        }

        @media (max-width: 768px) {
            table th, table td {
                font-size: 12px;
                padding: 8px;
            }

            button {
                font-size: 14px;
                padding: 8px 16px;
            }
        }
    </style>
</head>
<body>

    <h1>Lista de Empleados</h1>

    <!-- Tabla para mostrar los empleados -->
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Apellido Paterno</th>
                <th>Apellido Materno</th>
                <th>Teléfono</th>
                <th>CURP</th>
                <th>RFC</th>
                <th>Salario</th>
                <th>Sucursal</th>
                <th>Puesto</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Mostrar los empleados en la tabla
            foreach ($empleados as $empleado) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($empleado['Nombre']) . "</td>";
                echo "<td>" . htmlspecialchars($empleado['ApellidoP']) . "</td>";
                echo "<td>" . htmlspecialchars($empleado['ApellidoM']) . "</td>";
                echo "<td>" . htmlspecialchars($empleado['Telefono']) . "</td>";
                echo "<td>" . htmlspecialchars($empleado['CURP']) . "</td>";
                echo "<td>" . htmlspecialchars($empleado['RFC']) . "</td>";
                echo "<td>" . htmlspecialchars($empleado['Salario']) . "</td>";
                echo "<td>" . htmlspecialchars($empleado['sucursal']) . "</td>";
                echo "<td>" . htmlspecialchars($empleado['puesto']) . "</td>";
                echo "<td>
                        <span>
                            <a href='editar_empleado.php?id=" . htmlspecialchars($empleado['idEmpleado']) . "'>
                                <img src='imagenes/Editar.png' alt='Editar'>
                            </a>
                            <a href='eliminar_empleado.php?id=" . htmlspecialchars($empleado['idEmpleado']) . "' onclick=\"return confirm('¿Estás seguro de que deseas eliminar este empleado?');\">
                                <img src='imagenes/Eliminar.png' alt='Eliminar'>
                            </a>
                        </span>
                      </td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>

    <button onclick="window.location.href='ListaSucursales.php';">Regresar</button>
    <button onclick="window.location.href='Agregar_Empleados.php';">Agregar Empleado</button>

</body>
</html>
