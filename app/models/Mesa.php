<?php

class Mesa {
    private $_id;
    private $_codigo;
    private $_estado;
    private $_total_pedido; 

    //se le agrega n° de mesa manual?

    public function crearMesa()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO Mesas (codigo, estado, total_pedido) VALUES (:codigo, :estado, :total_pedido)");
        $consulta->bindValue(':codigo', $this->generarCodigo());
        $consulta->bindValue(':estado', 'con cliente esperando pedido');
        $consulta->bindValue(':total_pedido', 0);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigo, estado, total_pedido FROM mesas");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }

    public static function obtenerMesa($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigo, estado, total_pedido FROM mesas WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Mesa');
    }

    /*public function modificarProducto($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE productos SET tipo = :tipo, nombre = :nombre, precio = :precio, sector = :sector WHERE id = :id");
        $consulta->bindValue(':id', $id);
        $consulta->bindValue(':tipo', $this->getTipo());
        $consulta->bindValue(':nombre', $this->getNombre(), PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->getPrecio(), PDO::PARAM_STR);
        $consulta->bindValue(':sector', $this->getSector(), PDO::PARAM_STR);
        $consulta->execute();
    }

    public static function borrarProducto($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET fechaBaja = :fechaBaja WHERE id = :id");
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->execute();
    }*/

    static private function generarCodigo(){
        return rand(11111,99999);
    }

    public function actualizarTotalPedido($nuevoTotal) {
        $this->_total_pedido = $nuevoTotal;
    }

    public function getTotalPedido() {
        return $this->_total_pedido;
    }

    // Otros métodos...
}
