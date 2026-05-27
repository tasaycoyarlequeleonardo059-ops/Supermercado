<?php
require_once __DIR__ . '/../config/Database.php';

class ProductoModel {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->conectar();
    }

    public function listarProductos() {
        try {
            // Llama a tu procedimiento almacenado sp_Productos_Read pasándole 0
            $query = "CALL sp_Productos_Read(0)";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error al listar productos: " . $e->getMessage());
        }
    }
}
?>