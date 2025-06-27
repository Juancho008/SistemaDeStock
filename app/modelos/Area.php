<?php

class Area extends Base
{
    public function __construct()
    {
        parent::__construct('areas', 'id');
    }

    public function obtenerAreaPorId($id)
    {
        $this->consultar("SELECT id,nombre,id_sector FROM $this->tabla WHERE id=".$id);
        return $this->obtenerRegistro();
    }
}