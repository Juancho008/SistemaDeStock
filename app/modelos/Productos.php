<?php

class Productos extends Base
{
    public function __construct()
    {
        parent::__construct('productos', 'id');
    }
    public function obtenerProductosPorCategoria($id)
    {
        $id = $this->escapar($id);
        $this->consultar("SELECT * FROM $this->tabla WHERE id_categoria = $id");
        return $this->obtenerRegistros();
    }
    public function darDeBajaProductosPorCategoria($categoria)
    {
        $retorno = false;
        $categoria = $this->escapar($categoria);
        if ($this->consultar("UPDATE $this->tabla SET estado = 5 WHERE id_categoria =$categoria")) {
            $retorno = true;
        }
        return  $retorno;
    }
    public function darDeAltaProductosPorCategoria($categoria)
    {
        $retorno = false;
        $categoria = $this->escapar($categoria);
        if ($this->consultar("UPDATE $this->tabla SET estado = 6 WHERE id_categoria =$categoria")) {
            $retorno = true;
        }
        return  $retorno;
    }
    public function existeCodigoBarra($codigoBarra)
    {
        $codigoBarra = $this->escapar($codigoBarra);
        $this->consultar("SELECT * FROM $this->tabla WHERE codigo_barra=$codigoBarra");
        return $this->obtenerRegistro();
    }

    public function obtenerNombreProducto($id){
        $id = $this->escapar($id);
        $this->consultar("SELECT nombre FROM $this->tabla WHERE id = $id");
        return $this->obtenerRegistro();
    }
}
