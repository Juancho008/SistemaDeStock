<main class="container">
    <h1 class="mt-3">Lista de Categorías</h1>
    <div class="card">

        <div class="card-body">
            <div class="mb-3 row">
                <div class="col-auto">
                    <button onclick="modalCategoria()" title="Crea una nueva categoria" class="btn btn-outline-primary " data-toggle='modal' data-target='#modalSubCategorias'>
                        Crear categoría
                    </button>
                </div>
            </div>
        </div>

        <div class="table-responsive-xl">
            <table id="catTabla" class="table table-bordered  table-hover align-middle" style="width:100%;">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Nombres</th>
                        <th scope="col">Imagen</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Opciones</th>
                    </tr>
                </thead>
                <tbody id="tblCategorias" name="tblCategorias">
                    <?php echo $datos['crearLista']; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Modal Subcategorias -->
<div class="modal fade" id="modalSubCategorias" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="labelCrearSubcategoria" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="labelCrearSubcategoria"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!--  inputs  -->
                <form action="<?php echo RUTA_URL ?>/Categoria/nuevaCategoria" method="POST" class="mx-auto" id="nuevaCategoria" autocomplete="off" enctype="multipart/form-data">
                    <input type="hidden" name="inputModalHidden" id="inputModalHidden" value="">
                    <input type="hidden" name="imagenValor" id="imagenValor" value="">
                    <input type="hidden" name="inputModalHidden-id" id="inputModalHidden-id" value="">
                    <div class="form-group">
                        <label id="subcategoriaLabel" for="subcategoriaLabel">Nombre Subcategoria</label>
                        <input type="text" class="form-control" name="inputSubcategoriaModal" id="inputSubcategoriaModal" aria-describedby="subCategoriaHelp" required>
                        <small id="subCategoriaHelp" class="form-text text-muted">Debe colocar el nombre de la subcategoria.</small>
                    </div>
                    <div class="form-group">
                        <label for="labelImagenSubcategoria" id="labelImagenSubcategoria">Imagen de la subcategoria</label>
                        <div id="btn-file" class="btn-datos-desactivados">
                            <label class="custom-file-upload" id="labelImagen">
                                <input type="file" onclick="inputFileImagen('activar')" name="imagenSubcategoria" value="" id="imagenSubcategoria" aria-describedby="subCategoriaImagen">
                                Subir imagen
                            </label>
                        </div>
                        <small id="subCategoriaImagen" class="form-text text-muted">No es obligatorio colocar una imagen.</small>
                        <footer class="blockquote-footer" id="categoriaLabelFooter">El cuadro se mostrara en <cite title="Source Title"><b class="text-primary">Celeste</b></cite> cuando tenga una imagen cargada</footer>
                    </div>
                    <!--fin inputs-->
            </div>
            <div class="modal-footer">
                <button id="botonCrearCategoria" type="submit" class="btn btn-primary">Crear Subcategoria</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
    </form>
</div>
<script src="<?php echo URL_JS ?>/categorias.js"></script>
<!-- Fin modal Subcategorias-->