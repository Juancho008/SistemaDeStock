$(document).ready(function () {
  $("#productoTabla").DataTable({
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json",
    },
    order: [[2, "desc"]],
    lengthMenu: [50, 100, 150, 200],
  });
  $("#inputStock").on("input", function () {
    // Reemplaza cualquier carácter que no sea número, coma o punto con una cadena vacía
    this.value = this.value.replace(/[^0-9,.]/g, "");
    let medida = document.getElementById("medidaSelect").value;
    if (medida == 2) {
      if ((this.value.match(/,/g) || []).length > 1) {
        this.value =
          this.value.slice(0, this.value.lastIndexOf(",")) +
          this.value.slice(this.value.lastIndexOf(",") + 1);
      }
    }
  });
  $("#medidaSelect").select2({
    dropdownParent: $("#modalProducto .modal-body"),
    language: {
      noResults: function () {
        return "No hay resultado";
      },
      searching: function () {
        return "Buscando..";
      },
    },
  });
  $("#productoSelect").select2({
    dropdownParent: $("#modalProducto .modal-body"),
    language: {
      noResults: function () {
        return "No hay resultado";
      },
      searching: function () {
        return "Buscando..";
      },
    },
  });
  $("#selectPermisos").select2({
    dropdownParent: $("#modalProducto .modal-body"),
    language: {
      noResults: function () {
        return "No hay resultado";
      },
      searching: function () {
        return "Buscando..";
      },
    },
  });
  
  document.getElementById("imprimirActa").addEventListener("click", function(e) {
    const confirmar = confirm("¿Está seguro que desea imprimir el acta de baja?");
    if (!confirmar) {
        e.preventDefault(); // Evita abrir el enlace
    }
  });
});




function editarProducto(objeto) {
  if (obs && obs.style.display === "block") {
    obs.style.display = "none";
  }

  if (!document.getElementById("formProducto")) return;

  // --- Acción y etiquetas principales ---
  document.getElementById(
    "formProducto"
  ).action = `${objeto.url}/Producto/editarProducto`;
  document.getElementById("botonCrearProducto").innerHTML = "Editar Producto";
  document.getElementById("labelProductos").innerHTML = "Editar Producto";
  document.getElementById("stockLabel").innerHTML = "Cantidad";

  // --- Mostrar elementos necesarios ---
  const mostrar = [
    "productoSelect",
    "selectCategoriaLabel",
    "selectPermisosLabel",
    "selectPermisos",
    "mutedText",
    "labelImagen",
    "productoImagen",
    "labelImagenProducto",
    "codigoBarraLabel",
    "inputCodigoDeBarra",
    "codigoDeBarraHelp",
    "nombreLabel",
    "inputNombreProducto",
    "productoHelp",
  ];
  mostrar.forEach((id) => mostrarElemento(id));

  // --- Ocultar elementos innecesarios ---
  const ocultar = [
    "inputStock",
    "stockMutedText",
    "valorMutedText",
    "valorMinimo",
    "valorMinimoLabel",
    "stockLabel",
    "selectProductoLabel",
    "imagenCargadaProducto",
  ];
  ocultar.forEach((id) => ocultarElemento(id));

  // --- Asignar valores ---
  document.getElementById("inputHiddenId").value = objeto.id;
  document.getElementById("inputCodigoDeBarra").value = objeto.codigo_barra;
  document.getElementById("inputNombreProducto").value = objeto.nombre;

  // --- Asignar categoría y permisos con Select2 ---
  $("#productoSelect").val(objeto.categoria.id).trigger("change");
  aplicarSelect2("#productoSelect");
  aplicarSelect2("#selectPermisos");

  const selectPermisos = document.getElementById("selectPermisos");
  selectPermisos.selectedIndex = -1;
  objeto.oficinas.forEach((oficina) => {
    [...selectPermisos.options].forEach((option) => {
      if (option.value === oficina.id_oficina) {
        option.selected = true;
      }
    });
  });

  // --- Remover botones adicionales si existen ---
  eliminarSiExiste("sumarStock");
  eliminarSiExiste("sumarBulto");
  eliminarSiExiste("editarDañado");
  // --- Ocultar medidaSelect si está visible ---
  if ($("#medidaSelect").css("display") === "block") {
    $("#medidaSelect").select2("destroy");
    document.getElementById("medidaSelect").style.display = "none";
  }

  // --- Funciones auxiliares ---
  function mostrarElemento(id) {
    const el = document.getElementById(id);
    if (el) el.style.display = "block";
  }

  function ocultarElemento(id) {
    const el = document.getElementById(id);
    if (el) el.style.display = "none";
  }

  function eliminarSiExiste(id) {
    const el = document.getElementById(id);
    if (el) el.remove();
  }

  function aplicarSelect2(selector) {
    $(selector).select2({
      dropdownParent: $("#modalProducto .modal-body"),
      language: {
        noResults: () => "No hay resultado",
        searching: () => "Buscando..",
      },
    });
  }
}

