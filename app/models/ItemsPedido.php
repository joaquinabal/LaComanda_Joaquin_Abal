<?php

class ItemPedido{

    private $_id;
    private $_id_pedido;
    private $_id_producto;
    private $_id_empleado;

    public function crearItemPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO itemspedido (id_pedido, id_producto, id_empleado) VALUES (:id_pedido, :id_producto, :id_empleado)");
        $consulta->bindValue(':id_pedido', $this->getIdPedido());
        $consulta->bindValue(':id_producto', $this->getIdProducto());
        $consulta->bindValue(':id_empleado', $this->getIdEmpleado());
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

    public static function obtenerItemPedido($id_pedido, $id_empleado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, id_pedido, id_producto, id_empleado FROM itemspedido WHERE id_pedido = :id_pedido AND id_empleado = :id_empleado");
        $consulta->bindValue(':id_pedido', $id_pedido, PDO::PARAM_INT);
        $consulta->bindValue(':id_empleado', $id_empleado, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('ItemPedido');
    }




// Getter para _id
public function getId() {
    return $this->_id;
}

// Setter para _id
public function setId($id) {
    $this->_id = $id;
}

// Getter para _id_pedido
public function getIdPedido() {
    return $this->_id_pedido;
}

// Setter para _id_pedido
public function setIdPedido($id_pedido) {
    $this->_id_pedido = $id_pedido;
}

// Getter para _id_producto
public function getIdProducto() {
    return $this->_id_producto;
}

// Setter para _id_producto
public function setIdProducto($id_producto) {
    $this->_id_producto = $id_producto;
}


// Getter para _id_empleado
public function getIdEmpleado() {
    return $this->_id_empleado;
}

// Setter para _id_empleado
public function setIdEmpleado($id_empleado) {
    $this->_id_empleado = $id_empleado;
}

}