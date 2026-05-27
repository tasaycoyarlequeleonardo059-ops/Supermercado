<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. CARGAMOS TODOS LOS CONTROLADORES DEL SISTEMA (Uso estricto de rutas)
require_once __DIR__ . '/../app/controllers/PersonaController.php';
require_once __DIR__ . '/../app/controllers/ClienteAuthController.php';
require_once __DIR__ . '/../app/controllers/EmpleadoAuthController.php';

// 2. INSTANCIAMOS LOS OBJETOS PRINCIPALES
$personaCtrl    = new PersonaController();
$clienteCtrl    = new ClienteAuthController();
$empleadoCtrl   = new EmpleadoAuthController();

// 3. CAPTURAMOS LA ACCIÓN
$action = $_GET['action'] ?? 'login_cliente';

// 4. REGLAS DE PROTECCIÓN PREVIAS CORREGIDAS
// Si intentan ir al panel administrativo ('index') pero NO hay empleado logueado, mandarlos a su login
if ($action === 'index' && !isset($_SESSION['id_empleado'])) {
    $action = 'login_empleado';
}

// 5. EL SWITCH MAESTRO QUE DIRIGE TODO EL TRÁFICO
switch ($action) {
    
    // ==========================================
    //    ACCIONES DEL FLUJO DE CLIENTES (TIENDA)
    // ==========================================
    case 'login_cliente':
        $clienteCtrl->index();
        break;
        
    case 'acceder_cliente':
        $clienteCtrl->acceder();
        break;

    case 'registro_cliente':
        $clienteCtrl->registro();
        break;

    case 'guardar_cliente':
        $clienteCtrl->guardar();
        break;
        
    case 'salir_cliente':
        $clienteCtrl->salir();
        break;

    case 'inicio_tienda':
        require_once __DIR__ . '/../app/controllers/ClienteAuthController.php';
        $authController = new ClienteAuthController();
        $authController->tienda(); 
        break;

    // ==========================================
    // NUEVA ACCIÓN: VISTA EXCLUSIVA DEL CARRITO
    // ==========================================
    case 'ver_carrito':
        require_once __DIR__ . '/../app/controllers/ClienteAuthController.php';
        $authController = new ClienteAuthController();
        $authController->verCarrito(); 
        break;


    // ==========================================
    //          RUTAS DE LOS EMPLEADOS
    // ==========================================
    case 'login_empleado':
        if (isset($_SESSION['id_empleado'])) {
            header("Location: index.php?action=index");
            exit;
        }
        $empleadoCtrl->index();
        break;

    case 'acceder_empleado':
        $empleadoCtrl->acceder();
        break;

    case 'salir_empleado':
        $empleadoCtrl->salir();
        break;


    // ==========================================
    //    ACCIONES DEL PANEL ADMINISTRATIVO (PERSONAS)
    // ==========================================
    case 'index':
        // 1. Verificamos que esté logueado como empleado
        if (!isset($_SESSION['id_empleado'])) {
            header("Location: index.php?action=login_empleado");
            exit;
        }
        
        // 2. CANDADO: Validamos que el rol limpio sea estrictamente Administrador
        if (trim($_SESSION['empleado_rol'] ?? '') !== 'Administrador') {
            header('HTTP/1.0 403 Forbidden');
            die("<div style=\"font-family:'Segoe UI', Arial, sans-serif; text-align:center; margin-top:100px;\">
                    <h1 style=\"color:#e74c3c;\">🛑 Acceso Denegado</h1>
                    <p style=\"color:#7f8c8d; font-size:18px;\">Tu rol actual es: <strong>" . htmlspecialchars($_SESSION['empleado_rol'] ?? 'Ninguno') . "</strong>. Esta zona es exclusiva para el Administrador.</p>
                    <a href=\"index.php?action=salir_empleado\" style=\"display:inline-block; background-color:#27ae60; color:white; padding:12px 24px; text-decoration:none; font-weight:bold; border-radius:5px;\">Regresar al Login</a>
                 </div>");
            exit;
        }
        
        // Si pasa el filtro, carga el panel
        $personaCtrl->index();
        break;

    case 'guardar':
        $personaCtrl->guardar();
        break;

    case 'eliminar':
        $personaCtrl->eliminar();
        break;

    default:
        if (isset($_SESSION['id_cliente'])) {
            header("Location: index.php?action=inicio_tienda");
        } elseif (isset($_SESSION['id_empleado'])) {
            header("Location: index.php?action=index");
        } else {
            $clienteCtrl->index();
        }
        break;
}
?>