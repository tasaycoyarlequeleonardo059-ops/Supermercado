<?php
class Database
{
    private $host       = "localhost";          // Dirección del servidor MySQL
    private $dbname     = "SUPERMERCADO_HT";    // Nombre de la base de datos
    private $user       = "root";               // Usuario de MySQL
    private $password   = "";                   // Contraseña del usuario MySQL

    public $conexion;
    public function conectar() {
        try {

            $this->conexion = new PDO(

                "mysql:host=" . $this->host .
                ";dbname=" . $this->dbname,

                $this->user,

                $this->password
            );
        
            $this->conexion->exec("SET NAMES utf8");    // Permite tildes y Ñ

        } catch (PDOException $e) {

            die("Error de conexión: " . $e->getMessage());
        }
        return $this->conexion;
    }
}

?>