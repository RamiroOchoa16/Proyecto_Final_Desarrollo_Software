<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Puesto y Salario</title>
    <link rel="stylesheet" type="text/css" href="css_agregar_puesto.css">
</head>
<body>
    <div class="form-container">
        <h1>Agregar Puesto y Salario</h1>
        <form action="Agregar_Puestos.php" method="POST"> 
            <div class="form-group">
                <label for="puesto">Puesto:</label>
                <input type="text" id="puesto" name="puesto" required>
            </div>

            <div class="form-group">
                <label for="salario">Salario:</label>
                <input type="number" id="salario" name="salario" required>
            </div>

            <div class="button-container">
                <button type="submit">Agregar Puesto</button>
                <button type="button" class="exit-button" onclick="window.location.href='Pagina_Principal.html';">Salir</button>
            </div>
        </form>
    </div>
</body>
</html>
