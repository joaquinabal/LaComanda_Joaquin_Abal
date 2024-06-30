<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once "./models/Usuario.php";
require_once "./models/Producto.php";
require_once "./models/Pedido.php";
require_once "./models/ItemsPedido.php";
require_once "./utils/AutentificadorJWT.php";
class UsuarioMiddleware
{

    public function __invoke(Request $request, RequestHandler $handler)
    {

        $method = $request->getMethod();

        if($method === 'GET'){
            $params = $request->getQueryParams();    
        }
        
        if($method === 'POST'){
            $params = $request->getParsedBody();   
        }

        
        if (isset($params['id_usuario'])) {
            if (Usuario::obtenerUsuarioPorID($params['id_usuario'])){
                if(!Usuario::obtenerUsuario($params['id_usuario'])->fecha_baja){
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

    public function ChequearSuspensionODadoDeBaja(Request $request, RequestHandler $handler)
    {

        $data = AutentificadorJWT::DevolverDataSegunHeader($request);
        if($data->fecha_baja){
            $response = new Response();
            $response->getBody()->write(json_encode(array("error" => "Usuario dado de baja.")));
            return $response;
        } elseif($data->suspendido == 1){
            $response = new Response();
            $response->getBody()->write(json_encode(array("error" => "Usuario suspendido.")));
            return $response;
        } else{
            $response = $handler->handle($request);            
        }
        return $response;
    }
}