<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda - Supermercado</title>
    <link rel="stylesheet" href="css/tienda.css">
</head>
<body>

    <header>    

        <header style="background-color: #1a202c; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center;">
        <h1 style="margin: 0; font-size: 24px;">🛒 SÚPER MARKET</h1>
        
        <div class="user-info">
            <a href="index.php?action=ver_carrito" class="btn-ir-carrito" style="background-color: #2ecc71; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 15px; display: inline-block; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
                🛒 Mi Carrito (<span id="contador-carrito">0</span>)
            </a>
                        <span>Bienvenido(a), <strong><?= htmlspecialchars($_SESSION['cliente_nombre'] ?? 'Cliente'); ?></strong></span>
            <a href="index.php?action=salir_cliente" class="btn-logout" style="background-color: white">Cerrar Sesión</a>
        </div>

    </header>

    <main class="container">
        
        <div class="welcome-box" style="margin-bottom: 30px;">

        </div>

        <h2 class="section-title">Nuestros Productos:</h2>
        
        <div class="productos-grid">
            <?php if (!empty($productos)): ?>
                <?php foreach ($productos as $producto): ?>
                    <div class="card-producto">
                        
                        <?php 
                        // Evaluamos la descripción para poner la clase CSS exacta
                        $claseImagen = match($producto['Descripcion']) {
                            'Yogurt Fresa'                 => 'img-yogurt-fresa',
                            'Leche Entera'                 => 'img-leche-entera',
                            'Cereal Chocolate'             => 'img-cereal-choco',
                            'Arroz Premium'                => 'img-arroz-premium',
                            'Yogurt Vainilla'              => 'img-yogurt-vainilla',
                            'Gaseosa Coca Cola'            => 'img-gaseosa-coca',
                            'Gaseosa Pepsi'                => 'img-gaseosa-pepsi',
                            'Fideos Spaghetti'             => 'img-fideos-spaghetti',
                            'Pasta Dental Triple Accion'   => 'img-pasta-dental',
                            'Detergente Multiuso'          => 'img-detergente',
                            default                        => ''
                        };
                        ?>

                        <div class="img-container">
                            <div class="img-producto-vacia <?= $claseImagen; ?>"></div>
                        </div>
                        
                        <span class="producto-meta">
                            <?= htmlspecialchars($producto['Categoria']); ?> | <?= htmlspecialchars($producto['Marca']); ?>
                        </span>
                        
                        <h3 class="producto-nombre">
                            <?= htmlspecialchars($producto['Descripcion']); ?>
                        </h3>
                        
                        <p class="producto-precio">
                            S/. <?= number_format($producto['Precio'], 2); ?>
                        </p>
                        
                        <button class="btn-agregar" 
                                data-id="<?= $producto['IdProducto']; ?>" 
                                data-nombre="<?= htmlspecialchars($producto['Descripcion']); ?>" 
                                data-precio="<?= $producto['Precio']; ?>"
                                data-clase="<?= $claseImagen; ?>"> Agregar al carrito
                        </button>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-products">
                    <p>No hay productos disponibles en este momento. Vuelve más tarde.</p>
                </div>
            <?php endif; ?>
        </div>
        
    </main>

    <script src="js/carrito.js"></script>
</body>
</html>