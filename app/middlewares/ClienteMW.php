<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once './models/Mesa.php';
require_once './models/Pedido.php';
class ClienteMiddleware {

    public function ParamsConsulta(Request $request, RequestHandler $handler) {

        $method = $request->getMethod();

        if($method === 'GET'){
            $params = $request->getQueryParams();    
        }
        
        if($method === 'POST'){
            $params = $request->getParsedBody();   
        }


        if(isset($params["codigo_mesa"], $params['codigo_pedido'])){
            $response = $handler->handle($request);
            } else {
                $response = new Response();
                $response->getBody()->write(json_encode(array("error" => "Parametros equivocados.")));
            }
        echo "Salgo del Pedido Params MW \n";
        return $response;
    }

    public function ValidarConsulta(Request $request, RequestHandler $handler) {

        $method = $request->getMethod();

        if($method === 'GET'){
            $params = $request->getQueryParams();    
        }
        
        if($method === 'POST'){
            $params = $request->getParsedBody();   
        }
        $mensaje = [];
        
        $error = false;


        if(!Mesa::obtenerMesaSegunCodigo($params["codigo_mesa"])){
            $mensaje[] = ["error" => "Codigo de Mesa Inexistente"];
            $error = true;
        }

        if(!Pedido::obtenerPedidoSegunCodigo($params["codigo_pedido"])){
            $mensaje[] = ["error" => "Codigo de Pedido Inexistente"];
            $error = true;
        }

        if(!Pedido::obtenerPedidoSegunCodigoPedidoYMesa($params["codigo_pedido"], $params["codigo_mesa"])){
            $mensaje[] = ["error" => "No existe pedido y mesa relacionados con ese codigo."];
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