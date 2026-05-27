<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Supermercado</title>
    <link rel="stylesheet" href="css/auth.css">
</head>
<body>

    <div class="login-card" style="width: 380px; margin: 20px auto;">
        <h2>CREAR CUENTA</h2>
        
        <form id="registroForm" action="index.php?action=guardar_cliente" method="POST">
            <div class="form-group">
                <label>Nombres</label>
                <input type="text" name="Nombres" required placeholder="Ej. Juan" maxlength="50">
            </div>

            <div class="form-group">
                <label>Apellido Paterno</label>
                <input type="text" name="ApePaterno" required placeholder="Ej. Pérez" maxlength="50">
            </div>

            <div class="form-group">
                <label>Apellido Materno</label>
                <input type="text" name="ApeMaterno" required placeholder="Ej. Ramos" maxlength="50">
            </div>

            <div class="form-group">
                <label>Tipo de Documento (DOI)</label>
                <select id="tipoDOI" name="TipoDOI" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; background-color: white;">
                    <option value="DNI" selected>DNI</option>
                    <option value="CE">C.E.</option>
                    <option value="CPP/PTP">PTP / CCP</option>
                    <option value="PASAPORTE">Pasaporte</option>
                </select>
            </div>

            <div class="form-group">
                <label>Número de Documento</label>
                <input type="text" id="numDOI" name="NumDOI" required placeholder="8 dígitos numéricos" maxlength="8" autocomplete="off">
            </div>

            <div class="form-group">
                <label>Género</label>
                <select name="Genero" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; background-color: white;">
                    <option value="" disabled selected>Selecciona tu género</option>
                    <option value="M">Masculino</option>
                    <option value="F">Femenino</option>
                </select>
            </div>

            <div class="form-group">
                <label>Teléfono</label>
                <div style="display: flex; align-items: center; border: 1px solid #cbd5e1; border-radius: 6px; background-color: white; padding: 0 10px;">
                    <span style="font-weight: bold; color: #64748b; padding-left: 12px; font-size: 14px; white-space: nowrap; display: flex; align-items: center;">+51 9
                    </span>
                    <input type="text" id="telefono" name="Telefono" required placeholder="12345678" maxlength="8" style="border: none; width: 100%; padding: 10px 0; outline: none;">
                </div>
            </div>

            <div class="form-group">
                <label>Correo Electrónico</label>
                <input type="email" name="Email" required placeholder="correo@ejemplo.com" maxlength="75">
            </div>

            <div class="form-group">
                <label>Contraseña</label>
                <div class="password-container">
                    <input type="password" id="password" name="Contrasenya" required placeholder="Crea tu contraseña">
                    <button type="button" id="togglePassword" class="toggle-btn" style="font-size: 13px; font-weight: 600; color: #3498db;">Mostrar</button>
                </div>
                <small style="color: #7f8c8d; font-size: 11px; display: block; margin-top: 5px;">
                    Mínimo 9 caracteres, Mayúscula, Minúscula, Número y Símbolo (@, #, $, etc.)
                </small>
            </div>

            <div class="form-group" style="margin-top: 15px;">
                <label>Confirmar Contraseña</label>
                <div class="password-container">
                    <input type="password" id="confirmPassword" required placeholder="Repite tu contraseña">
                    <button type="button" id="toggleConfirmPassword" class="toggle-btn" style="font-size: 13px; font-weight: 600; color: #3498db;">Mostrar</button>
                </div>
            </div>

            <button type="submit" class="btn-submit" style="background-color: #3498db; margin-top: 20px;">Confirmar Registro</button>
            
            <div style="text-align: center; margin-top: 15px; font-size: 14px;">
                ¿Ya tienes cuenta? <a href="index.php?action=login_cliente" style="color: #2ecc71; text-decoration: none; font-weight: bold;">Inicia sesión</a>
            </div>
        </form>
    </div>

    <script src="js/auth.js?=v1000"></script>
</body>
</html>