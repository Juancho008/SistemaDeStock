<?php
class Pedidos extends Base{

    public function __construct()
    {
        parent::__construct('pedidos', 'id');
    }

    public function obtenerPendientes(){
        $this->consultar("SELECT id, fecha, id_solicitante,estado,id_orden_del_dia FROM $this->tabla WHERE  estado = 1");
        return $this->obtenerRegistros();
    }

    public function obtenerEnviados(){
        $this->consultar("SELECT id, fecha, id_solicitante FROM $this->tabla WHERE  estado = 2");
        return $this->obtenerRegistros();
    }

    public function obtenerModificados($id){
        $this->consultar("SELECT id, fecha, id_solicitante FROM $this->tabla WHERE id = $id");
        return $this->obtenerRegistros();
    }

    public function obtenerRechazados(){
        $this->consultar("SELECT id, fecha, id_solicitante FROM $this->tabla WHERE estado = 4");
        return $this->obtenerRegistros();
    }
    
    public function obtenerAceptados(){
        $this->consultar("SELECT id, fecha, id_solicitante FROM $this->tabla WHERE estado = 3");
        return $this->obtenerRegistros();
    }

    public function obtenerPedidosPorOrdenDelDia($id_pedido)
    {
        $this->escapar($id_pedido);
        $this->consultar("SELECT id,estado,fecha,id_orden_del_dia FROM $this->tabla WHERE id_orden_del_dia =".$id_pedido ." AND estado = 1");
        return $this->obtenerRegistros();
    }

    public function obtenerOrdenDiaConArea($id_pedido)
    {
        $this->escapar($id_pedido);
        $this->consultar("SELECT p.id,p.estado,p.fecha,p.id_orden_del_dia,p.id_area,a.nombre FROM $this->tabla p,areas a WHERE p.id_area = a.id  AND p.id_orden_del_dia =".$id_pedido);
        return $this->obtenerRegistros();
    }
    public function obtenerPedidoDeProductoAnterior($id_area,$id_producto)
    {
        $id_area = $this->escapar($id_area);
        $id_producto = $this->escapar($id_producto);
        $this->consultar("SELECT p.id,p.fecha,p.id_solicitante,id_area,dp.id_producto,dp.cantidad, pr.nombre,p.estado FROM pedidos p, detalles_pedidos dp, productos pr WHERE p.id = dp.id_pedido AND pr.id = dp.id_producto AND p.id_area = ".$id_area." AND dp.id_producto = ".$id_producto." AND DATE(p.fecha) != CURDATE() ORDER BY p.id DESC LIMIT 1");
        return $this->obtenerRegistro();
    }

    public function obtenerPedidosOrden($id){
        $this->consultar("SELECT id, estado, id_area FROM $this->tabla WHERE id_orden_del_dia = $id ");
        return $this->obtenerRegistros();
    }

    public function aceptarPedido($id){
        $retorno = false;
        $id = $this->escapar($id);
        if($this->consultar("UPDATE $this->tabla SET estado = 3 WHERE id = $id")){
           $retorno = true; 
        }
        return $retorno;
    }

    public function rechazarPedido($id){
        $retorno = false;
        $id = $this->escapar($id);
        if($this->consultar("UPDATE $this->tabla SET estado = 4 WHERE id = $id")){
           $retorno = true; 
        }
        return $retorno;
    }
}
?>
