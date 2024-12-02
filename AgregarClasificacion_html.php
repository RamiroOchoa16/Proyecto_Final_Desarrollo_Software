<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar</title>
    <link rel="stylesheet" type="text/css" href="styleAgregar.css">
</head>
<body>
    <div class="container">
        <h1>Agregar </h1>
        <form id="tipo-form" action="Agregar_Clasificacion.php" method="post">
    
    <div class="form-group">
        <label for="Tipo">Tipo:</label>
        <input type="text" id="MedRegresable" name="MedRegresable" placeholder="Ingrese el MedRegresable" required>
    </div>
    
    
    <div class="button-container">
        <button type="submit">Agregar Tipo</button>
        <button type="button" class="exit-button" onclick="window.location.href='Pagina_Principal.html';">Salir</button>
    </div>
</form>
    </div>
</body>
</html>
