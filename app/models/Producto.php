<?php

class Producto {
    private $_id;
    private $_tipo;
    private $_nombre;
    private $_precio;
    private $_fechaBaja;

     public function crearProducto()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO productos (tipo, nombre, precio) VALUES (:tipo, :nombre, :precio)");
        $consulta->bindValue(':tipo', $this->getTipo());
        $consulta->bindValue(':nombre', $this->getNombre(), PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->getPrecio(), PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->getPrecio(), PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

   public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, tipo, nombre, precio FROM productos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');
    }

    public static function obtenerProducto($nombreProducto)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, tipo, nombre, precio FROM productos WHERE nombre = :nombre");
        $consulta->bindValue(':nombre', $nombreProducto, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Producto');
    }

    public function modificarProducto($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE productos SET tipo = :tipo, nombre = :nombre, precio = :precio WHERE id = :id");
        $consulta->bindValue(':id', $id);
        $consulta->bindValue(':tipo', $this->getTipo());
        $consulta->bindValue(':nombre', $this->getNombre(), PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->getPrecio(), PDO::PARAM_STR);
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
    }

    // Métodos Getters
    public function getId() {
        return $this->_id;
    }

    public function getTipo() {
        return $this->_tipo;
    }

    public function getNombre() {
        return $this->_nombre;
    }

    public function getPrecio() {
        return $this->_precio;
    }


    // Métodos Setters
    public function setId($id) {
        $this->_id = $id;
    }

    public function setTipo($tipo) {
        $this->_tipo = $tipo;
    }

    public function setNombre($nombre) {
        $this->_nombre = $nombre;
    }

    public function setPrecio($precio) {
        $this->_precio = $precio;
    }

}