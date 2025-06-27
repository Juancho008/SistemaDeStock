<?php
class Categoria extends Controlador
{
	/**
	 * Funciona constructora controla que el usuario este logueado
	 */
	public function __construct($parametros = [])
	{
		$this->verificarAcceso();
		$this->productos = $this->modelo('Productos');
		$this->categorias = $this->modelo('Categorias');
		$this->stock = $this->modelo('Stock');
		$this->estados = $this->modelo('Estado');
		$this->usuario = $this->modelo('Usuario');
		$this->sesion = $this->modelo('Sesion');
	}
	public function index()
	{
		$this->lista();
	}
	public function lista()
	{
		$this->helper('manipularImagen');
		$lista = $this->agregarSubcategorias($this->categorias->listadoDeCategorias());
		$crearLista = $this->crearTablasCategorias($lista);
		$datos = [
			'lista' => $lista,
			'crearLista' => $crearLista
		];
		$this->header('header.php');
		$this->vista('paginas/categoria/lista', $datos);
		$this->footer();
	}
	//Metodo que me permite generar nuevas tablas dentro de un tr
	public function crearTablasCategorias($categorias)
	{
		$tabla = "";
		if (!empty($categorias)) {
			foreach ($categorias as $index => $valor) {
				$extension = pathinfo($index['imagen'], PATHINFO_EXTENSION);
				$imagen = "<img src='" . base64EncodeImage(RUTA_APP . '/imagenes/categoria/' . $valor['imagen'], $extension) . "' alt='No hay imagen disponible' style='width:150px; height:100px; border-radius:5px; margin-left:2rem;'/>";
				if ($valor['estado'] == 6) {
					$estado = '<p style="font-size:1rem;" class="bg-success text-white p-3 mb-2 text-center rounded">Activo</p>';
					$estadoOpcion =  "<button onclick=darBaja(" . $valor['id'] . ") class='mr-1 ml-1 btn btn-warning btn-sm'>Dar de baja</button>";
				} elseif($valor['estado'] == 5){
					$estado = '<p style="font-size:1rem;" class="bg-secondary text-white p-3 mb-2 text-center rounded">Inactivo</p>';
					$estadoOpcion = "<button onclick=darAlta(" . $valor['id'] . ") class='mr-1 ml-1 btn btn-success btn-sm'>Dar de Alta</button>";
				};
				if ($valor['cat_padre'] > 0) {
					$imagen = "<img src='" . base64EncodeImage(RUTA_APP . '/imagenes/categoria/' . $valor['imagen'], $extension) . "' alt='No hay imagen disponible' style='width:100px; height:50px; border-radius:5px; margin-left:2rem;'/>";
					$tabla .= "<tr  class='table-info filas-" . $valor['cat_padre'] . " collapsed collapse' aria-expanded='false'>
					<input type='hidden' id='categoriaId' value='" . $valor['id'] . "'>
					<td style='font-size:0.9rem;'>" . $valor['id'] . "</td>
					<td style='font-size:0.9rem;'>" . $valor['nombre'] . "</td>
					<td class='imagen-td'>" . $extension . $imagen . "</td>
					<td style='font-size:0.9rem;'>" . $estado . "</td>
					<td><button class='btn btn-dark btn-sm ml-1' onclick='modalSubcategoria(" . json_encode($valor) . ")' data-toggle='modal' data-target='#modalSubCategorias'>Agregar Subcategoria</button> <button  onclick='editarCategoria(" . json_encode($valor) . ")'  class='btn btn-primary btn-sm' data-toggle='modal' data-target='#modalSubCategorias'>Editar</button>" . $estadoOpcion;
					if (isset($valor['subcategorias'])) {
						$tabla .= "<input aria-expanded='false' data-target='.filas-" . $valor['id'] . "' data-toggle='collapse' id='" . $valor['id'] . "' onclick='cambiarColores(this)' type='button' class='btn btn-success btn-sm ml-1' value='+'>";
					}
					$tabla .= "</td></tr>";
				} else {
					$tabla .= "<tr>
									<input type='hidden' id='categoriaId' value='" . $valor['id'] . "'>
									<td style='font-size:1.3rem;'>" . $valor['id'] . "</td>
									<td style='font-size:1.3rem;'>" . $valor['nombre'] . "</td>
									<td class='imagen-td'>" . $extension . $imagen . "</td>
									<td style='font-size:1.3rem;'>" . $estado . "</td>
									<td><button class='btn btn-dark btn-sm ml-1' onclick='modalSubcategoria(" . json_encode($valor) . ")' data-toggle='modal' data-target='#modalSubCategorias'>Agregar Subcategoria</button> <button  onclick='editarCategoria(" . json_encode($valor) . ")'  class='btn btn-primary btn-sm' data-toggle='modal' data-target='#modalSubCategorias'>Editar</button>" . $estadoOpcion;
					if (isset($valor['subcategorias'])) {
						$tabla .= "<input data-toggle='collapse' data-target='.filas-" . $valor['id'] . "' aria-expanded='false' onclick='cambiarColores(this)' type='button' class='collapsed btn btn-success btn-sm ml-1' value='+'>";
					}
					$tabla .= "</td></tr>";
				};
				if (!empty($valor['subcategorias'])) {
					$tabla .= $this->crearTablasCategorias($valor['subcategorias']);
				}
			}
			return $tabla;
		} else {
			return array();
		}
	}
	//Metodo que lista las sub categorias anidandolas con las categorais padres
	public function agregarSubcategorias($subcategorias)
	{
		if (!empty($subcategorias)) {
			foreach ($subcategorias as $index => $valor) {
				$sub = $this->categorias->listarSubCategoria($valor["id"]);
				if (!empty($sub)) {
					$subcategorias[$index]["subcategorias"] = $this->agregarSubcategorias($sub);
				}
			}
			return $subcategorias;
		} else {
			return array();
		}
	}
	//Metodo que permite crear nuevas subcategorias
	public function nuevaCategoria()
	{
		$retorno = array();
		$categoriaImagenNombre = "";
		$extension = "";
		$separador = "";
		$id_categoria = $_POST['inputModalHidden-id'];
		$idPadreCategoria = $_POST['inputModalHidden'];
		$nombreCategoria = $_POST['inputSubcategoriaModal'];
		$imagenCategoria = $_FILES['imagenSubcategoria'];
		//Pregunta el estado del padre
		$obtenerEstadoDelPadre = $this->categorias->obtenerEstadoPadre($idPadreCategoria);
		//escapando las variables con php htmlspecialchars
		$id_categoria = htmlspecialchars($id_categoria);
		$idPadreCategoria = htmlspecialchars($idPadreCategoria);
		$nombreCategoria = htmlspecialchars($nombreCategoria);
		//Validacion de post, si este viene vacio no recibe la imagen o archivo como parametro por ende no se crea la variable.
		if (!isset($_FILES['imagenSubcategoria'])) {
			//Nombre de la imagen, recibe como parametro el nombre de la subcategoria.
			$categoriaImagenNombre = "";
			$extension = "";
		} else {
			if ($imagenCategoria['size'] > 0) {
				if ($idPadreCategoria == 0) {
					$categoriaImagenNombre = "imagen_{$nombreCategoria}_sub_categoria_" . date('Ymd');
				} else {
					$categoriaImagenNombre = "imagen_{$nombreCategoria}_categoria_" . date('Ymd');
				}
				$separador = ".";
				$extension = pathinfo($imagenCategoria['name'], PATHINFO_EXTENSION);
				if ($extension == 'jpeg') {
					$extension = 'jpg';
				}
			}
			$this->subirArchivo($imagenCategoria, $categoriaImagenNombre, 'imagenes/categoria', ['publica' => 0]);
		}
		//Si el id de la categoria padre es 0 crea una nueva categoria con estado Activo
		if ($idPadreCategoria == 0) {
			$retorno = [
				'nombre' =>	$nombreCategoria,
				'estado' => 6,
				'cat_padre' => $idPadreCategoria
			];
			//Si la categoria pertenece a un id padre este busca el estado del id padre y crea un categoria con el estado del padre
		} else {
			$retorno = [
				'nombre' =>	$nombreCategoria,
				'estado' => $obtenerEstadoDelPadre['estado'],
				'cat_padre' => $idPadreCategoria
			];
		}

		if (!empty($id_categoria)) {
			$retorno['id'] = $id_categoria;
		}
		if ($imagenCategoria['size'] > 0) {
			$retorno['imagen'] = $categoriaImagenNombre . $separador . $extension;
		}
		$this->categorias->guardar($retorno);
		$this->redir('Categoria', 'index');
	}
	public function darBaja($parametros = [])
	{
		if (!empty($parametros[0])) {
			$this->darBajaPrivado(array(array('id' => $parametros[0])));
			$this->redir('Categoria', 'index');
		} else {
			echo '<script>
			alert("Error,no se puede acceder a esta parte del sistema");
			</script>';
		}
	}


