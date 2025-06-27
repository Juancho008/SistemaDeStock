<main class="container">
    <h1 class="mt-3">Ordenes del día</h1>
    <div class="card">

        <div class="card-body">
            <div class="mb-3 row">
                <div class="col-auto">
                    <button onclick="" title="Crea una nueva orden del dia" class="btn btn-outline-primary " data-toggle='modal' data-target='#modalOrdenDelDia'>
                        Generar Orden del dia
                    </button>
                </div>
            </div>
        </div>

        <div class="table-responsive-xl">
            <table id="ordenDelDiaTabla" class="table table-bordered  table-hover align-middle" style="width:100%;">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Fecha</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Opciones</th>
                    </tr>
                </thead>
                <tbody id="tblOrdenDia" name="tblOrdenDia">
                    <?php foreach ($datos['ordenDelDia'] as $ordendeldia) { ?>
                        <tr>
                            <td><?php echo $ordendeldia['id']  ?></td>
                            <td><?php echo $ordendeldia['fecha']  ?> </td>
                            <td> <?php if ($ordendeldia['estado'] == 1) {
                                        echo '<p type="text" id="textoProducto" style="font-size:1rem; pointer-events:none; "
                                class="bg-primary text-white p-1 rounded border-0 text-center">Pendiente</p>';
                                    } elseif ($ordendeldia['estado'] == 2) {
                                        echo '<p type="text" id="textoProducto" style="font-size:1rem; pointer-events:none; "
                                class="bg-warning text-white p-1 rounded border-0 text-center">Enviado</p>';
                                    }elseif ($ordendeldia['estado'] == 3) {
                                        echo '<p type="text" id="textoProducto" style="font-size:1rem; pointer-events:none; "
                                class="bg-success text-white p-1 rounded border-0 text-center">Aceptado</p>';
                                    }elseif ($ordendeldia['estado'] == 4) {
                                        echo '<p type="text" id="textoProducto" style="font-size:1rem; pointer-events:none; "
                                class="bg-danger text-white p-1 rounded border-0 text-center">Rechazado</p>';
                                    }
                                    elseif ($ordendeldia['estado'] == 7) {
                                        echo '<p type="text" id="textoProducto" style="font-size:1rem; pointer-events:none; "
                                class="bg-info text-white p-1 rounded border-0 text-center">Entregado</p>';
                                    }
                                    ?>
                            </td>
                            <td>
                           <?php if($ordendeldia['estado'] == 1) {
                                    echo '<button class="btn btn-primary m-1" onclick="enviarOrdenDia('.htmlspecialchars(json_encode($ordendeldia)) .')">Enviar Orden del día</button>';
                                    echo '<button class="btn btn-primary m-1" onclick="verOrdenPdf('.htmlspecialchars(json_encode($ordendeldia)) .')">Mostrar Orden del dia</button>';
                                    echo '<button type="button" class="btn btn-warning m-1" data-toggle="modal" data-target="#modalOrdenDelDia" onclick="editarFecha('. htmlspecialchars(json_encode($ordendeldia)) .')">Editar</button>';
                                    echo '<button class="btn btn-danger m-1" onclick="eliminarOrdenDia('.htmlspecialchars(json_encode($ordendeldia)) .')">Eliminar</button>';
                                }elseif($ordendeldia['estado'] == 3){
                                    echo '<button class="btn btn-dark m-1" onclick="imprimirOrdenDia('.htmlspecialchars(json_encode($ordendeldia)) .')">Imprimir Orden del dia</button>';
                                    echo '<button class="btn btn-success m-1" onclick="entregarDia('.htmlspecialchars(json_encode($ordendeldia)) .')">Entregar</button>';
                                }elseif($ordendeldia['estado'] == 7){
                                    echo '<button class="btn btn-dark m-1" onclick="imprimirOrdenDia('.htmlspecialchars(json_encode($ordendeldia)) .')">Imprimir Orden del dia</button>';
                                }
                            ?>
                                
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Modal Orden del dia -->
<div class="modal fade" id="modalOrdenDelDia" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="labelOrdenDia" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="labelOrdenDia">Orden del dia</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!--  inputs  -->
                <form action="<?php echo RUTA_URL ?>/OrdenDelDia/generarNuevaOrdenDelDia" method="POST" class="mx-auto" id="nuevaOrdenDelDia" autocomplete="off" enctype="multipart/form-data">
                    <div class="form-group">
                        <label id="ordenDelDiaLabel" for="ordenDelDiaLabel">Fecha orden del dia</label>
                        <input type="date" class="form-control" name="inputFechaOrden" id="inputFechaOrden" aria-describedby="ordenDelDiaHelp" required>
                        <input type="hidden" id="idOcultoFecha" name="idOcultoFecha" value="">
                    </div>
                    <!--fin inputs-->
            </div>
            <div class="modal-footer">
                <button id="botonCrearOrdenDelDia" type="submit" class="btn btn-primary">Generar Orden del Dia</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
    </form>
</div>
<script src="<?php echo URL_JS ?>/ordenDelDia.js"></script>
<!-- Fin modal Subcategorias-->