function modificarStock(objeto) {
  if (obs && obs.style.display === "block") {
    obs.style.display = "none";
  }

  document.getElementById("formProducto").action =
    RUTA_URL + "/Producto/modificarStock";
  document.getElementById("botonCrearProducto").innerHTML = "Editar";
  document.getElementById("labelProductos").innerHTML = "Edicion Stock";
  document.getElementById("stockLabel").innerHTML = "Cantidad";
  function eliminarSiExiste(id) {
    const el = document.getElementById(id);
    if (el) el.remove();
  }

  if (
    objeto.stock &&
    objeto.stock.length > 0 &&
    objeto.stock[0].medida &&
    objeto.stock[0].medida.id
  ) {
    $("#medidaSelect").val(objeto.stock[0].medida.id).trigger("change");
  } else {
    console.warn("No se encontró la medida dentro del stock:", objeto);
    $("#medidaSelect").val(null).trigger("change");
  }

  document.getElementById("inputStock").style.display = "block";
  document.getElementById("stockMutedText").style.display = "block";
  document.getElementById("stockLabel").style.display = "inline-block";

  let stockDisponible = objeto.stock.filter((item) => item.estado == 0); // o item.estado === "0" si es string
  if (stockDisponible.length > 0) {
    document.getElementById("inputStock").value = stockDisponible[0].cantidad;
    inputHiddenStockId = document.getElementById("inputModalHidden-id").value =
      stockDisponible[0].id_productos;
    inputHiddenId = document.getElementById("inputHiddenId").value =
      stockDisponible[0].id;
  } else {
    console.warn("No hay stock con estado 'DISPONIBLE'.");
    document.getElementById("inputStock").value = "";
  }
  document.getElementById("selectProductoLabel").style.display = "block";
  document.getElementById("medidaSelect").style.display = "block";
  document.getElementById("valorMinimo").style.display = "block";
  document.getElementById("valorMinimoLabel").style.display = "block";
  document.getElementById("valorMutedText").style.display = "block";

  // Ocultar elementos que no se necesitan
  let elementosOcultar = [
    "imagenCargadaProducto",
    "productoSelect",
    "selectCategoriaLabel",
    "selectPermisosLabel",
    "mutedText",
    "labelImagen",
    "productoImagen",
    "labelImagenProducto",
    "codigoBarraLabel",
    "inputCodigoDeBarra",
    "codigoDeBarraHelp",
    "nombreLabel",
    "inputNombreProducto",
    "productoHelp",
    "stockMutedText",
  ];
  elementosOcultar.forEach((id) => {
    let elem = document.getElementById(id);
    if (elem) elem.style.display = "none";
  });

  let existeBotonRetirar = document.getElementById("retirarBoton");
  let valorMinimo = document.getElementById("valorMinimo");
  valorMinimo.value = objeto.valor_minimo;

  // Destruir select2 si existe
  if ($("#selectPermisos").css("display") == "block") {
    document.getElementById("selectPermisos").style.display = "none";
    $("#productoSelect").select2("destroy");
    $("#selectPermisos").select2("destroy");
  }

  eliminarSiExiste("editarDañado");
  // Volver a inicializar el select
  $("#medidaSelect").select2({
    dropdownParent: $("#modalProducto .modal-body"),
    language: {
      noResults: () => "No hay resultado",
      searching: () => "Buscando..",
    },
  });

  // Botones adicionales
  let modalButtons = document.getElementById("modal-footer-buttons");

  if (!document.getElementById("sumarStock")) {
    let sumarStockButton = document.createElement("button");
    sumarStockButton.innerHTML = "Agregar por Unidad";
    sumarStockButton.id = "sumarStock";
    sumarStockButton.classList.add("btn", "btn-success", "btn-sm");
    sumarStockButton.style.height = "52px";
    modalButtons.appendChild(sumarStockButton);
    sumarStockButton.addEventListener("click", () => {
      document.getElementById("formProducto").action =
        RUTA_URL + "/Producto/sumarStockActual";
    });
  }

  if (!document.getElementById("sumarBulto")) {
    let sumarBultoStockButton = document.createElement("button");
    sumarBultoStockButton.innerHTML = "Agregar por Paquete";
    sumarBultoStockButton.id = "sumarBulto";
    sumarBultoStockButton.classList.add("btn", "btn-danger", "btn-sm");
    sumarBultoStockButton.style.height = "52px";
    modalButtons.appendChild(sumarBultoStockButton);
    sumarBultoStockButton.addEventListener("click", () => {
      document.getElementById("formProducto").action =
        RUTA_URL + "/Producto/sumarStockBultoActual";
    });
  }

  if (existeBotonRetirar) {
    existeBotonRetirar.remove();
  }
}

