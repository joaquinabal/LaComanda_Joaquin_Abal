<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once './models/Mesa.php';

class EncuestaMiddleware {

    public function ParamsEncuesta(Request $request, RequestHandler $handler) {

        $method = $request->getMethod();

        if($method === 'GET'){
            $params = $request->getQueryParams();    
        }
        
        if($method === 'POST'){
            $params = $request->getParsedBody();   
        }


        if(isset($params["mesa_puntaje"], $params["restaurante_puntaje"], $params["mozo_puntaje"], $params["cocinero_puntaje"], $params["bartender_puntaje"], $params["cervecero_puntaje"], $params["comentario"])){
            $response = $handler->handle($request);
            } else {
                $response = new Response();
                $response->getBody()->write(json_encode(array("error" => "Parametros equivocados.")));
            }

        return $response;
    }

    public function ValidarEncuesta(Request $request, RequestHandler $handler) {

        $method = $request->getMethod();

        $data = AutentificadorJWT::DevolverDataSegunHeader($request);

        if($method === 'GET'){
            $params = $request->getQueryParams();    
        }
        
        if($method === 'POST'){
            $params = $request->getParsedBody();   
        }
        $mensaje = [];
        
        $error = false;

        if(isset($params["mesa_puntaje"]) && !is_numeric($params["mesa_puntaje"])){
            $mensaje[] = ["error" => "Puntaje de mesa no numérico."];
            $error = true;
        } elseif($params["mesa_puntaje"] > 10){
            $mensaje[] = ["error" => "El puntaje de la mesa debe ser entre 1 y 10"];
            $error = true;
        }

        if(isset($params["restaurante_puntaje"]) && !is_numeric($params["restaurante_puntaje"])){
            $mensaje[] = ["error" => "Puntaje de restaurante no numérico."];
            $error = true;
        } elseif($params["restaurante_puntaje"] > 10){
            $mensaje[] = ["error" => "El puntaje del restaurante debe ser entre 1 y 10"];
            $error = true;
        }

        if(isset($params["cocinero_puntaje"]) && !is_numeric($params["cocinero_puntaje"])){
            $mensaje[] = ["error" => "Puntaje de cocinero no numérico."];
            $error = true;
        } elseif($params["cocinero_puntaje"] > 10){
            $mensaje[] = ["error" => "El puntaje del cocinero debe ser entre 1 y 10"];
            $error = true;
        }

        if(isset($params["bartender_puntaje"]) && !is_numeric($params["bartender_puntaje"])){
            $mensaje[] = ["error" => "Puntaje del bartender no numérico."];
            $error = true;
        } elseif($params["bartender_puntaje"] > 10){
            $mensaje[] = ["error" => "El puntaje del bartender debe ser entre 1 y 10"];
            $error = true;
        }

        if(isset($params["cervecero_puntaje"]) && !is_numeric($params["cervecero_puntaje"])){
            $mensaje[] = ["error" => "Puntaje de cervecero no numérico."];
            $error = true;
        } elseif($params["cervecero_puntaje"] > 10){
            $mensaje[] = ["error" => "El puntaje del cervecero debe ser entre 1 y 10"];
            $error = true;
        }

        if(!is_string($params["comentario"])){
            $mensaje[] = ["error" => "Comentario no es un string."];
            $error = true;
        } elseif(mb_strlen($params["comentario"], 'UTF-8') > 66){
            $mensaje[] = ["error" => "El comentario excede los 66 caracteres."];
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

    public function MejorComentario(Request $request, RequestHandler $handler) {

        $method = $request->getMethod();

        if($method === 'GET'){
            $params = $request->getQueryParams();    
        }
        
        if($method === 'POST'){
            $params = $request->getParsedBody();   
        }

        $array_filtro = ['mesa','restaurante','mozo','cocinero','cervecero','bartender'];

        if(isset($params["filtro"])){
            if(in_array($params['filtro'], $array_filtro)){
                $response = $handler->handle($request);
            } else {
                $response = new Response();
                $response->getBody()->write(json_encode(array("error" => "No se puede filtrar por ese valor.")));
            }
            } else {
                $response = new Response();
                $response->getBody()->write(json_encode(array("error" => "Parametros equivocados.")));
            }

        return $response;
    }

}