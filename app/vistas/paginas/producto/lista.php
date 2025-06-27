<link rel="stylesheet" href="<?php echo URL_CSS ?>/productos_rework.css">
<main class="container-fluid">
    <h1 class="mt-3">Listado de Productos</h1>
    <div class="card">

        <div class="card-body">
            <div class="d-flex mb-3 gap-2">
                <button onclick="modalProducto('<?php echo RUTA_URL ?>')" title="Crear un nuevo producto"
                    class="btn btn-outline-primary" data-toggle="modal" data-target="#modalProducto">
                    Crear Producto
                </button>

                <a href="<?php echo RUTA_URL ?>/Pdf/imprimirStockDisponible" target="_blank">
                <button  title="Imprimir Stock Disponible"
                    class="btn ml-2 btn-outline-success" >
                    Imprimir Stock Disponible
                </button>
                </a>

                <a href="<?php echo RUTA_URL ?>/Pdf/imprimirStockDanado"  target="_blank" id="imprimirActa">
                <button title="Acta de baja"
                    class="btn ml-2 btn-outline-danger" >
                    Acta de Baja
                </button>
                </a>

                <a href="<?php echo RUTA_URL ?>/Pdf/imprimirStockTotal" target="_blank">
                <button  title="Imprimir Stock Total"
                    class="btn ml-2 btn-outline-warning" >
                    Imprimir Stock Total
                </button>
                </a>

            </div>
        </div>

        <div class="table-responsive">
            <table id="productoTabla" name="productoTabla" class="table table-bordered table-hover align-middle" style="width:100%;">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Producto</th>
                        <th scope="col" style="white-space: nowrap;">Codigo de Barra</th>
                        <th scope="col">Stock Disponible</th>
                        <th scope="col">Stock Dañado</th>
                        <th scope="col">Stock Total</th>
                        <th scope="col">Categoria</th>
                        <th scope="col">Permisos de solicitud</th>
                        <th scope="col">Imagen</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Opciones</th>
                    </tr>
                </thead>
                <tbody id="tblCategorias" name="tblCategorias">
                    <?php foreach ($datos['productos'] as $productos) { ?>
                        <?php
                        $stockDisponible = 0;
                        $stockDaniado = 0;

                        foreach ($productos['stock'] as $itemStock) {
                            if ($itemStock['estado'] == 0) {
                                $stockDisponible += $itemStock['cantidad'];
                            } elseif ($itemStock['estado'] == 1) {
                                $stockDaniado += $itemStock['cantidad'];
                            }
                        }

                        $stockTotal = $stockDisponible + $stockDaniado;
                        $valorMinimo = $productos['valor_minimo'];
                        $claseFila = ($stockDisponible <= $valorMinimo) ? 'table-danger' : '';
                        ?>
                        <tr class="<?php echo $claseFila; ?>">
                            <th scope="col"><?php echo $productos['id'] ?></th>
                            <th scope="col"><?php echo $productos['nombre'] ?></th>
                            <th scope="col"><?php echo $productos['codigo_barra'] ?></th>

                            <th scope="col"><?php echo $stockDisponible; ?></th>
                            <th scope="col"><?php echo $stockDaniado; ?></th>
                            <th scope="col"><?php echo $stockTotal; ?></th>

                            <th scope="col"><?php echo $productos['categoria']['nombre']  ?></th>
                            <th scope="col">
                                <?php
                                $nombresOficinas = array();
                                foreach ($productos['oficinas'] as $datosOfi) {
                                    foreach ($datos['oficinas'] as $oficina) {
                                        if ($oficina['id'] == $datosOfi['id_oficina']) {
                                            $nombresOficinas[] = $oficina['nombre'];
                                        }
                                    }
                                }
                                echo implode(', ', $nombresOficinas);
                                ?>
                            </th>
                            <th scope="col"><?php
                                            $extension = pathinfo($productos['imagen'], PATHINFO_EXTENSION);
                                            echo "<img src='" . base64EncodeImage(RUTA_APP . '/imagenes/producto/' .
                                                $productos['imagen'], $extension) .
                                                "' alt='No hay imagen disponible' style='width:150px; height:100px;
                                                border-radius:5px; margin-left:2rem;'/>"
                                            ?>
                            </th>
                            <th scope="col">
                                <?php if ($productos['estado'] == 0) {
                                    echo '<p type="text" id="textoProducto" style="font-size:1rem; pointer-events:none; "
                                class="bg-success text-white p-1 rounded border-0 text-center">Activo</p>';
                                } elseif ($productos['estado'] == 1) {
                                    echo '<p type="text" id="textoProducto" style="font-size:1rem; pointer-events:none; "
                                class="bg-secondary text-white p-1 rounded border-0 text-center">Inactivo</p>';
                                }
                                ?>
                            </th>
                            <th scope="col">
                                <button type="button" class="btn btn-primary btn-sm mt-1" data-toggle='modal' data-target='#modalProducto' onclick="editarProducto(<?php echo htmlspecialchars(json_encode($productos)) ?>)">Editar</button>
                                <?php
                                if ($productos['estado'] == 0) {
                                    echo '<button type="button" class="btn btn-warning btn-sm mt-1" onclick="desactivarProducto(' . htmlspecialchars(json_encode($productos)) . ')">Desactivar</button>';
                                } elseif ($productos['estado'] == 1) {
                                    echo '<button type="button" class="btn btn-success btn-sm mt-1" onclick="activarProducto(' . htmlspecialchars(json_encode($productos)) . ')">Activar</button>';
                                } ?>
                                <button type="button" class="btn btn-success btn-sm mt-1" data-toggle='modal' data-target='#modalProducto' onclick="modificarStock(<?php echo htmlspecialchars(json_encode($productos)) ?>)">Modificar Stock</button>
                                <button type="button" class="btn btn-danger btn-sm mt-1" data-toggle='modal' data-target='#modalProducto' onclick="retirarStock(<?php echo htmlspecialchars(json_encode($productos)) ?>)">Retirar Stock</button>
                                <button type="button" class="btn btn-dark btn-sm mt-1" data-toggle='modal' data-target='#modalProducto' onclick="reportarStock(<?php echo htmlspecialchars(json_encode($productos)) ?>)">Reportar Stock dañado</button>
                            </th>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Modal Productos -->
<div class="modal fade" id="modalProducto" tabindex="-1" data-backdrop="static" data-keyboard="false" aria-labelledby="labelProductos" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="labelProductos"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!--  inputs  -->
                <form action="#" method="POST" class="mx-auto" id="formProducto" name="formProducto" autocomplete="off" enctype="multipart/form-data">
                    <input type="hidden" name="inputHiddenId" id="inputHiddenId" value="">
                    <input type="hidden" name="inputModalProductoHidden" id="inputModalProductoHidden" value="">
                    <input type="hidden" name="inputModalHidden-id" id="inputModalHidden-id" value="">

                    <div class="form-group">
                        <label id="nombreLabel" for="nombreLabel">Nombre Producto</label>
                        <input type="text" class="form-control" name="inputNombreProducto" id="inputNombreProducto" aria-describedby="productoHelp" value="">
                        <small id="productoHelp" class="form-text text-muted">Debe colocar el nombre del producto.</small>
                    </div>

                    <div class="form-group">
                        <label id="codigoBarraLabel" for="codigoBarraLabel">Codigo de Barra</label>
                        <input type="text" class="form-control" name="inputCodigoDeBarra" id="inputCodigoDeBarra" aria-describedby="codigoDeBarraHelp" value="">
                        <small id="codigoDeBarraHelp" class="form-text text-muted">En caso de tener codigo de barra debe colocarlo.</small>
                    </div>
                    <div class="form-group">
                        <label for="selectProductoLabel" id="selectProductoLabel">Selecione medida</label>
                        <select id="medidaSelect" name="medidaSelect" class="form-control">
                            <?php foreach ($datos['medida'] as $medidas) {
                                echo '<option value="' . $medidas['id'] . '">' . $medidas['descripcion'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="selectCategoriaLabel" id="selectCategoriaLabel">Selecion de Categorias</label>
                        <select id="productoSelect" class="form-control" name="productoSelect">
                            <?php foreach ($datos['categorias'] as $categorias) {
                                echo '<option value="' . $categorias['id'] . '">' . $categorias['nombre'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label id="stockLabel" for="stockLabel">Cantidad</label>
                        <input type="text" class="form-control" name="inputStock" id="inputStock" aria-describedby="inputStockHelp" value="">
                        <small id="stockMutedText" class="form-text text-muted">Ingrese el stock actual del producto.</small>
                    </div>

                    <div class="form-group">
                        <label id="valorMinimoLabel" for="valorMinimoLabel">Valor Mínimo</label>
                        <input type="number" class="form-control" name="valorMinimo" id="valorMinimo" aria-describedby="valorMinimoHelp" value="">
                        <small id="valorMutedText" class="form-text text-muted">Ingrese el valor mínimo para control de stock.</small>
                    </div>

                    <div class="form-group" id="obs" name="obs" style="display:none;">
                        <label id="observaciones" for="observaciones">Observaciones</label>
                        <input type="text" class="form-control" name="observaciones" id="observaciones" aria-describedby="observacionesHelp" value="">
                        <small id="observaciones" class="form-text text-muted">Ingrese las Observaciones</small>
                    </div>

                    <div class="form-group">
                        <label for="labelImagenProducto" id="labelImagenProducto">Imagen Producto</label>
                        <div id="btn-file" class="btn-datos-desactivados">
                            <label class="custom-file-upload" id="labelImagen">
                                <input type="file" onclick="inputFileImagen('activar')" name="imagenProducto" value="" id="imagenProducto" aria-describedby="imagenProducto">
                                Subir imagen
                            </label>
                        </div>
                        <small id="productoImagen" class="form-text text-muted">No es obligatorio colocar una imagen.</small>
                        <footer id="imagenCargadaProducto" name="imagenCargadaProducto" class="blockquote-footer">El cuadro se mostrara en <cite title="Source Title"><b class="text-primary">Celeste</b></cite> cuando tenga una imagen cargada</footer>
                    </div>
                    <div class="form-group">
                        <label for="selectPermisosLabel" id="selectPermisosLabel">Selecion de permisos</label>
                        <select id="selectPermisos" name="selectPermisos[]" class="form-control" multiple="multiple" style="width:25vw;">
                            <?php foreach ($datos['oficinas'] as $oficina) {
                                echo '<option value="' . $oficina['id'] . '">' . $oficina['nombre'] . '</option>';
                            }
                            ?>
                        </select>
                        <footer id="mutedText" name="mutedText" class="blockquote-footer">Puede selecionar a quien mostrar los <cite title="Source Title"><b class="text-danger">PRODUCTOS</b></cite>, <cite title="Source Title">no es obligatorio</cite></footer>
                    </div>
                    <!--Fin input's-->
                    <div id="modal-footer-buttons" class="modal-footer col-12">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" style="height:52px;">Cerrar</button>
                        <button id="botonCrearProducto" type="submit" class="btn btn-primary btn-sm" style="height:52px;">Crear Producto</button>
                    </div>
            </div>
        </div>
        </form>
    </div>

    <!-- Fin modal Productos-->