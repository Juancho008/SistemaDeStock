<?php

class OrdenesDelDia extends Base
{
    public function __construct()
    {
        parent::__construct('orden_del_dia', 'id');
    }

    public function obtenerOrdenDelDia()
    {
        $this->consultar("SELECT id,estado,fecha FROM $this->tabla WHERE estado IN (1,2,3,4,7)");
        return $this->obtenerRegistros();
    }
    public function buscarOrdenDelDiaPendiente()
    {
        $this->consultar("SELECT id,estado,fecha FROM $this->tabla WHERE estado = 1");
        return $this->obtenerRegistros();
    }

    public function obtenerOrdenDelDiaPorId($id)
    {
        $this->consultar("SELECT id,estado,fecha FROM $this->tabla WHERE id =". $id);
        return $this->obtenerRegistro();
    }

    public function obtenerOrdenesEnviadas(){
        $this->consultar("SELECT id,estado,fecha FROM $this->tabla WHERE estado = 2 ORDER BY fecha DESC");
        return $this->obtenerRegistros();
    }

    public function obtenerOrdenesCerradas(){
        $this->consultar("SELECT id,estado,fecha FROM $this->tabla WHERE estado IN(3,4) ORDER BY fecha DESC");
        return $this->obtenerRegistros();
    }
}