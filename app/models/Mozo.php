<?php
/*
class Mozo extends Empleado {
    
    
     public function crearMozo()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (username, password, nombre, fecha_ingreso, rol_empleado) VALUES (:username, :password, :nombre, :fecha_ingreso, :rol_empleado)");
        $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $claveHash);
        $consulta->bindValue(':nombre', $this->nombre);
        $fechaActual = date('Y-m-d H:i:s');
        $consulta->bindValue(':fecha_ingreso', $fechaActual);
        $consulta->bindValue(':rol_empleado', "mozo" );
        $consulta->execute();
        
        return $objAccesoDatos->obtenerUltimoId();
    }
    
    public function cambiarEstadoMesa($mesa, $estado) {
    // Lógica para cambiar el estado de la mesa
    }

    
    public function tomarPedido($pedido) {
        // Lógica para tomar un pedido
    }
}