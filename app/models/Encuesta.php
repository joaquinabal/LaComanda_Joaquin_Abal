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