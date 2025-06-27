<?php

class Pedido extends Controlador
{
	/**
	 * Funciona constructora controla que el usuario este logueado
	 */
	function __construct($parametros = [])
	{
		$this->verificarAcceso();
		$this->pedidos = $this->modelo('Pedidos');
		$this->medidas = $this->modelo('Medidas');
		$this->detallesPedidos = $this->modelo('DetallesPedidos');
		$this->productos = $this->modelo('Productos');
		$this->categorias = $this->modelo('Categorias');
		$this->stock = $this->modelo('Stock');
		$this->estados = $this->modelo('Estado');
		$this->usuario = $this->modelo('Usuario');
		$this->sesion = $this->modelo('Sesion');
		$this->ordenDia = $this->modelo('OrdenesDelDia');
		$this->perfiles = $this->modelo('Perfil');
		$this->oficina = $this->modelo('Oficina');
		$this->permiso = $this->modelo('Permiso');

	}

	function index()
	{
		$this->inicio();
	}

	function inicio()
	{
		//$this->verificarPermiso('L');
		$datos = [];

		$this->header();
		$this->vista('paginas/pedidos/index', $datos);
		$this->footer();
	}

    function solicitarPedido()
    {
		$datos = [];
        $this->header('header.php');
		$this->vista('paginas/pedido/solicitarPedido', $datos);
		$this->footer();
    }

	function lista()
    {	
		$datos = [];
        $this->header('headerPedido.php');
		$this->vista('paginas/pedido/lista', $datos);
		$this->footer();
    }


}