function retirarStock(objeto) {
  if (obs && obs.style.display === "block") {
    obs.style.display = "none";
  }

  const form = document.getElementById("formProducto");
  const footer = document.getElementById("modal-footer-buttons");

  // Cambiar acción del formulario y etiquetas
  form.action = RUTA_URL + "/Producto/retirarStockPorUnidad";
  document.getElementById("botonCrearProducto").innerHTML = "Retirar Unidad";
  document.getElementById("labelProductos").innerHTML = "Retirar Stock";
  document.getElementById("stockLabel").innerHTML = "Cantidad a retirar";

  // Mostrar solo los campos necesarios
  const mostrar = ["inputStock", "stockMutedText", "stockLabel"];
  mostrar.forEach((id) => {
    const el = document.getElementById(id);
    el.style.display = id === "stockLabel" ? "inline-block" : "block";
    if (id === "inputStock") el.value = "";
  });

  // Ocultar campos que no se usan
  const ocultar = [
    "selectProductoLabel",
    "imagenCargadaProducto",
    "productoSelect",
    "valorMinimo",
    "valorMinimoLabel",
    "valorMutedText",
    "selectCategoriaLabel",
    "selectPermisosLabel",
    "mutedText",
    "labelImagen",
    "productoImagen",
    "labelImagenProducto",
    "codigoBarraLabel",
    "inputCodigoDeBarra",
    "codigoDeBarraHelp",
    "nombreLabel",
    "inputNombreProducto",
    "productoHelp",
    "stockMutedText",
  ];
  ocultar.forEach((id) => (document.getElementById(id).style.display = "none"));

  // ===>> AQUÍ SE AGREGA LA LÓGICA PARA stockDisponible
  let stockDisponible = [];
  if (objeto.stock && Array.isArray(objeto.stock)) {
    stockDisponible = objeto.stock.filter((item) => item.estado == 0);
  } else if (objeto.stock && objeto.stock.estado == 0) {
    stockDisponible = [objeto.stock];
  }

  if (stockDisponible.length > 0) {
    document.getElementById("inputHiddenId").value = stockDisponible[0].id;
    document.getElementById("inputModalHidden-id").value =
      stockDisponible[0].id_productos;
  } else {
    console.warn("No hay stock con estado 'DISPONIBLE'.");
    document.getElementById("inputHiddenId").value = "";
    document.getElementById("inputModalHidden-id").value = "";
  }

  // Eliminar botones si existen
  const eliminarSiExiste = (id) => {
    const el = document.getElementById(id);
    if (el) el.remove();
  };
  eliminarSiExiste("sumarStock");
  eliminarSiExiste("sumarBulto");
  eliminarSiExiste("editarDañado");
  // Crear botón para retirar por paquete si no existe
  if (!document.getElementById("retirarBoton")) {
    const retirarBtn = document.createElement("button");
    retirarBtn.id = "retirarBoton";
    retirarBtn.innerHTML = "Retirar por paquete";
    retirarBtn.classList.add("btn", "btn-success", "btn-sm");
    retirarBtn.style.height = "52px";
    retirarBtn.addEventListener("click", () => {
      form.action = RUTA_URL + "/Producto/retirarStockPorBulto";
    });
    footer.appendChild(retirarBtn);
  }

  const destruirSelect2 = (id) => {
    const $el = $("#" + id);
    if ($el.hasClass("select2-hidden-accessible")) {
      $el.select2("destroy");
    }
    $el.hide();
  };
  
  // Usar la función con tus elementos
  destruirSelect2("selectPermisos");
  destruirSelect2("productoSelect");
  destruirSelect2("medidaSelect");
}

