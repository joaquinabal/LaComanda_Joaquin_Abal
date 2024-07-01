<?php

class Producto {
    public $id;
    public $tipo;
    public $nombre;
    public $precio;

    public $tiempo;
    public $fechaBaja;

     public function crearProducto()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO productos (tipo, nombre, precio, tiempo) VALUES (:tipo, :nombre, :precio, :tiempo)");
        $consulta->bindValue(':tipo', $this->getTipo());
        $consulta->bindValue(':nombre', $this->getNombre(), PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->getPrecio(), PDO::PARAM_STR);
        $consulta->bindValue(':tiempo', $this->getTiempo(), PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

   public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, tipo, nombre, precio, tiempo FROM productos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerTodosSinID()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT tipo, nombre, precio, tiempo FROM productos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerProducto($nombreProducto)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, tipo, nombre, precio FROM productos WHERE nombre = :nombre");
        $consulta->bindValue(':nombre', $nombreProducto, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Producto');
    }

    public static function obtenerProductoSegunId($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, tipo, nombre, precio, tiempo FROM productos WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Producto');
    }

    public static function obtenerProductosMasVendidos(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT p.nombre, COUNT(ip.id_producto) as cantidad FROM productos p JOIN itemspedido ip ON ip.id_producto = p.id GROUP BY p.nombre ORDER BY cantidad DESC");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function modificarProducto($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE productos SET tipo = :tipo, nombre = :nombre, precio = :precio, tiempo = :tiempo WHERE id = :id");
        $consulta->bindValue(':id', $id);
        $consulta->bindValue(':tipo', $this->getTipo());
        $consulta->bindValue(':nombre', $this->getNombre(), PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->getPrecio(), PDO::PARAM_STR);
        $consulta->bindValue(':tiempo', $this->getTiempo(), PDO::PARAM_STR);
        $consulta->execute();
    }


    
    public  function modificarPrecioTiempoSegunNombre()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE productos SET precio = :precio, tiempo = :tiempo WHERE nombre = :nombre");
        $consulta->bindValue(':nombre', $this->getNombre(), PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->getPrecio(), PDO::PARAM_STR);
        $consulta->bindValue(':tiempo', $this->getTiempo(), PDO::PARAM_STR);
        $consulta->execute();
    }

    

    public static function borrarProducto($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE productos SET fecha_baja = :fechaBaja WHERE id = :id");
        $fecha = new DateTime(date("d-m-Y H:i:s"));
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->execute();
    }





    // Métodos Getters
    public function getId() {
        return $this->id;
    }

    public function getTipo() {
        return $this->tipo;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function getPrecio() {
        return $this->precio;
    }

    public function getTiempo() {
        return $this->tiempo;
    }


    // Métodos Setters
    public function setId($id) {
        $this->id = $id;
    }

    public function setTipo($tipo) {
        $this->tipo = $tipo;
    }

    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    public function setPrecio($precio) {
        $this->precio = $precio;
    }

    public function setTiempo($tiempo){
    $this->tiempo = $tiempo;
}
}