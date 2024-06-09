<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once "./models/Usuario.php";

class MozoMiddleware
{

    public function __invoke(Request $request, RequestHandler $handler)
    {

        echo "Mozo MW \n";

        $params = $request->getParsedBody();
        if (isset($params["usuario"], $params["clave"], $params["id_pedido"]) || (isset($params["usuario"], $params["clave"], $params["id_mesa"]))) {
            if (Usuario::obtenerUsuario($params["usuario"])) {
                if (Usuario::obtenerUsuario($params["usuario"])->rol_empleado == "mozo") {
                    $response = $handler->handle($request);
                } else {
                    $response = new Response();
                    $response->getBody()->write(json_encode(array("error" => "El usuario no posee rol de Mozo.")));
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
