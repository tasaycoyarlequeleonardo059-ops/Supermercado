<?php
require_once 'app/models/ProductoModel.php';

class ProductoController {
    private $model;

    public function __construct() {
        $this->model = new ProductoModel();
    }

    // Método principal para mostrar la tienda en el inicio
    public function mostrarTienda() {
        // 1. Obtener los productos desde el modelo
        $productos = $this->model->listarProductos();

        // 2. Cargar la vista pasándole los datos
        // Asegúrate de que esta sea la ruta exacta a tu vista de inicio
        require_once 'app/views/tienda/inicio.php';
    }
}
?>