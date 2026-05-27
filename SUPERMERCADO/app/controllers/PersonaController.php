<?php

require_once __DIR__ . '/../models/PersonaModel.php';
// Importamos la base de datos para poder realizar las consultas de las pestañas adicionales
require_once __DIR__ . '/../config/Database.php';

class PersonaController 
{
    private $model;

    public function __construct() {
        $this->model = new PersonaModel();
    }

    /**
     * 1. MÉTODO INDEX: Carga la vista de tu panel administrativo con pestañas
     */
    public function index() {
        // Aseguramos que la sesión esté activa para leer quién está logueado
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 2. CANDADO ULTRA ESTRICTO: Si no está logueado o su rol de sesión NO es Administrador, lo expulsamos
        // 2. CANDADO ULTRA ESTRICTO: Si no está logueado o su rol de sesión NO es Administrador, lo expulsamos
        if (!isset($_SESSION['id_empleado']) || ($_SESSION['empleado_rol'] ?? '') !== 'Administrador') {
            
            // CONSEJO CLAVE: Limpiamos SOLO las credenciales de empleado en lugar de destruir toda la sesión
            unset($_SESSION['id_empleado']);
            unset($_SESSION['empleado_nombre']);
            unset($_SESSION['empleado_rol']);
            
            header('HTTP/1.0 403 Forbidden');
            
            // HTML Ajustado para mantenerte estrictamente en el flujo de la Intranet
            die("
            <div style=\"display: flex; justify-content: center; align-items: center; min-height: 100vh; font-family: 'Segoe UI', Arial, sans-serif; background-color: #f4f6f9; margin: -30px;\">
                <div style=\"background: white; padding: 40px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); text-align: center; max-width: 450px; width: 100%; margin: 20px;\">
                    <span style=\"font-size: 50px;\">🛑</span>
                    <h1 style=\"color: #e74c3c; font-size: 26px; margin: 15px 0 10px 0;\">Acceso Denegado</h1>
                    <p style=\"color: #64748b; font-size: 15px; line-height: 1.5; margin-bottom: 25px;\">
                        Este panel de gestión interna es de uso exclusivo para el personal con cargo de <strong>Administrador</strong>.
                    </p>
                    <button onclick=\"window.location.href='index.php?action=login_empleado'\" 
                            style=\"display: inline-block; width: 100%; background-color: #27ae60; color: white; padding: 12px; font-size: 15px; font-weight: bold; border-radius: 6px; box-shadow: 0 3px 6px rgba(39,174,96,0.2); border: none; cursor: pointer; font-family: 'Segoe UI', sans-serif;\">
                        Volver al Login
                    </button>
                </div>
            </div>
            ");
            exit;
        }

        // Jalamos la lista original de personas por si la necesitas
        $personas = $this->model->listarActivos();

        // --- CONEXIÓN DIRECTA PARA PASAR DATOS A LAS PESTAÑAS REALES ---
        $db = (new Database())->conectar();

        try {
            // 1. CONSULTA DE EMPLEADOS: Unimos con PERSONAS para armar el Nombre Completo
            $sqlEmpleados = "SELECT e.IdEmpleado, p.Nombres, p.ApePaterno, p.ApeMaterno, 
                                    e.TelefonoEmergencia, e.EmailEmpresarial, e.Estado,
                                    GROUP_CONCAT(r.Nombre SEPARATOR ' / ') AS NombreRol
                             FROM EMPLEADOS e
                             INNER JOIN PERSONAS p ON e.IdPersona = p.IdPersona
                             LEFT JOIN EMPLEADO_ROL er ON e.IdEmpleado = er.IdEmpleado AND er.Estado = '1'
                             LEFT JOIN ROLES r ON er.IdRol = r.IdRol
                             GROUP BY e.IdEmpleado";
            $stmtEmp = $db->query($sqlEmpleados);
            $listaEmpleados = $stmtEmp->fetchAll(PDO::FETCH_ASSOC);

            // 2. CONSULTA DE PROVEEDORES: Jalamos directo de su tabla con sus 3 teléfonos
            $sqlProveedores = "SELECT IdProveedor, RUC, RazonSocial, TelContacto1, 
                                      TelContacto2, TelContacto3, Email, Estado
                               FROM PROVEEDORES";
            $stmtProv = $db->query($sqlProveedores);
            $listaProveedores = $stmtProv->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            die("Error al cargar los datos de las pestañas en el Controlador: " . $e->getMessage());
        }
        
        // Cargamos la vista apuntando exactamente a tu carpeta "PersonasViews"
        // Ahora la vista tendrá acceso a $listaEmpleados y $listaProveedores de manera nativa
        require_once __DIR__ . '/../views/PersonasViews/index.php';
    }

    /**
     * 2. MÉTODO GUARDAR: Vincula el switch de public/index.php con tu procesarAccion
     */
    public function guardar() {
        $this->procesarAccion();
    }

    /**
     * 3. MÉTODO ELIMINAR: Procesa el borrado lógico cuando le dan clic a eliminar
     */
    public function eliminar() {
        if (isset($_GET['id'])) {
            $idPersona = intval($_GET['id']);
            if ($this->model->deshabilitar($idPersona)) {
                // Redirecciona al index limpio para refrescar la tabla
                header("Location: index.php");
                exit;
            }
        }
    }

    /**
     * Procesar la petición que viene del formulario o de la vista
     */
    public function procesarAccion() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            $accion = isset($_POST['accion']) ? $_POST['accion'] : 'registrar';

            $tipoDOI    = trim($_POST['TipoDOI']);
            $numDOI     = trim($_POST['NumDOI']);
            $apePaterno = trim($_POST['ApePaterno']);
            $apeMaterno = trim($_POST['ApeMaterno']);
            $nombres    = trim($_POST['Nombres']);
            $genero     = trim($_POST['Genero']);
            
            $telefono   = !empty($_POST['Telefono']) ? trim($_POST['Telefono']) : null;
            $email      = !empty($_POST['Email']) ? trim($_POST['Email']) : null;

            if (empty($tipoDOI) || empty($numDOI) || empty($apePaterno) || empty($apeMaterno) || empty($nombres) || empty($genero)) {
                die("Error: Faltan completar campos obligatorios.");
            }

            // --- ACCIÓN: REGISTRAR ---
            if ($accion === 'registrar') {
                $resultado = $this->model->registrar($tipoDOI, $numDOI, $apePaterno, $apeMaterno, $nombres, $genero, $telefono, $email);
                
                if ($resultado === true) {
                    header("Location: index.php");
                    exit;
                } else {
                    echo $resultado;
                }
            }
            
            // --- ACCIÓN: ACTUALIZAR ---
            if ($accion === 'actualizar') {
                $idPersona = intval($_POST['IdPersona']);
                $estado    = isset($_POST['Estado']) ? $_POST['Estado'] : '1'; 

                $resultado = $this->model->actualizar($idPersona, $tipoDOI, $numDOI, $apePaterno, $apeMaterno, $nombres, $genero, $telefono, $email, $estado);
                
                if ($resultado === true) {
                    header("Location: index.php");
                    exit;
                } else {
                    echo $resultado;
                }
            }
        }
    }
}
?>