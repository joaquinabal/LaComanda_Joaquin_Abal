<?php

require_once "./utils/AutentificadorJWT.php";

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class EmpleadoPedidosMiddleware
{

    public function __invoke(Request $request, RequestHandler $handler)
    {

        echo "Empleado Pedidos MW \n";

        $data = AutentificadorJWT::DevolverDataSegunHeader($request);

        if (chequearKeyValues($data->rol_empleado, "cocinero", "bartender", "cervecero", "socio")) {
            $response = $handler->handle($request);
        } else {
            $response = new Response();
            $response->getBody()->write(json_encode(array("error" => "Rol de Empleado erróneo.")));
            return $response;
        }
        return $response;
    }
}


