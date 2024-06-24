<?php

class ItemPedido{

    public $id;
    public $id_pedido;
    public $id_producto;
    public $id_empleado;
    
    public $estado;

    public function crearItemPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO itemspedido (id_pedido, id_producto, estado) VALUES (:id_pedido, :id_producto, :estado)");
        $consulta->bindValue(':id_pedido', $this->getIdPedido());
        $consulta->bindValue(':id_producto', $this->getIdProducto());
       // $consulta->bindValue(':id_empleado', $this->getIdEmpleado());
        $consulta->bindValue(':estado', 'pendiente');
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, id_pedido, id_producto, id_empleado FROM itemspedido");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'ItemPedido');
    }

    public static function obtenerItemPedidoSegunPedidoYEmpleado($id_pedido, $id_empleado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, id_pedido, id_producto, id_empleado, estado FROM itemspedido WHERE id_pedido = :id_pedido AND id_empleado = :id_empleado");
        $consulta->bindValue(':id_pedido', $id_pedido, PDO::PARAM_INT);
        $consulta->bindValue(':id_empleado', $id_empleado, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('ItemPedido');
    }

    public static function obtenerItemPedido($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, id_pedido, id_producto, id_empleado, estado FROM itemspedido WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('ItemPedido');
    }


    public static function actualizarTiempoEstimadoPedido($id_producto){
        
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedidos p JOIN itemspedido a on p.id = a.id_pedido LEFT JOIN productos b on b.id = a.id_producto SET tiempo_estimado = b.tiempo WHERE b.id = :id");
        $consulta->bindValue(':id', $id_producto, PDO::PARAM_INT);
        $consulta->execute();

    }

    public static function asignarEmpleado($id_item, $id_empleado){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE itemspedido SET id_empleado = :id_empleado WHERE id = :id");
        $consulta->bindValue(':id', $id_item, PDO::PARAM_INT);
        $consulta->bindValue(':id_empleado', $id_empleado, PDO::PARAM_INT);
        $consulta->execute();

    }
    


public function getId() {
    return $this->id;
}

public function setId($id) {
    $this->id = $id;
}

public function getIdPedido() {
    return $this->id_pedido;
}

public function setIdPedido($id_pedido) {
    $this->id_pedido = $id_pedido;
}

public function getIdProducto() {
    return $this->id_producto;
}

public function setIdProducto($id_producto) {
    $this->id_producto = $id_producto;
}


public function getIdEmpleado() {
    return $this->id_empleado;
}

public function setIdEmpleado($id_empleado) {
    $this->id_empleado = $id_empleado;
}

public function getEstado() {
    return $this->estado;
}

public function setEstado($estado) {
    $this->estado = $estado;
}

}