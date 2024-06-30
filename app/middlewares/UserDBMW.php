<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once "./models/Usuario.php";
require_once "./models/Producto.php";
require_once "./models/Pedido.php";
require_once "./models/ItemsPedido.php";
require_once "./utils/AutentificadorJWT.php";
class UserDBMiddleware
{

    public function __invoke(Request $request, RequestHandler $handler)
    {

        $params = $request->getQueryParams();
        
        if (isset($params['usuario'])) {
            if (Usuario::obtenerUsuario($params['usuario'])){
                if(!Usuario::obtenerUsuario($params['usuario'])->fecha_baja){
                    $response = $handler->handle($request);
                } else {
                    $response = new Response();
                    $response->getBody()->write(json_encode(array("error" => "Usuario dado de baja.")));
                    return $response;
                }
            } else {
                $response = new Response();
                $response->getBody()->write(json_encode(array("error" => "No existe tal usuario.")));
                return $response;
            }
        } else {
            $response = new Response();
            $response->getBody()->write(json_encode(array("error" => "Faltan Parámetros.")));
            return $response;
        }
        return $response;
    }
}