<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Carrito de Compras - SÚPER MARKET</title>
    <link rel="stylesheet" href="css/tienda.css">
</head>
<body>

    <header class="carrito-header-principal">
        <div class="header-izquierdo">
            <a href="index.php?action=inicio_tienda" class="btn-regresar-tienda">
                🫲 REGRESAR A LA TIENDA
            </a>
            <h1>🛒 SÚPER MARKET</h1>
        </div>
        <div class="user-info">
            <span>Bienvenido(a), <strong><?= htmlspecialchars($_SESSION['cliente_nombre'] ?? 'Cliente'); ?></strong></span>
        </div>
    </header>

    <main class="container">
        <h2 class="section-title">Carro de Compras</h2>

        <div class="carrito-pagina-layout">
            
            <div class="carrito-productos-columna">
                <div id="pagina-carrito-items">
                    </div>
            </div>

            <div class="carrito-resumen-columna">
                <div class="resumen-card">
                    <h3>Resumen de la orden</h3>
                    <hr>
                    <div class="resumen-fila">
                        <span>Subtotal Productos:</span>
                        <span id="resumen-subtotal">S/. 0.00</span>
                    </div>
                    <div class="resumen-fila text-verde">
                        <span>Descuentos:</span>
                        <span>- S/. 0.00</span>
                    </div>
                    <hr>
                    <div class="resumen-fila total-grande">
                        <span>Total:</span>
                        <span id="resumen-total">S/. 0.00</span>
                    </div>
                    <button class="btn-continuar-compra">Continuar compra</button>
                </div>
            </div>

        </div>
    </main>

    <script src="js/carrito.js"></script>
</body>
</html>