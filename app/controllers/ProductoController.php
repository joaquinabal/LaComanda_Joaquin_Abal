<?php
require_once './models/Producto.php';
require_once './interfaces/IApiUsable.php';
require_once './utils/Archivos.php';

date_default_timezone_set('America/Argentina/Buenos_Aires');

class ProductoController extends Producto
{
  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $nombre = $parametros['nombre'];
    $tipo = $parametros['tipo'];
    $precio = $parametros['precio'];
    $tiempo = $parametros['tiempo'];


    // Creamos el usuario
    $prod = new Producto();
    $prod->setNombre($nombre);
    $prod->setTipo($tipo);
    $prod->setPrecio($precio);
    $prod->setTiempo($tiempo);
    $prod->crearProducto();

    $payload = json_encode(array("mensaje" => "Producto creado con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function CargarCSV($request, $response, $args)
  {
    $archivo = $_FILES['datos_productos']['tmp_name'];


    if (($handle = fopen($archivo, 'r')) !== FALSE) {

      $cabecera = fgetcsv($handle, 10000, ',');


      while (($data = fgetcsv($handle, 10000, ',')) !== FALSE) {

        $fila = array_combine($cabecera, $data);
        $prod = new Producto();
        $prod->setNombre($fila['nombre']);
        $prod->setTipo($fila['tipo']);
        $prod->setPrecio($fila['precio']);
        $prod->setTiempo($fila['tiempo']);
        if(!Producto::obtenerProducto($fila['nombre'])){
          $prod->crearProducto();
        } else {
          $prod->modificarPrecioTiempoSegunNombre();
        }
      }
      fclose($handle);
      $payload = json_encode(array("mensaje" => "Productos cargados con exito"));
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    } else {
      echo "No se puede abrir el archivo.";
    }
  }

  public function DescargarCSV($request, $response, $args){
    $datos = Producto::obtenerTodosSinID();
    Archivos::descargarCSV($datos);
    $payload = json_encode(array("mensaje" => "Descarga exitosa."));
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
  }

  public function listarProductosMasVendidos($request, $response, $args){
  $lista = Producto::obtenerProductosMasVendidos();
  $payload = json_encode(array("Productos" => $lista));
  $response->getBody()->write($payload);
  return $response
    ->withHeader('Content-Type', 'application/json');

  }

  public function TraerUno($request, $response, $args)
  {
    // Buscamos producto por nombre
    $prod = $args['producto'];
    $producto = Producto::obtenerProducto($prod);
    $payload = json_encode($producto);

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $lista = Producto::obtenerTodos();
    $payload = json_encode(array("listaProducto" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function ModificarUno($request, $response, $args)
  {
    $inputData = file_get_contents('php://input');
    $parametros = json_decode($inputData, true);


    $id = $parametros['id'];

    $nombre = $parametros['nombre'];
    $tipo = $parametros['tipo'];
    $precio = $parametros['precio'];
    $tiempo = $parametros['tiempo'];


    $prod = new Producto();
    $prod->setNombre($nombre);
    $prod->setTipo($tipo);
    $prod->setPrecio($precio);
    $prod->setTiempo($tiempo);

    $prod->modificarProducto($id);

    $payload = json_encode(array("mensaje" => "Producto modificado con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function BorrarUno($request, $response, $args)
  {
    $inputData = file_get_contents('php://input');
    $parametros = json_decode($inputData, true);

    $id = $parametros['id'];
    Producto::borrarProducto($id);

    $payload = json_encode(array("mensaje" => "Producto borrado con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
}
