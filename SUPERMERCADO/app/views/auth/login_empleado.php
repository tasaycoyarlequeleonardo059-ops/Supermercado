<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login Personal - Supermercado</title>
    <link rel="stylesheet" href="css/auth.css">
</head>
<body style="background-color: #2c3e50;"> <div class="login-card" style="width: 360px; margin: 100px auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.3);">
        <h2 style="color: #2ecc71; text-align: center; margin-bottom: 5px;">INTRANET</h2>
        <p style="text-align: center; color: #7f8c8d; font-size: 13px; margin-bottom: 20px;">Acceso exclusivo para colaboradores</p>

        <?php 
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (isset($_SESSION['error_login_emp'])): ?>
            <div style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 6px; margin-bottom: 15px; font-size: 14px; text-align: center; border: 1px solid #f5c6cb;">
                <?= $_SESSION['error_login_emp']; unset($_SESSION['error_login_emp']); ?>
            </div>
        <?php endif; ?>

        <form action="index.php?action=acceder_empleado" method="POST">
            <div class="form-group" style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #34495e;">Correo Empresarial</label>
                <input type="email" name="EmailEmpresarial" required placeholder="usuario@supermercado.com" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #34495e;">Contraseña</label>
                <input type="password" name="Contrasenya" required placeholder="Ingrese su clave" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
            </div>

            <button type="submit" class="btn-submit" style="background-color: #2ecc71; color: white; width: 100%; padding: 12px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 16px; margin-top: 10px;">
                Ingresar al Sistema
            </button>
        </form>
    </div>

</body>
</html>