<?php
require_once './models/Usuario.php';
require_once './models/Empleado.php';
require_once './interfaces/IApiUsable.php';
require_once './models/ItemsPedido.php';
require_once './utils/AutentificadorJWT.php';
require_once './models/Encuesta.php';

date_default_timezone_set('America/Argentina/Buenos_Aires');

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
        $usr->setContraseña($clave);
        $usr->setNombre($nombre);
        $usr->crearEmpleado($rol);

        $payload = json_encode(array("mensaje" => "Usuario creado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function DevolverDataLogueo($request, $response){
      $parametros = $request->getParsedBody();
      $usuario = $parametros['usuario'];
      $datos_usuario = Usuario::obtenerUsuario($usuario);
      $datos_usuario->guardarFechaIngreso();
      $datos = array('id' => $datos_usuario->id, 'usuario' => $datos_usuario->usuario, 'nombre' => $datos_usuario->nombre, 'rol_empleado' => $datos_usuario->rol_empleado, 'fecha_ingreso' => $datos_usuario->fecha_ingreso, 'fecha_baja' => $datos_usuario->fecha_baja, 'suspendido' => $datos_usuario->suspendido);
      $token = AutentificadorJWT::CrearToken($datos);
      $payload = json_encode(array('jwt' => $token));

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function AsignarItemPedido($request, $response, $args){
      $data =   AutentificadorJWT::DevolverDataSegunHeader($request);
      $parametros = $request->getParsedBody();
      $id_item = $parametros['id_item'];
      ItemPedido::asignarEmpleado($id_item, $data->id);
      Usuario::modificarEstadoPedido($id_item);
      $item = ItemPedido::obtenerItemPedido($id_item);
      $tiempo_estimado_pedido = Pedido::obtenerPedido($item->getIdPedido())->tiempo_estimado;
      $tiempo_producto = Producto::obtenerProductoSegunId($item->getIdProducto())->tiempo;
      if($tiempo_producto > $tiempo_estimado_pedido){
        ItemPedido::actualizarTiempoEstimadoPedido($item->getIdProducto());
      }
      $payload = json_encode(array("mensaje" => "ItemPedido Asignado a Empleado"));

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function ActualizarItemPedido($request, $response, $args){
      $parametros = $request->getParsedBody();

      $id_item = $parametros['id_item'];
      $item = ItemPedido::obtenerItemPedido($id_item);

      $id_item = $item->id;
      Usuario::modificarEstadoPedido($id_item);
      $payload = json_encode(array("mensaje" => "Estado modificado con exito"));

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function ActualizarMesa($request, $response, $args){
      $parametros = $request->getParsedBody();
      $id_mesa = $parametros['id_mesa'];
      Usuario::modificarEstadoMesa($id_mesa);

      $payload = json_encode(array("mensaje" => "Estado de mesa modificado con exito"));

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function CerrarMesa($request, $response, $args){
      $parametros = $request->getParsedBody();

      $id_mesa = $parametros['id_mesa'];
      Usuario::cerrarEstadoMesa($id_mesa);

      $payload = json_encode(array("mensaje" => "Estado de mesa modificado con exito"));

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function ListarPendientes($request, $response, $args) {
      $data = AutentificadorJWT::DevolverDataSegunHeader($request);
      $pendientes = Usuario::obtenerPendientesSegunTipoEmpleado($data->rol_empleado);
      $payload = json_encode(array("Pendientes" => $pendientes));
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function ListarEnPreparacion($request, $response, $args) {
      $data = AutentificadorJWT::DevolverDataSegunHeader($request);
      $usuario = Usuario::obtenerUsuario($data->usuario);
      $en_preparacion = $usuario->obtenerEnPreparacionDeEmpleado();
      $payload = json_encode(array("En Preparacion" =>$en_preparacion));
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function ListarListosParaServirYActualizarMesa($request, $response, $args) {

      $data = AutentificadorJWT::DevolverDataSegunHeader($request);

      $listos_para_servir = Pedido::obtenerPedidosListosParaServir();
      Usuario::modificarEstadoTodasMesasAClientesComiendo();
      Usuario::modificarEstadoTodosItemsPedidosAServidos($data->id);
      $payload = json_encode(array ("Eventos"=>(array("Pedidos Listos para Servir" =>$listos_para_servir, "mensaje" =>"Mesas Actualizadas"))));
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function ActualizarMontoTotalDePedido($request, $response, $args) {
      $params = $request->getParsedBody();
      $id_mesa = $params['id_mesa'];
      Pedido::sumarMontoTotal($id_mesa);
      Usuario::modificarEstadoMesa($id_mesa);
      $payload = json_encode(array ("mensaje"=>"Monto total actualizado - Mesa Actualizada"));
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }


    public function TraerUno($request, $response, $args)
    {
        // Buscamos usuario por nombre
        $params = $request->getQueryParams();
        $usr = $params['usuario'];
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
      $inputData = file_get_contents('php://input');
      $parametros = json_decode($inputData, true);

        $id = $parametros['id'];
        $username = $parametros['usuario'];
        $contraseña = $parametros['contraseña'];
        $usuario = Usuario::obtenerUsuarioPorID($id);
        $usuario->setUsuario($username);
        $usuario->setContraseña($contraseña);
      
        $usuario->modificarUsuario();

        $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
      $inputData = file_get_contents('php://input');
      $parametros = json_decode($inputData, true);

        $usuarioId = $parametros['id_usuario'];
        Usuario::borrarUsuario($usuarioId);

        $payload = json_encode(array("mensaje" => "Usuario dado de baja con éxito."));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function ManejarSuspension($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $usuarioId = $parametros['id_usuario'];
        $usuario = Usuario::obtenerUsuarioPorID($usuarioId);

        if($usuario->suspendido == 0){
          Usuario::suspenderUsuario($usuarioId);
          $payload = json_encode(array("mensaje" => "Usuario suspendido con éxito."));
        } else {
          Usuario::habilitarUsuario($usuarioId);
          $payload = json_encode(array("mensaje" => "Usuario habilitado con éxito."));          
        }


        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}
