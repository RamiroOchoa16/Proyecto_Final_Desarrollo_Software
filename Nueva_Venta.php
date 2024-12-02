<?php
$host = 'localhost';
$dbname = 'farmacia';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error en la conexión: " . htmlspecialchars($e->getMessage()));
}



$empleadosPorSucursal = [];
$queryEmpleados = $pdo->query("SELECT idEmpleado, Nombre, idSucursal FROM empleados");
while ($empleado = $queryEmpleados->fetch(PDO::FETCH_ASSOC)) {
    $empleadosPorSucursal[$empleado['idSucursal']][] = $empleado;
}

$medicamentos = $pdo->query("SELECT M.idMedicamento, M.Nombre, M.PrecioVenta, M.Cantidad, C.idClasificacion, C.Tipo
                             FROM medicamentos M
                             JOIN clasificacion C ON M.idClasificacion = C.idClasificacion
                             WHERE M.Estatus = 'Disponible'")->fetchAll(PDO::FETCH_ASSOC);



$empleados = $pdo->query("SELECT idEmpleado, Nombre FROM empleados")->fetchAll(PDO::FETCH_ASSOC);
$sucursales = $pdo->query("SELECT idSucursal, Nombre FROM sucursal")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idSucursal = $_POST['idSucursal'];
    $idEmpleado = $_POST['idEmpleado'];
    $fechaVenta = date('Y-m-d H:i:s');
    $medicamentosVendidos = $_POST['medicamento'] ?? [];
    $cantidades = $_POST['cantidad'] ?? [];


    try {
        $pdo->beginTransaction();

        // Insertar la venta
        $stmtVenta = $pdo->prepare("INSERT INTO ventas (idSucursal, idEmpleado, FechaVenta) VALUES (?, ?, ?)");
        $stmtVenta->execute([$idSucursal, $idEmpleado, $fechaVenta]);
        $idVenta = $pdo->lastInsertId();

        // Manejo de cada medicamento vendido
        foreach ($medicamentosVendidos as $index => $idMedicamento) {
            $cantidad = filter_var($cantidades[$index], FILTER_VALIDATE_INT);
            if ($cantidad && $cantidad > 0) {
                // Obtener información del medicamento
            
                $stmtMed = $pdo->prepare("SELECT M.PrecioVenta, M.Cantidad, C.idClasificacion, O.PorcentajeDescuento, CM.porcentaje_comision, 
                                          IF(CM.PorcentajeDescuento > 0, 1, 0) AS TieneOferta 
                                          FROM medicamentos M
                                          LEFT JOIN comiciones CM ON M.idMedicamento = CM.idMedicamento
                                          JOIN clasificacion C ON M.idClasificacion = C.idClasificacion
                                          JOIN ofertas O ON O.idMedicamento = M.idMedicamento
                                          WHERE M.idMedicamento = ?");
                
                $stmtMed->execute([$idMedicamento]);
                $medicamento = $stmtMed->fetch(PDO::FETCH_ASSOC);

                if (!$medicamento || $medicamento['Cantidad'] < $cantidad) {
                    throw new Exception("Stock insuficiente para el medicamento con ID: $idMedicamento");
                }

                
    $stmtOferta = $pdo->prepare("SELECT COUNT(*) FROM ofertas WHERE idMedicamento = ?");
    $stmtOferta->execute([$idMedicamento]);
    $tieneOferta = $stmtOferta->fetchColumn() > 0;

    
    $stmtComision = $pdo->prepare("SELECT COUNT(*) FROM comiciones WHERE idMedicamento = ?");
    $stmtComision->execute([$idMedicamento]);
    $tieneComision = $stmtComision->fetchColumn() > 0;

    echo json_encode([
        'tieneOferta' => $tieneOferta,
        'tieneComision' => $tieneComision
    ]);

                $precioVenta = $medicamento['PrecioVenta'];
                $descuento = $medicamento['Descuento'];
                $precioFinal = $precioVenta * (1 - $descuento / 100) * $cantidad;

                
                $stmtUpdate = $pdo->prepare("UPDATE medicamentos SET Cantidad = Cantidad - ? WHERE idMedicamento = ?");
                $stmtUpdate->execute([$cantidad, $idMedicamento]);

                
                $stmtDetalle = $pdo->prepare("INSERT INTO detalleventas (idVenta, idMedicamento, Cantidad, PrecioTotal) VALUES (?, ?, ?, ?)");
                $stmtDetalle->execute([$idVenta, $idMedicamento, $cantidad, $precioFinal]);

                
                if ($medicamento['idClasificacion'] == 4) {
                    $nombrePaciente = $_POST['NombrePaciente'];
                    $nombreDoctor = $_POST['NombreDoctor'];
                    $telefonoDoctor = $_POST['TelefonoDoctor'];
                    $cedulaDoctor = $_POST['CedulaDoctor'];

                    $stmtUpdateVenta = $pdo->prepare("UPDATE ventas 
                                                      SET NombrePaciente = ?, NombreDoctor = ?, TelefonoDoctor = ?, CedulaDoctor = ? 
                                                      WHERE idVenta = ?");
                    $stmtUpdateVenta->execute([$nombrePaciente, $nombreDoctor, $telefonoDoctor, $cedulaDoctor, $idVenta]);
                }
            }
        }

        $pdo->commit();
        echo "<p style='color:green;'>Venta realizada con éxito.</p>";
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "<p style='color:red;'>Error al realizar la venta: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Realizar Venta</title>
    <link rel="stylesheet" href="styleVentas.css">
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

        .Formulario {
            background-color: #fff;
            padding: 20px 40px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
        }

        label {
            display: block;
            margin: 10px 0 5px;
            font-size: 14px;
            color: #333;
        }

        input[type="text"], input[type="number"], select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 14px;
            box-sizing: border-box;
        }

        #camposExtras {
            margin-top: 20px;
            padding: 15px;
            background-color: #f9fafb;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        button {
            padding: 10px 20px;
            font-size: 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .act {
            background-color: #4e73df;
            color: white;
            margin-right: 10px;
        }

        .act:hover {
            background-color: #3b62c3;
        }

        .exit-button {
            background-color: #e74a3b;
            color: white;
        }

        .exit-button:hover {
            background-color: #d73229;
        }

        .arriba, .abajo {
            display: flex;
            flex-direction: column;
        }

        @media (max-width: 768px) {
            .Formulario {
                padding: 20px;
            }

            button {
                font-size: 12px;
                padding: 8px 16px;
            }

            input, select {
                font-size: 12px;
                padding: 8px;
            }
        }
    </style>
    <script>
        const empleadosPorSucursal = <?= json_encode($empleadosPorSucursal); ?>;

function cargarEmpleados(idSucursal) {
    const selectEmpleado = document.getElementById('idEmpleado');
    selectEmpleado.innerHTML = '<option value="">Seleccione un empleado</option>';

    if (empleadosPorSucursal[idSucursal]) {
        empleadosPorSucursal[idSucursal].forEach(empleado => {
            const option = document.createElement('option');
            option.value = empleado.idEmpleado;
            option.textContent = empleado.Nombre;
            selectEmpleado.appendChild(option);
        });
    }
}

        function actualizarDatosMedicamento() {
            const selectMedicamento = document.getElementById('medicamento');
            const cantidadStock = document.getElementById('cantidadStock');
            const precioUnitario = document.getElementById('precioUnitario');

            const optionSeleccionada = selectMedicamento.options[selectMedicamento.selectedIndex];
            cantidadStock.value = optionSeleccionada.dataset.cantidad || '';
            precioUnitario.value = `$${optionSeleccionada.dataset.precio || ''}`;
        }

        function verificarClasificacion() {
            const medicamentoSelect = document.getElementById("medicamento");
            const clasificacion = medicamentoSelect.options[medicamentoSelect.selectedIndex].dataset.clasificacion;
            const camposExtras = document.getElementById("camposExtras");

            if (clasificacion === "4") {
                camposExtras.style.display = "block";
            } else {
                camposExtras.style.display = "none";
            }
        }
    </script>
</head>
<body>
    <h1>Realizar Venta</h1>

    <div class="Formulario">
        <form method="POST" action="Listas_Ventas.php">
            <div class="arriba">
                <label for="idSucursal">Sucursal:</label>
                <select name="idSucursal" id="idSucursal" onchange="cargarEmpleados(this.value)" required>
    <option value="">Seleccione una sucursal</option>
    <?php foreach ($sucursales as $sucursal): ?>
        <option value="<?= $sucursal['idSucursal'] ?>"><?= $sucursal['Nombre'] ?></option>
    <?php endforeach; ?>
</select>

                <label for="idEmpleado">Empleado:</label>
                <select name="idEmpleado" id="idEmpleado" required>
                    <option value="">Seleccione un empleado</option>
                </select>

                <label for="medicamento">Medicamento:</label>
                <select name="medicamento" id="medicamento" onchange="actualizarDatosMedicamento(); verificarClasificacion()" required>
                    <option value="">Seleccione un medicamento</option>
                    <?php foreach ($medicamentos as $medicamento): ?>
                        <option value="<?= $medicamento['idMedicamento'] ?>" 
                            data-clasificacion="<?= $medicamento['idClasificacion'] ?>" 
                            data-cantidad="<?= $medicamento['Cantidad'] ?>" 
                            data-precio="<?= $medicamento['PrecioVenta'] ?>">
                            <?= $medicamento['Nombre'] ?> - Tipo: <?= $medicamento['Tipo'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>Cantidad en Stock:</label>
                <input type="text" id="cantidadStock" readonly>

                <label>Precio Unitario:</label>
                <input type="text" id="precioUnitario" readonly>

                <label>¿Tiene Oferta?</label>
                <input type="text" id="tieneOferta" disabled>

                <label>¿Tiene Comisión?</label>
                <input type="text" id="tieneComision" disabled>
            </div>

            <div class="abajo">
                <label for="cantidad">Cantidad:</label>
                <input type="number" name="cantidad" min="1" required>

                <div id="camposExtras" style="display: none;">
                    <h3>Datos Adicionales:</h3>
                    <label>Nombre del Paciente:</label>
                    <input type="text" name="NombrePaciente">

                    <label>Nombre del Doctor:</label>
                    <input type="text" name="NombreDoctor">

                    <label>Teléfono del Doctor:</label>
                    <input type="text" name="TelefonoDoctor">

                    <label>Cédula del Doctor:</label>
                    <input type="text" name="CedulaDoctor">
                </div>

                <label>Total a Pagar:</label>
                <input type="text" id="totalPagar" readonly>

                <button class="act" type="submit">Registrar Venta</button>
                <button class="exit-button" type="button" onclick="window.location.href='Pagina_Principal.html'">Salir sin Guardar</button>
            </div>
        </form>
    </div>
</body>
</html>
