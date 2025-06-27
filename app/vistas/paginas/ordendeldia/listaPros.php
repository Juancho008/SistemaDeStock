<?php
$cont = 1;
// echo '<pre>';
// print_r($pedidos);
// die;
?>

<main id="container">
    <h1 class="mt-3">Lista de Ordenes del día</h1>
    <br>
    <div>
        <button type="button" onclick="buscarOrdenesPendientes()" class="btn btn-dark">Recibidos</button>
        <button type="button" onclick="buscarOrdenesCerradas()" class="btn btn-primary">Controlados</button>
    </div>
    <form action="">
        <br><br>
        <div class="card">
            <div class="table-responsive-xl">
                <table id="ordenDelDiaTabla" class="table table-bordered  table-hover align-middle" style="width:100%;">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Fecha</th>
                            <th scope="col">Opciones</th>
                        </tr>
                    </thead>
                    <tbody id="tblOrdenDia" name="tblOrdenDia">
                        <?php foreach ($datos as $valor) { ?>
                            <tr>
                                <td scope="col"><?php echo $cont ?></td>
                                <td scope="col"><?php echo $valor['fecha'] ?></td>
                                <td scope="col"><button type="button" class="btn btn-warning" onclick="setTimeout(revisarOrden(<?php echo  $valor['id'] ?>), 3000)" data-toggle='modal' data-target='#modalRevisarOrden'>Revisar</button></td>
                            </tr>
                        <?php $cont++;
                        } ?>
                    </tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</main>

<!-- Modal revisarOrden -->
<div class="modal fade" id="modalRevisarOrden" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="label" aria-hidden="true">
    <div class="modal-dialog" id="dialogModalRevisar">
        <div class="modal-content" id="contentModalRevisar">
            <div class="modal-header">
                <h5 class="modal-title" id="label">Detalles de la Orden del día</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="#" method="POST" class="mx-auto" id="revisionForm" onsubmit="return false" autocomplete="off" enctype="multipart/form-data">
                    <!--  inputs  -->

                    <table id="detalles" class="table table-bordered  table-hover align-middle" style="width:100%;">
                        <thead>
                            <tr class="row-12">
                                <th class="col-1">#</th>
                                <th class="col-2">Oficina</th>
                                <th class="col-5">Detalles del pedido</th>
                                <th class="col-4">Opciones</th>
                            </tr>
                        </thead>
                        <tbody id="detallesBody" name="detallesBody">

                        </tbody>
                    </table>
            </div>
            <div class="modal-footer">
            <button id="botonCrearCategoria" type="submit" class="btn btn-info">Aceptar Seleccionados</button>
                <button id="botonCrearCategoria" type="submit" class="btn btn-success">Aceptar Todos</button>
                <button id="botonCrearCategoria" type="submit" class="btn btn-danger">Rechazar Todos</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
    </form>
</div>
</div>

<!-- Modal revisarOrden -->
<div class="modal fade" id="modalModificarPedido" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="label" aria-hidden="true">
    <div class="modal-dialog" id="dialogModalModificar">
        <div class="modal-content" id="contentModalModificar">
            <div class="modal-header">
                <h5 class="modal-title" id="label">Modificar Pedido</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="#" method="POST" class="mx-auto" id="revisionForm" onsubmit="return false" autocomplete="off" enctype="multipart/form-data">
                    <!--  inputs  -->

                    <table id="detalles" class="table table-bordered  table-hover align-middle" style="width:100%;">
                        <thead>
                            <tr class="row-12">
                                <th class="col-1">#</th>
                                <th class="col-5">Producto</th>
                                <th class="col-2">Cantidad</th>
                                <th class="col-4">Opciones</th>
                            </tr>
                        </thead>
                        <tbody id="detallesPedidos" name="detallesPedidos">

                        </tbody>
                    </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
    </form>
</div>
<script src="<?php echo URL_JS ?>/ordenDelDia.js"></script>