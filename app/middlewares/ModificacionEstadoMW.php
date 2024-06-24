<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once "./models/Usuario.php";

class ModificacionEstadoMiddleware
{

    public function __invoke(Request $request, RequestHandler $handler)
    {

        echo "Mod Estado Mesa/Pedido MW \n";
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $data = AutentificadorJWT::ObtenerData($token);

        $params = $request->getParsedBody();
        if (isset($data->usuario, $params["id_item"]) || (isset($params["usuario"], $params["id_mesa"]))) {
            if (Usuario::obtenerUsuario($data->usuario)) {
                $response = $handler->handle($request);
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
