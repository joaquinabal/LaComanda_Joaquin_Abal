<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once './models/Mesa.php';
require_once './models/Producto.php';

class ProductoMiddleware {

    public function ParamsCargarUno(Request $request, RequestHandler $handler) {

        $method = $request->getMethod();

        if($method === 'GET'){
            $params = $request->getQueryParams();    
        }
        
        if($method === 'POST'){
            $params = $request->getParsedBody();   
        }


        if(isset($params["nombre"], $params['tipo'], $params['precio'], $params['tiempo'])){
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


        if(!is_string($params["nombre"])){
            $mensaje[] = ["error" => "El nombre debe ser un string."];
            $error = true;
        }

        $array_tipo = ['TragoVino','PlatoPrincipal','Postre','Cerveza'];

        if(!in_array($params['tipo'], $array_tipo)){
            $mensaje[] = ["error" => "El tipo de producto debe ser TragoVino, PlatoPrincipal, Postre o Cerveza."];
            $error = true;
        }

        if(!is_numeric($params['precio'])){
            $mensaje[] = ["error" => "El precio debe ser un valor numérico."];
            $error = true;
        }

        if(!is_numeric($params['tiempo'])){
            $mensaje[] = ["error" => "El tiempo debe ser un valor numérico."];
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

        if(isset($params["id"], $params["nombre"], $params['tipo'], $params['precio'], $params['tiempo'])){
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

        if(!Producto::obtenerProductoSegunId($params["id"])){
            $mensaje[] = ["error" => "El nombre debe ser un string."];
            $error = true;
        }

        if(!is_string($params["nombre"])){
            $mensaje[] = ["error" => "El nombre debe ser un string."];
            $error = true;
        }

        $array_tipo = ['TragoVino','PlatoPrincipal','Postre','Cerveza'];

        if(!in_array($params['tipo'], $array_tipo)){
            $mensaje[] = ["error" => "El tipo de producto debe ser TragoVino, PlatoPrincipal, Postre o Cerveza."];
            $error = true;
        }

        if(!is_numeric($params['precio'])){
            $mensaje[] = ["error" => "El precio debe ser un valor numérico."];
            $error = true;
        }

        if(!is_numeric($params['tiempo'])){
            $mensaje[] = ["error" => "El tiempo debe ser un valor numérico."];
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