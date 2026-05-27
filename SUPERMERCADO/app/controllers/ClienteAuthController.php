<?php
require_once __DIR__ . '/../models/ClienteModel.php';
// 1. INYECTAMOS EL MODELO DE PRODUCTOS AQUÍ
require_once __DIR__ . '/../models/ProductoModel.php';

class ClienteAuthController 
{
    private $model;

    public function __construct() {
        $this->model = new ClienteModel();
    }

    /**
     * Muestra la vista de Login
     */
    public function index() {
        require_once __DIR__ . '/../views/auth/login_cliente.php';
    }

    /**
     * Muestra la vista de Registro
     */
    public function registro() {
        require_once __DIR__ . '/../views/auth/registro_cliente.php';
    }

    /**
     * Procesa el inicio de sesión del cliente
     */
    public function acceder() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (session_status() === PHP_SESSION_NONE) session_start();

            $email    = trim($_POST['EmailCliente'] ?? '');
            $password = trim($_POST['Contrasenya'] ?? '');

            if (empty($email) || empty($password)) {
                $_SESSION['error_login'] = "Por favor, complete todos los campos.";
                header("Location: index.php?action=login_cliente");
                exit;
            }

            $cliente = $this->model->login($email);

            if ($cliente) {
                if ($password === $cliente['Contrasenya']) {
                    $_SESSION['id_cliente']     = $cliente['IdCliente'];
                    $_SESSION['cliente_nombre']  = $cliente['Nombres'] . ' ' . $cliente['ApePaterno'];

                    header("Location: index.php?action=inicio_tienda");
                    exit;
                } else {
                    $_SESSION['error_login'] = "Contraseña incorrecta.";
                    header("Location: index.php?action=login_cliente");
                    exit;
                }
            } else {
                $_SESSION['error_login'] = "El correo electrónico no está registrado.";
                header("Location: index.php?action=login_cliente");
                exit;
            }
        }
    }

    /**
     * Procesa y guarda el registro del nuevo cliente
     */
    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombres    = trim($_POST['Nombres'] ?? '');
            $apePat     = trim($_POST['ApePaterno'] ?? '');
            $apeMat     = trim($_POST['ApeMaterno'] ?? '');
            $tipoDOI    = trim($_POST['TipoDOI'] ?? '');
            $numDOI     = trim($_POST['NumDOI'] ?? '');
            $genero     = trim($_POST['Genero'] ?? ''); 
            $telefono   = trim($_POST['Telefono'] ?? '');
            $email      = trim($_POST['Email'] ?? '');
            $password   = trim($_POST['Contrasenya'] ?? '');

            if (empty($nombres) || empty($apePat) || empty($apeMat) || empty($tipoDOI) || empty($numDOI) || empty($genero) || empty($telefono) || empty($email) || empty($password)) {
                echo "<div style='color:#e74c3c; font-family:Arial; padding:20px; background:#fadbd8; border-radius:8px; max-width:500px; margin:20px auto; text-align:center;'>";
                echo "<h3>❌ Error de envío</h3>";
                echo "<p>Todos los campos del formulario son estrictamente obligatorios.</p>";
                echo "<br><a href='javascript:history.back()' style='background:#e74c3c; color:white; padding:8px 15px; text-decoration:none; border-radius:4px;'>Volver</a>";
                echo "</div>";
                exit;
            }

            $tieneLongitud  = strlen($password) >= 9;
            $tieneMayuscula = preg_match('/[A-Z]/', $password);
            $tieneMinuscula = preg_match('/[a-z]/', $password);
            $tieneNumero    = preg_match('/\d/', $password);
            $tieneEspecial  = strlen(preg_replace('/[A-Za-z0-9]/', '', $password)) > 0;

            if (!$tieneLongitud || !$tieneMayuscula || !$tieneMinuscula || !$tieneNumero || !$tieneEspecial) {
                echo "<div style='color:#e74c3c; font-family:Arial; padding:20px; background:#fadbd8; border-radius:8px; max-width:500px; margin:20px auto;'>";
                echo "<h3>❌ Contraseña Inválida</h3>";
                echo "<p>La contraseña proporcionada no cumple con los requisitos mínimos de seguridad.</p>";
                echo "<br><a href='javascript:history.back()' style='background:#e74c3c; color:white; padding:8px 15px; text-decoration:none; border-radius:4px;'>Volver a intentar</a>";
                echo "</div>";
                exit;
            }

            $resultado = $this->model->registrar($tipoDOI, $numDOI, $apePat, $apeMat, $nombres, $genero, $telefono, $email, $password);

            if ($resultado === true) {
                header("Location: index.php?action=login_cliente");
                exit;
            } else {
                echo "<div style='color:#c0392b; font-family:Arial; padding:20px; background:#f9ebd2; border: 1px solid #f39c12; border-radius:8px; max-width:600px; margin:20px auto; text-align:center;'>";
                echo "<h3>⚠️ Registro Rechazado</h3>";
                echo "<p style='font-size: 15px; font-weight: bold;'>" . htmlspecialchars($resultado) . "</p>";
                echo "<br><a href='javascript:history.back()' style='background:#d35400; color:white; padding:8px 15px; text-decoration:none; border-radius:4px;'>Corregir Datos</a>";
                echo "</div>";
            }
        }
    }

    /**
     * Muestra la tienda con los productos de la Base de Datos
     */
    public function tienda() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Seguridad: Si no hay sesión de cliente, rebótalo al login
        if (!isset($_SESSION['id_cliente'])) {
            header("Location: index.php?action=login_cliente");
            exit;
        }

        // 2. MODIFICAMOS AQUÍ: Instanciamos el modelo y traemos los productos de la BD
        $productoModel = new ProductoModel();
        $productos = $productoModel->listarProductos();

        // Carga la vista de la tienda (ahora con la variable $productos lista para usarse)
        require_once __DIR__ . '/../views/tienda/inicio.php';
    }

    /**
     * Cierra la sesión del cliente
     */
    public function salir() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_destroy();
        header("Location: index.php?action=login_cliente");
        exit;
    }

    /**
     * Muestra la página dedicada exclusivamente al carrito de compras
     */
    public function verCarrito() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Seguridad: Si no hay sesión, al login
        if (!isset($_SESSION['id_cliente'])) {
            header("Location: index.php?action=login_cliente");
            exit;
        }

        // Cargamos la nueva vista independiente
        require_once __DIR__ . '/../views/tienda/carrito_vista.php';
    }
}
?>