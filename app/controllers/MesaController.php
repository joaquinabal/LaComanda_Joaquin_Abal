<?php
require_once './models/Mesa.php';
require_once './interfaces/IApiUsable.php';

date_default_timezone_set('America/Argentina/Buenos_Aires');

class MesaController extends Mesa implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
                

        // Creamos la Mesa
        $mesa = new Mesa();
        $mesa->crearMesa();

        $payload = json_encode(array("mensaje" => "Mesa creada con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos mesa por nombre
        $arg_mesa = $args['mesa'];
        $mesa = Mesa::obtenerMesa($arg_mesa);
        $payload = json_encode($mesa);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Mesa::obtenerTodos();
        $payload = json_encode(array("Mesas" => $lista));
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerMesaMasUsada($request, $response, $args)
    {
        $mesa = Mesa::obtenerMesaMasUsada();
        $payload = json_encode(array("Mesa más Usada" => $mesa));
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerMesasOrdAscPorFactura($request, $response, $args)
    {
        $mesas = Mesa::obtenerMesasOrdAscPorFactura();
        $payload = json_encode(array("Mesas ordenadas por factura" => $mesas));
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerFacturacionPorFechas($request, $response, $args)
    {
        $params = $request->getQueryParams();
        $fecha_inicio = $params["fecha_inicio"];
        $fecha_final = $params["fecha_final"];
        $facturacion = Mesa::obtenerFacturacionPorFechas($fecha_inicio, $fecha_final);
        $payload = json_encode(array("Facturacion de Mesas" => $facturacion));
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    //obtenerFacturacionPorFechas

    public function ModificarUno($request, $response, $args)
    {
      $inputData = file_get_contents('php://input');
      $parametros = json_decode($inputData, true);

        $id = $parametros['id'];
        $codigo = $parametros['codigo'];
        Mesa::modificarMesa($id, $codigo);

        $payload = json_encode(array("mensaje" => "Mesa modificada con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
      $inputData = file_get_contents('php://input');
      $parametros = json_decode($inputData, true);

        $id = $parametros['id'];
        Mesa::borrarMesa($id);

        $payload = json_encode(array("mensaje" => "Mesa borrada con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}
