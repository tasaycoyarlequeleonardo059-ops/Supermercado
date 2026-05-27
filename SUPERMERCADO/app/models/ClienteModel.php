<?php
require_once __DIR__ . '/../config/Database.php';

class ClienteModel 
{
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->conectar();
    }

    /**
     * Registra una nueva persona y su cuenta de cliente asociada
     */
    public function registrar($tipoDOI, $numDOI, $apePaterno, $apeMaterno, $nombres, $genero, $telefono, $email, $contrasenya) {
        try {
            // 1. Validar si la combinación TipoDOI + NumDOI ya existe (uq_PersonaTipoNumDOI)
            $checkDOI = "SELECT COUNT(*) FROM PERSONAS WHERE TipoDOI = :tipoDOI AND NumDOI = :numDOI";
            $stmtDOI = $this->db->prepare($checkDOI);
            $stmtDOI->bindParam(':tipoDOI', $tipoDOI);
            $stmtDOI->bindParam(':numDOI', $numDOI);
            $stmtDOI->execute();
            
            if ($stmtDOI->fetchColumn() > 0) {
                return "El documento de identidad ($tipoDOI - $numDOI) ya se encuentra registrado.";
            }

            // 2. Validar si el Correo ya existe (uq_PersonaEmail)
            if (!empty($email)) {
                $checkEmail = "SELECT COUNT(*) FROM PERSONAS WHERE Email = :email";
                $stmtEmail = $this->db->prepare($checkEmail);
                $stmtEmail->bindParam(':email', $email);
                $stmtEmail->execute();
                
                if ($stmtEmail->fetchColumn() > 0) {
                    return "El correo electrónico ya se encuentra registrado.";
                }
            }

            // 3. Iniciar Transacción Segura
            $this->db->beginTransaction();

            // Insertar en la tabla PERSONAS
            $queryPersona = "INSERT INTO PERSONAS (TipoDOI, NumDOI, ApePaterno, ApeMaterno, Nombres, Genero, Telefono, Email, Estado) 
                             VALUES (:tipoDOI, :numDOI, :apePaterno, :apeMaterno, :nombres, :genero, :telefono, :email, '1')";
            
            $stmtP = $this->db->prepare($queryPersona);
            $stmtP->bindParam(':tipoDOI', $tipoDOI);
            $stmtP->bindParam(':numDOI', $numDOI);
            $stmtP->bindParam(':apePaterno', $apePaterno);
            $stmtP->bindParam(':apeMaterno', $apeMaterno);
            $stmtP->bindParam(':nombres', $nombres);
            $stmtP->bindParam(':genero', $genero);
            $stmtP->bindParam(':telefono', $telefono);
            
            // Si el email llega vacío desde el formulario, se inserta como NULL en la BD
            $emailParam = empty($email) ? null : $email;
            $stmtP->bindParam(':email', $emailParam);
            
            $stmtP->execute();

            // Obtener el ID generado para la Persona
            $idPersona = $this->db->lastInsertId();

            // Insertar en la tabla CLIENTES 
            // (Nota: Contrasenya ya NO es tratada como UNIQUE en la lógica)
            $queryCliente = "INSERT INTO CLIENTES (IdPersona, Contrasenya, Estado) 
                             VALUES (:idPersona, :contrasenya, '1')";
            
            $stmtC = $this->db->prepare($queryCliente);
            $stmtC->bindParam(':idPersona', $idPersona);
            $stmtC->bindParam(':contrasenya', $contrasenya); 
            $stmtC->execute();

            // Confirmar cambios si todo salió bien
            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            // Deshacer cambios en caso de error de base de datos o violación de CHECKs REGEXP
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return "Error de restricción en Base de Datos: " . $e->getMessage();
        }
    }

    /**
     * Busca al cliente a través del Email registrado en PERSONAS para el inicio de sesión
     */
    public function login($email) {
        try {
            $query = "SELECT c.*, p.Nombres, p.ApePaterno, p.Email 
                      FROM CLIENTES c
                      INNER JOIN PERSONAS p ON c.IdPersona = p.IdPersona
                      WHERE p.Email = :email AND c.Estado = '1' AND p.Estado = '1'";
                      
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error crítico en Login (Modelo): " . $e->getMessage());
        }
    }
}
?>