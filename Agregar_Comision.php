<?php
$host = 'localhost';
$dbname = 'farmacia';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error en la conexión: " . htmlspecialchars($e->getMessage()));
}


if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Error: ID de medicamento no especificado o inválido.");
}
$idMedicamento = (int)$_GET['id'];


$query = "SELECT Nombre FROM medicamentos WHERE idMedicamento = :idMedicamento";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':idMedicamento', $idMedicamento, PDO::PARAM_INT);
$stmt->execute();
$medicamento = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$medicamento) {
    die("Error: Medicamento no encontrado.");
}


$queryCheck = "SELECT COUNT(*) FROM comiciones WHERE idMedicamento = :idMedicamento";
$stmtCheck = $pdo->prepare($queryCheck);
$stmtCheck->bindParam(':idMedicamento', $idMedicamento, PDO::PARAM_INT);
$stmtCheck->execute();
$comisionExistente = $stmtCheck->fetchColumn();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($comisionExistente > 0) {
        header("Location: Checar_Caducidades.php?mensaje=" . urlencode("Este medicamento ya tiene una comisión activa") . "&tipo=error");
        exit;
    } else {
        $porcentajeDescuento = filter_input(INPUT_POST, 'PorcentajeDescuento', FILTER_VALIDATE_FLOAT);

        if ($porcentajeDescuento === false || $porcentajeDescuento <= 0 || $porcentajeDescuento > 100) {
            echo "<p style='color:red;'>Error: Por favor ingrese un porcentaje de descuento válido entre 1 y 100.</p>";
        } else {
            $queryInsert = "INSERT INTO comiciones (idMedicamento, porcentaje_comision) VALUES (:idMedicamento, :porcentajeDescuento)";
            $stmtInsert = $pdo->prepare($queryInsert);
            $stmtInsert->bindParam(':idMedicamento', $idMedicamento, PDO::PARAM_INT);
            $stmtInsert->bindParam(':porcentajeDescuento', $porcentajeDescuento);

            if ($stmtInsert->execute()) {
                header("Location: Checar_Caducidades.php?mensaje=" . urlencode("Oferta agregada exitosamente"));
                exit;
            } else {
                echo "<p style='color:red;'>Error al agregar la oferta.</p>";
            }
        }
    }
}


if (isset($_GET['mensaje'])) {
    echo "<div class='mensaje'>" . htmlspecialchars($_GET['mensaje']) . "</div>";
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Comisión</title>
    <link rel="stylesheet" href="styleEditar.css">
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
            margin-top: 20px;
        }

        form {
            background-color: white;
            width: 50%;
            margin: 20px auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
        }

        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        button {
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

        .exit-button {
            background-color: #e74c3c;
            margin-left: 10px;
        }

        .exit-button:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>
    <h1>Agregar Comisión al Medicamento</h1>
    
    <form method="POST">
        <label for="nombre">Medicamento:</label>
        <input type="text" name="Nombre" id="Nombre" value="<?php echo htmlspecialchars($medicamento['Nombre']); ?>" readonly>

        <label for="porcentajeDescuento">Porcentaje de Comisión (%):</label>
        <input type="number" name="PorcentajeDescuento" id="PorcentajeDescuento" min="1" max="100" required><br>

        <button class="act" type="submit">Agregar Comisión</button>
        <button type="button" class="exit-button" onclick="window.location.href='Checar_Caducidades.php';">Cancelar</button>
    </form>
</body>
</html>
