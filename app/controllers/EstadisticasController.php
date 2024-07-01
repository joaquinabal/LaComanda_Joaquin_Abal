<?php

use function Symfony\Component\Clock\now;

require_once './models/Cliente.php';
require_once './models/Empleado.php';
require_once './interfaces/IApiUsable.php';
require_once './models/ItemsPedido.php';
require_once './models/Encuesta.php';
require_once './models/Pedido.php';
require_once './models/Encuesta.php';
require_once './utils/AutentificadorJWT.php';
require_once './utils/PDFGenerador.php';



date_default_timezone_set('America/Argentina/Buenos_Aires');

class EstadisticasController
{ 
    public function MostrarMejorComentario($request, $response, $args) {
        $params = $request->getQueryParams();


        $consulta = $params['filtro'];
        $resultado = "";

        switch ($consulta) {
            case 'mesa':
                $resultado = Encuesta::obtenerComentarioSegunMejorPuntajeMesa();
                break;
            
            case 'restaurante':
                $resultado = Encuesta::obtenerComentarioSegunMejorPuntajeRestaurante();
                break;

            case 'mozo':
                $resultado = Encuesta::obtenerComentarioSegunMejorPuntajeMozo();
                break;

            case 'cocinero':
                $resultado = Encuesta::obtenerComentarioSegunMejorPuntajeCocinero();
                break;

            case 'bartender':
                $resultado = Encuesta::obtenerComentarioSegunMejorPuntajeBartender();
                break;

            case 'cervecero':
                $resultado = Encuesta::obtenerComentarioSegunMejorPuntajeCervecero();
                break;
        }

        $payload = json_encode(array("Mejor Comentario" => $resultado));
        $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function MostrarPeorComentario($request, $response, $args) {
        $params = $request->getQueryParams();


        $consulta = $params['filtro'];
        $resultado = "";

        switch ($consulta) {
            case 'mesa':
                $resultado = Encuesta::obtenerComentarioSegunPeorPuntajeMesa();
                break;
            
            case 'restaurante':
                $resultado = Encuesta::obtenerComentarioSegunPeorPuntajeRestaurante();
                break;

            case 'mozo':
                $resultado = Encuesta::obtenerComentarioSegunPeorPuntajeMozo();
                break;

            case 'cocinero':
                $resultado = Encuesta::obtenerComentarioSegunPeorPuntajeCocinero();
                break;

            case 'bartender':
                $resultado = Encuesta::obtenerComentarioSegunPeorPuntajeBartender();
                break;

            case 'cervecero':
                $resultado = Encuesta::obtenerComentarioSegunPeorPuntajeCervecero();
                break;
        }

        $payload = json_encode(array("Peor Comentario" => $resultado));
        $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }


    public function MostrarMejorComentarioYGenerarPDF($request, $response, $args) {
        $params = $request->getQueryParams();

        $pdf = new PDFGenerator();

        $consulta = $params['filtro'];
        $resultado = "";

        switch ($consulta) {
            case 'mesa':
                $resultado = Encuesta::obtenerComentarioSegunMejorPuntajeMesa();
                break;
            
            case 'restaurante':
                $resultado = Encuesta::obtenerComentarioSegunMejorPuntajeRestaurante();
                break;

            case 'mozo':
                $resultado = Encuesta::obtenerComentarioSegunMejorPuntajeMozo();
                break;

            case 'cocinero':
                $resultado = Encuesta::obtenerComentarioSegunMejorPuntajeCocinero();
                break;

            case 'bartender':
                $resultado = Encuesta::obtenerComentarioSegunMejorPuntajeBartender();
                break;

            case 'cervecero':
                $resultado = Encuesta::obtenerComentarioSegunMejorPuntajeCervecero();
                break;
        }

        $fecha = new DateTime(date("d-m-Y H:i:s"));



        $pdf->generatePDFFromAssocArray($resultado, 'Reporte-MejorComentario-' . $params['filtro'] . date_format($fecha, 'Y-m-d H:i:s') . ".pdf", 'Reporte - Mejor Comentario - ' . $params['filtro']);

        $payload = json_encode(array("Mejor Comentario" => $resultado));
        $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    
    public function MostrarCantOperacionesPorSector($request, $response, $args) {
        $array = Archivos::cargarArchivoFiltrado("./log.json", "rol_empleado");
        
        $counts = [
            "socio" => 0,
            "mozo" => 0,
            "cocinero" => 0,
            "bartender" => 0,
            "cervecero" => 0
        ];

        foreach ($array as $rol) {
            if (array_key_exists($rol, $counts)) {
                $counts[$rol]++;
            }
        }
        $payload = json_encode(array("cant_operaciones" => $counts));
        $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    
    }

    public function MostrarIngresosPorUsuario($request, $response, $args) {
        $array = Archivos::cargarArchivo("./log.json");

        $empleados = [];

        foreach ($array as $value) {
            if ($value['accion'] == "/2024C1/TP/app/usuarios/login") {
                $empleados[$value["usuario"]] = $value["fecha"];
            }
        }
        $payload = json_encode(array("cant_operaciones" => $empleados));
        $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    
    }
}
