<?php

class Stock extends Base
{
    public function __construct()
    {
        parent::__construct('stock', 'id');
    }
    public function obtenerStockPorIdProducto($id)
    {
        $id = $this->escapar($id);
        $this->consultar("SELECT * FROM $this->tabla WHERE id_productos = $id");
        return $this->obtenerRegistros();
    }
    public function obtenerStockPorIdTabla($id)
    {
        $id = $this->escapar($id);
        $this->consultar("SELECT * FROM $this->tabla WHERE id=$id");
        return $this->obtenerRegistro();
    }

    public function obtenerStockDisponiblePorIdProducto($id)
    {
        $id = $this->escapar($id);
        $this->consultar("SELECT * FROM $this->tabla WHERE id_productos = $id and estado = 0");
        return $this->obtenerRegistro();
    }

    public function obtenerStockDanadoPorIdProducto($id)
    {
        $id = $this->escapar($id);
        $this->consultar("SELECT * FROM $this->tabla WHERE id_productos = $id and estado = 1 and cantidad > 0");
        return $this->obtenerRegistro();
    }
}