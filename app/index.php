<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';

require_once './db/AccesoDatos.php';
// require_once './middlewares/Logger.php';

require_once './controllers/UsuarioController.php';
require_once './controllers/ClienteController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/MesaController.php';
require_once './controllers/PedidoController.php';
require_once './controllers/ItemsPedidoController.php';

require_once './middlewares/UserParamsMW.php';
require_once './middlewares/MozoMW.php';
require_once './middlewares/EmpleadoMW.php';
require_once './middlewares/SocioMW.php';
require_once './middlewares/UserLoggerMW.php';
require_once './middlewares/LoggerMW.php';
require_once './middlewares/ModificacionEstadoMW.php';
require_once './middlewares/AuthMW.php';
require_once './middlewares/UserDBMW.php';
require_once './middlewares/EmpleadoPedidosMW.php';


// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

$app->setBasePath('/2024C1/TP/app');

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Routes
$app->group('/usuarios', function (RouteCollectorProxy $group) {
  $group->get('/todos', \UsuarioController::class . ':TraerTodos')->add(new AuthMiddleware)->add(new SocioMiddleware);
  $group->post('/login', \UsuarioController::class . ':DevolverDataLogueo')->add(new UserLoggerMiddleware)->add(new LoggerMiddleware);
  $group->get('/buscar/{usuario}', \UsuarioController::class . ':TraerUno')->add(new AuthMiddleware)->add(new SocioMiddleware)->add(new UserDBMiddleware);
    $group->get('/pendientes', \UsuarioController::class . ':ListarPendientes')->add(new AuthMiddleware)->add(new EmpleadoPedidosMiddleware);
    $group->get('/en_preparacion', \UsuarioController::class . ':ListarEnPreparacion')->add(new AuthMiddleware);
    $group->get('/listos_para_servir', \UsuarioController::class . ':ListarListosParaServirYActualizarMesa')->add(new AuthMiddleware)->add(new MozoMiddleware); 
    $group->post('[/]', \UsuarioController::class . ':CargarUno')->add(new UserParamsMiddleware);
    $group->post('/asignar_item', \UsuarioController::class . ':AsignarItemPedido')->add(new AuthMiddleware);
    $group->post('/modificar_estado_pedido', \UsuarioController::class . ':ActualizarItemPedido')->add(new AuthMiddleware)->add(new EmpleadoMiddleware)->add(new ModificacionEstadoMiddleware);
    $group->post('/modificar_estado_mesa', \UsuarioController::class . ':ActualizarMesa')->add(new AuthMiddleware)->add(new MozoMiddleware)->add(new ModificacionEstadoMiddleware);  
    $group->put('/cobrar_mesa', \UsuarioController::class . 'ActualizarMontoTotalDePedido')->add(new AuthMiddleware)->add(new MozoMiddleware);
    $group->post('/socio/cerrar_mesa', \UsuarioController::class . ':CerrarMesa')->add(new AuthMiddleware)->add(new SocioMiddleware)->add(new ModificacionEstadoMiddleware);  
    
  });

  $app->group('/clientes', function (RouteCollectorProxy $group) {
    $group->get('/info', \ClienteController::class . ':ListarTiempoEstimado');
  });

  $app->group('/productos', function (RouteCollectorProxy $group) {
    $group->get('[/]', \ProductoController::class . ':TraerTodos');
    $group->get('/{producto}', \ProductoController::class . ':TraerUno');
    $group->post('[/]', \ProductoController::class . ':CargarUno');
    $group->post('/csv', \ProductoController::class . ':CargarCSV');
    $group->get('/csv/descargar', \ProductoController::class . ':DescargarCSV');
  });

  $app->group('/mesas', function (RouteCollectorProxy $group) {
    $group->get('[/]', \MesaController::class . ':TraerTodos');
    $group->get('/{mesa}', \MesaController::class . ':TraerUno');
    $group->post('[/]', \MesaController::class . ':CargarUno');
  });

  $app->group('/pedidos', function (RouteCollectorProxy $group) {
    $group->get('/all', \PedidoController::class . ':TraerTodos');
    $group->get('/id/{pedido}', \PedidoController::class . ':TraerUno');
    $group->get('/en_preparacion', \PedidoController::class . ':ListarTotalEnPreparacion');
    $group->get('/{pedido}/items', \PedidoController::class . ':TraerItemsPedido');
    $group->post('[/]', \PedidoController::class . ':CargarUno');
  });

  $app->group('/itemspedidos', function (RouteCollectorProxy $group) {
    $group->get('[/]', \ItemsPedidoController::class . ':TraerTodos');
    $group->get('/{pedido}', \ItemsPedidoController::class . ':TraerUno');
    $group->post('[/]', \ItemsPedidoController::class . ':CargarUno')/*->add(new MozoMiddleware)->add(new AuthMiddleware)*/;
  });

$app->get('[/]', function (Request $request, Response $response) {    
    $payload = json_encode(array("mensaje" => "Slim Framework 4 PHP"));
    
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