	public function darAltaCategoria($parametros = [])
	{
		if (!empty($parametros[0])) {
			$padres = $this->obtenerCategoriaPadre($parametros[0]);
			$estadoPadre = $this->obtenerEstadoCategoriaPadre($parametros[0]);
			if (!empty($padres)) {
				if ($estadoPadre) {
					$this->productos->darDeAltaProductosPorCategoria($parametros[0]);
					$this->categorias->darDeAlta($parametros[0]);
					$this->redir('Categoria', 'index');
				} else {
					$padreDatos = json_encode($padres);
					echo '<script>
					activarPadre= confirm("¿Desea activar la categoria padre?");
							if(activarPadre === true){
								fetch("'.RUTA_URL.'/Categoria/activarPadres",{
								method:"POST",
								body:JSON.stringify({
									categoria:'.$parametros[0].',
									subcategorias:'.$padreDatos.'
								})
							})
							.then(respuesta=>respuesta.json)
							.catch(err => console.log(err))
							setTimeout(() => {
								window.location = "' . RUTA_URL . '/Categoria/index";
							  }, 500);
							}else{
								alert("Debe activar el padre de la subcategoria");
								window.location = "' . RUTA_URL . '/Categoria/index";
							}
					</script>';
				}
			} else {
				$this->productos->darDeAltaProductosPorCategoria($parametros[0]);
				$this->categorias->darDeAlta($parametros[0]);
				$this->redir('Categoria', 'index');
			}
		} else {
			echo '<script>
			alert("Error,no se puede acceder a esta parte del sistema");
			</script>';
		}
	}

