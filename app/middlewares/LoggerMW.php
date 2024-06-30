<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;


require_once './utils/AutentificadorJWT.php';
require_once './utils/Logger.php';
class LoggerMiddleware
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger();
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {   
        $method = $request->getMethod();
        
        $data = AutentificadorJWT::DevolverDataSegunHeader($request);
        
        $id = $data->id;
        $usuario = $data->usuario;
        $nombre = $data->nombre;
        $rol_empleado = $data->rol_empleado;  

        $response = $handler->handle($request);

        $path = $request->getUri()->getPath();


        
        $this->logger->log($path, $method, $id, $usuario, $nombre, $rol_empleado);

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function Login(Request $request, RequestHandler $handler): Response
    {
        $response = $handler->handle($request);

        // Acceder al cuerpo de la respuesta
        $body = $response->getBody();
        $payload = (string) $body; // Convertir el cuerpo a string
        $body->rewind(); // Rebobinar el flujo para futuras lecturas/escrituras

        // Decodificar el JSON del payload
        $data = json_decode($payload, true);

        if (isset($data['jwt'])) {
            // Procesar el token JWT
            $jwt = $data['jwt'];
            $decodedData = AutentificadorJWT::ObtenerData($jwt);

            $id = $decodedData->id;
            $usuario = $decodedData->usuario;
            $nombre = $decodedData->nombre;
            $rol_empleado = $decodedData->rol_empleado;

            $method = $request->getMethod();
            $path = $request->getUri()->getPath();

    $this->logger->log($path, $method, $id, $usuario, $nombre, $rol_empleado);


    }
    return $response->withHeader('Content-Type', 'application/json');

}
}


