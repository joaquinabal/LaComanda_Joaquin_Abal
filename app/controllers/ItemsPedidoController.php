<?php
require_once './models/ItemsPedido.php';
require_once './interfaces/IApiUsable.php';

date_default_timezone_set('America/Argentina/Buenos_Aires');

class ItemsPedidoController extends ItemPedido implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id_pedido = $parametros['id_pedido'];
        $id_producto = $parametros['id_producto'];   

        // Creamos el usuario
        $item = new ItemPedido();
        $item->setIdPedido($id_pedido);
        $item->setIdProducto($id_producto);
        $item->setEstado("pendiente");
        $item->crearItemPedido();
        

        $payload = json_encode(array("mensaje" => "ItemPedido creado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos Itempedido por id
        $prod = $args['itemPedido'];
        $producto = ItemPedido::obtenerItemPedido($prod);
        $payload = json_encode($producto);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = ItemPedido::obtenerTodos();
        $payload = json_encode(array("listaPedido" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
      $inputData = file_get_contents('php://input');
      $parametros = json_decode($inputData, true);

        $id = $parametros['id'];
        $id_producto = $parametros['id_producto'];
        ItemPedido::modificarItemPedido($id, $id_producto);

        $payload = json_encode(array("mensaje" => "ItemPedido modificado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
      $inputData = file_get_contents('php://input');
      $parametros = json_decode($inputData, true);

        $id = $parametros['id'];
        ItemPedido::borrarItemPedido($id);

        $payload = json_encode(array("mensaje" => "ItemPedido borrado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}