	public function activarPadres($fetchRespuesta)
	{
		// Decodifica los datos JSON que vienen en la solicitud y los convierte en un array asociativo
		$fetchRespuesta = json_decode(file_get_contents('php://input'), true);
		$retorno = $this->recorrerSubcategoriasRecursivamente($fetchRespuesta);
		// Recorre el array $retorno que contiene los resultados procesados
		foreach ($retorno as $value) {
			$this->productos->darDeAltaProductosPorCategoria($value);
			$this->categorias->darDeAlta($value);
		}
	}

	private function recorrerSubcategoriasRecursivamente($array)
	{
		$resultado = [];
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				// Si el elemento es un array, llamamos a la función de forma recursiva
				$resultado = array_merge($resultado, $this->recorrerSubcategoriasRecursivamente($value));
			} else {
				// Si el elemento no es un array, añadimos su valor al resultado
				$resultado[$key] = $value;
			}
		}
		return $resultado;
	}

	//Función para obtener la jerarquía de categorías padre de una categoría dada.
	private function obtenerCategoriaPadre($categoria)
	{
		$retorno = array();
		$datoCategoriaActual = $this->categorias->obtenerPorId($categoria);
		// Obtener los productos asociados a la categoría actual.
		$datosCategoriaActual['productos'] = $this->productos->obtenerProductosPorCategoria($datoCategoriaActual['id']);
		// Comprobar si la categoría actual tiene una categoría padre.
		if ($datoCategoriaActual["cat_padre"] != 0) {
			// Si tiene una categoría padre, agregamos su ID al array de retorno.
			$retorno[] = $datoCategoriaActual["cat_padre"];
			 // Luego, llamamos recursivamente a la función para obtener la jerarquía de categorías del padre.
			$padre = $this->obtenerCategoriaPadre($datoCategoriaActual["cat_padre"]);
			if (!empty($padre)) {
				// Si se encontraron categorías padre, las agregamos al array de retorno.
				array_push($retorno, $padre);
			}
		} else {
			// Si la categoría actual no tiene una categoría padre, establecemos el retorno como false.
			$retorno = false;
		}
		return $retorno;
	}

	//Función para obtener el estado de la categoría padre de una categoría dada.
	private function obtenerEstadoCategoriaPadre($id)
	{
		//Inicializar el valor de retorno como falso.
		$retorno = false;
		$obtenerDatosCategorias = $this->categorias->obtenerPorId($id);
		// Obtener los datos de la categoría padre.
		$obtenerPadre = $this->categorias->obtenerPorId($obtenerDatosCategorias['cat_padre']);
		// Comprobar si el estado de la categoría padre es igual a 0 (estado desactivado).
		if ($obtenerPadre['estado'] == 0) {
			$retorno = true;
		}
		return $retorno;
	}

	//Método privado para dar de baja categorías y sus productos de forma recursiva.
	private function darBajaPrivado($categorias)
	{
		//Iterar sobre cada categoría en la lista de categorías.
		foreach ($categorias as $categoria) {
		//Dar de baja los productos asociados a la categoría actual.
			$producto = $this->productos->darDeBajaProductosPorCategoria($categoria['id']);
			if ($producto) {
				//Si se dieron de baja productos, procedemos a dar de baja la categoría.
				// Dar de baja la categoría actual.
				$categoriaBaja = $this->categorias->darDeBaja($categoria['id']);
				if ($categoriaBaja) {
					//Si se dio de baja la categoría actual, obtenemos sus subcategorías.
					$subCategorias = $this->categorias->listarSubCategoria($categoria['id']);
					if (!empty($subCategorias)) {
						//Si existen subcategorías, llamamos de forma recursiva a este método para darles de baja.
						$this->darBajaPrivado($subCategorias);
					}
				}
			}
		}
	}
	
}
