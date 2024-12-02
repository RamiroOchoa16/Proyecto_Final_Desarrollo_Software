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

if (!isset($_GET['id'])) {
    die("ID de medicamento no especificado.");
}

$idMedicamento = (int)$_GET['id'];


$query = "SELECT Nombre FROM medicamentos WHERE idMedicamento = :idMedicamento";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':idMedicamento', $idMedicamento);
$stmt->execute();
$medicamento = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$medicamento) {
    die("Medicamento no encontrado.");
}


$queryCheck = "SELECT COUNT(*) FROM ofertas WHERE idMedicamento = :idMedicamento AND FechaFin >= CURDATE()";
$stmtCheck = $pdo->prepare($queryCheck);
$stmtCheck->bindParam(':idMedicamento', $idMedicamento);
$stmtCheck->execute();
$ofertaExistente = $stmtCheck->fetchColumn();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($ofertaExistente > 0) {
        echo "<p> Este medicamento ya tiene una oferta activa.</p>";
    } else {
        $fechaFin = $_POST['FechaFin'];
        $porcentajeDescuento = $_POST['PorcentajeDescuento'];
        $fechaInicio = date('Y-m-d');

        if (empty($fechaFin) || empty($porcentajeDescuento) || !is_numeric($porcentajeDescuento) || $porcentajeDescuento <= 0 || $porcentajeDescuento > 100) {
            echo "<p>Error: Por favor ingrese una fecha de fin válida y un porcentaje de descuento entre 1 y 100.</p>";
        } else {
            $queryInsert = "INSERT INTO ofertas (idMedicamento, FechaInicio, FechaFin, PorcentajeDescuento) VALUES (:idMedicamento, :fechaInicio, :fechaFin, :porcentajeDescuento)";
            $stmtInsert = $pdo->prepare($queryInsert);
            $stmtInsert->bindParam(':idMedicamento', $idMedicamento);
            $stmtInsert->bindParam(':fechaInicio', $fechaInicio);
            $stmtInsert->bindParam(':fechaFin', $fechaFin);
            $stmtInsert->bindParam(':porcentajeDescuento', $porcentajeDescuento);

            if ($stmtInsert->execute()) {
                header("Location: Checar_Caducidades.php?mensaje=Oferta%20agregada%20exitosamente");
                exit;
            } else {
                echo "<p>Error al agregar la oferta.</p>";
            }
        }
    }
}
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
    <title>Agregar Oferta</title>
    <link rel="stylesheet" href="styleEditar.css">
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

        .login {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 320px;
            padding: 20px;
            margin: 100px auto;
            text-align: center;
        }

        .login label {
            color: #555;
            font-size: 14px;
            text-align: left;
            display: block;
            margin: 10px 0 5px;
        }

        .login input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #f9f9f9;
            font-size: 14px;
        }

        .login input:focus {
            outline-color: #4e73df;
            background-color: #e6f0ff;
        }

        .login button {
            background-color: #6c83d0;
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .login button:hover {
            background-color: #5a6dbd;
        }

        .login button:active {
            background-color: #4e73df;
        }

        .exit-button {
            background-color: #d9534f;
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }

        .exit-button:hover {
            background-color: #c9302c;
        }

        .exit-button:active {
            background-color: #d43f00;
        }

        footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #777;
        }

        footer a {
            color: #4e73df;
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="login">
        <h1>Agregar Oferta al Medicamento</h1>
        <form method="POST" action="">
            <label for="nombre">Medicamento:</label>
            <input type="text" name="Nombre" id="Nombre" value="<?php echo htmlspecialchars($medicamento['Nombre']); ?>" readonly>      

            <label for="fechaFin">Fecha de Fin:</label>
            <input type="date" name="FechaFin" id="fechaFin" required><br>

            <label for="porcentajeDescuento">Porcentaje de Descuento (%):</label>
            <input type="number" name="PorcentajeDescuento" id="porcentajeDescuento" min="1" max="100" required><br>

            <button class="act" type="submit">Agregar Oferta</button>
            <button type="button" class="exit-button" onclick="window.location.href='Checar_Caducidades.php';">Cancelar</button>
        </form>
    </div>

    <footer>
        <p>&copy; 2024 Todos los derechos reservados. <a href="#">Política de Privacidad</a> | <a href="#">Términos de Servicio</a></p>
    </footer>

</body>
</html>
