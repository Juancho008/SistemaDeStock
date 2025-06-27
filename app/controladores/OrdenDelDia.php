<?php

use Fpdf\Fpdf;

class OrdenDelDia extends Controlador
{
	public function __construct($parametros = [])
	{
		$this->verificarAcceso();
		$this->ordendeldia = $this->modelo('OrdenesDelDia');
		$this->pedidos = $this->modelo('Pedidos');
		$this->area = $this->modelo('Area');
		$this->detallesPedidos = $this->modelo('DetallesPedidos');
		$this->productos = $this->modelo('Productos');
		$this->area = $this->modelo('Area');
	}

	public function index()
	{
		$this->inicio();
	}

	public function inicio()
	{
		$ordenesdeldia = $this->ordendeldia->obtenerOrdenDelDia();
		$datos = [
			'ordenDelDia' => $ordenesdeldia,
		];
		$this->header('headerOrdenDia.php');
		$this->vista('paginas/ordendeldia/index', $datos);
		$this->footer();
	}

	public function generarNuevaOrdenDelDia()
	{
		if (!empty($_POST['inputFechaOrden'])) {
			$fecha_ordenDia = $_POST['inputFechaOrden'];
			$retorno = [
				'fecha' => $fecha_ordenDia,
				'estado' => 1
			];
			$this->ordendeldia->guardar($retorno);
			$this->redir('OrdenDelDia', 'index');
		}
	}

	public function enviarOrdenDelDia($parametro = [])
	{
		$obtenerOrdenDia = $parametro[0];
		$obtenerPedidos = $this->pedidos->obtenerOrdenDiaConArea($obtenerOrdenDia);
		$retorno = [
			'id' => $obtenerOrdenDia,
			'estado' => 2
		];
		$this->ordendeldia->guardar($retorno);

		foreach ($obtenerPedidos as $enviarPedidos) {
			$retorno_pedidos = [
				'id' => $enviarPedidos['id'],
				'estado' => 2
			];
			$this->pedidos->guardar($retorno_pedidos);
		}
		$this->redir('OrdenDelDia', 'index');
	}

	public function eliminarOrdenDelDia($parametro = [])
	{
		$obtenerOrdenDia = $parametro[0];
		$obtenerPedidosAsociadosAOrdenDia = $this->pedidos->obtenerPedidosPorOrdenDelDia($obtenerOrdenDia);
		$retorno = [
			'id' => $obtenerOrdenDia,
			'estado' => 8
		];
		$this->ordendeldia->guardar($retorno);
		if (count($obtenerPedidosAsociadosAOrdenDia) > 0) {
			foreach ($obtenerPedidosAsociadosAOrdenDia as $eliminarOrdenDelDiaPorPedidos) {
				$retorno_pedidos = [
					'id' => $eliminarOrdenDelDiaPorPedidos['id'],
					'id_orden_del_dia' => '',
				];
				$this->pedidos->guardar($retorno_pedidos);
			}
		}
		$this->redir('OrdenDelDia', 'index');
	}

	public function ordenDiaListado()
	{
		$ordenesDia = $this->ordendeldia->buscarOrdenDelDiaPendiente();
		$retorno = [
			'mensaje' => 'Datos de órdenes del día obtenidos correctamente.',
			'ordenDelDia' => $ordenesDia,
		];
		echo json_encode($retorno);
	}

	public function modificarOrden()
	{
		$fecha_ordenDia = $_POST['inputFechaOrden'];
		$id_ordenDia = $_POST['idOcultoFecha'];
		$retorno = [
			'fecha' => $fecha_ordenDia,
			'id' => $id_ordenDia
		];
		$this->ordendeldia->guardar($retorno);
		$this->redir('OrdenDelDia', 'index');
	}

