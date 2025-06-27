<?php
class Reporte extends Controlador
{
	/**
	 * Funciona constructora controla que el usuario este logueado
	 */
	public function __construct($parametros = [])
	{
		$this->verificarAcceso();
		$this->usuario = $this->modelo('Usuario');
		$this->sesion = $this->modelo('Sesion');
	}

}