<?php
require_once './models/Pedido.php';
require_once './interfaces/IApiUsable.php';
require_once './utils/Archivos.php';

class PedidoController extends Pedido implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id_mesa = $parametros['id_mesa'];       
        $nombre_cliente = $parametros['nombre_cliente'];

        $pedido = new Pedido();
        $pedido->setId_Mesa($id_mesa);
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

    public function ModificarUno($request, $response, $args)
    {
       /* $parametros = $request->getParsedBody();

        $id = $parametros['id'];
        Pedido::modificarPedido($id);

        $payload = json_encode(array("mensaje" => "Pedido modificado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');*/
    }

    public function BorrarUno($request, $response, $args)
    {
        /*$parametros = $request->getParsedBody();

        $id = $parametros['id'];
        Pedido::borrarPedido($id);

        $payload = json_encode(array("mensaje" => "Pedido borrado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json'); */
    }
}
