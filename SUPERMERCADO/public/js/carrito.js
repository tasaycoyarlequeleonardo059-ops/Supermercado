document.addEventListener('DOMContentLoaded', () => {
    const MAX_UNIDADES = 20;
    let carrito = JSON.parse(localStorage.getItem('super_carrito')) || [];

    // Elementos comunes u opcionales según la página donde estemos parado
    const contadorCarrito = document.getElementById('contador-carrito');
    const paginaItemsContenedor = document.getElementById('pagina-carrito-items');
    const resumenSubtotal = document.getElementById('resumen-subtotal');
    const resumenTotal = document.getElementById('resumen-total');

    // 1. Escuchar clics en los botones "Agregar al carrito" (Solo en la tienda)
    document.querySelectorAll('.btn-agregar').forEach(boton => {
        boton.addEventListener('click', (e) => {
            const id = e.target.getAttribute('data-id');
            const nombre = e.target.getAttribute('data-nombre');
            const precio = parseFloat(e.target.getAttribute('data-precio'));
            const claseImg = e.target.getAttribute('data-clase');

            agregarProducto(id, nombre, precio, claseImg);
        });
    });

    function agregarProducto(id, nombre, precio, claseImg) {
        const existe = carrito.find(item => item.id === id);

        if (existe) {
            if (existe.cantidad < MAX_UNIDADES) {
                existe.cantidad++;
            } else {
                alert(`Límite máximo de ${MAX_UNIDADES} unidades por producto.`);
            }
        } else {
            carrito.push({ id, nombre, precio, claseImg, quantity: 1, cantidad: 1 });
        }

        actualizarCarritoUI();
    }

    // 2. Función maestra para actualizar la interfaz según la página
    function actualizarCarritoUI() {
        let total = 0;
        let cantidadTotalItems = 0;

        // Calcular totales generales
        carrito.forEach(item => {
            total += item.precio * item.cantidad;
            cantidadTotalItems += item.cantidad;
        });

        // Si estamos en la TIENDA: Actualiza el numerito del botón verde del header
        if (contadorCarrito) {
            contadorCarrito.innerText = cantidadTotalItems;
        }

        // Si estamos en la PÁGINA EXCLUSIVA DEL CARRITO: Renderiza las filas estilo Tottus
        if (paginaItemsContenedor) {
            paginaItemsContenedor.innerHTML = '';

            if (carrito.length === 0) {
                paginaItemsContenedor.innerHTML = '<div class="carro-vacio"><p>Tu carro está vacío. ¡Regresa a la tienda para llenarlo! 🛒</p></div>';
            } else {
                carrito.forEach(item => {
                    const itemElemento = document.createElement('div');
                    itemElemento.classList.add('item-pagina-carrito');
                    // Busca esta línea dentro de tu carrito.js y reemplaza el bloque innerHTML:
                    itemElemento.innerHTML = `
    <div class="img-container-carrito" style="width: 100px; height: 100px; min-width: 100px;">
        <div class="img-producto-vacia ${item.claseImg}" style="height: 100%; margin-bottom: 0;"></div>
                </div>
    
                <div class="item-detalles">
        <h3>${item.nombre}</h3>
        <p class="precio-unidad">Precio unitario: S/. ${item.precio.toFixed(2)}</p>
        
        <div class="controles-cantidad">
            <button class="btn-cantidad btn-restar" data-id="${item.id}">-</button>
            <span class="cantidad-valor">${item.cantidad}</span>
            <button class="btn-cantidad btn-sumar" data-id="${item.id}" ${item.cantidad >= MAX_UNIDADES ? 'disabled' : ''}>+</button>
            <button class="btn-eliminar-basura" data-id="${item.id}" style="margin-left: 20px; background: none; border: none; color: #e74c3c; cursor: pointer; font-size: 15px; font-weight: bold;">🗑️ Eliminar</button>
                        </div>
                        </div>
    
                        <div class="item-subtotal-lado" style="font-weight: bold; font-size: 1.2rem; color: #2d3748; min-width: 100px; text-align: right;">
                        S/. ${(item.precio * item.cantidad).toFixed(2)}
                    </div>
                    `;
                    paginaItemsContenedor.appendChild(itemElemento);
                });
            }

            // Actualizar cuadro de resumen de la derecha
            if (resumenSubtotal && resumenTotal) {
                resumenSubtotal.innerText = `S/. ${total.toFixed(2)}`;
                resumenTotal.innerText = `S/. ${total.toFixed(2)}`;
            }
        }

        // Guardar cambios en LocalStorage siempre
        localStorage.setItem('super_carrito', JSON.stringify(carrito));
        asignarEventosInternos();
    }

    // 3. Controladores de botones dentro de la página del carro
    function asignarEventosInternos() {
        document.querySelectorAll('.btn-sumar').forEach(boton => {
            boton.replaceWith(boton.cloneNode(true)); // Limpia duplicación de eventos
        });
        document.querySelectorAll('.btn-restar').forEach(boton => {
            boton.replaceWith(boton.cloneNode(true));
        });
        document.querySelectorAll('.btn-eliminar-basura').forEach(boton => {
            boton.replaceWith(boton.cloneNode(true));
        });

        // Re-asignamos clics
        document.querySelectorAll('.btn-sumar').forEach(boton => {
            boton.addEventListener('click', (e) => {
                const id = e.target.getAttribute('data-id');
                const item = carrito.find(p => p.id === id);
                if (item && item.cantidad < MAX_UNIDADES) {
                    item.cantidad++;
                    actualizarCarritoUI();
                }
            });
        });

        document.querySelectorAll('.btn-restar').forEach(boton => {
            boton.addEventListener('click', (e) => {
                const id = e.target.getAttribute('data-id');
                const item = carrito.find(p => p.id === id);
                if (item) {
                    item.cantidad--;
                    if (item.cantidad <= 0) {
                        carrito = carrito.filter(p => p.id !== id);
                    }
                    actualizarCarritoUI();
                }
            });
        });

        document.querySelectorAll('.btn-eliminar-basura').forEach(boton => {
            boton.addEventListener('click', (e) => {
                const id = e.target.getAttribute('data-id');
                carrito = carrito.filter(p => p.id !== id);
                actualizarCarritoUI();
            });
        });
    }

    actualizarCarritoUI();
});