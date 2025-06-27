<main class="container-fluid">
    <h1 class="mt-3">Listado de Productos</h1>
    <div class="card">
        <div class="card-body">
        </div>
        <div class="table-responsive">
            <table id="productoTabla" name="productoTabla" class="table table-bordered table-hover align-middle" style="width:100%;">
                <thead class="table-dark" style="background-color: #343a40!important; color:whitesmoke;">
                    <tr>
                        <th scope="col" class="text-light" style="background-color: #343a40!important;">#</th>
                        <th scope="col" class="text-light" style="background-color: #343a40!important;">Producto</th>
                        <th scope="col" class="text-light" style="background-color: #343a40!important;" style="white-space: nowrap;">Codigo de Barra</th>
                        <th scope="col" class="text-light" style="background-color: #343a40!important;">Stock en unidad</th>
                        <th scope="col" class="text-light" style="background-color: #343a40!important;">Categoria</th>
                        <th scope="col" class="text-light" style="background-color: #343a40!important;">Permisos de solicitud</th>
                        <th scope="col" class="text-light" style="background-color: #343a40!important;">Imagen</th>
                        <th scope="col" class="text-light" style="background-color: #343a40!important;">Estado</th>
                    </tr>
                </thead>
                <tbody id="tblCategorias" name="tblCategorias">
                    <?php foreach ($datos['productos'] as $productos) { ?>
                        <tr>
                            <th scope="col"><?php echo $productos['id'] ?></th>
                            <th scope="col"><?php echo $productos['nombre'] ?></th>
                            <th scope="col"><?php echo $productos['codigo_barra'] ?></th>
                            <th scope="col"><?php echo $productos['stock']['cantidad'] ?></th>
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
                                <?php if ($productos['estado'] == 6) {
                                    echo '<p type="text" id="textoProducto" style="font-size:1rem; pointer-events:none; "
                                class="bg-success text-white p-1 rounded border-0 text-center">Activo</p>';
                                } elseif ($productos['estado'] == 5) {
                                    echo '<p type="text" id="textoProducto" style="font-size:1rem; pointer-events:none; "
                                class="bg-secondary text-white p-1 rounded border-0 text-center">Inactivo</p>';
                                }
                                ?>
                            </th>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</main>