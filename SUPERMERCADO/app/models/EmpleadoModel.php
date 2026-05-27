<?php
require_once __DIR__ . '/../config/Database.php';

class EmpleadoModel 
{
    private $db;
    private $table = "EMPLEADOS";

    public function __construct() {
        $database = new Database();
        $this->db = $database->conectar();
    }

    /**
     * Buscar empleado por Email para validar Login
     */
    public function login($email) {
        try {
            // Cambiamos a LEFT JOIN para que el correo SÍ sea reconocido aunque haya problemas con el rol
            $query = "SELECT e.*, p.Nombres, p.ApePaterno, r.Nombre AS NombreRol 
                      FROM " . $this->table . " e
                      INNER JOIN PERSONAS p ON e.IdPersona = p.IdPersona
                      LEFT JOIN EMPLEADO_ROL er ON e.IdEmpleado = er.IdEmpleado AND er.Estado = '1'
                      LEFT JOIN ROLES r ON er.IdRol = r.IdRol
                      WHERE e.EmailEmpresarial = :email 
                        AND e.Estado = '1'
                      ORDER BY (r.Nombre = 'Administrador') DESC LIMIT 1";
                      
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error en el modelo Login optimizado: " . $e->getMessage());
        }
    }
}
?>