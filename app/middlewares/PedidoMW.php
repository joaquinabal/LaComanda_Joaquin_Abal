<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once './models/Mesa.php';

class PedidoMiddleware {

    public function ParamsCargarUno(Request $request, RequestHandler $handler) {

        $method = $request->getMethod();

        if($method === 'GET'){
            $params = $request->getQueryParams();    
        }
        
        if($method === 'POST'){
            $params = $request->getParsedBody();   
        }


        if(isset($params["id_mesa"], $params['nombre_cliente']) && $_FILES['foto']){
            $response = $handler->handle($request);
            } else {
                $response = new Response();
                $response->getBody()->write(json_encode(array("error" => "Parametros equivocados.")));
            }

        return $response;
    }

    public function ValidarCargarUno(Request $request, RequestHandler $handler) {

        $method = $request->getMethod();

        if($method === 'GET'){
            $params = $request->getQueryParams();    
        }
        
        if($method === 'POST'){
            $params = $request->getParsedBody();   
        }
        $mensaje = [];
        
        $error = false;


        if(!Mesa::obtenerMesa($params["id_mesa"])){
            $mensaje[] = ["error" => "ID Mesa inexistente."];
            $error = true;
        }

        if(!is_string($params['nombre_cliente'])){
            $mensaje[] = ["error" => "El nombre del cliente debe ser un string."];
            $error = true;
        }

        if($error){
            $response = new Response();
            $payload = json_encode($mensaje);
            $response->getBody()->write($payload);
        } else {
            $response = $handler->handle($request); 
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
}