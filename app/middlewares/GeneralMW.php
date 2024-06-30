<?php


use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;


class GeneralMiddleware
{
    public function ConsultaSinParametros(Request $request, RequestHandler $handler): Response
    {
        $params = $request->getQueryParams();
        if (!$params) {
            $response = $handler->handle($request);
        } else {
            $response = new Response();
            $payload = json_encode(array('mensaje' => 'La consulta no requiere parametros.'));
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
}