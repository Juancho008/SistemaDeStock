<?php

class Api extends Controlador
{
	function __construct()
	{
		$this->productos = $this->modelo('Productos');
		$this->categorias = $this->modelo('Categorias');
		$this->stock = $this->modelo('Stock');
		$this->estados = $this->modelo('Estado');
		$this->usuario = $this->modelo('Usuario');
		$this->sesion = $this->modelo('Sesion');
		$this->medidas = $this->modelo('Medidas');
		$this->perfiles = $this->modelo('Perfil');
		$this->oficina = $this->modelo('Oficina');
		$this->permiso = $this->modelo('Permiso');
		header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
		header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
	}
	function index()
	{
		echo "Controlador de API";
	}

	function obtenerProductosJson()
	{
		$productos = $this->obtenerProductosConStock();
		$jsonProductos = json_encode($productos);
		header('Content-Type: application/json');
		echo $jsonProductos;
		exit;
	}

	private function obtenerProductosConStock()
	{
		$this->helper('manipularImagen');
		$productos = $this->productos->listar();

		foreach ($productos as $llave => $valor) {
			$productos[$llave]['url'] = RUTA_URL;

			// Obtener stock por producto
			$stocks = $this->stock->obtenerStockPorIdProducto($valor['id']);
			$productos[$llave]['stock'] = $stocks;

			// Obtener categoría del producto
			$productos[$llave]['categoria'] = $this->categorias->obtenerCategoriaPorIdProducto($valor['id_categoria']);

			// Obtener permisos/oficinas del producto
			$productos[$llave]['oficinas'] = $this->permiso->obtenerPermisoPorIdProducto($valor['id']);
			// Asociar medidas si hay stock
			if (!empty($stocks) && is_array($stocks)) {
				foreach ($stocks as $index => $stockItem) {
					if (isset($stockItem['id_medidas'])) {
						$productos[$llave]['stock'][$index]['medida'] = $this->medidas->obtenerMedidaPorIdStock($stockItem['id_medidas']);
					} else {
						$productos[$llave]['stock'][$index]['medida'] = null; // Por si falta el id_medidas
					}
				}
			}
			$imagenPath = RUTA_APP . '/imagenes/producto/' . $valor['imagen'];
			$extension = pathinfo($imagenPath, PATHINFO_EXTENSION);
			$productos[$llave]['imagen_base64'] = base64EncodeImage($imagenPath, $extension);
		}

		return $productos;
	}
}
