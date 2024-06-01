<?php

class Empleado extends Usuario
{
    protected $_nombre;
    protected $_fecha_ingreso;
    protected $_rol_empleado;


    public function crearEmpleado($rol_empleado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (username, password, nombre, fecha_ingreso, rol_empleado) VALUES (:username, :password, :nombre, :fecha_ingreso, :rol_empleado)");
        $claveHash = password_hash($this->getClave(), PASSWORD_DEFAULT);
        $consulta->bindValue(':username', $this->getUsuario(), PDO::PARAM_STR);
        $consulta->bindValue(':password', $claveHash);
        $consulta->bindValue(':nombre', $this->getNombre());
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':fecha_ingreso', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->bindValue(':rol_empleado', $rol_empleado);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }


    // Métodos comunes a todos los empleados
    public function getNombre() {
        return $this->_nombre;
    }

    public function getFechaIngreso() {
        return $this->_fecha_ingreso;
    }

    public function getRolEmpleado() {
        return $this->_rol_empleado;
    }

    // Setter for _nombre
    public function setNombre($nombre) {
        $this->_nombre = $nombre;
    }

    // Setter for _fecha_ingreso
    public function setFechaIngreso($fecha_ingreso) {
        $this->_fecha_ingreso = $fecha_ingreso;
    }

       // Setter for _rol_empleado
       public function setRolEmpleado($rol_empleado) {
        $this->_rol_empleado = $rol_empleado;
    }
}

