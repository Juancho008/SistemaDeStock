<main class="container-fluid">
    <h1 class="mt-3">Lista de las medidas</h1>
    <div class="card">
        <div class="card-body">
            <div class="mb-3 row">
                <div class="col-auto">
                    <button onclick="modalProducto('<?php echo RUTA_URL ?>')" title="Crear una Medida" class="btn btn-outline-primary " data-toggle='modal' data-target='#modalMedida'>
                        Crear Medida
                    </button>
                </div>
            </div>
            <div class="table-responsive">
                <table id="productoTabla" name="productoTabla" class="table table-bordered table-hover align-middle" style="width:100%;">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Descripcion</th>
                            <th scope="col" style="white-space: nowrap;">Medida</th>
                            <th scope="col" style="white-space: nowrap;">Estado</th>
                            <th scope="col">Opciones</th>
                        </tr>
                    </thead>
                    <tbody id="tblCategorias" name="tblCategorias">
                        <?php foreach ($datos['medida'] as $medida) { ?>
                            <tr>
                                <th scope="col"><?php echo $medida['id'] ?></th>
                                <th scope="col"><?php echo $medida['descripcion'] ?></th>
                                <th scope="col"><?php echo $medida['cantidad'] ?></th>
                                <th scope="col">
                                    <?php if ($medida['estado'] == 0) {
                                        echo '<p type="text" id="textoProducto" style="font-size:1rem; pointer-events:none; "
                                class="bg-success text-white p-1 rounded border-0 text-center">Activo</p>';
                                    } elseif ($medida['estado'] == 1) {
                                        echo '<p type="text" id="textoProducto" style="font-size:1rem; pointer-events:none; "
                                class="bg-secondary text-white p-1 rounded border-0 text-center">Inactivo</p>';
                                    }
                                    ?>
                                </th>
                                <th scope="col">
                                    <button type="button" class="btn btn-primary btn-sm mt-1" data-toggle='modal' data-target='#modalMedida' onclick="editarMedida(<?php echo htmlspecialchars(json_encode($medida)) ?>)">Editar</button>
                                    <?php
                                    if ($medida['estado'] == 0) {
                                        echo '<button type="button" class="btn btn-warning btn-sm mt-1" onclick="darBajaMedida(' . htmlspecialchars(json_encode($medida)) . ')">Desactivar</button>';
                                    } else {
                                        echo '<button type="button" class="btn btn-success btn-sm mt-1" onclick="darAltaMedida(' . htmlspecialchars(json_encode($medida)) . ')">Activar</button>';
                                    } ?>
                                </th>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
</main>

<div class="modal fade" id="modalMedida" tabindex="-1" data-backdrop="static" data-keyboard="false" aria-labelledby="labelMedida" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="labelMedida">Nueva medida</h2>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!--  inputs  -->

                <form action="<?php echo RUTA_URL ?>/Medida/agregarMedida" method="POST" class="mx-auto mt-3" id="formMedida" name="formMedida" autocomplete="off" enctype="multipart/form-data">
                <input type="hidden" id="idMedida" name="idMedida" value="">
                    <div class="form-group">
                        <label id="nombreLabel" for="nombreLabel">Nombre Medida</label>
                        <input type="text" class="form-control" name="inputNombreMedida" id="inputNombreMedida" aria-describedby="medidaHelp" value="">
                        <small id="medidaHelp" class="form-text text-muted">Descipcion/Nombre que tendra la medida.</small>
                    </div>

                    <div class="form-group">
                        <label id="codigoBarraLabel" for="codigoBarraLabel">Cantidad</label>
                        <input type="text" class="form-control" name="inputCantidad" id="inputCantidad" aria-describedby="cantidadHelp" value="">
                        <small id="cantidadHelp" class="form-text text-muted"></small>
                    </div>
                    <!--Fin input's-->
                    <div id="modal-footer-buttons" class="modal-footer">
                        <button id="botonCrearMedida" type="submit" class="btn btn-primary">Crear Medida</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
            </div>
        </div>
        </form>
    </div>