	public function entregarOrdenDelDia($parametro = [])
	{
		$obtenerOrdenDia = $parametro[0];
		$obtenerPedidos = $this->pedidos->obtenerOrdenDiaConArea($obtenerOrdenDia);
		$retorno = [
			'estado' => 7,
			'id' => $obtenerOrdenDia
		];
		$this->ordendeldia->guardar($retorno);

		foreach ($obtenerPedidos as $enviarPedidos) {
			$retorno_pedidos = [
				'id' => $enviarPedidos['id'],
				'estado' => 7
			];
			$this->pedidos->guardar($retorno_pedidos);
		}
		$this->redir('OrdenDelDia', 'index');
	}

	public function imprimirOrdenDia($parametro = [])
	{
		//Hago el llamadode la funcion que genera el pdf
		$this->pdf = new Fpdf();
		//Llamo a la funcion que crear la hoja con los paramestros que cree en la fucion
		$this->generarHojaConIdOrdenDia($parametro[0]);
		//Imprimo la hoja mostrando el nombre del pdf 
		$this->mostrarHojaOrdenDia();
	}

	public function generarHojaConIdOrdenDia($parametro)
	{
		$ordenDia = $this->ordendeldia->obtenerOrdenDelDiaPorId($parametro);
		$pedido = $this->pedidos->obtenerOrdenDiaConArea($ordenDia['id']);

		$fechaOrdenDia = date('d/m/Y', strtotime($ordenDia['fecha']));

		$this->pdf->AddPage();
		$this->pdf->SetFont('Arial', 'B', 12);
		$this->pdf->SetTextColor(80, 80, 80);
		$this->pdf->setY(12);
		$this->pdf->setX(95);
		$this->pdf->Image(URL_IMG . '/logo-pdf.png', 25, 10, 80, 0);
		$this->pdf->Cell(90, 10, utf8_decode('N° ') . $parametro, 0, 0, 'C');

		$this->pdf->Ln();
		$this->pdf->SetFont('Arial', 'B', 12);
		$this->pdf->setY(12);
		$this->pdf->setX(130);
		$this->pdf->cell(90, 10, utf8_decode(date("d/m/Y")), 0, 0, "C");

		$this->pdf->Ln();
		$this->pdf->SetFont('Arial', 'B', 12);
		$this->pdf->setY(20);
		$this->pdf->setX(115);
		$this->pdf->cell(90, 10, utf8_decode('Pedidos Dpto. Suministros'), 0, 0, "C");

		$this->pdf->Ln();
		$this->pdf->SetFont('Arial', 'B', 12);
		$this->pdf->setY(27);
		$this->pdf->setX(115);
		$this->pdf->cell(90, 10, utf8_decode($fechaOrdenDia), 0, 0, "C");

		$this->pdf->Ln();
		$this->pdf->setY(40);
		$this->pdf->setX(10);
		$this->pdf->MultiCell(0, 10, '', 'T', 0);

		$this->pdf->Ln();
		$this->pdf->setY(40);
		$this->pdf->setX(10);
		$this->pdf->MultiCell(0, 14.9, '', 'L', 0);

		$this->pdf->Ln();
		$this->pdf->setY(40);
		$this->pdf->setX(10);
		$this->pdf->MultiCell(0, 14.9, '', 'R', 0);

		$this->pdf->Ln();
		$this->pdf->setY(45);
		$this->pdf->setX(10);
		$this->pdf->MultiCell(0, 10, '', 'B', 0);

		$this->pdf->Ln();
		$this->pdf->setY(40);
		$this->pdf->setX(25);
		$this->pdf->MultiCell(0, 15, '', 'L', 0);

		$this->pdf->Ln();
		$this->pdf->setY(40);
		$this->pdf->setX(55);
		$this->pdf->MultiCell(0, 15, '', 'L', 0);

		$this->pdf->Ln();
		$this->pdf->setY(47.6);
		$this->pdf->setX(82);
		$this->pdf->MultiCell(0, 7.3, '', 'L', 0);

		$this->pdf->Ln();
		$this->pdf->setY(47.6);
		$this->pdf->setX(137);
		$this->pdf->MultiCell(0, 7.3, '', 'L', 0);

		$this->pdf->Ln();
		$this->pdf->setY(40);
		$this->pdf->setX(110);
		$this->pdf->MultiCell(0, 15, '', 'L', 0);

		$this->pdf->Ln();
		$this->pdf->setY(32.5);
		$this->pdf->setX(55);
		$this->pdf->MultiCell(110, 15, '', 'B', 0);

		$this->pdf->Ln();
		$this->pdf->setY(40);
		$this->pdf->setX(165);
		$this->pdf->MultiCell(0, 15, '', 'L', 0);

		$this->pdf->Ln();
		$this->pdf->setY(40);
		$this->pdf->setX(15);
		$this->pdf->MultiCell(0, 15, utf8_decode('#'), 0);

		$this->pdf->Ln();
		$this->pdf->setY(40);
		$this->pdf->setX(28);
		$this->pdf->MultiCell(0, 15, utf8_decode('Solicitante'), 0);

		$this->pdf->Ln();
		$this->pdf->setY(37);
		$this->pdf->setX(61);
		$this->pdf->MultiCell(0, 15, utf8_decode('Articulos Solicitados'), 0);

		$this->pdf->Ln();
		$this->pdf->setY(37);
		$this->pdf->setX(121);
		$this->pdf->MultiCell(0, 15, utf8_decode('Ultima Entrega'), 0);

		$this->pdf->Ln();
		$this->pdf->setY(44);
		$this->pdf->setX(59);
		$this->pdf->MultiCell(0, 15, utf8_decode('Cantidad'), 0);

		$this->pdf->Ln();
		$this->pdf->setY(44);
		$this->pdf->setX(87);
		$this->pdf->MultiCell(0, 15, utf8_decode('Detalle'), 0);

		$this->pdf->Ln();
		$this->pdf->setY(44);
		$this->pdf->setX(116);
		$this->pdf->MultiCell(0, 15, utf8_decode('Fecha'), 0);

		$this->pdf->Ln();
		$this->pdf->setY(44);
		$this->pdf->setX(140);
		$this->pdf->MultiCell(0, 15, utf8_decode('Cantidad'), 0);

		$this->pdf->Ln();
		$this->pdf->setY(40);
		$this->pdf->setX(175.5);
		$this->pdf->MultiCell(0, 15, utf8_decode('Firma'), 0);

		$yTamaño = 55;

		foreach ($pedido as $pedidos) {
			$detalles = $this->detallesPedidos->obtenerDetallesConProducto($pedidos['id']);
			$cantidadDetalles = count($detalles);
			$altoCeldaPrincipal = $cantidadDetalles * 10;
			$this->pdf->Ln();
			$this->pdf->setY($yTamaño);
			$this->pdf->setX(10);
			$this->pdf->MultiCell(15, $altoCeldaPrincipal, utf8_decode($pedidos['id']), 'LRB');
			$this->pdf->setXY(25, $yTamaño);
			$this->pdf->MultiCell(30, $altoCeldaPrincipal, utf8_decode($pedidos['nombre']), 'LB');

			$this->pdf->setXY(165.2, $yTamaño);
			$this->pdf->MultiCell(34.8, $altoCeldaPrincipal, '', 'RB');

			$yPedido = $yTamaño;
			foreach ($detalles as $detalle) {
				$pedidoProductoAnterior = $this->pedidos->obtenerPedidoDeProductoAnterior($pedidos['id_area'], $detalle['id_producto']);
				$this->pdf->SetFont('Arial', 'B', 12);
				$this->pdf->setXY(55, $yPedido);
				$this->pdf->MultiCell(27, 10, utf8_decode($detalle['cantidad']), 'LRB');
				if (strlen($detalle['nombre']) > 12) {
					$this->pdf->SetFont('Arial', '', 8);
					$this->pdf->setXY(82, $yPedido);
					$this->pdf->Cell(28, 10, utf8_decode(substr($detalle['nombre'], 0, 18)), 'RB');
				} else {
					$this->pdf->SetFont('Arial', '', 12);
					$this->pdf->setXY(82, $yPedido);
					$this->pdf->Cell(28, 10, utf8_decode($detalle['nombre']), 'RB');
				}
				if (!empty($pedidoProductoAnterior)) {
					$this->pdf->SetFont('Arial', 'B', 12);
					$this->pdf->setXY(110, $yPedido);
					$this->pdf->MultiCell(27, 10, utf8_decode(date('d/m/Y', strtotime($pedidoProductoAnterior['fecha']))), 'RB');

					$this->pdf->SetFont('Arial', 'B', 12);
					$this->pdf->setXY(137.2, $yPedido);
					$this->pdf->MultiCell(27.8, 10, utf8_decode($pedidoProductoAnterior['cantidad']), 'RB');
				} else {
					$this->pdf->SetFont('Arial', 'B', 12);
					$this->pdf->setXY(110, $yPedido);
					$this->pdf->MultiCell(27, 10, '', 'RB');

					$this->pdf->SetFont('Arial', 'B', 12);
					$this->pdf->setXY(137.2, $yPedido);
					$this->pdf->MultiCell(27.8, 10, '', 'RB');
				}
				$yPedido += 10;
			}
			$yTamaño += $altoCeldaPrincipal;
		}
	}

