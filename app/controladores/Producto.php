<?php
class Producto extends Controlador
{
	public function __construct($parametros = [])
	{
		$this->verificarAcceso();
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
	}
	public function index()
	{
		$this->lista();
	}
	public function lista()
	{
		$this->helper('manipularImagen');
		$productos = $this->obtenerProductosConStock();
		$categorias = $this->categorias->listar();
		$medida = $this->medidas->medidaActiva();
		$oficinas = $this->oficina->listar();
		$datos = [
			'oficinas' => $oficinas,
			'categorias' => $categorias,
			'productos' => $productos,
			'medida' => $medida
		];
		$this->header('headerProducto.php');
		$this->vista('paginas/producto/lista', $datos);
		$this->footer();
	}
	public function crearNuevoProducto()
	{
		$camposObligatorios = ['inputNombreProducto', 'inputStock', 'productoSelect', 'medidaSelect'];
		foreach ($camposObligatorios as $campo) {
			if (empty($_POST[$campo])) {
				echo '<script>
						alert("Debe rellenar todos los campos obligatorios.");
						window.location = "' . RUTA_URL . '/Producto/index";
					  </script>';
				exit; // Termina la ejecución del script si hay campos vacíos
			}
		}
		//Declaracion de variables
		$nombreProducto = $_POST['inputNombreProducto'];
		$codigoBarraProducto = $_POST['inputCodigoDeBarra'];
		$stock = $_POST['inputStock'];
		$imagenProducto = $_FILES['imagenProducto'];
		$productoImagenNombre = "";
		$id_medida = $_POST['medidaSelect'];
		$valor_minimo = $_POST['valorMinimo'];
		$id_categoria = $_POST['productoSelect'];
		//escapo los resultados que son ingresados por inputs
		$nombreProducto = $this->productos->escapar($nombreProducto);
		$codigoBarraProducto = $this->productos->escapar($codigoBarraProducto);
		$stock = $this->stock->escapar($stock);

		//consultas de validacion.
		$existeCodigoBarra = $this->productos->existeCodigoBarra($codigoBarraProducto);

		// Si $existeCodigoBarra está vacío, significa que el código de barras no existe en la base de datos.
		if (!empty($existeCodigoBarra)) {
			// Comprobamos si $existeCodigoBarra no está vacío, lo que significa que ya existe un producto con el mismo código de barras.
			echo '<script>
			agregarStock= confirm("Ya existe el codigo de barra, ¿desea agregarlo como stock?");
					if(agregarStock === true){
						fetch("' . RUTA_URL . '/Producto/agregarStockFetch",{
						method:"POST",
						body:JSON.stringify({
							id_producto:' . $existeCodigoBarra['id'] . ',
							cantidad:' . $stock . ',
							id_medida:' . $id_medida . ',
						})
					})
					.then(respuesta=>respuesta.json)
					.catch(err => console.log(err))
					setTimeout(() => {
						window.location = "' . RUTA_URL . '/Producto/index";
					  }, 500);
					}else{
						alert("Accion cancelada,no se pudo insertar a la lista.");
						window.location = "' . RUTA_URL . '/Producto/index";
					}
					
			</script>';
		} else {
			//Pregunto si es diferente de Null.
			if (!isset($_FILES['imagenProducto'])) {
				$imagenProducto = "";
				$extension = "";
			} else {
				// Verificamos el tamaño de la imagen y establecemos el nombre de archivo y extensión.
				if ($imagenProducto['size'] > 0) {
					$productoImagenNombre = "imagen_" . $nombreProducto;
				}
				$extension = pathinfo($imagenProducto['name'], PATHINFO_EXTENSION);
				if ($extension == 'jpeg') {
					$extension = 'jpg';
				}
			}

			// Subimos la imagen (si hay una), configuramos los datos del producto y lo guardamos en la base de datos.
			$this->subirArchivo($imagenProducto, $productoImagenNombre, 'imagenes/producto', ['publica' => 0]);
			$retorno = [
				'nombre' => $nombreProducto,
				'imagen' => $productoImagenNombre . '.' . $extension,
				'codigo_barra' => $codigoBarraProducto,
				'estado' => 0,
				'id_categoria' => $id_categoria,
				'valor_minimo' => $valor_minimo,
				'fecha_ingreso' => date('Y-m-d')
			];

			$this->productos->guardar($retorno);
			//a partir de aca se hace la solicitudes de datos para obtener la medida para calcular.
			$id_producto = $this->productos->insertId(); // Obtenemos el ID del producto recién insertado.
			$multiplicadorStock = $_POST['medidaSelect'];
			$medidaCantidad = $this->medidas->obtenerMultiplicadorMedida($multiplicadorStock);
			$resultadoMultiploStock = $medidaCantidad['cantidad'] * $stock;
			$retorno_stock = [
				'cantidad' => $resultadoMultiploStock,
				'estado' => 0,
				'id_productos' => $id_producto,
				'id_medidas' => $id_medida
			];
			$this->stock->guardar($retorno_stock); // Guardamos el stock del producto.
			if (isset($_POST['selectPermisos']) && !empty($_POST['selectPermisos'])) {
				$id_oficina = $_POST['selectPermisos'];
				foreach ($id_oficina as $oficinas) {
					$retorno_permisos = [
						'id_oficina' => $oficinas,
						'id_producto' => $id_producto
					];
					$this->permiso->guardar($retorno_permisos);
				}
			};
			$this->redir('Producto', 'index'); // Redirigimos a la página de inicio.

		}
	}
	public function editarProducto()
	{
		$camposObligatorios = ['inputNombreProducto', 'inputCodigoDeBarra', 'productoSelect'];
		foreach ($camposObligatorios as $campo) {
			if (empty($_POST[$campo])) {
				echo '<script>
						alert("Debe rellenar todos los campos obligatorios.");
						window.location = "' . RUTA_URL . '/Producto/index";
					  </script>';
				exit; // Termina la ejecución del script si hay campos vacíos
			}
		}
		$productoImagenNombre = "";
		$id_producto = $_POST['inputHiddenId'];
		$nombreProducto = $_POST['inputNombreProducto'];
		$codigoBarraProducto = $_POST['inputCodigoDeBarra'];
		$imagenProducto = $_FILES['imagenProducto'];
		$id_categoria = $_POST['productoSelect'];
		//escapando variables
		$nombreProducto = $this->productos->escapar($nombreProducto);
		$codigoBarraProducto = $this->productos->escapar($codigoBarraProducto);
		$id_producto = $this->productos->escapar($id_producto);
		if (!isset($_FILES['imagenProducto'])) {
			$imagenProducto = "";
			$extension = "";
		} else {
			// Verificamos el tamaño de la imagen y establecemos el nombre de archivo y extensión.
			if ($imagenProducto['size'] > 0) {
				$productoImagenNombre = "imagen_" . $nombreProducto;
			}
			$extension = pathinfo($imagenProducto['name'], PATHINFO_EXTENSION);
		}
		$this->subirArchivo($imagenProducto, $productoImagenNombre, 'imagenes/producto', ['publica' => 0]);
		$retorno = [
			'id' => $id_producto,
			'nombre' => $nombreProducto,
			'codigo_barra' => $codigoBarraProducto,
			'estado' => 0,
			'id_categoria' => $id_categoria,
			'fecha_ingreso' => date('Y-m-d')
		];
		if ($imagenProducto['size'] > 0) {
			$retorno['imagen'] = $productoImagenNombre . '.' . $extension;
		}
		//pregunto si existen permisos
		$permisosExistentes = $this->permiso->obtenerPermisoPorIdProducto($id_producto);
		if (isset($_POST['selectPermisos']) && !empty($_POST['selectPermisos'])) {
			$id_oficinaSeleccionadas = $_POST['selectPermisos'];
			foreach ($permisosExistentes as $permisoExistente) {
				$id_oficinaExistente = $permisoExistente['id_oficina'];
				if (!in_array($id_oficinaExistente, $id_oficinaSeleccionadas)) {
					$this->permiso->eliminarPermisos($id_producto, $id_oficinaExistente);
				}
			}
			foreach ($id_oficinaSeleccionadas as $id_oficina) {
				$permisoExistente = $this->permiso->verificarPermisoExistente($id_producto, $id_oficina);
				if (!$permisoExistente) {
					$retorno_permisos = [
						'id_oficina' => $id_oficina,
						'id_producto' => $id_producto
					];
					$this->permiso->guardar($retorno_permisos);
				}
			}
		} else {
			$this->permiso->eliminarPermisos($id_producto);
		}
		$this->productos->guardar($retorno);
		$this->redir('Producto', 'index');
	}
	public function agregarStockFetch($fetchProducto)
	{
		$fetchProducto = json_decode(file_get_contents('php://input'), true);
		$stock = $this->stock->obtenerStockPorIdProducto($fetchProducto['id_producto']);
		$resultadoStock = $fetchProducto['cantidad'] + $stock['cantidad'];

		$retorno_fetch = [
			'id' => $stock['id'],
			'cantidad' => $resultadoStock,
			'estado' => $stock['estado'],
			'id_productos' => $fetchProducto['id_producto'],
			'id_medidas' => $fetchProducto['id_medida']
		];
		$this->stock->guardar($retorno_fetch);
	}
	public function modificarStock()
	{
		$camposObligatorios = ['medidaSelect', 'inputStock'];
		foreach ($camposObligatorios as $campo) {
			if (empty($_POST[$campo])) {
				echo '<script>
						alert("Debe rellenar todos los campos obligatorios.");
						window.location = "' . RUTA_URL . '/Producto/index";
					  </script>';
				exit; // Termina la ejecución del script si hay campos vacíos
			}
		}
		$cantidad = $_POST['inputStock'];
		$id_medida = $_POST['medidaSelect'];
		$id_stock = $_POST['inputHiddenId'];
		$id_producto = $_POST['inputModalHidden-id'];
		if ($_POST['valorMinimo']) {
			$valorMinimo = $_POST['valorMinimo'];
			$retValorMinimo = [
				'id' => $id_producto,
				'valor_minimo' => $valorMinimo
			];
			$this->productos->guardar($retValorMinimo);
		}
		$stock_modificar = [
			'id' => $id_stock,
			'id_productos' => $id_producto,
			'id_medidas' => $id_medida,
			'cantidad' => $cantidad,
		];
		$this->stock->guardar($stock_modificar);
		$this->redir('Producto', 'index');
	}
	public function sumarStockActual()
	{
		$camposObligatorios = ['medidaSelect', 'inputStock'];
		foreach ($camposObligatorios as $campo) {
			if (empty($_POST[$campo])) {
				echo '<script>
						alert("Debe rellenar todos los campos obligatorios.");
						window.location = "' . RUTA_URL . '/Producto/index";
					  </script>';
				exit; // Termina la ejecución del script si hay campos vacíos
			}
		}
		$stockActual = $this->stock->obtenerStockPorIdTabla($_POST['inputHiddenId']);
		$cantidad = $_POST['inputStock'];
		$id_medida = $_POST['medidaSelect'];
		$id_stock = $_POST['inputHiddenId'];
		$id_producto = $_POST['inputModalHidden-id'];
		$sumaStock = $stockActual['cantidad'] + $cantidad;
		$stock_modificar = [
			'id' => $id_stock,
			'id_productos' => $id_producto,
			'id_medidas' => $id_medida,
			'cantidad' => $sumaStock,
		];
		$this->stock->guardar($stock_modificar);
		$this->redir('Producto', 'index');
	}
	public function sumarStockBultoActual()
	{
		$camposObligatorios = ['medidaSelect', 'inputStock'];
		foreach ($camposObligatorios as $campo) {
			if (empty($_POST[$campo])) {
				echo '<script>
						alert("Debe rellenar todos los campos obligatorios.");
						window.location = "' . RUTA_URL . '/Producto/index";
					  </script>';
				exit; // Termina la ejecución del script si hay campos vacíos
			}
		}
		$stockActual = $this->stock->obtenerStockPorIdTabla($_POST['inputHiddenId']);
		$cantidad = $_POST['inputStock'];
		$id_medida = $_POST['medidaSelect'];
		$id_stock = $_POST['inputHiddenId'];
		$id_producto = $_POST['inputModalHidden-id'];
		$medidaCantidad = $this->medidas->obtenerMultiplicadorMedida($id_medida);
		$sumaStock = $medidaCantidad['cantidad'] * $cantidad;
		$stockActualSuma = $stockActual['cantidad'] + $sumaStock;
		$stock_bulto = [
			'id' => $id_stock,
			'id_productos' => $id_producto,
			'id_medidas' => $id_medida,
			'cantidad' => $stockActualSuma,
		];
		$this->stock->guardar($stock_bulto);
		$this->redir('Producto', 'index');
	}
	public function retirarStockPorUnidad()
	{
		$camposObligatorios = ['inputStock'];
		foreach ($camposObligatorios as $campo) {
			if (empty($_POST[$campo])) {
				echo '<script>
						alert("Debe rellenar todos los campos obligatorios.");
						window.location = "' . RUTA_URL . '/Producto/index";
					  </script>';
				exit; // Termina la ejecución del script si hay campos vacíos
			}
		}
		$stockActual = $this->stock->obtenerStockDisponiblePorIdProducto($_POST['inputModalHidden-id']);
		$cantidad = $_POST['inputStock'];
		$id_medida = $_POST['medidaSelect'];
		$id_stock = $_POST['inputHiddenId'];
		$id_producto = $_POST['inputModalHidden-id'];
		$sumaStock = $stockActual['cantidad'] - $cantidad;
		if ($sumaStock < 0) {
			echo '<script>alert("No se puede retirar productos mayor al stock actual y no puede tener valores negativos en el stock")</script>';
			$this->redir('Producto', 'index');
		} else {
			$stock_modificar = [
				'id' => $id_stock,
				'id_productos' => $id_producto,
				'id_medidas' => $id_medida,
				'cantidad' => $sumaStock,
			];
			$this->stock->guardar($stock_modificar);
			$this->redir('Producto', 'index');
		}
	}
	public function retirarStockPorBulto()
	{
		$camposObligatorios = ['inputStock'];
		foreach ($camposObligatorios as $campo) {
			if (empty($_POST[$campo])) {
				echo '<script>
						alert("Debe rellenar todos los campos obligatorios.");
						window.location = "' . RUTA_URL . '/Producto/index";
					  </script>';
				exit; // Termina la ejecución del script si hay campos vacíos
			}
		}
		$stockActual = $this->stock->obtenerStockDisponiblePorIdProducto($_POST['inputModalHidden-id']);
		$cantidad = $_POST['inputStock'];
		$id_medida = $_POST['medidaSelect'];
		$id_stock = $_POST['inputHiddenId'];
		$id_producto = $_POST['inputModalHidden-id'];
		$medidaCantidad = $this->medidas->obtenerMultiplicadorMedida($id_medida);
		$sumaStock = $medidaCantidad['cantidad'] * $cantidad;
		$stockActualSuma = $stockActual['cantidad'] - $sumaStock;
		if ($stockActualSuma < 0) {
			echo '<script>alert("No se puede retirar productos mayor al stock actual y no puede tener valores negativos en el stock")</script>';
			$this->redir('Producto', 'index');
		} else {
			$stock_bulto = [
				'id' => $id_stock,
				'id_productos' => $id_producto,
				'id_medidas' => $id_medida,
				'cantidad' => $stockActualSuma,
			];
		}
		$this->stock->guardar($stock_bulto);
		$this->redir('Producto', 'index');
	}
	public function desactivarProducto($parametro = [])
	{
		$id_producto =  $parametro[0];
		$estado = 1;
		$retorno = [
			'id' => $id_producto,
			'estado' => $estado
		];
		$this->productos->guardar($retorno);
		$this->redir('Producto', 'index');
	}
	public function activarProducto($parametro = [])
	{
		$id_producto =  $parametro[0];
		$estado = 0;
		$retorno = [
			'id' => $id_producto,
			'estado' => $estado
		];
		$this->productos->guardar($retorno);
		$this->redir('Producto', 'index');
	}

