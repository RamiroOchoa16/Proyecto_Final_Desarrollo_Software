<?php
session_start();

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

$errorMsg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg = "Correo electrónico inválido.";
    } else {
        try {
            // Consulta SQL para verificar el usuario
            $sql = "SELECT idSucursal, Usuario, Clave FROM sucursal WHERE Usuario = :email AND Clave = :password";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Guardar información del usuario en la sesión
                $_SESSION['user_id'] = $user['idSucursal'];

                // Redirigir según el idSucursal
                if ($user['idSucursal'] == 2) {
                    header("Location: Pagina_Principal.html"); // Página de administrador
                } else {
                    header("Location: Pagina_Secundaria.html"); // Página de sucursal
                }
                exit();
            } else {
                $errorMsg = "Usuario o contraseña incorrectos.";
            }
        } catch (Exception $e) {
            error_log("Error en inicio de sesión: " . $e->getMessage());
            $errorMsg = "Hubo un problema al procesar la solicitud. Intenta de nuevo.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="icon" type="image/x-icon" href="imagenes/Logo1.jpg">
    <link rel="stylesheet" type="text/css" href="styleLogin.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f2f5;  /* Fondo suave */
            margin: 0;
            padding: 0;
        }

        .login {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 300px;
            padding: 20px;
            margin: 100px auto;
            text-align: center;
        }

        .login h2 {
            color: #4e73df;  /* Color del título */
            margin-bottom: 20px;
        }

        .login img {
            width: 60px;
            margin-bottom: 20px;
        }

        .login label {
            color: #555;  /* Color suave para las etiquetas */
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
            outline-color: #4e73df;  /* Color del borde al enfocar */
            background-color: #e6f0ff;
        }

        .login button {
            background-color: #6c83d0;  /* Azul suave para el botón */
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
            background-color: #5a6dbd;  /* Azul más suave al hacer hover */
        }

        .login button:active {
            background-color: #4e73df;  /* Azul al hacer clic */
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
    <h2>Iniciar Sesión</h2>
    <img src="img/datos-del-usuario.png" alt="Ícono de inicio de sesión">
    <form id="loginForm" method="POST" action="login.php">
        <label for="email">Usuario:</label>
        <input type="email" id="email" name="email" required>
        
        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Ingresar</button>
    </form>
  </div>

  <footer>
    <p>&copy; 2024 Todos los derechos reservados. <a href="#">Política de Privacidad</a> | <a href="#">Términos de Servicio</a></p>
  </footer>
  
</body>
</html>
