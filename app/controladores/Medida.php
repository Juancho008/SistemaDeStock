<?php
class Medida extends Controlador
{
    public function __construct($parametros = [])
    {
        $this->verificarAcceso();
        $this->usuario = $this->modelo('Usuario');
        $this->sesion = $this->modelo('Sesion');
        $this->medida = $this->modelo('Medidas');
        $this->perfiles = $this->modelo('Perfil');
    }
    public function index()
    {
        $this->lista();
    }
    public function lista()
    {
        $medida = $this->obtenerMedidaConUrl();
        $datos =[
            'medida' => $medida
        ];
        $this->header('headerProducto.php');
        $this->vista('paginas/medida/lista',$datos);
        $this->footer();
    }
    public function nuevo()
    {
        $this->header('headerProducto.php');
        $this->vista('paginas/medida/nuevo');
        $this->footer();
    }
    public function agregarMedida()
    {
        $nombreMedida = $_POST['inputNombreMedida'];
        $cantidadMedida = $_POST['inputCantidad'];

        $retorno = [
            'descripcion' => $nombreMedida,
            'cantidad' => $cantidadMedida
        ];
        $this->medida->guardar($retorno);
        $this->redir('Medida', 'index');
    }
    public function darBajaMedida($parametro = [])
    {
		$id_medida =  $parametro[0];
		$estado = 1;
		$retorno = [
			'id'=>$id_medida,
			'estado'=>$estado
		];
		$this->medida->guardar($retorno);
		$this->redir('Medida','index');
    }
    
    public function editarMedida()
    {
		$id_medida =  $_POST['idMedida'];
		$nombreMedida = $_POST['inputNombreMedida'];
        $cantidadMedida = $_POST['inputCantidad'];
		$retorno = [
			'id'=>$id_medida,
			'descripcion'=>$nombreMedida,
            'cantidad'=>$cantidadMedida
		];
		$this->medida->guardar($retorno);
        $this->redir('Medida', 'index');
    }
    public function darAltaMedida($parametro = [])
    {
		$id_medida =  $parametro[0];
		$estado = 0;
		$retorno = [
			'id'=>$id_medida,
			'estado'=>$estado
		];
		$this->medida->guardar($retorno);
		$this->redir('Medida','index');
    }
    private function obtenerMedidaConUrl()
	{
		$medida = $this->medida->listar();
		foreach ($medida as $llave => $valor) {
			$medida[$llave]['url'] = RUTA_URL;
		}
		return $medida;
	}
}
