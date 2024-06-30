<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once "./models/Usuario.php";
require_once "./models/Producto.php";
require_once "./models/Pedido.php";
require_once "./models/ItemsPedido.php";
require_once "./utils/AutentificadorJWT.php";
class EmpleadoMiddleware
{

    public function __invoke(Request $request, RequestHandler $handler)
    {

        echo "Empleado MW \n";

        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $data = AutentificadorJWT::ObtenerData($token);

        $params = $request->getParsedBody();
        
        if (ItemPedido::obtenerItemPedidoSegunPedidoYEmpleado(ItemPedido::obtenerItemPedido($params['id_item'])->id_pedido, $data->id)) {
            
            $response = $handler->handle($request);
        } else {
            $response = new Response();
            $response->getBody()->write(json_encode(array("error" => "El usuario no posee rol correspondiente.")));
        }
        return $response;
    }

    public function chequearAsignacionIP(Request $request, RequestHandler $handler)
    {



        $data = AutentificadorJWT::DevolverDataSegunHeader($request);

        $params = $request->getParsedBody();
        
        if (ItemPedido::chequearTipoConRol($data->id, $params["id_item"])) {
            
            $response = $handler->handle($request);
        } else {
            $response = new Response();
            $response->getBody()->write(json_encode(array("error" => "El usuario no posee rol correspondiente.")));
        }
        return $response;
    }

    function chequearKeyValues($key, ...$values)
    {
        foreach ($values as $value) {
            if ($key === $value) {
                return true;
            }
        }
        return false;
    }
}
