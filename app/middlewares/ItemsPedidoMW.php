<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once './models/Pedido.php';
require_once './models/Producto.php';
require_once './models/ItemsPedido.php';


class ItemPedidoMiddleware {

    public function ParamsCargarUno(Request $request, RequestHandler $handler) {

        $method = $request->getMethod();

        if($method === 'GET'){
            $params = $request->getQueryParams();    
        }
        
        if($method === 'POST'){
            $params = $request->getParsedBody();   
        }


        if(isset($params["id_pedido"], $params['id_producto'])){
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


        if(!Pedido::obtenerPedido($params['id_pedido'])){
            $mensaje[] = ["error" => "ID de Pedido inexistente."];
            $error = true;
        }

        if(!Producto::obtenerProductoSegunId($params['id_producto'])){
            $mensaje[] = ["error" => "ID de Producto inexistente."];
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

    public function ParamsAsignarUno(Request $request, RequestHandler $handler) {

        $method = $request->getMethod();

        if($method === 'GET'){
            $params = $request->getQueryParams();    
        }
        
        if($method === 'POST'){
            $params = $request->getParsedBody();   
        }


        if(isset($params["id_item"])){
            $response = $handler->handle($request);
            } else {
                $response = new Response();
                $response->getBody()->write(json_encode(array("error" => "Parametros equivocados.")));
            }

        return $response;
    }

    public function ValidarAsignarUno(Request $request, RequestHandler $handler) {

        $method = $request->getMethod();

        if($method === 'GET'){
            $params = $request->getQueryParams();    
        }
        
        if($method === 'POST'){
            $params = $request->getParsedBody();   
        }
        $mensaje = [];
        
        $error = false;


        if(!ItemPedido::obtenerItemPedido($params['id_item'])){
            $mensaje[] = ["error" => "ID de Item Pedido inexistente."];
            $error = true;
        }

        elseif(ItemPedido::obtenerItemPedido($params['id_item'])->estado != 'pendiente'){
            $mensaje[] = ["error" => "El item no se encuentra pendiente."];
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

    public function ParamsModificarUno(Request $request, RequestHandler $handler) {

        $inputData = file_get_contents('php://input');
        $params = json_decode($inputData, true);


        if(isset($params["id"], $params['id_producto'])){
            $response = $handler->handle($request);
            } else {
                $response = new Response();
                $response->getBody()->write(json_encode(array("error" => "Parametros equivocados.")));
            }

        return $response;
    }

    public function ValidarModificarUno(Request $request, RequestHandler $handler) {

        $inputData = file_get_contents('php://input');
        $params = json_decode($inputData, true);

        $mensaje = [];
        
        $error = false;


        if(!ItemPedido::obtenerItemPedido($params['id'])){
            $mensaje[] = ["error" => "ID de Item Pedido inexistente."];
            $error = true;
        } elseif(ItemPedido::obtenerItemPedido($params['id'])->estado != "pendiente"){
            $mensaje[] = ["error" => "Item Pedido ya no se encuentra pendiente."];
            $error = true;
        }

        if(!Producto::obtenerProductoSegunId($params['id_producto'])){
            $mensaje[] = ["error" => "ID de Producto inexistente."];
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

    public function ParamsBorrarUno(Request $request, RequestHandler $handler) {

        $inputData = file_get_contents('php://input');
        $params = json_decode($inputData, true);


        if(isset($params["id"])){
            $response = $handler->handle($request);
            } else {
                $response = new Response();
                $response->getBody()->write(json_encode(array("error" => "Parametros equivocados.")));
            }

        return $response;
    }

    public function ValidarBorrarUno(Request $request, RequestHandler $handler) {

        $inputData = file_get_contents('php://input');
        $params = json_decode($inputData, true);

        $mensaje = [];
        
        $error = false;


        if(!ItemPedido::obtenerItemPedido($params['id'])){
            $mensaje[] = ["error" => "ID de Item Pedido inexistente."];
            $error = true;
        } elseif(ItemPedido::obtenerItemPedido($params['id'])->estado != "pendiente"){
            $mensaje[] = ["error" => "Item Pedido ya no se encuentra pendiente."];
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