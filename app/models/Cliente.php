<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');

class Cliente {

    public static function ObtenerTiempoEstimadoSegunPedidoYMesa($codigo_pedido, $codigo_mesa){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT a.nombre as Nombre, b.codigo as Codigo_Mesa, a.codigo as Codigo_Pedido, a.tiempo_estimado as Tiempo_Estimado,
         a.fecha_hora as Inicio_Preparacion FROM pedidos a JOIN mesas b on b.id = a.id_mesa WHERE b.codigo = :codigo_mesa AND a.codigo = :codigo_pedido");
        $consulta->bindValue(':codigo_mesa', $codigo_mesa, PDO::PARAM_STR);
        $consulta->bindValue(':codigo_pedido', $codigo_pedido, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }


}