	private function obtenerProductosConStock()
	{
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
		}

		return $productos;
	}

	public function listaPros()
	{
		$this->helper('manipularImagen');
		$productos = $this->obtenerProductosConStock();
		$categorias = $this->categorias->listar();
		$medida = $this->medidas->medidaActiva();
		$oficinas = $this->oficina->listar();
		$datos = [
			'oficinas' => $oficinas,
			'categorias' => $categorias,
			'productos' => $productos,
			'medida' => $medida
		];
		$this->header('headerProducto.php');
		$this->vista('paginas/producto/listaPros', $datos);
		$this->footer();
	}

	public function reportarStock()
	{
		$camposObligatorios = ['medidaSelect', 'inputStock'];
		foreach ($camposObligatorios as $campo) {
			if (empty($_POST[$campo])) {
				echo '<script>
						alert("Debe rellenar todos los campos obligatorios.");
						window.location = "' . RUTA_URL . '/Producto/index";
					  </script>';
				exit;
			}
		}
		$stockActual = $this->stock->obtenerStockPorIdTabla($_POST['inputHiddenId']);
		$cantidad = $_POST['inputStock'] ?? 0;
		$id_medida = $_POST['medidaSelect'] ?? null;
		$id_stock = $_POST['inputHiddenId'] ?? null;
		$id_producto = $_POST['inputModalHidden-id'] ?? null;
		$id_obs = $_POST['observaciones'] ?? null;
		$sumaStock = $stockActual['cantidad'] + $cantidad;
		$stock_modificar = [
			'id' => $id_stock,
			'id_productos' => $id_producto,
			'id_medidas' => $id_medida,
			'cantidad' => $sumaStock,
			'observacion' => $id_obs
		];
		$this->stock->guardar($stock_modificar);
		$this->redir('Producto', 'index');
	}

	public function editarStockDanado()
	{
		$camposObligatorios = ['medidaSelect', 'inputStock'];
		foreach ($camposObligatorios as $campo) {
			if (empty($_POST[$campo])) {
				echo '<script>
						alert("Debe rellenar todos los campos obligatorios.");
						window.location = "' . RUTA_URL . '/Producto/index";
					  </script>';
				exit;
			}
		}
		$cantidad = $_POST['inputStock'];
		$id_medida = $_POST['medidaSelect'];
		$id_stock = $_POST['inputHiddenId'];
		$id_producto = $_POST['inputModalHidden-id'];
		$observaciones = $_POST['observaciones'];
		$stock_modificar = [
			'id' => $id_stock,
			'id_productos' => $id_producto,
			'id_medidas' => $id_medida,
			'cantidad' => $cantidad,
			'observacion' => $observaciones
		];
		$this->stock->guardar($stock_modificar);
		$this->redir('Producto', 'index');
	}
}
