<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once "./models/Usuario.php";
require_once "./models/ItemsPedido.php";

class ModificacionEstadoMiddleware
{

    public function __invoke(Request $request, RequestHandler $handler)
    {


        $data = AutentificadorJWT::DevolverDataSegunHeader($request);

        $params = $request->getParsedBody();
        if (isset($data->usuario, $params["id_item"]) || (isset($params["usuario"], $params["id_mesa"]))) {
            if (ItemPedido::obtenerItemPedido($params["id_item"]) && ItemPedido::obtenerItemPedido($params["id_item"])->estado == "en preparación") {
                $response = $handler->handle($request);
            } else {
                $response = new Response();
                $response->getBody()->write(json_encode(array("error" => "Item Pedido no válido.")));
            }
        } else {
            $response = new Response();
            $response->getBody()->write(json_encode(array("error" => "parámetros inexistentes.")));
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
