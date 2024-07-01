<?php

date_default_timezone_set('America/Argentina/Buenos_Aires');

class Pedido
{
    public $id;
    public $codigo;
    public $id_mesa; 
    public $id_mozo;
    public $nombre_cliente;
    public $foto; 
    public $tiempo_estimado; 
    public $monto_total;
    public $fecha_hora;
    public $hora_entregada;
    public $cancelado;

    public function crearPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (codigo, id_mesa, id_mozo, nombre_cliente, fecha_hora, foto) VALUES (:codigo, :id_mesa, :id_mozo, :nombre_cliente, :fecha_hora, :foto)");
        $consulta->bindValue(':codigo', $this->generarCodigo());
        $consulta->bindValue(':id_mesa', $this->getId_Mesa());
        $consulta->bindValue(':id_mozo', $this->getId_Mozo());
        $consulta->bindValue(':nombre_cliente', $this->getNombreCliente());
        $fecha = new DateTime(date("d-m-Y H:i:s"));
        $consulta->bindValue(':fecha_hora', date_format($fecha, 'Y-m-d H:i:s'));;
        $consulta->bindValue(':foto', $this->getFoto());
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigo, id_mesa, id_mozo, nombre_cliente, foto, monto_total, tiempo_estimado, fecha_hora FROM pedidos WHERE cancelado = 0");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerPedido($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigo, id_mesa, id_mozo, nombre_cliente, foto, monto_total, tiempo_estimado, fecha_hora FROM pedidos WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    public static function obtenerPedidoSegunCodigo($codigo){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigo, id_mesa, id_mozo, nombre_cliente, foto, monto_total, tiempo_estimado, fecha_hora FROM pedidos WHERE codigo = :codigo");
        $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
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
        $consulta = $objAccesoDatos->prepararConsulta("SELECT a.id as ID, a.nombre_cliente as Nombre_Cliente, u.nombre as Mozo, p.nombre as Producto, a.codigo as Codigo, a.id_mesa as Mesa, 
        a.tiempo_estimado as Tiempo_Estimado, a.fecha_hora as Inicio_Preparacion FROM pedidos a JOIN usuarios u on u.id = a.id_mozo JOIN itemspedido b on b.id_pedido = a.id JOIN productos p on p.id = b.id_producto WHERE b.estado = 'en preparación'");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerPedidosListosParaServir(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT c.id AS ID_Mesa, a.nombre_cliente AS Nombre_Cliente, u.nombre as Mozo, c.codigo AS Codigo_Mesa, a.codigo AS Codigo_Pedido FROM pedidos a JOIN itemspedido b ON b.id_pedido = a.id JOIN mesas c ON a.id_mesa = c.id JOIN usuarios u on u.id = a.id_mozo GROUP BY a.id_mesa, a.nombre_cliente HAVING COUNT(b.id) = SUM(CASE WHEN b.estado = 'listo para servir' THEN 1 ELSE 0 END)");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function sumarMontoTotal($id_mesa){
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos p SET p.monto_total = (SELECT SUM(pr.precio) FROM itemspedido i JOIN productos pr ON pr.id = i.id_producto WHERE i.id_pedido = p.id) WHERE p.id_mesa = :id_mesa");
        $consulta->bindValue(':id_mesa', $id_mesa, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function obtenerPedidoSegunCodigoPedidoYMesa($codigo_pedido, $codigo_mesa){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT p.id, p.codigo, p.id_mesa, p.id_mozo, p.nombre_cliente, p.foto, p.monto_total, p.tiempo_estimado, p.fecha_hora FROM pedidos p JOIN mesas m on m.id = p.id_mesa WHERE p.codigo = :codigo_pedido AND m.codigo = :codigo_mesa");
        $consulta->bindValue(':codigo_pedido', $codigo_pedido, PDO::PARAM_STR);
        $consulta->bindValue(':codigo_mesa', $codigo_mesa, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }

    public static function obtenerPedidosSegunIDMesa($id_mesa){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT p.id, p.codigo, p.id_mesa, p.id_mozo, p.nombre_cliente, p.foto, p.tiempo_estimado, p.fecha_hora, p.monto_total, p.hora_entregada FROM pedidos p JOIN mesas m on m.id = p.id_mesa JOIN usuarios u on u.id = p.id_mozo WHERE id_mesa = :id_mesa");
        $consulta->bindValue(':id_mesa', $id_mesa, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function obtenerPedidosEntregadosFueraDeHora(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigo, id_mesa, nombre_cliente, tiempo_estimado, TIMESTAMPDIFF(MINUTE, fecha_hora, hora_entregada) AS diferencia_minutos FROM pedidos WHERE TIMESTAMPDIFF(MINUTE, fecha_hora, hora_entregada) > tiempo_estimado");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC); 
    }

    static public function modificarPedido($id, $id_mesa, $nombre_cliente)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET id_mesa = :id_mesa, nombre_cliente = :nombre_cliente WHERE id = :id");
        $consulta->bindValue(':id', $id);
        $consulta->bindValue(':id_mesa', $id_mesa, PDO::PARAM_INT);
        $consulta->bindValue(':nombre_cliente', $nombre_cliente, PDO::PARAM_STR);
        $consulta->execute();
    }

    static public function cancelarPedido($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET cancelado = :cancelado WHERE id = :id");
        $consulta->bindValue(':id', $id);
        $consulta->bindValue(':cancelado', 1);

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

    public function getId_Mozo()
    {
        return $this->id_mozo;
    }

    public function setId_Mozo($id_mozo)
    {
        $this->id_mozo = $id_mozo;
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

