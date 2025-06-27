<link rel="stylesheet" href="<?php echo URL_CSS ?>/solicitarPedidos.css"/>
<div class="mt-5">
    <h1>Solicitar Pedidos</h1>

    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <!-- Ícono a la izquierda -->
        <div class="form-group d-flex align-items-center" style="margin-right: 1rem;">
            <i onclick="solicitarPedido()" class="button-custom fa-solid fa-file-lines fa-3x"></i>
        </div>

        <!-- Contenedor de los dos selects a la derecha -->
        <div class="d-flex flex-wrap justify-content-end" style="gap: 1rem;">
            <div class="form-group">
                <label for="solicitante">Sector Solicitante:</label>
                <select id="solicitante" class="form-control">
                    <?php foreach ($datos['test'] as $opciones) { echo '<option>...</option>'; } ?>
                </select>
            </div>

            <div class="form-group">
                <label for="categoria">Filtrar Categoría:</label>
                <select id="categoria" class="form-control">
                    <?php foreach ($datos['test'] as $opciones) { echo '<option>...</option>'; } ?>
                </select>
            </div>
        </div>
    </div>
</div>

<table id="pedidosTable" name="pedidosTable" class="table  table-bordered table-striped table-responsive-xxl">
    <thead class="table-dark">
        <tr>
            <th scope="col" class="text-light">#</th>
            <th scope="col" class="text-light">Producto</th>
            <th scope="col" class="text-light">Categoria</th>
            <th scope="col" class="text-light">Imagen</th>
            <th scope="col" class="text-light">Codigo</th>
            <th scope="col" class="text-light">Stock Disponible</th>
            <th scope="col" class="text-light">Opciones</th>
        </tr>
    </thead>
    <tbody>

    </tbody>
</table>


<div class="modal fade" id="carritoModal" tabindex="-1" aria-labelledby="carritoLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title" id="carritoLabel">Productos seleccionados</h3>
        <button type="button" class="btn-close btn" data-bs-dismiss="modal" aria-label="Cerrar">X</button>
      </div>
      <div class="modal-body">
       <div class="d-flex flex-row"> <h4 class="p-1"> Sector solicitante: </h4> <input class="p-2" id="solicitante" name="solicitante" type="text" value="" readonly></div>
        <ul id="listaCarrito" class="list-group mt-2"></ul>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary">Enviar pedido</button>
      </div>
    </div>
  </div>
</div>

<script src="<?php echo URL_JS ?>/solicitarPedido.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>