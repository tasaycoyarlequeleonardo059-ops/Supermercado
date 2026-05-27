<?php
require_once __DIR__ . '/../models/EmpleadoModel.php';

class EmpleadoAuthController 
{
    private $model;

    public function __construct() {
        $this->model = new EmpleadoModel();
    }

    // 1. Mostrar la vista del formulario de Login para Empleados
    public function index() {
        // CORRECCIÓN: Apuntamos al archivo de vista exclusivo que creamos para ellos
        require_once __DIR__ . '/../views/auth/login_empleado.php';
    }

    // 2. Procesar el formulario cuando le den al botón "Ingresar"
    public function acceder() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // CORRECCIÓN: Usamos isset u operador null coalescing (??) para evitar alertas si el dato no viaja
            $email    = trim($_POST['EmailEmpresarial'] ?? '');
            $password = trim($_POST['Contrasenya'] ?? '');

            if (empty($email) || empty($password)) {
                // CORRECCIÓN: Usamos la variable específica de errores para empleados
                $_SESSION['error_login_emp'] = "Por favor, llene todos los campos.";
                header("Location: index.php?action=login_empleado");
                exit;
            }

            $empleado = $this->model->login($email);

            if ($empleado) {
                // Validamos la contraseña
                if ($password === $empleado['Contrasenya']) {
    
                    // Guardamos las variables de sesión
                    $_SESSION['id_empleado']      = $empleado['IdEmpleado'];
                    $_SESSION['empleado_nombre']  = $empleado['Nombres'] . ' ' . $empleado['ApePaterno'];
                    
                    // ¡ESTA LÍNEA ES CRÍTICA! 
                    // Asegura que guarde el 'NombreRol' que viene del LEFT JOIN de tu EmpleadoModel
                    $_SESSION['empleado_rol']     = $empleado['NombreRol']; 

                    header("Location: index.php?action=index");
                    exit;
                }
            } else {
                // Correo no registrado o empleado inactivo
                $_SESSION['error_login_emp'] = "El correo empresarial no existe o el usuario está inactivo.";
                header("Location: index.php?action=login_empleado");
                exit;
            }
        }
    }

    // 3. Cerrar sesión
    public function salir() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // CORRECCIÓN: Usamos unset() en lugar de session_destroy() 
        // ¿Por qué? Si usas session_destroy(), borrarías también la sesión del cliente 
        // si es que estuvieras probando ambos perfiles en el mismo navegador.
        unset($_SESSION['id_empleado']);
        unset($_SESSION['empleado_nombre']);
        
        header("Location: index.php?action=login_empleado");
        exit;
    }
}
?>