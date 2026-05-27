<<?php
// Usamos la ruta relativa correcta para salir de 'models' e ingresar a 'config'
require_once __DIR__ . '/../config/Database.php'; 

class PersonaModel 
{
    private $db;
    private $table = "PERSONAS";

    public function __construct() {
        $database = new Database();
        $this->db = $database->conectar();
    }

    // 1. Listar solo los activos (Estado = '1')
    public function listarActivos() {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE Estado = '1'";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error al listar personas: " . $e->getMessage());
        }
    }

    // 2. Buscar por documento (Para validar tu UNIQUE KEY uq_PersonaTipoNumDOI)
    public function buscarPorDocumento($tipoDOI, $numDOI) {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE TipoDOI = :tipoDOI AND NumDOI = :numDOI";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':tipoDOI', $tipoDOI);
            $stmt->bindParam(':numDOI', $numDOI);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error al buscar persona: " . $e->getMessage());
        }
    }

    // 3. Registrar una nueva persona
    public function registrar($tipoDOI, $numDOI, $apePaterno, $apeMaterno, $nombres, $genero, $telefono = null, $email = null) {
        try {
            $query = "INSERT INTO " . $this->table . " 
                      (TipoDOI, NumDOI, ApePaterno, ApeMaterno, Nombres, Genero, Telefono, Email) 
                      VALUES 
                      (:tipoDOI, :numDOI, :apePaterno, :apeMaterno, :nombres, :genero, :telefono, :email)";
            
            $stmt = $this->db->prepare($query);

            $stmt->bindParam(':tipoDOI', $tipoDOI);
            $stmt->bindParam(':numDOI', $numDOI);
            $stmt->bindParam(':apePaterno', $apePaterno);
            $stmt->bindParam(':apeMaterno', $apeMaterno);
            $stmt->bindParam(':nombres', $nombres);
            $stmt->bindParam(':genero', $genero);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->bindParam(':email', $email);

            return $stmt->execute();
            
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                return "Error: El documento o el correo ya se encuentran registrados.";
            }
            die("Error al registrar persona: " . $e->getMessage());
        }
    }

    // 4. ACTUALIZAR (Este faltaba para completar el mantenimiento de tu tabla)
    public function actualizar($idPersona, $tipoDOI, $numDOI, $apePaterno, $apeMaterno, $nombres, $genero, $telefono = null, $email = null, $estado = '1') {
        try {
            $query = "UPDATE " . $this->table . " SET 
                      TipoDOI = :tipoDOI, NumDOI = :numDOI, ApePaterno = :apePaterno, 
                      ApeMaterno = :apeMaterno, Nombres = :nombres, Genero = :genero, 
                      Telefono = :telefono, Email = :email, Estado = :estado 
                      WHERE IdPersona = :idPersona";
            
            $stmt = $this->db->prepare($query);

            $stmt->bindParam(':idPersona', $idPersona, PDO::PARAM_INT);
            $stmt->bindParam(':tipoDOI', $tipoDOI);
            $stmt->bindParam(':numDOI', $numDOI);
            $stmt->bindParam(':apePaterno', $apePaterno);
            $stmt->bindParam(':apeMaterno', $apeMaterno);
            $stmt->bindParam(':nombres', $nombres);
            $stmt->bindParam(':genero', $genero);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':estado', $estado);

            return $stmt->execute();
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                return "Error: No se pudo actualizar. El documento o correo ya existen en otra persona.";
            }
            die("Error al actualizar persona: " . $e->getMessage());
        }
    }

    // 5. Deshabilitar (Eliminación lógica respetando tu columna Estado)
    public function deshabilitar($idPersona) {
        try {
            $query = "UPDATE " . $this->table . " SET Estado = '0' WHERE IdPersona = :idPersona";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':idPersona', $idPersona, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            die("Error al deshabilitar persona: " . $e->getMessage());
        }
    }
}
?>