<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class UserLoggerMiddleware {

    public function __invoke(Request $request, RequestHandler $handler) {
        
        $params = $request->getParsedBody();
        if(isset($params["usuario"], $params["clave"])){ 
            if(Usuario::obtenerUsuario($params["usuario"])->fecha_baja || (Usuario::obtenerUsuario($params["usuario"])->suspendido == 1)){
                $response = new Response();
                $response->getBody()->write(json_encode(array("error" => "Usuario dado de baja o suspendido.")));
                return $response;
            } else {    
            } 
            if(password_verify($params["clave"], Usuario::obtenerUsuario($params["usuario"])->contraseña)){
                $response = $handler->handle($request);
            } else {
                $response = new Response();
                $response->getBody()->write(json_encode(array("error" => "Contraseña errónea")));
                return $response;
            }
        } else {
            $response = new Response();
            $response->getBody()->write(json_encode(array("error" => "Parametros equivocados.")));
            return $response;
        }
        
        echo "Salgo del MW User Params \n";
        return $response;

    }
}