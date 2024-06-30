<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');
require_once "Empleado.php";

class Usuario
{
    public $id;
    public $usuario;
    public $contraseña;

    public function crearUsuario()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (usuario, contraseña) VALUES (:usuario, :contraseña)");
        $claveHash = password_hash($this->getContraseña(), PASSWORD_DEFAULT);
        $consulta->bindValue(':usuario', $this->getUsuario(), PDO::PARAM_STR);
        $consulta->bindValue(':contraseña', $claveHash);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function modificarEstadoPedido($id)
    {
        $estados = ["pendiente", "en preparación", "listo para servir", "servido"];
        
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT estado FROM itemspedido WHERE id = :id");
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
    
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE itemspedido SET estado = :estado WHERE id = :id");
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

    public static function modificarEstadoTodasMesasAClientesComiendo()
    {
        $estados_itemspedido = ["pendiente", "en preparación", "listo para servir", "servido"];
        $estados_mesa = ["con cliente esperando pedido", "con cliente comiendo", "con cliente pagando", "cerrada"];   
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE mesas m JOIN  (SELECT a.id_mesa FROM pedidos a JOIN itemspedido b ON b.id_pedido = a.id GROUP BY a.id_mesa HAVING COUNT(b.id) = SUM(CASE WHEN b.estado = :estado_itemspedido THEN 1 ELSE 0 END)) sub ON  m.id = sub.id_mesa SET m.estado = :estado_mesa");
        $consulta->bindValue(':estado_itemspedido',$estados_itemspedido[2], PDO::PARAM_STR);
        $consulta->bindValue(':estado_mesa',$estados_mesa[1], PDO::PARAM_STR);
        $consulta->execute();
    }

    public static function modificarEstadoTodosItemsPedidosAServidos($id_mozo)
    {
        $estados_itemspedido = ["pendiente", "en preparación", "listo para servir", "servido"];
        $estados_mesa = ["con cliente esperando pedido", "con cliente comiendo", "con cliente pagando", "cerrada"];   
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE itemspedido ip JOIN pedidos p ON ip.id_pedido = p.id JOIN (SELECT a.id FROM pedidos a JOIN itemspedido b ON b.id_pedido = a.id WHERE a.id_mozo = :id_mozo GROUP BY a.id HAVING COUNT(b.id) = SUM(CASE WHEN b.estado = :estado_itemspedido THEN 1 ELSE 0 END)) sub ON ip.id_pedido = sub.id SET ip.estado = :estado_itemspedido_2");
        $consulta->bindValue(':estado_itemspedido',$estados_itemspedido[2], PDO::PARAM_STR);
        $consulta->bindValue(':estado_itemspedido_2',$estados_itemspedido[3], PDO::PARAM_STR);
        $consulta->bindValue(':id_mozo',$id_mozo, PDO::PARAM_INT);
        $consulta->execute();
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

    public static function obtenerPendientesSegunTipoEmpleado($rol_empleado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT ip.id AS ID_Item, c.nombre AS Item, p.codigo AS Codigo_Pedido, p.nombre_cliente AS Nombre_Cliente, 'pendiente' AS Estado FROM productos c JOIN itemspedido ip ON c.id = ip.id_producto JOIN pedidos p ON p.id = ( SELECT id_pedido FROM itemspedido ip_inner WHERE ip_inner.id_producto = c.id AND ip_inner.estado = 'pendiente' LIMIT 1 ) LEFT JOIN rol_empleado_tipo_producto r ON c.tipo = r.tipo WHERE EXISTS ( SELECT 1 FROM itemspedido ip WHERE ip.id_producto = c.id AND ip.estado = 'pendiente' AND ip.id_empleado IS NULL ) AND r.rol_empleado = :rol_empleado AND ip.estado = 'pendiente'  ");
        $consulta->bindValue(':rol_empleado', $rol_empleado);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    
    public function obtenerEnPreparacionDeEmpleado()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT a.nombre as Empleado, a.rol_empleado as Rol, c.id as ID_Item, c.nombre as Item, b.estado as Estado FROM usuarios a JOIN itemspedido b on b.id_empleado = a.id JOIN productos c on c.id = b.id_producto WHERE b.estado = 'en preparación' AND a.id = :id");
        $consulta->bindValue(':id', $this->getId());
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, usuario, nombre, rol_empleado, fecha_ingreso, fecha_baja  FROM usuarios");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerUsuario($usuario)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, usuario, contraseña, nombre, rol_empleado, fecha_ingreso, fecha_baja, suspendido  FROM usuarios WHERE usuario = :usuario");
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Empleado');
    }

    public static function obtenerUsuarioPorID($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, usuario, contraseña, nombre, rol_empleado, fecha_ingreso, fecha_baja, suspendido  FROM usuarios WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Empleado');
    }

    public function modificarUsuario()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET usuario = :usuario, contraseña = :contrasena WHERE id = :id");
        $claveHash = password_hash($this->getContraseña(), PASSWORD_DEFAULT);
        $consulta->bindValue(':id', $this->getId(), PDO::PARAM_INT  );
        $consulta->bindValue(':usuario', $this->getUsuario(), PDO::PARAM_STR);
        $consulta->bindValue(':contrasena', $claveHash, PDO::PARAM_STR);
        $consulta->execute();
    }

    public static function borrarUsuario($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET fecha_baja = :fecha_baja WHERE id = :id");
        $fecha = new DateTime(date("d-m-Y H:i:s"));
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':fecha_baja', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->execute();
    }

    public static function suspenderUsuario($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET suspendido = 1 WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function habilitarUsuario($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET suspendido = 0 WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
    }

    public function guardarFechaIngreso()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("INSERT INTO fecha_ingreso (id_usuario, fecha_hora) VALUES (:id_usuario, :fecha_hora)");
        $fecha = new DateTime(date('Y-m-d H:i:s'));
        $consulta->bindValue(':id_usuario', $this->id, PDO::PARAM_INT);
        $consulta->bindValue(':fecha_hora', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->execute();
    }

    // Getter for _id
    public function getId()
    {
        return $this->id;
    }

    // Setter for _id
    public function setId($id)
    {
        $this->id = $id;
    }

    // Getter for _usuario
    public function getUsuario()
    {
        return $this->usuario;
    }

    // Setter for _usuario
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;
    }

    // Getter for _contraseña
    public function getContraseña()
    {
        return $this->contraseña;
    }

    // Setter for _contraseña
    public function setContraseña($contraseña)
    {
        $this->contraseña = $contraseña;
    }
}
