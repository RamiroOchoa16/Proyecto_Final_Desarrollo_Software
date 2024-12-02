<?php
try {
    // Conexión a la base de datos
    $pdo = new PDO("mysql:host=localhost;dbname=farmacia;charset=utf8", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $clasificacion = $pdo->query("SELECT idClasificacion, Tipo FROM clasificacion")->fetchAll(PDO::FETCH_ASSOC);
    $eliminacion = $pdo->query("SELECT idEliminacion, MedRegresable FROM eliminacionmedicamento")->fetchAll(PDO::FETCH_ASSOC);
    $provedores = $pdo->query("SELECT idProvedor, Nombre FROM provedores")->fetchAll(PDO::FETCH_ASSOC);
    $sucursales = $pdo->query("SELECT idSucursal, Nombre FROM sucursal")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Error al conectar con la base de datos: " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Medicamentos</title>
    <link rel="icon" type="image/x-icon" href="imagenes/Logo1.jpg">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
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

        form {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px 30px;
            width: 100%;
            max-width: 500px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }

        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        input:focus, select:focus {
            border-color: #4e73df;
            outline: none;
        }

        button {
            width: 100%;
            padding: 10px;
            margin-top: 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button[type="submit"] {
            background-color: #4e73df;
            color: white;
        }

        button[type="submit"]:hover {
            background-color: #3b62c3;
        }

        .exit-button {
            background-color: #ddd;
            color: #333;
        }

        .exit-button:hover {
            background-color: #bbb;
        }
    </style>
</head>
<body>
    <h1>Registro de Medicamento</h1>
    <form id="employee-form" action="Medicamentos.php" method="post">
        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>
        </div>

        <div class="form-group">
            <label for="clasificacion">Clasificación:</label>
            <select id="clasificacion" name="idClasificacion" required>
                <option value="">Selecciona una Clasificación</option>
                <?php foreach ($clasificacion as $clas): ?>
                    <option value="<?php echo htmlspecialchars($clas['idClasificacion']); ?>">
                        <?php echo htmlspecialchars($clas['Tipo']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="cantidad">Cantidad:</label>
            <input type="text" id="cantidad" name="cantidad" required>
        </div>

        <div class="form-group">
            <label for="precio_c">Precio Compra:</label>
            <input type="text" id="precio_c" name="precio_c">
        </div>

        <div class="form-group">
            <label for="precio_v">Precio Venta:</label>
            <input type="text" id="precio_v" name="precio_v" required>
        </div>

        <div class="form-group">
            <label for="eliminacion">En caso de no venderse:</label>
            <select id="eliminacion" name="idEliminacion" required>
                <option value="">Selecciona una Opción</option>
                <?php foreach ($eliminacion as $eli): ?>
                    <option value="<?php echo htmlspecialchars($eli['idEliminacion']); ?>">
                        <?php echo htmlspecialchars($eli['MedRegresable']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="provedor">Proveedor:</label>
            <select id="provedor" name="idProvedor" required>
                <option value="">Selecciona un Proveedor</option>
                <?php foreach ($provedores as $provedor): ?>
                    <option value="<?php echo htmlspecialchars($provedor['idProvedor']); ?>">
                        <?php echo htmlspecialchars($provedor['Nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="fechaCaducidad">Fecha de Caducidad:</label>
            <input type="date" id="fechaCaducidad" name="fechaCaducidad" required>
        </div>

        <div class="form-group">
            <label for="descripcion">Descripción:</label>
            <input type="text" id="descripcion" name="descripcion" required>
        </div>

        <div class="form-group">
            <label for="sucursal">Sucursal:</label>
            <select id="sucursal" name="idSucursal" required>
                <option value="">Selecciona una Sucursal</option>
                <?php foreach ($sucursales as $sucursal): ?>
                    <option value="<?php echo htmlspecialchars($sucursal['idSucursal']); ?>">
                        <?php echo htmlspecialchars($sucursal['Nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <input type="hidden" id="estatus" name="estatus" value="Disponible">

        <button type="submit">Agregar Medicamento</button>
        <button type="button" class="exit-button" onclick="window.location.href='Pagina_Principal.html';">Salir</button>
    </form>
</body>
</html>
