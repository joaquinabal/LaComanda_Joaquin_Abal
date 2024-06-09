<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class PedidoParamsMiddleware {

    public function __invoke(Request $request, RequestHandler $handler) {
        
        echo "Pedido Params MW \n";
        
        $params = $request->getQueryParams();
        
        if(isset($params["id_mesa"])){
            $response = $handler->handle($request);
            } else {
                $response = new Response();
                $response->getBody()->write(json_encode(array("error" => "Parametros equivocados.")));
            }
        echo "Salgo del Pedido Params MW \n";
        return $response;
    }
}