<?php

class Medidas extends Base
{
    public function __construct()
    {
        parent::__construct('medidas', 'id');
    }
    public function obtenerMedidaPorIdStock($id)
    {
        $id = $this->escapar($id);
        $this->consultar("SELECT * FROM $this->tabla WHERE $this->id = $id and estado= 0;");
        return $this->obtenerRegistro();
    }
    public function medidaActiva()
    {
        $this->consultar("SELECT * FROM $this->tabla WHERE estado = 0");
        return $this->obtenerRegistros();
    }
    public function obtenerMultiplicadorMedida($id_medida)
    {
        $id_medida = $this->escapar($id_medida);
        $this->consultar("SELECT cantidad FROM $this->tabla WHERE id=$id_medida");
        return $this->obtenerRegistro();
    }
}
