<?php
class DetallesPedidos extends Base{

    public function __construct()
    {
        parent::__construct('detalles_pedidos', 'id_pedidos');
    }

    public function obtenerDetalles($id){
        $this->consultar("SELECT id_producto, cantidad, observacion FROM $this->tabla WHERE id_pedido = $id");
        return $this->obtenerRegistros();
    }
    public function obtenerDetallesConId($id){
        $this->consultar("SELECT id_pedido,id_producto, cantidad, observacion FROM $this->tabla WHERE id_pedido = $id");
        return $this->obtenerRegistros();
    }
    public function obtenerDetallesConProducto($id){
        $this->consultar("SELECT dp.id_producto, dp.cantidad, dp.observacion,p.id,p.nombre FROM detalles_pedidos dp, productos p WHERE dp.id_producto = p.id AND id_pedido = $id");
        return $this->obtenerRegistros();
    }
    public function obtenerDetallesModificados(){
        $this->consultar("SELECT id_pedido, id_producto, cantidad, observacion FROM $this->tabla WHERE observacion IS NOT NULL");
        return $this->obtenerRegistros();
    }

}
?>