<?php

require_once "Empleado.php";

class Usuario
{
    protected $_id;
    protected $_usuario;
    protected $_clave;

    public function crearUsuario()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (usuario, clave) VALUES (:usuario, :clave)");
        $claveHash = password_hash($this->getClave(), PASSWORD_DEFAULT);
        $consulta->bindValue(':usuario', $this->getUsuario(), PDO::PARAM_STR);
        $consulta->bindValue(':clave', $claveHash);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function modificarEstadoPedido($id)
    {
        $estados = ["pendiente", "en preparación", "listo para servir", "servido"];
        
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT estado FROM pedidos WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

        if ($resultado) {
            $estadoActual = $resultado['estado'];
            $nuevoEstado = '';

            switch ($estadoActual) {
                case $estados[0]:
                    $nuevoEstado = $estados[1];
                    break;
                case $estados[1]:
                    $nuevoEstado = $estados[2];
                    break;
                case $estados[2]:
                    $nuevoEstado = $estados[3]; //ver
                    break;
                default:
                    throw new Exception('Estado desconocido: ' . $estadoActual);
            }
    
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedidos SET estado = :estado WHERE id = :id");
        $consulta->bindValue(':estado', $nuevoEstado, PDO::PARAM_STR);
        $consulta->bindValue(':id', $id);
        $consulta->execute();
        }
    }

    public static function modificarEstadoMesa($id)
    {
        $estados = ["con cliente esperando pedido", "con cliente comiendo", "con cliente pagando", "cerrada"];
        
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT estado FROM mesas WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

        if ($resultado) {
            $estadoActual = $resultado['estado'];
            $nuevoEstado = '';

            switch ($estadoActual) {
                case $estados[0]:
                    $nuevoEstado = $estados[1];
                    break;
                case $estados[1]:
                    $nuevoEstado = $estados[2];
                    break;
                default:
                    throw new Exception('Estado desconocido: ' . $estadoActual);
            }
    
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE mesas SET estado = :estado WHERE id = :id");
        $consulta->bindValue(':estado', $nuevoEstado, PDO::PARAM_STR);
        $consulta->bindValue(':id', $id);
        $consulta->execute();
        }
    }

    public static function cerrarEstadoMesa($id)
    {
        $estados = ["con cliente esperando pedido", "con cliente comiendo", "con cliente pagando", "cerrada"];
        
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT estado FROM mesas WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

        if ($resultado) {
            $estadoActual = $resultado['estado'];
            $nuevoEstado = '';

            if($estadoActual == $estados[2]){
                $nuevoEstado = $estados[3];
            }
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE mesas SET estado = :estado WHERE id = :id");
        $consulta->bindValue(':estado', $nuevoEstado, PDO::PARAM_STR);
        $consulta->bindValue(':id', $id);
        $consulta->execute();
        }
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, username, nombre, rol_empleado, fecha_ingreso, fecha_baja  FROM usuarios");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }

    public static function obtenerUsuario($usuario)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, username, nombre, rol_empleado, fecha_ingreso, fecha_baja  FROM usuarios WHERE username = :usuario");
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Empleado');
    }

    public function modificarUsuario()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET username = :username, password = :password WHERE id = :id");
        $consulta->bindValue(':username', $this->getUsuario(), PDO::PARAM_STR);
        $consulta->bindValue(':password', $this->getClave(), PDO::PARAM_STR);
        $consulta->execute();
    }

    public static function borrarUsuario($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET fecha_baja = :fecha_baja WHERE id = :id");
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':fecha_baja', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->execute();
    }

    // Getter for _id
    public function getId()
    {
        return $this->_id;
    }

    // Setter for _id
    public function setId($id)
    {
        $this->_id = $id;
    }

    // Getter for _usuario
    public function getUsuario()
    {
        return $this->_usuario;
    }

    // Setter for _usuario
    public function setUsuario($usuario)
    {
        $this->_usuario = $usuario;
    }

    // Getter for _clave
    public function getClave()
    {
        return $this->_clave;
    }

    // Setter for _clave
    public function setClave($clave)
    {
        $this->_clave = $clave;
    }
}
