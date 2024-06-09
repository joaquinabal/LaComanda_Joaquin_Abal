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
        $params = $request->getQueryParams();

        if (Usuario::obtenerUsuario($params["usuario"])->rol_empleado == "mozo") {
            $response = $handler->handle($request);
        } else {
            $response = new Response();
            $response->getBody()->write(json_encode(array("error" => "El usuario no posee rol de Mozo.")));
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
