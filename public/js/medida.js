function darBajaMedida(parametro)
{
	resultado = window.confirm('¿Desea dar de baja el tipo de medida?');	
	if(resultado === true ){
		window.location = "darBajaMedida/"+parametro.id;
	}else{
		location.reload();
	}
};
function darAltaMedida(parametro)
{
	resultado = window.confirm('¿Desea activar el tipo de medida?');	
	if(resultado === true ){
		window.location = "darAltaMedida/"+parametro.id;
	}else{
		location.reload();
	}
};
function editarMedida(objeto)
{
    if (document.getElementById("formMedida")) {
        document.getElementById("formMedida").action =
          objeto.url + "/Medida/editarMedida";

    document.getElementById("botonCrearMedida").innerHTML = "Terminar Edición";
    document.getElementById("labelMedida").innerHTML = "Editar Medida";

    let inputMedida = document.getElementById("inputNombreMedida");
    let inputCantidad = document.getElementById("inputCantidad");
    let inputIdMedida= document.getElementById("idMedida");

    inputIdMedida.value = objeto.id;
    inputMedida.value = objeto.descripcion;
    inputCantidad.value = objeto.cantidad;
    }
}