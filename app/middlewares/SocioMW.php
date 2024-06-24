<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once "./models/Usuario.php";
require_once "./models/Producto.php";
require_once "./models/Pedido.php";
require_once "./models/ItemsPedido.php";
class SocioMiddleware
{

    public function __invoke(Request $request, RequestHandler $handler)
    {

        echo "Socio MW \n";

        $params = $request->getParsedBody();
        if (Usuario::obtenerUsuario($params["usuario"])->rol_empleado == "socio") {
            $response = $handler->handle($request);
        } else {
            $response = new Response();
            $response->getBody()->write(json_encode(array("error" => "El usuario no posee el rol de socio.")));
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
