<?php
require_once './models/Cliente.php';
require_once './models/Empleado.php';
require_once './interfaces/IApiUsable.php';
require_once './models/ItemsPedido.php';
require_once './models/Encuesta.php';
require_once './models/Pedido.php';
require_once './utils/AutentificadorJWT.php';
class ClienteController
{ 
    public function ListarTiempoEstimado($request, $response, $args) {
        $params = $request->getQueryParams();
        $codigo_mesa = $params['codigo_mesa'];
        $codigo_pedido = $params['codigo_pedido'];
        $tiempo_estimado = Cliente::ObtenerTiempoEstimadoSegunPedidoYMesa($codigo_pedido, $codigo_mesa);
        var_dump($tiempo_estimado);
      $payload = json_encode(array("Informacion Pedido" => $tiempo_estimado));
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function completarEncuesta($request, $response, $args) {
        $params = $request->getParsedBody();

        $encuesta = new Encuesta();

        $codigo_pedido = $params['codigo_pedido'];
        $codigo_mesa = $params['codigo_mesa'];
        $id_pedido = Pedido::obtenerPedidoSegunCodigo($codigo_pedido);
        $mesa_puntaje = $params['mesa_puntaje'];
        $restaurante_puntaje = $params['restaurante_puntaje'];;
        $mozo_puntaje = $params['mozo_puntaje'];
        $cocinero_puntaje = $params['cocinero_puntaje'];
        $bartender_puntaje = $params['bartender_puntaje'];
        $cervecero_puntaje = $params['cervecero_puntaje'];
        $comentario = $params['comentario'];

        $encuesta->setIdPedido($id_pedido);
        $encuesta->setMesaPuntaje($mesa_puntaje);
        $encuesta->setRestaurantePuntaje($restaurante_puntaje);
        $encuesta->setMozoPuntaje($mozo_puntaje);
        $encuesta->setCocineroPuntaje($cocinero_puntaje);
        $encuesta->setBartenderPuntaje($bartender_puntaje);
        $encuesta->setCerveceroPuntaje($cervecero_puntaje);
        $encuesta->setComentario($comentario);

        $encuesta->generarEncuesta();

        $payload = json_encode(array("mensaje" => "Encuesta generada"));
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}
