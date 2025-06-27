<?php

class ActaBaja extends Base
{
    public function __construct()
    {
        parent::__construct('acta_baja', 'id');
    }

    public function obtenerActaBajaContador()
    {
        $this->consultar("SELECT COUNT(*) as contador FROM $this->tabla");
        return $this->obtenerRegistro();
    }
}