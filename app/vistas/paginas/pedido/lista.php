<?php //echo json_encode($datos)
?>

<main id="container">

    <h1 class="mt-3">Listado de pedidos</h1>
    <form action="">
        <div class="card">
            <div class="table-responsive-xl">
                <table id="pedidosTabla" name= "pedidosTabla"class="table table-bordered  table-hover align-middle" style="width:100%;">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Pedido por</th>
                            <th scope="col">Detalles</th>
                            <th scope="col">Estados</th>
                            <th scope="col">Fecha de Solicitud</th>
                            <th scope="col">Observación</th>
                            <th scope="col">Opciones</th>
                        </tr>
                    </thead>
                    <tbody id="tblPedidos" name="tblPedidos">
                        <?php foreach ($datos as $valor) {
                            if(empty($valor['id_orden_del_dia']))
                            {
                                echo '<tr>
                                <input type="hidden" name="tblPedidosId" id="tblPedidosId" value ="'.$valor['id'].'">
                                <th scope="col">'.$valor['id'].'</th>
                                <th scope="col">'.$valor['nombreUsuario'].'</th>
                                <th scope="col">';
                                
                                foreach ($valor['detalles'] as $productos) { 
                                     echo $productos.'<br>';
                                    }
                                echo '</th>
                                <th scope="col">'.$valor['fecha'].'</th>
                                <th scope="col">';
                                                if (!isset($valor['observacion'])) {
                                                    echo '';
                                                } else {
                                                    echo $valor['observacion'];
                                                }
                                echo '</th>';
                                if ($valor['estado'] == 1 && empty($valor['id_orden_del_dia'])) {
                                    echo '<th scope="col"><button type="button" onclick="traerLasOrdenesDia('. $valor['id'] .')" class="btn btn-success btn-sm mt-1" data-toggle="modal" data-target="#modalOrdenDia">Seleccionar Orden del dia</button></th>';
                                }else{
                                    echo '<th scope="col"><button type="button" onclick="traerLasOrdenesDia('. $valor['id'] .')" class="btn btn-success btn-sm mt-1" data-toggle="modal" data-target="#modalOrdenDia">Modificar Orden del dia</button></th>';
                                    }
                                echo '</tr>';
                            }else{
                                echo '<tr class="bg-verdeBienClarito">
                                    <input type="hidden" name="tblPedidosId" id="tblPedidosId" value ="'.$valor['id'].'">
                                    <th scope="col" class="text-white-border">'.$valor['id'].'</th>
                                    <th scope="col" class="text-white-border">'.$valor['nombreUsuario'].'</th>
                                    <th scope="col" class="text-white-border">';
                                
                                foreach ($valor['detalles'] as $productos) {
                                        echo $productos.'<br>';
                                }
                                echo '</th>
                                <th scope="col"  class="text-white-border">'.$valor['fecha'].'</th>
                                <th scope="col"  class="text-white-border">';
                                                if (!isset($valor['observacion'])) {
                                                    echo '';
                                                } else {
                                                    echo $valor['observacion'];
                                                }
                                echo '</th>';
                                if ($valor['estado'] == 1 && empty($valor['id_orden_del_dia'])) {
                                    echo '<th scope="col"><button type="button" onclick="traerLasOrdenesDia('. $valor['id'] .')" class="btn btn-success btn-sm mt-1" data-toggle="modal" data-target="#modalOrdenDia">Seleccionar Orden del dia</button></th>';
                                }else{
                                    echo '<th scope="col"><button type="button" onclick="traerLasOrdenesDia('. $valor['id'] .')" class="btn btn-warning btn-sm mt-1" data-toggle="modal" data-target="#modalOrdenDia">Modificar Orden del dia</button></th>';
                                    }
                                echo '</tr>';
                            }
                            } ?>
                    </tbody>
                    </tbody>
                </table>
            </div>
        </div>

    </form>
</main>

<div class="modal fade" id="modalOrdenDia" tabindex="-1" data-backdrop="static" data-keyboard="false" aria-labelledby="labelProductos" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="labelProductos">Selecion de fecha de orden del dia</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!--  inputs  -->
                <form action="#" method="POST" class="mx-auto" id="formOrdenDia" name="formOrdenDia" autocomplete="off" enctype="multipart/form-data">
                    <input type="hidden" name="inputHiddenId" id="inputHiddenId" value="">
                    <div class="form-group" id = "contenedorSelect">
                        <label id="stockLabel" for="stockLabel">Fecha de la orden del dia</label>
                        <small id="stockMutedText" class="form-text text-muted">Ingrese la orden del dia.</small>
                    </div>
                    <div id="modal-footer-buttons" class="modal-footer col-12">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" style="height:52px;">Cerrar</button>
                        <button id="botonEnviarOrden" type="submit" class="btn btn-primary btn-sm" style="height:52px;">Confirmar</button>
                    </div>
            </div>
        </div>
        </form>
    </div>