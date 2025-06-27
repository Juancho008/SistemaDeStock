<?php

/**
 *
 */
class Entregas extends Controlador
{
	/**
	 * Funciona constructora controla que el usuario este logueado
	 */
	function __construct($parametros = [])
	{
		$this->verificarAcceso();
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
		$this->vista('paginas/inicio', $datos);
		$this->footer();
	}
    function lista()
    {
        $datos = [];
        $this->header();
		$this->vista('paginas/entregas/lista', $datos);
		$this->footer();
    }

}