function desactivarProducto(objeto) {
  resultado = window.confirm("¿Desea desactivar el Producto?");
  if (resultado === true) {
    window.location = "desactivarProducto/" + objeto.id;
  }
}
function activarProducto(objeto) {
  resultado = window.confirm("¿Desea activar el Producto?");
  if (resultado === true) {
    window.location = "activarProducto/" + objeto.id;
  }
}

function modalProducto(RUTA_URL) {
  if (obs && obs.style.display === "block") {
    obs.style.display = "none";
  }
  // === Formulario y etiquetas ===
  document.getElementById("formProducto").action =
    RUTA_URL + "/Producto/crearNuevoProducto";
  document.getElementById("botonCrearProducto").innerText = "Crear Producto";
  document.getElementById("labelProductos").innerText = "Crear Producto";
  document.getElementById("nombreLabel").innerText = "Nombre Producto";

  // === Reseteo de inputs ===
  document.getElementById("inputModalProductoHidden").value = "0";
  document.getElementById("inputStock").value = "";
  document.getElementById("inputCodigoDeBarra").value = "";
  document.getElementById("inputNombreProducto").value = "";
  document.getElementById("valorMinimo").value = "";
  $("#btn-file input").val("");

  // === Ocultar elementos ===
  document.getElementById("imagenCargadaProducto").style.display = "none";

  // === Mostrar elementos relevantes ===
  const elementosAMostrar = [
    "inputStock",
    "stockMutedText",
    "valorMinimo",
    "valorMinimoLabel",
    "valorMutedText",
    "stockLabel",
    "selectProductoLabel",
    "medidaSelect",
    "productoSelect",
    "selectCategoriaLabel",
    "selectPermisosLabel",
    "selectPermisos",
    "mutedText",
    "labelImagen",
    "productoImagen",
    "labelImagenProducto",
    "codigoBarraLabel",
    "inputCodigoDeBarra",
    "codigoDeBarraHelp",
    "nombreLabel",
    "inputNombreProducto",
    "productoHelp",
    "stockMutedText",
  ];

  elementosAMostrar.forEach((id) => {
    const el = document.getElementById(id);
    if (el) el.style.display = "block";
  });

  // === Estado de clase de botón ===
  const contenedor = document.getElementById("btn-file");
  if (contenedor.classList.contains("btn-datos-activos")) {
    $("#btn-file")
      .removeClass("btn-datos-activos")
      .addClass("btn-datos-desactivados");
  }

  // === Resetear selects ===
  $("#selectPermisos, #productoSelect, #medidaSelect")
    .val(null)
    .trigger("change");

  // === Inicializar select2 ===
  const selects = ["#productoSelect", "#selectPermisos", "#medidaSelect"];
  selects.forEach((selector) => {
    $(selector).select2({
      dropdownParent: $("#modalProducto .modal-body"),
      language: {
        noResults: () => "No hay resultado",
        searching: () => "Buscando..",
      },
    });
  });

  // === Eliminar botones si existen ===
  ["sumarStock", "sumarBulto","editarDañado","retirarBoton"].forEach((id) => {
    const boton = document.getElementById(id);
    if (boton) boton.remove();
  });
}

