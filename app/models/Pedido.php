<?php

date_default_timezone_set('America/Argentina/Buenos_Aires');

class Pedido
{
    public $id;
    public $codigo;
    public $id_mesa; //se agrega despues
    public $nombre_cliente;
    public $foto; //se agrega despues
    public $tiempo_estimado; //se agrega despues
    public $monto_total;
    public $fecha_hora;

    public function crearPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (codigo, id_mesa, nombre_cliente, fecha_hora, foto) VALUES (:codigo, :id_mesa, :nombre_cliente, :fecha_hora, :foto)");
        $consulta->bindValue(':codigo', $this->generarCodigo());
        $consulta->bindValue(':id_mesa', $this->getId_Mesa());
        $consulta->bindValue(':nombre_cliente', $this->getNombreCliente());
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':fecha_hora', date_format($fecha, 'Y-m-d H:i:s'));
        $imagenBinaria = file_get_contents($_FILES['foto']['tmp_name']);
        $consulta->bindValue(':foto', $imagenBinaria, PDO::PARAM_LOB);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigo, id_mesa, nombre_cliente, foto, monto_total, tiempo_estimado, fecha_hora FROM pedidos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function obtenerPedido($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigo, id_mesa, nombre_cliente, foto, monto_total, tiempo_estimado, fecha_hora FROM pedidos WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }

    public static function obtenerPedidoSegunCodigo($codigo){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigo, id_mesa, nombre_cliente, foto, monto_total, tiempo_estimado, fecha_hora FROM pedidos WHERE codigo = :codigo");
        $consulta->bindValue(':codigo', $codigo, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }

    public static function obtenerItemsPedidoSegunPedido($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT c.nombre FROM pedidos a LEFT JOIN itemspedido b on b.id_pedido = a.id LEFT JOIN productos c on c.id = b.id_producto WHERE a.id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function obtenerPedidosEnPreparacion(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT a.id as ID, a.nombre_cliente as Nombre_Cliente, a.codigo as Codigo, a.id_mesa as Mesa, 
        a.tiempo_estimado as Tiempo_Estimado, a.fecha_hora as Inicio_Preparacion FROM pedidos a JOIN itemspedido b on b.id_pedido = a.id WHERE b.estado = 'en preparación'");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerPedidosListosParaServir(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT c.id AS ID_Mesa, a.nombre_cliente AS Nombre_Cliente, c.codigo AS Codigo_Mesa, a.codigo AS Codigo_Pedido FROM pedidos a JOIN itemspedido b ON b.id_pedido = a.id JOIN mesas c ON a.id_mesa = c.id GROUP BY a.id_mesa, a.nombre_cliente HAVING COUNT(b.id) = SUM(CASE WHEN b.estado = 'listo para servir' THEN 1 ELSE 0 END)");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function sumarMontoTotal($id_mesa){
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos p SET p.monto_total = ( SELECT SUM(precio) FROM itemspedido i JOIN productos pr on pr.id = i.id_producto JOIN mesas m on m.id = i.id_mesa WHERE i.id_pedido = p.id AND m.estado = :estado_mesa AND WHERE m.id = :id_mesa");
        $consulta->bindValue(':id_mesa', $id_mesa, PDO::PARAM_INT);
        $consulta->bindValue(':estado_mesa', "listo para servir", PDO::PARAM_STR);
        $consulta->execute();
    }


    static private function generarCodigo()
    {
        return rand(11111, 99999);
    }

    public function getId()
    {
        return $this->id;
    }
    public function getId_Mesa()
    {
        return $this->id_mesa;
    }

    public function setId_Mesa($id_mesa)
    {
        $this->id_mesa = $id_mesa;
    }

    public function getNombreCliente()
    {
        return $this->nombre_cliente;
    }

    public function setNombreCliente($nombre_cliente)
    {
        $this->nombre_cliente = $nombre_cliente;
    }

    public function getFoto()
    {
        return $this->foto;
    }

    public function setFoto($foto)
    {
        $this->foto = $foto;
    }

    public function getMontoTotal()
    {
        return $this->monto_total;
    }

    public function setMontoTotal($monto_total)
    {
        $this->monto_total = $monto_total;
    }
}

