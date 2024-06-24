<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');
class Empleado extends Usuario
{
    public $nombre;
    public $fecha_ingreso;
    public $rol_empleado;


    public function crearEmpleado($rol_empleado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (username, password, nombre, fecha_ingreso, rol_empleado) VALUES (:username, :password, :nombre, :fecha_ingreso, :rol_empleado)");
        $claveHash = password_hash($this->getContraseña(), PASSWORD_DEFAULT);
        $consulta->bindValue(':username', $this->getUsuario(), PDO::PARAM_STR);
        $consulta->bindValue(':password', $claveHash);
        $consulta->bindValue(':nombre', $this->getNombre());
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':fecha_ingreso', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->bindValue(':rol_empleado', $rol_empleado);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    

    public function getNombre() {
        return $this->nombre;
    }

    public function getFechaIngreso() {
        return $this->fecha_ingreso;
    }

    public function getRolEmpleado() {
        return $this->rol_empleado;
    }

    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }


    public function setFechaIngreso($fecha_ingreso) {
        $this->fecha_ingreso = $fecha_ingreso;
    }

    
       public function setRolEmpleado($rol_empleado) {
        $this->rol_empleado = $rol_empleado;
    }
}

