<?php

class Encuesta {
    public $id;
    public $id_pedido;
    public $mesa_puntaje;
    public $restaurante_puntaje;
    public $mozo_puntaje;
    public $cocinero_puntaje;
    public $bartender_puntaje;
    public $cervecero_puntaje;
    public $comentario;

    public function generarEncuesta(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO encuestas (id_pedido, mesa_puntaje, restaurante_puntaje, mozo_puntaje, comentario, cocinero_puntaje, bartender_puntaje, 
        cervecero_puntaje) VALUES (:id_pedido, :mesa_puntaje, :restaurante_puntaje, :mozo_puntaje, :comentario, :cocinero_puntaje, :bartender_puntaje, 
        :cervecero_puntaje)");
        $consulta->bindValue(':id_pedido', $this->getIdPedido(), PDO::PARAM_STR);
        $consulta->bindValue(':mesa_puntaje', $this->getMesaPuntaje(), PDO::PARAM_STR);
        $consulta->bindValue(':restaurante_puntaje', $this->getRestaurantePuntaje(), PDO::PARAM_STR);
        $consulta->bindValue(':mozo_puntaje', $this->getMozoPuntaje(), PDO::PARAM_STR);
        $consulta->bindValue(':comentario', $this->getComentario(), PDO::PARAM_STR);
        $consulta->bindValue(':cocinero_puntaje', $this->getCocineroPuntaje(), PDO::PARAM_STR);
        $consulta->bindValue(':bartender_puntaje', $this->getBartenderPuntaje(), PDO::PARAM_STR);
        $consulta->bindValue(':cervecero_puntaje', $this->getCerveceroPuntaje(), PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    static public function obtenerComentarioSegunMejorPuntajeMesa(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT e.id_pedido, m.id as Mesa, e.mesa_puntaje, e.comentario FROM encuestas e JOIN pedidos p on p.id = e.id_pedido JOIN mesas m on m.id = p.id_mesa  ORDER BY e.mesa_puntaje DESC LIMIT 1");
        $consulta->execute();

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    static public function obtenerComentarioSegunMejorPuntajeRestaurante(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT e.id_pedido, e.restaurante_puntaje, e.comentario FROM encuestas e ORDER BY e.restaurante_puntaje DESC LIMIT 1");
        $consulta->execute();

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    static public function obtenerComentarioSegunMejorPuntajeMozo(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT e.id_pedido, e.mozo_puntaje, u.nombre as nombre_mozo, e.comentario FROM encuestas e JOIN pedidos p on p.id = e.id_pedido JOIN usuarios u on u.id = p.id_mozo ORDER BY e.mozo_puntaje DESC LIMIT 1");
        $consulta->execute();

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    static public function obtenerComentarioSegunMejorPuntajeCocinero(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT e.id_pedido, e.cocinero_puntaje, e.comentario FROM encuestas e ORDER BY e.cocinero_puntaje DESC LIMIT 1");
        $consulta->execute();

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    static public function obtenerComentarioSegunMejorPuntajeBartender(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT e.id_pedido, e.bartender_puntaje, e.comentario FROM encuestas e ORDER BY e.bartender_puntaje DESC LIMIT 1");
        $consulta->execute();

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    static public function obtenerComentarioSegunMejorPuntajeCervecero(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT e.id_pedido, e.cervecero_puntaje, e.comentario FROM encuestas e ORDER BY e.cervecero_puntaje DESC LIMIT 1");
        $consulta->execute();

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    static public function obtenerComentarioSegunPeorPuntajeMesa(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT e.id_pedido, m.id as Mesa, e.mesa_puntaje, e.comentario FROM encuestas e JOIN pedidos p on p.id = e.id_pedido JOIN mesas m on m.id = p.id_mesa  ORDER BY e.mesa_puntaje ASC LIMIT 1");
        $consulta->execute();

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    static public function obtenerComentarioSegunPeorPuntajeRestaurante(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT e.id_pedido, e.restaurante_puntaje, e.comentario FROM encuestas e ORDER BY e.restaurante_puntaje ASC LIMIT 1");
        $consulta->execute();

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    static public function obtenerComentarioSegunPeorPuntajeMozo(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT e.id_pedido, e.mozo_puntaje, u.nombre as nombre_mozo, e.comentario FROM encuestas e JOIN pedidos p on p.id = e.id_pedido JOIN usuarios u on u.id = p.id_mozo ORDER BY e.mozo_puntaje ASC LIMIT 1");
        $consulta->execute();

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    static public function obtenerComentarioSegunPeorPuntajeCocinero(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT e.id_pedido, e.cocinero_puntaje, e.comentario FROM encuestas e ORDER BY e.cocinero_puntaje ASC LIMIT 1");
        $consulta->execute();

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    static public function obtenerComentarioSegunPeorPuntajeBartender(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT e.id_pedido, e.bartender_puntaje, e.comentario FROM encuestas e ORDER BY e.bartender_puntaje ASC LIMIT 1");
        $consulta->execute();

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    static public function obtenerComentarioSegunPeorPuntajeCervecero(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT e.id_pedido, e.cervecero_puntaje, e.comentario FROM encuestas e ORDER BY e.cervecero_puntaje ASC LIMIT 1");
        $consulta->execute();

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

     // Getter y Setter para id
     public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    // Getter y Setter para id_pedido
    public function getIdPedido() {
        return $this->id_pedido;
    }

    public function setIdPedido($id_pedido) {
        $this->id_pedido = $id_pedido;
    }

    // Getter y Setter para mesa_puntaje
    public function getMesaPuntaje() {
        return $this->mesa_puntaje;
    }

    public function setMesaPuntaje($mesa_puntaje) {
        $this->mesa_puntaje = $mesa_puntaje;
    }

    // Getter y Setter para restaurante_puntaje
    public function getRestaurantePuntaje() {
        return $this->restaurante_puntaje;
    }

    public function setRestaurantePuntaje($restaurante_puntaje) {
        $this->restaurante_puntaje = $restaurante_puntaje;
    }

    // Getter y Setter para mozo_puntaje
    public function getMozoPuntaje() {
        return $this->mozo_puntaje;
    }

    public function setMozoPuntaje($mozo_puntaje) {
        $this->mozo_puntaje = $mozo_puntaje;
    }

    // Getter y Setter para cocinero_puntaje
    public function getCocineroPuntaje() {
        return $this->cocinero_puntaje;
    }

    public function setCocineroPuntaje($cocinero_puntaje) {
        $this->cocinero_puntaje = $cocinero_puntaje;
    }

    // Getter y Setter para bartender_puntaje
    public function getBartenderPuntaje() {
        return $this->bartender_puntaje;
    }

    public function setBartenderPuntaje($bartender_puntaje) {
        $this->bartender_puntaje = $bartender_puntaje;
    }

    // Getter y Setter para cervecero_puntaje
    public function getCerveceroPuntaje() {
        return $this->cervecero_puntaje;
    }

    public function setCerveceroPuntaje($cervecero_puntaje) {
        $this->cervecero_puntaje = $cervecero_puntaje;
    }

    // Getter y Setter para comentario
    public function getComentario() {
        return $this->comentario;
    }

    public function setComentario($comentario) {
        $this->comentario = $comentario;
    }

}