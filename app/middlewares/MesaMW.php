<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once './models/Mesa.php';

class MesaMiddleware {

    public function ParamsIDMesa(Request $request, RequestHandler $handler) {

        $method = $request->getMethod();

        if($method === 'GET'){
            $params = $request->getQueryParams();    
        }
        
        if($method === 'POST'){
            $params = $request->getParsedBody();   
        }


        if(isset($params["id_mesa"])){
            $response = $handler->handle($request);
            } else {
                $response = new Response();
                $response->getBody()->write(json_encode(array("error" => "Parametros equivocados.")));
            }

        return $response;
    }

    public function ValidarCobrarMesa(Request $request, RequestHandler $handler) {

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

        if(!Mesa::obtenerMesa($params["id_mesa"])){
            $mensaje[] = ["error" => "ID Mesa inexistente."];
            $error = true;
        }

        elseif(Mesa::obtenerMesa($params["id_mesa"])->estado != "con cliente comiendo"){
            $mensaje[] = ["error" => "Esta mesa no se encuentra en estado para cobrar"];
            $error = true;
        }

        else{
            $pedidos = Pedido::obtenerPedidosSegunIDMesa($params["id_mesa"]);
            $mozoId = $data->id;
            $mesaAtendidaPorMozo = false;
            
            foreach ($pedidos as $pedido) {
                if ($pedido->id_mozo == $mozoId) {
                    $mesaAtendidaPorMozo = true;
                    break;
                }
            }
            
            if (!$mesaAtendidaPorMozo) {
                $mensaje[] = ["error" => "Esta mesa no se encuentra siendo atendida por el usuario."];
                $error = true;
            }
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

    public function ValidarCerrarMesa(Request $request, RequestHandler $handler) {

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

        elseif(Mesa::obtenerMesa($params["id_mesa"])->estado != "con cliente pagando"){
            $mensaje[] = ["error" => "Esta mesa no se encuentra en estado para cerrar"];
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

    public function ParamsFechaMinMax(Request $request, RequestHandler $handler): Response
    {
        $params = $request->getQueryParams();

        if (isset($params["fecha_inicio"], $params["fecha_final"])) {
            $response = $handler->handle($request);
        } else {
            $response = new Response();
            $payload = json_encode(array('mensaje' => 'Parametros erroneos.'));
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }


    public function ValoresFechaMinMax(Request $request, RequestHandler $handler): Response
    {
        $params = $request->getQueryParams();

        $fecha_inicio = $params["fecha_inicio"];
        $fecha_final = $params["fecha_final"];


        if (strtotime($fecha_inicio) !== false || strtotime($fecha_final) !== false) {
            $response = new Response();

            $payload = json_encode(array('mensaje' => 'Fechas invalidas.'));
            $response->getBody()->write($payload);
        } else if ($fecha_final < $fecha_inicio) {
            $response = new Response();
            $payload = json_encode(array('mensaje' => 'Fecha de Inicio mayor a Fecha Final.'));
            $response->getBody()->write($payload);
        } else {
            $response = $handler->handle($request);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
}