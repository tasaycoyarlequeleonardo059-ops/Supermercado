<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Control de seguridad por si intentan entrar escribiendo la URL directamente
if (!isset($_SESSION['id_empleado'])) {
    header("Location: index.php?action=login_empleado");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Intranet</title>
    <link rel="stylesheet" href="css/emp_pro.css">
</head>
<body>

    <header>
        <h2>⚙️ Sistema Interno de Gestión Administrativa</h2>
        <div>
            <span>Bienvenido(a), <strong><?= htmlspecialchars($_SESSION['empleado_nombre'] ?? 'Administrador'); ?></strong></span> | 
            <a href="index.php?action=salir_empleado" class="logout-btn">Cerrar Sesión</a>
        </div>
    </header>

    <div class="tabs-container">
        <button class="tab-button active" onclick="cambiarPestaña(event, 'inicio')">🏠 Inicio</button>
        <button class="tab-button" onclick="cambiarPestaña(event, 'empleados')">👥 EMPLEADOS</button>
        <button class="tab-button" onclick="cambiarPestaña(event, 'proveedores')">🚚 PROVEEDORES</button>
    </div>

    <div id="inicio" class="content-section active">
        <div class="welcome-box">
            <h1>🏪 Panel de Control Central</h1>
            <p style="color: #64748b; font-size: 16px; margin-top: 10px;">
                Por favor, elija una de las categorías en la barra superior para visualizar o gestionar los registros de la base de datos.
            </p>
        </div>
    </div>

    <div id="empleados" class="content-section">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h3>📋 Listado de Empleados Registrados</h3>
        </div>
        <table class="main-table">
            <thead>
                <tr>
                    <th>ID Empleado</th>
                    <th>Nombre Completo</th>
                    <th>Cargo / Rol</th> <th>Teléf. Emergencia</th>
                    <th>Correo Empresarial</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($listaEmpleados)): ?>
                    <?php foreach ($listaEmpleados as $emp): ?>
                        <tr>
                            <td><strong><?= $emp['IdEmpleado']; ?></strong></td>
                            <td><?= htmlspecialchars($emp['Nombres'] . ' ' . $emp['ApePaterno'] . ' ' . $emp['ApeMaterno']); ?></td>
                            <td><mark style="background-color: #e0f2fe; color: #0369a1; padding: 2px 6px; border-radius: 4px; font-weight: bold; font-size: 12px;"><?= htmlspecialchars($emp['NombreRol'] ?? 'Sin Asignar'); ?></mark></td>
                            <td><?= htmlspecialchars($emp['TelefonoEmergencia']); ?></td>
                            <td><?= htmlspecialchars($emp['EmailEmpresarial']); ?></td>
                            <td>
                                <span class="<?= $emp['Estado'] === '1' ? 'status-active' : 'status-inactive'; ?>">
                                    <?= $emp['Estado'] === '1' ? 'Activo' : 'Inactivo'; ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; color: #94a3b8; padding: 20px;">
                            No existen empleados registrados en el sistema.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div id="proveedores" class="content-section">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h3>🚛 Directorio de Proveedores / Distribuidores</h3>
        </div>
        <table class="main-table">
            <thead>
                <tr>
                    <th>ID Prov.</th>
                    <th>RUC</th>
                    <th>Razón Social</th>
                    <th>Teléfonos de Contacto</th>
                    <th>Email Corporativo</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($listaProveedores)): ?>
                    <?php foreach ($listaProveedores as $prov): ?>
                        <tr>
                            <td><?= $prov['IdProveedor']; ?></td>
                            <td><strong><?= htmlspecialchars($prov['RUC']); ?></strong></td>
                            <td><?= htmlspecialchars($prov['RazonSocial']); ?></td>
                            <td>
                                <?php 
                                    // Validamos e imprimimos dinámicamente solo los teléfonos que tengan datos
                                    $telefonos = [$prov['TelContacto1']];
                                    if (!empty($prov['TelContacto2'])) $telefonos[] = $prov['TelContacto2'];
                                    if (!empty($prov['TelContacto3'])) $telefonos[] = $prov['TelContacto3'];
                                    echo htmlspecialchars(implode(' / ', $telefonos));
                                ?>
                            </td>
                            <td><?= htmlspecialchars($prov['Email']); ?></td>
                            <td>
                                <span class="<?= $prov['Estado'] === '1' ? 'status-active' : 'status-inactive'; ?>">
                                    <?= $prov['Estado'] === '1' ? 'Activo' : 'Inactivo'; ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: #94a3b8; padding: 20px;">
                            No existen proveedores registrados en el sistema.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        function cambiarPestaña(event, idSeccion) {
            // 1. Ocultamos todas las secciones de contenido
            const secciones = document.querySelectorAll('.content-section');
            secciones.forEach(seccion => seccion.classList.remove('active'));

            // 2. Quitamos el sombreado y línea verde a todos los botones de pestañas
            const botones = document.querySelectorAll('.tab-button');
            botones.forEach(boton => boton.classList.remove('active'));

            // 3. Mostramos la sección en la que se hizo clic
            document.getElementById(idSeccion).classList.add('active');

            // 4. Marcamos el botón actual como el activo
            event.currentTarget.classList.add('active');
        }
    </script>

</body>
</html>