<?php
 class Conexion {
    public $servidor = 'localhost';
    public $usuario = 'root';
    public $password = '';
    public $database = 'farmacia';
    public $port = 3306;

    public function conectar() {
        return mysqli_connect(
            $this->servidor,
            $this->usuario,
            $this->password,
            $this->database,
            $this->port
        );
    }
}
?>