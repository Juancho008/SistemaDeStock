$(document).ready(function () {
	$("#catTabla").DataTable({
		language: {
			url: "//cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json",
		},
		order: [[2, "desc"]],
		lengthMenu: [50, 100, 150, 200],
	});
	setTimeout(() => {
		let contenedor = document.getElementById("catTabla_filter");
		let inputtest = contenedor.querySelector("input");
		inputtest.onkeyup = () => {
			if (inputtest.value) {
				$("#expandir_tabla").removeClass("collapsed");
				$("tr").addClass("show");
			} else {
				$("#expandir_tabla").addClass("collapsed");
				$("tr").removeClass("show");
			}
		};
	}, 1000);
	$("#inputSubcategoriaModal").on('input', function () { 
		this.value = this.value.replace(/[^0-9a-zA-ZñÑáéíóúÁÉÍÓÚ.()::,@ _-]/g,'');
	});
});

function darBaja(parametro)
{
	resultado = window.confirm('¿Desea dar de baja la categoria?');	
	if(resultado === true ){
		window.location = "darBaja/"+parametro;
	}else{
		location.reload();
	}
};
function darAlta(parametro)
{
	resultado = window.confirm('¿Desea dar de alta la categoria?');	
	if(resultado === true ){
		window.location = "darAltaCategoria/"+parametro;
	}else{
		location.reload();
	}
};
const cambiarColores = (elemento) => {
	if (elemento.classList.contains("btn-success")) {
		elemento.classList.remove("btn-success");
		elemento.classList.add("btn-danger");
	} else if (elemento.classList.contains("btn-danger")) {
		elemento.classList.add("btn-success");
		elemento.classList.remove("btn-danger");
	}
};
function modalSubcategoria(objeto) {
	document.getElementById("inputSubcategoriaModal").value = "";
	let contenedor = document.getElementById("btn-file");
	if (contenedor.classList.contains("btn-datos-activos")) {
		$("#btn-file").removeClass("btn-datos-activos");
		$("#btn-file").addClass("btn-datos-desactivados");
	}
	$("#btn-file input").val("");
	let tituloModal = (document.getElementById("labelCrearSubcategoria").innerHTML = "");
	Object.entries(objeto).forEach(() => {
		document.getElementById("labelCrearSubcategoria").innerHTML = tituloModal.replace("", objeto.nombre);
		document.getElementById("inputModalHidden").value = objeto.id;
		document.getElementById("subcategoriaLabel").innerHTML = "Nombre Subcategoria";
		document.getElementById("labelImagenSubcategoria").innerHTML = "Imagen Subcategoria";
		document.getElementById("subCategoriaHelp").innerHTML = "Debe colocar el nombre de la subcategoria.";
		document.getElementById("botonCrearCategoria").innerHTML = "Crear Subcategoria";
		document.getElementById("categoriaLabelFooter").style.cssText="display:none;";
	});
};
function modalCategoria() {
	let contenedor = document.getElementById("btn-file");
	if (contenedor.classList.contains("btn-datos-activos")) {
		$("#btn-file").removeClass("btn-datos-activos");
		$("#btn-file").addClass("btn-datos-desactivados");
	}
	$("#btn-file input").val("");
	document.getElementById("inputSubcategoriaModal").value = "";
	document.getElementById("inputModalHidden").value = "0";
	document.getElementById("labelCrearSubcategoria").innerHTML = "Crear Categoria";
	document.getElementById("subcategoriaLabel").innerHTML = "Nombre Categoria";
	document.getElementById("labelImagenSubcategoria").innerHTML = "Imagen Categoria";
	document.getElementById("subCategoriaHelp").innerHTML = "Debe colocar el nombre de la categoria.";
	document.getElementById("botonCrearCategoria").innerHTML = "Crear Categoria";
	document.getElementById("categoriaLabelFooter").style.cssText="display:none;"
};
function editarCategoria(objeto) {
	console.log(objeto);
	document.getElementById("inputModalHidden-id").value = "";
    let contenedor = document.getElementById("btn-file");
	if (contenedor.classList.contains("btn-datos-desactivados")) {
		$("#btn-file").removeClass("btn-datos-desactivados");
		$("#btn-file").addClass("btn-datos-activos");
	}
    document.getElementById("inputSubcategoriaModal").value ="";
	$("#btn-file input").val("");
	document.getElementById("labelCrearSubcategoria").innerHTML ="Editar Categoria";
	document.getElementById("subcategoriaLabel").innerHTML = "Nombre";
	document.getElementById("labelImagenSubcategoria").innerHTML = "Imagen";
	document.getElementById("subCategoriaHelp").innerHTML = "";
	document.getElementById("botonCrearCategoria").innerHTML = "Editar";
	document.getElementById("categoriaLabelFooter").style.cssText="display:show;"
	Object.entries(objeto).forEach(() => {
		document.getElementById("inputSubcategoriaModal").value = objeto.nombre;
		document.getElementById("inputModalHidden").value = objeto.cat_padre;
		document.getElementById("inputModalHidden-id").value = objeto.id;
		$("#inputSubcategoriaModal").on('input', function () { 
			this.value = this.value.replace(/[^0-9a-zA-ZñÑáéíóúÁÉÍÓÚ.()::,@ _-]/g,'');
		});
	});
};
function inputFileImagen(parametro) {
	let contenedorInput = document.getElementById("btn-file");
	let inputFileValor = contenedorInput.querySelector("input");
	inputFileValor.addEventListener("change", (event) => {
		if ((parametro = "activar")) {
			$("#btn-file").removeClass("btn-datos-desactivados");
			$("#btn-file").addClass("btn-datos-activos");
		}
	});
};
