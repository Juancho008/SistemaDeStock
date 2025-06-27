<?php

class Categorias extends Base
{
    public function __construct()
    {
        parent::__construct('categorias', 'id');
    }
    public function listadoDeCategorias()
    {
        $this->consultar("SELECT * FROM $this->tabla WHERE  estado IN (5,6) and cat_padre = 0");
        return $this->obtenerRegistros();
    }
    public function listarSubCategoria($id)
    {
        $id = $this->escapar($id);
        $this->consultar("SELECT * FROM $this->tabla WHERE estado IN(5,6) and cat_padre = $id");
        return $this->obtenerRegistros();
    }
    public function darDeBaja($id)
    {
        $retorno = false;
        $id = $this->escapar($id);
        if( $this->consultar("UPDATE $this->tabla SET estado = 5 WHERE $this->id =$id" )){
            $retorno = true;
        }
        return  $retorno;
    }
    public function darDeAlta($id)
    {
        $retorno = false;
        $id = $this->escapar($id);
        if( $this->consultar("UPDATE $this->tabla SET estado = 6 WHERE $this->id =$id" )){
            $retorno = true;
        }
        return  $retorno;
    }
    public function obtenerEstadoPadre($id)
    {
        $id= $this->escapar($id);
        $this->consultar("SELECT cat_padre,estado FROM $this->tabla WHERE $this->id = $id");
        return $this->obtenerRegistro();
    }
    public function obtenerCategoriaPorIdProducto($id)
    {
        $id= $this->escapar($id);
        $this->consultar("SELECT * FROM $this->tabla WHERE $this->id = $id");
        return $this->obtenerRegistro();
    }
    public function obtenerCategoriaDisponible($id)
    {
        $id= $this->escapar($id);
        $this->consultar("SELECT * FROM $this->tabla WHERE $this->id = $id");
        return $this->obtenerRegistro();
    }
}
