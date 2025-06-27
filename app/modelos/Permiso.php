<?php

class Permiso extends Base
{
    public function __construct()
    {
        parent::__construct('permisos_productos');
    }
    public function obtenerPermisoPorIdProducto($id_producto)
    {
        $id_producto = $this->escapar($id_producto);
        $this->consultar("SELECT * FROM permisos_productos WHERE id_producto = $id_producto");
        return $this->obtenerRegistros();
    }
    public function eliminarPermisos($id_producto)
    {
        $id_producto = $this->escapar($id_producto);
        $this->consultar("DELETE FROM permisos_productos WHERE id_producto = $id_producto");
    }
    public function verificarPermisoExistente($id_producto, $oficina)
    {
        $id_producto = $this->escapar($id_producto);
        $oficina = $this->escapar($oficina);
        $this->consultar("SELECT * FROM $this->tabla WHERE id_producto = $id_producto AND id_oficina = $oficina");
        return $this->obtenerRegistros();
    }
}