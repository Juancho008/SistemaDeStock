$(document).ready(function () {
  $("#ordenDelDiaTabla").DataTable({
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json",
    },
    order: [[2, "desc"]],
    lengthMenu: [50, 100, 150, 200],
  });
});

var tr = document.getElementById("datos-modal")
var aceptado = 3
var rechazado = 4
var pendiente = 1

function enviarOrdenDia(objeto) {
  resultado = window.confirm("¿Desea Enviar la orden del dia?");
  if (resultado === true) {
    window.location = "enviarOrdenDelDia/" + objeto.id;
  }
  
}

function eliminarOrdenDia(objeto) {
  resultado = window.confirm("¿Desea Eliminar la orden del dia?");
  if (resultado === true) {
    window.location = "eliminarOrdenDelDia/" + objeto.id;
  } 
}
function entregarDia(objeto) {
  resultado = window.confirm("¿Ya se entregaron los pedidos de la orden del dia?");
  if (resultado === true) {
    window.location = "entregarOrdenDelDia/" + objeto.id;
  } 
}

function editarFecha (objeto)
{
  document.getElementById("nuevaOrdenDelDia").action =
  RUTA_URL + "/OrdenDelDia/modificarOrden";
  document.getElementById("botonCrearOrdenDelDia").innerHTML = "Editar";
  document.getElementById("inputFechaOrden").value= objeto.fecha;
  document.getElementById("idOcultoFecha").value= objeto.id;
}

function verOrdenPdf(objeto)
{
  window.open("imprimirOrdenDia/" + objeto.id,'_blank');
}

function imprimirOrdenDia(objeto)
{
  resultado = window.confirm("¿Desea imprimir la orden del dia?");
  if (resultado === true) {
    window.open("imprimirOrdenDia/" + objeto.id,'_blank');
  } 
}

function buscarOrdenesPendientes(){
  window.location = "obtenerOrdenesPendientes";
}

function buscarOrdenesCerradas(){
  window.location = "obtenerOrdenesCerradas";
}

function revisarOrden(id){
let contador = 0;
let estado = ''
  $.ajax({
    data: {id: id},
    //data: id,
    url: RUTA_URL + '/OrdenDelDia/obtenerPedidos/',
    type: 'post',
    beforeSend: function() {},
    success: function(response) {
        var datos = JSON.parse(response);     
        //console.log('respuesta', response)  
        var detallesBody = document.getElementById('detallesBody');
        // Limpia el contenido anterior de la tabla
        detallesBody.innerHTML = '';

        for (var key in datos) {
          contador++
          if (datos.hasOwnProperty(key)) {
              var itemArray = datos[key]; // Obtiene el array correspondiente a la clave       
              var row = detallesBody.insertRow(); // Inserta una nueva fila
              var cellIndex = row.insertCell(0); // Inserta celda para el índice
              var cellOficina = row.insertCell(1); // Inserta celda para el índice
              var cellPedido = row.insertCell(2); // Inserta celda para el pedido
              var cellOpciones = row.insertCell(3); // Inserta celda para las opciones
              
              for (var i = 0; i < itemArray.length; i++) {
                
                  row.setAttribute("id", "fila_"+key);
                  row.setAttribute("id", "fila_"+key);
                  var item = itemArray[i];     

                  switch(item.estado) {
                    case '1':
                    case '2':
                      estado = ''
                      break;
                    case '3':
                      estado = '#CAF7CE'
                      break;
                    case '4':
                      estado = '#F6CBCB'
                      break;
                    default:
                  }
                  row.style.backgroundColor = estado
                  cellPedido.textContent += item.nombreProducto + '(' + item.cantidad+') ';
                  
                  cellOpciones.innerHTML = '<button class="btn btn-success" onclick="aceptarPedido(' + key + ')">Aceptar</button> <button class="btn btn-danger" onclick="rechazarPedido(' + key + ')">Rechazar</button> <button class="btn btn-warning" data-toggle="modal" href="#modalModificarPedido" onclick="modificarPedido('+key+')">Modificar</button>'; 
              }
              cellOficina.textContent += item.nombreArea; 
              //cellIndex.textContent = contador
              cellIndex.innerHTML = contador + ' &emsp; <input type="checkbox" id="pedido_'+key+'" name="pedidos[]" value="'+key+'">';
          }
        }
      }
  });
}

function aceptarPedido(pedido){
  resultado = window.confirm("¿Aceptar pedido?");
  if (resultado === true) {
    $.ajax({
      data: {pedido: pedido},
      url: RUTA_URL + '/OrdenDelDia/aceptarPedido/',
      type: 'post',
      beforeSend: function() {},
      success: function(response) {
          if(response == 'true'){
            alert('Pedido aceptado correctamente.')
            var fila = document.getElementById('fila_'+pedido)
            fila.style.backgroundColor = '#A7F1AD';
          }else{
            alert('ERROR: No pudo aceptarse el pedido.')
          }
        }
    });
  }
}

function rechazarPedido(pedido){
  resultado = window.confirm("¿Rechazar pedido?");
  if (resultado === true) {
    $.ajax({
      data: {pedido: pedido},
      url: RUTA_URL + '/OrdenDelDia/rechazarPedido/',
      type: 'post',
      beforeSend: function() {},
      success: function(response) {
          if(response == 'true'){
            alert('Pedido rechazado correctamente.')
            var fila = document.getElementById('fila_'+pedido)
            fila.style.backgroundColor = '#EF9595';
          }else{
            alert('ERROR: No pudo rechazarse el pedido.')
          }
        }
    });
  }
}

function modificarPedido(pedido){
  let contador = 0;
  let estado = ''
    $.ajax({
      data: {id: pedido},
      //data: id,
      url: RUTA_URL + '/OrdenDelDia/obtenerDetallesPedidos/',
      type: 'post',
      beforeSend: function() {},
      success: function(response) {
          var datos = JSON.parse(response);      
          var detallesBody = document.getElementById('detallesPedidos');
          // Limpia el contenido anterior de la tabla
          detallesBody.innerHTML = '';
  
          for (var key in datos) {
            contador++
            if (datos.hasOwnProperty(key)) {
                var itemArray = datos[key]; // Obtiene el array correspondiente a la clave    
                console.log('datos', itemArray.nombreProducto);   
                var row = detallesBody.insertRow(); // Inserta una nueva fila
                var cellIndex = row.insertCell(0); // Inserta celda para el índice
                var cellPedido = row.insertCell(1); // Inserta celda para el índice
                var cellCantidad = row.insertCell(2); // Inserta celda para el pedido
                var cellOpciones = row.insertCell(3); // Inserta celda para las opciones
                
                cellPedido.textContent += itemArray.nombreProducto;
                cellCantidad.innerHTML += '<input type="number" name="cantidad" style="width:50px; text-align: center;" value="'+itemArray.cantidad+'">';
                cellOpciones.innerHTML = ' <button class="btn btn-warning" data-toggle="modal" href="#modalModificarPedido" onclick="modificarPedido('+key+')">Modificar</button>'; 
                cellIndex.innerHTML = contador;
            }
          }
        }
    });
}