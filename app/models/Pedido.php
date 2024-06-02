<?php

class Pedido {
    private $_id;
    private $_codigo;
    private $_id_mesa; //se agrega despues
    private $_estado;
    private $_foto; //se agrega despues
    private $_tiempo_estimado; //se agrega despues
    private $_fecha_hora;
    private $_comanda; // Array de ItemPedido
    private $_empleados; // Array de EmpleadoPedido

    public function crearPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (codigo, id_mesa, estado, fecha_hora) VALUES (:codigo, :id_mesa, :estado, :fecha_hora)");
        $consulta->bindValue(':codigo', $this->generarCodigo());
        $consulta->bindValue(':id_mesa', $this->getId_Mesa());
        $consulta->bindValue(':estado', 'pendiente');
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':fecha_hora', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public function agregarProductoAPedido($producto){
        
    }
    
    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigo, id_mesa, estado, foto, tiempo_estimado, fecha_hora FROM pedidos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function obtenerPedido($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigo, id_mesa, estado, foto, tiempo_estimado, fecha_hora FROM pedidos WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }

    static private function generarCodigo(){
       return rand(11111,99999);
    }
    

    public function getId_Mesa(){
        return $this->_id_mesa;
    }

    public function setId_Mesa($id_mesa){
        $this->_id_mesa = $id_mesa;
    }
}