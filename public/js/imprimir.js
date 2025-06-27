let print = () => {};
//Funcion que ayuda a imprimir la pantalla creando el header de un index, espera que le pases un parametro(o parte de una pagina) para construir
print = (elem, htmlPrint) => {
  let pantallaImprimir = window.open("", "PRINT");
  URL_COMPONENTES = elem + "/public/vendor/components";
  URL_JS = elem + "/public/js";
  URL_CSS = elem + "/public/css";
  URL_IMG = elem + "/public/img/";
  URL_VENDOR = elem + "/public/vendor";

  pantallaImprimir.document.write(
    `<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" href="` + URL_COMPONENTES + `/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="` + URL_CSS + `/colores.css">
    <link rel="stylesheet" href="` + URL_CSS + `/home.css">
    <link rel="stylesheet" href="` + URL_CSS + `/imprimir.css">

    <script src="` + URL_COMPONENTES + `/jquery/jquery.min.js"></script>
    <script src="` + URL_COMPONENTES + `/bootstrap/js/bootstrap.min.js"></script>
    <script src="` + URL_COMPONENTES + `/jqueryui/jquery-ui.js"></script>
    <script src="` + URL_JS + `/lib/popper/popper.min.js"></script>
    <script src="`+ URL_VENDOR +`/datatables/datatables/media/js/jquery.dataTables.min.js"></script>
    <script src="`+ URL_VENDOR +`/datatables/datatables/media/js/dataTables.bootstrap4.js"></script>
</head>
<div class="container">
    <div class="row row-cols-2 row-cols-lg-2 g-1 g-lg-2">
        <div class="col">
            <img src="`+URL_IMG+`logo.webp" style="width:470px; height:160px;">
        </div>
        <div class="col text-center" style="margin-top:4rem !important;">
            <h2>Reporte de Stock</h2>
        </div>
    </div>
</div>
<style>
.bg-oscuro{
    background-color: #343a40!important;
    color: white;
}
}
</style>`
  );
  pantallaImprimir.document.write(htmlPrint);
  pantallaImprimir.document.title = "Reporte de stock";

  pantallaImprimir.document.close();
  pantallaImprimir.focus();

  pantallaImprimir.onload = () => {
    pantallaImprimir.print();
    pantallaImprimir.close();
    pantallaImprimir.focus();

  };
  return true;
};

//Funcion que imprime la pantalla con la funcion print
ImprimirPantalla = (elem) => {
  let htmlPrint = document.querySelector("#imprimirTabla").innerHTML;
  print(elem, htmlPrint);
};

// cuando la pagina este lista modifica una tabla con la libreria datatables
$(document).ready(function(){
  $("#reporteStock").DataTable({
		language: {
			url: "//cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json",
		},
		order: [[2, "desc"]],
		lengthMenu: [15, 20, 25, 30],
	});
});