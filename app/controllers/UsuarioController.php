<?php
require_once './models/Usuario.php';
require_once './models/Empleado.php';
require_once './interfaces/IApiUsable.php';

class UsuarioController extends Empleado implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $usuario = $parametros['usuario'];
        $clave = $parametros['clave'];
        $nombre = $parametros['nombre'];
        $rol = $parametros['rol_empleado'];
                

        // Creamos el usuario
        $usr = new Empleado();
        $usr->setUsuario($usuario);
        $usr->setClave($clave);
        $usr->setNombre($nombre);
        $usr->crearEmpleado($rol);

        $payload = json_encode(array("mensaje" => "Usuario creado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function ActualizarPedido($request, $response, $args){
      $parametros = $request->getParsedBody();
      $usuario = $parametros['usuario'];
      $clave = $parametros['clave'];
      $id_pedido = $parametros['id_pedido'];
      Usuario::modificarEstadoPedido($id_pedido);

      $payload = json_encode(array("mensaje" => "Estado modificado con exito"));

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function ActualizarMesa($request, $response, $args){
      $parametros = $request->getParsedBody();
      $usuario = $parametros['usuario'];
      $clave = $parametros['clave'];
      $id_mesa = $parametros['id_mesa'];
      Usuario::modificarEstadoMesa($id_mesa);

      $payload = json_encode(array("mensaje" => "Estado de mesa modificado con exito"));

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function CerrarMesa($request, $response, $args){
      $parametros = $request->getParsedBody();
      $usuario = $parametros['usuario'];
      $clave = $parametros['clave'];
      $id_mesa = $parametros['id_mesa'];
      Usuario::cerrarEstadoMesa($id_mesa);

      $payload = json_encode(array("mensaje" => "Estado de mesa modificado con exito"));

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    /*public function ActualizarProducto($request, $response, $args){
      echo "entramos \n";
      $parametros = $request->getParsedBody();
      $usuario = $parametros['usuario'];
      $clave = $parametros['clave'];
      $id_pedido = $parametros['id_pedido'];
      Usuario::modificarEstadoPedido($id_pedido);

      $payload = json_encode(array("mensaje" => "Estado modificado con exito"));

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }*/

    public function TraerUno($request, $response, $args)
    {
        // Buscamos usuario por nombre
        $usr = $args['usuario'];
        $usuario = Usuario::obtenerUsuario($usr);
        $payload = json_encode($usuario);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Usuario::obtenerTodos();
        $payload = json_encode(array("listaUsuario" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $nombre = $parametros['nombre'];
        Usuario::modificarUsuario($nombre); //chequear esto

        $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $usuarioId = $parametros['usuarioId'];
        Usuario::borrarUsuario($usuarioId);

        $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}
