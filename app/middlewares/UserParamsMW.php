<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class UserParamsMiddleware {

    public function __invoke(Request $request, RequestHandler $handler) {
        
        echo "User Params MW \n";
        
        $params = $request->getParsedBody();
        if(isset($params["usuario"], $params["clave"], $params["nombre"], $params["rol_empleado"])){
            if(chequearKeyValues($params["rol_empleado"], "mozo", "cocinero", "bartender", "cervecero")){
                $response = $handler->handle($request);
            } else {
                $response = new Response();
                $response->getBody()->write(json_encode(array("error" => "Rol de Empleado erróneo.")));
            }
        } else {
            $response = new Response();
            $response->getBody()->write(json_encode(array("error" => "Parametros equivocados.")));
        }
        echo "Salgo del MW User Params \n";
        return $response;
    }
}

function chequearKeyValues($key, ...$values) {
    foreach ($values as $value) {
        if ($key === $value) {
            return true;
        }
    }
    return false;
}