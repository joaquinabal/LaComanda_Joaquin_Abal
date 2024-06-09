<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once "./models/Usuario.php";
require_once "./models/Producto.php";
require_once "./models/Pedido.php";
require_once "./models/ItemsPedido.php";
class EmpleadoMiddleware
{

    public function __invoke(Request $request, RequestHandler $handler)
    {

        echo "Empleado MW \n";

        $params = $request->getParsedBody();
        if (isset($params["usuario"], $params["clave"], $params["id_pedido"])) {
            if (Usuario::obtenerUsuario($params["usuario"])) {
                if (ItemPedido::obtenerItemPedido($params["id_pedido"], Usuario::obtenerUsuario($params["usuario"])->id)) {
                    $response = $handler->handle($request);
                } else {
                    $response = new Response();
                    $response->getBody()->write(json_encode(array("error" => "El usuario no posee rol correspondiente.")));
                }
            } else {
                $response = new Response();
                $response->getBody()->write(json_encode(array("error" => "El usuario no existe.")));
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