function inputFileImagen(parametro) {
  let contenedorInput = document.getElementById("btn-file");
  let inputFileValor = contenedorInput.querySelector("input");
  inputFileValor.addEventListener("change", (event) => {
    if ((parametro = "activar")) {
      $("#btn-file").removeClass("btn-datos-desactivados");
      $("#btn-file").addClass("btn-datos-activos");
    }
  });
}

function reportarStock(objeto) {
  const form = document.getElementById("formProducto");
  const footer = document.getElementById("modal-footer-buttons");
  const obs = document.getElementById("obs");
  if (!form || !footer) {
    console.error("Formulario o pie de modal no encontrado");
    return;
  }

  // Establecer acción por defecto
  form.action = RUTA_URL + "/Producto/reportarStock";

  // Editar etiquetas
  document.getElementById("botonCrearProducto").innerHTML =
    "Cargar Stock Dañado";
  document.getElementById("labelProductos").innerHTML =
    "Reporte de Stock Dañado";

  // Crear botón "Editar Stock Dañado" solo si no existe
  if (!document.getElementById("editarDañado")) {
    const editarButton = document.createElement("button");
    editarButton.id = "editarDañado";
    editarButton.type = "submit"; // Hacer que envíe el formulario
    editarButton.innerHTML = "Editar Stock Dañado";
    editarButton.classList.add("btn", "btn-success", "btn-sm");
    editarButton.style.height = "52px";
    editarButton.addEventListener("click", () => {
      form.action = RUTA_URL + "/Producto/editarStockDanado";
    });
    footer.appendChild(editarButton);
  }

  // Limpiar valores y mostrar inputs relevantes
  document.getElementById("inputStock").value = "";
  document.getElementById("inputStock").style.display = "block";
  document.getElementById("stockMutedText").style.display = "block";
  document.getElementById("stockLabel").style.display = "inline-block";
  document.getElementById("stockLabel").innerHTML = "Agregar Stock Dañado";

  // Ocultar elementos innecesarios
  const elementosOcultar = [
    "valorMinimo",
    "valorMinimoLabel",
    "valorMutedText",
    "selectProductoLabel",
    "imagenCargadaProducto",
    "productoSelect",
    "selectCategoriaLabel",
    "selectPermisosLabel",
    "mutedText",
    "labelImagen",
    "productoImagen",
    "labelImagenProducto",
    "codigoBarraLabel",
    "inputCodigoDeBarra",
    "codigoDeBarraHelp",
    "nombreLabel",
    "inputNombreProducto",
    "productoHelp",
  ];

  elementosOcultar.forEach((id) => {
    const el = document.getElementById(id);
    if (el) el.style.display = "none";
  });

  // Obtener valores del objeto
  let stockDisponible = objeto.stock.filter((item) => item.estado == 1);

  if (obs && obs.style.display === "none") {
    obs.style.display = "block";
  }
  const inputObs = document.getElementsByName("observaciones")[0];
  if (inputObs) {
    if(inputObs.value < 0){
      inputObs.value = stockDisponible[0].observacion;
    }else{
      inputObs.value = '';
    }
  }
console.log(objeto);
  if (stockDisponible.length > 0) {
    
    document.getElementById("inputStock").value = stockDisponible[0].cantidad;
    document.getElementById("inputModalHidden-id").value = stockDisponible[0].id_productos;
    document.getElementById("inputHiddenId").value = stockDisponible[0].id;
  } else {
    console.warn("No hay stock con estado 'DISPONIBLE'.");
    document.getElementById("inputStock").value = "";
    document.getElementById("inputModalHidden-id").value = objeto.id;
  }

  // Eliminar botones adicionales si existen
  ["sumarStock", "sumarBulto", "retirarBoton"].forEach((id) => {
    const btn = document.getElementById(id);
    if (btn) btn.remove();
  });

  // Destruir select2 si están activos
  if ($("#selectPermisos").css("display") === "block") {
    document.getElementById("selectPermisos").style.display = "none";
    $("#productoSelect").select2("destroy");
    $("#selectPermisos").select2("destroy");
  }

  if ($("#medidaSelect").css("display") === "block") {
    document.getElementById("medidaSelect").style.display = "none";
    $("#medidaSelect").select2("destroy");
  }
}