	public function mostrarHojaOrdenDia($nombrePdf = "OrdenDelDia.pdf")
	{
		$this->pdf->Output("I", $nombrePdf);
	}

	public function listaPros()
	{
		$datos = [];
		$this->header('headerOrdenDia.php');
		$this->vista('paginas/ordendeldia/listaPros', $datos);
		$this->footer();
	}

	public function obtenerOrdenesPendientes()
	{
		$datos = [];
		$datos = $this->ordendeldia->obtenerOrdenesEnviadas();

		$this->header('headerOrdenDia.php');
		$this->vista('paginas/ordendeldia/listaPros', $datos);
		$this->footer();
	}

	public function obtenerOrdenesCerradas()
	{
		$datos = [];
		$datos = $this->ordendeldia->obtenerOrdenesCerradas();

		$this->header('headerOrdenDia.php');
		$this->vista('paginas/ordendeldia/listaPros', $datos);
		$this->footer();
	}

	public function obtenerPedidos(){

		$id = $_POST['id'];
		$detallesPedidos = [];
		$pedidos = $this->pedidos->obtenerPedidosOrden($id);
		foreach($pedidos as $valor){
			$detallesPedidos[$valor['id']] = $this->detallesPedidos->obtenerDetalles($valor['id']);
			$area = $this->area->obtenerAreaPorId($valor['id']);
			foreach($detallesPedidos[$valor['id']] as $key => $dato){
				$nombreProducto = $this->productos->obtenerNombreProducto($dato['id_producto']);
				$detallesPedidos[$valor['id']][$key]['nombreProducto'] = $nombreProducto['nombre'];
				$detallesPedidos[$valor['id']][$key]['nombreArea'] = $area['nombre'];
				$detallesPedidos[$valor['id']][$key]['estado'] = $valor['estado'];
			}
		}

		echo json_encode($detallesPedidos);
	}

	public function aceptarPedido(){
		$pedido = $_POST['pedido'];
		$retorno = $this->pedidos->aceptarPedido($pedido);
		echo json_encode($retorno);
	}

	public function rechazarPedido(){
		$pedido = $_POST['pedido'];
		$retorno = $this->pedidos->rechazarPedido($pedido);
		echo json_encode($retorno);
	}

	public function obtenerDetallesPedidos(){
		$pedido = $_POST['id'];
		$lista = [];
		$productos = $this->detallesPedidos->obtenerDetallesConId($pedido);
		
		foreach($productos as $key => $dato){
			$nombreProducto = $this->productos->obtenerNombreProducto($dato['id_producto']);
			$productos[$key]['nombreProducto'] = $nombreProducto['nombre'];
		}
		
		echo json_encode($productos);
	}
}
