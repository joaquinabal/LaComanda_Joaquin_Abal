<?php
require_once './models/Pedido.php';
require_once './interfaces/IApiUsable.php';
require_once './utils/Archivos.php';
require_once './utils/AutentificadorJWT.php';


date_default_timezone_set('America/Argentina/Buenos_Aires');

class PedidoController extends Pedido
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $data = AutentificadorJWT::DevolverDataSegunHeader($request);

        $id_mesa = $parametros['id_mesa'];       
        $nombre_cliente = $parametros['nombre_cliente'];
        $foto = Archivos::darAltaImagen($_FILES['foto'], $nombre_cliente . "_foto_pedido", dirname(__DIR__) . "/ImagenesPedido/");

        $pedido = new Pedido();
        $pedido->setId_Mesa($id_mesa);
        $pedido->setId_Mozo($data->id);
        $pedido->setFoto($foto);
        $pedido->setNombreCliente($nombre_cliente);
        $pedido->crearPedido();

        $payload = json_encode(array("mensaje" => "Pedido creado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos pedido por id
        $prod = $args['pedido'];
        $producto = Pedido::obtenerPedido($prod);
        $payload = json_encode($producto);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Pedido::obtenerTodos();
        $payload = json_encode(array("listaPedido" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerItemsPedido($request, $response, $args)
    {
        $id_pedido = $args['pedido'];
        $items_pedido = Pedido::obtenerItemsPedidoSegunPedido($id_pedido);
        $payload = json_encode($items_pedido);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
  
    public function ListarTotalEnPreparacion($request, $response){
      $en_preparacion = Pedido::obtenerPedidosEnPreparacion();
      $payload = json_encode($en_preparacion);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function ListarPedidosEntregadosFueraDeHora($request, $response){
    $pedidos_fuera_de_hora = Pedido::obtenerPedidosEntregadosFueraDeHora();
    $payload = json_encode(array("Pedidos" => $pedidos_fuera_de_hora));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args)
    {
      $inputData = file_get_contents('php://input');
      $parametros = json_decode($inputData, true);

        $id = $parametros['id'];
        $id_mesa = $parametros['id_mesa'];
        $nombre_cliente = $parametros['nombre_cliente'];
        Pedido::modificarPedido($id, $id_mesa, $nombre_cliente);

        $payload = json_encode(array("mensaje" => "Pedido modificado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function CancelarUno($request, $response, $args)
    {
      $inputData = file_get_contents('php://input');
      $parametros = json_decode($inputData, true);

        $id = $parametros['id'];
        Pedido::cancelarPedido($id);

        $payload = json_encode(array("mensaje" => "Pedido borrado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}
