<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login Clientes - Supermercado</title>
    <link rel="stylesheet" href="css/auth.css">
</head>
<body>

    <div class="login-card">
        <h2>INICIAR SESIÓN</h2>
        
        <form action="index.php?action=acceder_cliente" method="POST">
            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="EmailCliente" required placeholder="ejemplo@correo.com">
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <div class="password-container">
                    <input type="password" id="password" name="Contrasenya" required placeholder="********">
                    <button type="button" id="togglePassword" class="toggle-btn" style="font-size: 13px; font-weight: 600; color: #3498db;">Mostrar</button>
                </div>
                
                <?php if (isset($_SESSION['error_login'])): ?>
                    <span class="error-text"><?= $_SESSION['error_login']; ?></span>
                    <?php unset($_SESSION['error_login']); ?>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn-submit">Ingresar</button>
            
            <div style="text-align: center; margin-top: 15px; font-size: 14px;">
                ¿Aún no tienes cuenta? <a href="index.php?action=registro_cliente" style="color: #3498db; text-decoration: none; font-weight: bold;">Regístrate aquí</a>
            </div>
        </form>
    </div>

    <script src="js/auth.js"></script>
</body>
</html>