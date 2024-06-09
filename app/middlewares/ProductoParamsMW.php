<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class ProductoParamsMiddleware
{

    public function __invoke(Request $request, RequestHandler $handler)
    {

        echo "Producto Params MW \n";

        $params = $request->getQueryParams();

        if (isset($params["nombre"], $params["tipo"], $params["precio"])) {
            if (chequearKeyValues($params["tipo"], "TragoVino", "PlatoPrincipal", "Postre", "Cerveza")) {
                $response = $handler->handle($request);
            } else {
                $response = new Response();
                $response->getBody()->write(json_encode(array("error" => "Tipo de producto incorrecto.")));
            }
        } else {
            $response = new Response();
            $response->getBody()->write(json_encode(array("error" => "Parametros equivocados.")));
        }
        echo "Salgo del Producto Params MW \n";
        return $response;
    }
}
