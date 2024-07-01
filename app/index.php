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
require_once './controllers/EstadisticasController.php';

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
require_once './middlewares/csvMW.php';


require_once './middlewares/PedidoMW.php';
require_once './middlewares/ItemsPedidoMW.php';
require_once './middlewares/ClienteMW.php';
require_once './middlewares/GeneralMW.php';
require_once './middlewares/MesaMW.php';
require_once './middlewares/EncuestaMW.php';
require_once './middlewares/UsuarioMW.php';
require_once './middlewares/ProductoMW.php';



// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

$app->setBasePath('/2024C1/TP/app');

// Add error middleware
$app->addErrorMiddleware(true, true, true);


// Routes
$app->group('/usuarios', function (RouteCollectorProxy $group) {
  $group->get('/todos', \UsuarioController::class . ':TraerTodos')->add(new SocioMiddleware)->add([new UsuarioMiddleware, "ChequearSuspensionODadoDeBaja"])->add(new AuthMiddleware);
  $group->post('/login', \UsuarioController::class . ':DevolverDataLogueo')->add([new LoggerMiddleware(), "Login"])->add(new UserLoggerMiddleware);
  $group->get('/buscar', \UsuarioController::class . ':TraerUno')->add(new LoggerMiddleware)->add(new SocioMiddleware)->add(new UserDBMiddleware)->add([new UsuarioMiddleware, "ChequearSuspensionODadoDeBaja"])->add(new AuthMiddleware);
    $group->get('/pendientes', \UsuarioController::class . ':ListarPendientes')->add(new LoggerMiddleware)->add(new EmpleadoPedidosMiddleware)->add([new UsuarioMiddleware, "ChequearSuspensionODadoDeBaja"])->add(new AuthMiddleware);
    $group->get('/en_preparacion', \UsuarioController::class . ':ListarEnPreparacion')->add(new LoggerMiddleware)->add([new GeneralMiddleware, "ConsultaSinParametros"])->add([new UsuarioMiddleware, "ChequearSuspensionODadoDeBaja"])->add(new AuthMiddleware);
    
    $group->post('[/]', \UsuarioController::class . ':CargarUno')->add(new LoggerMiddleware)->add(new UserParamsMiddleware)->add(new SocioMiddleware)->add([new UsuarioMiddleware, "ChequearSuspensionODadoDeBaja"])->add(new AuthMiddleware); 
    $group->post('/asignar_item', \UsuarioController::class . ':AsignarItemPedido')->add(new LoggerMiddleware)->add([new EmpleadoMiddleware(), "chequearAsignacionIP"])->add([new ItemPedidoMiddleware(), 'ValidarAsignarUno'])->add([new ItemPedidoMiddleware(), 'ParamsAsignarUno'])->add(new EmpleadoPedidosMiddleware)->add([new UsuarioMiddleware, "ChequearSuspensionODadoDeBaja"])->add(new AuthMiddleware);
    $group->post('/modificar_estado_pedido', \UsuarioController::class . ':ActualizarItemPedido')->add(new LoggerMiddleware)->add([new EmpleadoMiddleware(), "chequearAsignacionIP"])->add(new ModificacionEstadoMiddleware)->add([new UsuarioMiddleware, "ChequearSuspensionODadoDeBaja"])->add(new AuthMiddleware);
    $group->post('/modificar_estado_mesa', \UsuarioController::class . ':ActualizarMesa')->add(new AuthMiddleware)->add(new ModificacionEstadoMiddleware)->add(new LoggerMiddleware)->add(new MozoMiddleware)->add([new UsuarioMiddleware, "ChequearSuspensionODadoDeBaja"]) ->add(new AuthMiddleware);  
    $group->post('/cobrar_mesa', \UsuarioController::class . ':ActualizarMontoTotalDePedido')->add(new LoggerMiddleware)->add([new MesaMiddleware(), "ValidarCobrarMesa"])->add([new MesaMiddleware(), "ParamsIDMesa"])->add(new MozoMiddleware)->add([new UsuarioMiddleware, "ChequearSuspensionODadoDeBaja"])->add(new AuthMiddleware);
    $group->post('/socios/cerrar_mesa', \UsuarioController::class . ':CerrarMesa')->add(new LoggerMiddleware)->add([new MesaMiddleware(), "ValidarCerrarMesa"])->add([new MesaMiddleware(), "ParamsIDMesa"])->add(new SocioMiddleware)->add([new UsuarioMiddleware, "ChequearSuspensionODadoDeBaja"])->add(new AuthMiddleware);  
    $group->get('/socios/mejor_comentario', \EstadisticasController::class . ':MostrarMejorComentario')->add(new LoggerMiddleware)->add([new EncuestaMiddleware(), 'MejorComentario'])->add(new SocioMiddleware)->add([new UsuarioMiddleware, "ChequearSuspensionODadoDeBaja"])->add(new AuthMiddleware);  
    $group->get('/socios/peor_comentario', \EstadisticasController::class . ':MostrarMejorComentario')->add(new LoggerMiddleware)->add([new EncuestaMiddleware(), 'MejorComentario'])->add(new SocioMiddleware)->add([new UsuarioMiddleware, "ChequearSuspensionODadoDeBaja"])->add(new AuthMiddleware);  
    $group->put('/modificar', \UsuarioController::class . ':ModificarUno')->add(new SocioMiddleware)->add([new UsuarioMiddleware, "ChequearSuspensionODadoDeBaja"])->add(new AuthMiddleware);
    $group->delete('/borrar', \UsuarioController::class . ':BorrarUno')->add(new SocioMiddleware)->add([new UsuarioMiddleware, "ChequearSuspensionODadoDeBaja"])->add(new AuthMiddleware);

    $group->post('/socios/cambiar_suspension', \UsuarioController::class . ':ManejarSuspension')->add(new UsuarioMiddleware)->add(new SocioMiddleware)->add([new UsuarioMiddleware, "ChequearSuspensionODadoDeBaja"])->add(new AuthMiddleware);
    
    $group->get('/socios/PDF/mejor_comentario', \EstadisticasController::class . ':MostrarMejorComentarioYGenerarPDF')->add(new LoggerMiddleware)->add([new EncuestaMiddleware(), 'MejorComentario'])->add(new SocioMiddleware)->add([new UsuarioMiddleware, "ChequearSuspensionODadoDeBaja"])->add(new AuthMiddleware);  
  });

  $app->group('/clientes', function (RouteCollectorProxy $group) {
    $group->get('/info', \ClienteController::class . ':ListarTiempoEstimado')->add([new ClienteMiddleware(), "ValidarConsulta"])->add([new ClienteMiddleware(), "ParamsConsulta"]);
    $group->post('/encuesta', \ClienteController::class . ':CompletarEncuesta')->add([new EncuestaMiddleware(), "ValidarEncuesta"])->add([new EncuestaMiddleware(), "ParamsEncuesta"])->add([new ClienteMiddleware(), "ValidarConsulta"])->add([new ClienteMiddleware(), "ParamsConsulta"]);
  });

  $app->group('/productos', function (RouteCollectorProxy $group) {
    $group->get('[/]', \ProductoController::class . ':TraerTodos')->add(new LoggerMiddleware)->add([new UsuarioMiddleware, "ChequearSuspensionODadoDeBaja"])->add(new SocioMiddleware)->add(new AuthMiddleware);
    $group->get('/id/{producto}', \ProductoController::class . ':TraerUno')->add(new LoggerMiddleware)->add([new UsuarioMiddleware, "ChequearSuspensionODadoDeBaja"])->add(new SocioMiddleware)->add(new AuthMiddleware);
    $group->post('[/]', \ProductoController::class . ':CargarUno')->add(new LoggerMiddleware)->add([new ProductoMiddleware(), 'ValidarCargarUno'])->add([new ProductoMiddleware(), 'ParamsCargarUno'])->add([new UsuarioMiddleware, "ChequearSuspensionODadoDeBaja"])->add(new SocioMiddleware)->add(new AuthMiddleware);
    $group->post('/csv', \ProductoController::class . ':CargarCSV')->add(new LoggerMiddleware)->add(new CSVMiddleware)->add([new UsuarioMiddleware, "ChequearSuspensionODadoDeBaja"])->add(new SocioMiddleware)->add(new AuthMiddleware);
    $group->get('/csv/descargar', \ProductoController::class . ':DescargarCSV')->add(new LoggerMiddleware)->add([new UsuarioMiddleware, "ChequearSuspensionODadoDeBaja"])->add(new AuthMiddleware);
    $group->put('/modificar', \ProductoController::class . ':ModificarUno')->add(new LoggerMiddleware)->add([new ProductoMiddleware(), 'ValidarModificarUno'])->add([new ProductoMiddleware(), 'ParamsModificarUno'])->add([new UsuarioMiddleware, "ChequearSuspensionODadoDeBaja"])->add(new SocioMiddleware)->add(new AuthMiddleware);
    $group->delete('/borrar', \ProductoController::class . ':BorrarUno')->add(new LoggerMiddleware)->add([new UsuarioMiddleware, "ChequearSuspensionODadoDeBaja"])->add(new SocioMiddleware)->add(new AuthMiddleware);
    $group->get('/mas_vendidos', \ProductoController::class . ':listarProductosMasVendidos')->add(new LoggerMiddleware)->add([new UsuarioMiddleware, "ChequearSuspensionODadoDeBaja"])->add(new SocioMiddleware)->add(new AuthMiddleware);
    
  });

  $app->group('/mesas', function (RouteCollectorProxy $group) {
    $group->get('/mostrar/todas', \MesaController::class . ':TraerTodos')->add(new LoggerMiddleware)->add(new SocioMiddleware)->add([new UsuarioMiddleware, "ChequearSuspensionODadoDeBaja"])->add(new AuthMiddleware); 
    $group->get('/mostrar/{mesa}', \MesaController::class . ':TraerUno')->add(new LoggerMiddleware)->add([new UsuarioMiddleware, "ChequearSuspensionODadoDeBaja"])->add(new AuthMiddleware);
    $group->get('/mas_usada', \MesaController::class . ':TraerMesaMasUsada')->add(new LoggerMiddleware)->add(new SocioMiddleware)->add([new UsuarioMiddleware, "ChequearSuspensionODadoDeBaja"])->add(new AuthMiddleware);
    $group->post('[/]', \MesaController::class . ':CargarUno')->add(new LoggerMiddleware)->add(new SocioMiddleware)->add(new AuthMiddleware);
    $group->put('/modificar', \MesaController::class . ':ModificarUno')->add(new LoggerMiddleware)->add(new SocioMiddleware)->add(new AuthMiddleware);
    $group->delete('/borrar', \MesaController::class . ':BorrarUno')->add(new LoggerMiddleware)->add(new SocioMiddleware)->add(new AuthMiddleware);
    $group->put('/listos_para_servir', \UsuarioController::class . ':ListarListosParaServirYActualizarMesa')->add(new LoggerMiddleware)->add(new MozoMiddleware)->add([new UsuarioMiddleware, "ChequearSuspensionODadoDeBaja"])->add(new AuthMiddleware); 
    $group->get('/facturacion', \MesaController::class . ':TraerFacturacionPorFechas')->add(new LoggerMiddleware)->add(new SocioMiddleware)->add([new UsuarioMiddleware, "ChequearSuspensionODadoDeBaja"])->add(new AuthMiddleware);
    $group->get('/listado_por_factura', \MesaController::class . ':TraerMesasOrdAscPorFactura')->add(new LoggerMiddleware)->add(new SocioMiddleware)->add([new UsuarioMiddleware, "ChequearSuspensionODadoDeBaja"])->add(new AuthMiddleware);
  });

  $app->group('/pedidos', function (RouteCollectorProxy $group) {
    $group->get('/all', \PedidoController::class . ':TraerTodos')->add(new LoggerMiddleware)->add([new UsuarioMiddleware, "ChequearSuspensionODadoDeBaja"])->add(new AuthMiddleware);
    $group->get('/id/{pedido}', \PedidoController::class . ':TraerUno')->add(new LoggerMiddleware)->add([new UsuarioMiddleware, "ChequearSuspensionODadoDeBaja"])->add(new AuthMiddleware);
    $group->get('/fuera_de_hora', \PedidoController::class . ':ListarPedidosEntregadosFueraDeHora')->add(new LoggerMiddleware)->add([new UsuarioMiddleware, "ChequearSuspensionODadoDeBaja"])->add(new AuthMiddleware);
    $group->get('/en_preparacion', \PedidoController::class . ':ListarTotalEnPreparacion')->add(new LoggerMiddleware)->add([new GeneralMiddleware, "ConsultaSinParametros"])->add(new SocioMiddleware)->add([new UsuarioMiddleware, "ChequearSuspensionODadoDeBaja"])->add(new AuthMiddleware);;
    $group->get('/{pedido}/items', \PedidoController::class . ':TraerItemsPedido')->add(new LoggerMiddleware)->add(new AuthMiddleware);
    $group->post('[/]', \PedidoController::class . ':CargarUno')->add(new LoggerMiddleware)->add([new PedidoMiddleware(), 'ValidarCargarUno'])->add([new PedidoMiddleware(), 'ParamsCargarUno'])->add(new MozoMiddleware)->add([new UsuarioMiddleware, "ChequearSuspensionODadoDeBaja"])->add(new AuthMiddleware);
    $group->put('/modificar', \PedidoController::class . ':ModificarUno')->add(new LoggerMiddleware)->add(new SocioMiddleware)->add(new AuthMiddleware); 
    $group->delete('/cancelar', \PedidoController::class . ':CancelarUno')->add(new LoggerMiddleware)->add(new SocioMiddleware)->add(new AuthMiddleware); 
  });

  $app->group('/itemspedidos', function (RouteCollectorProxy $group) {
    $group->get('[/]', \ItemsPedidoController::class . ':TraerTodos')->add(new LoggerMiddleware)->add([new UsuarioMiddleware, "ChequearSuspensionODadoDeBaja"])->add(new AuthMiddleware);
    $group->get('/{pedido}', \ItemsPedidoController::class . ':TraerUno')->add(new LoggerMiddleware)->add([new UsuarioMiddleware, "ChequearSuspensionODadoDeBaja"])->add(new AuthMiddleware);
    $group->post('[/]', \ItemsPedidoController::class . ':CargarUno')->add(new LoggerMiddleware)->add([new ItemPedidoMiddleware(), 'ValidarCargarUno'])->add([new ItemPedidoMiddleware(), 'ParamsCargarUno'])->add(new MozoMiddleware)->add([new UsuarioMiddleware, "ChequearSuspensionODadoDeBaja"])->add(new AuthMiddleware);
    $group->put('/modificar', \ItemsPedidoController::class . ':ModificarUno')->add(new LoggerMiddleware)->add([new ItemPedidoMiddleware(), 'ValidarModificarUno'])->add([new ItemPedidoMiddleware(), 'ParamsModificarUno'])->add(new SocioMiddleware)->add(new AuthMiddleware); 
    $group->delete('/borrar', \ItemsPedidoController::class . ':BorrarUno')->add(new LoggerMiddleware)->add([new ItemPedidoMiddleware(), 'ValidarBorrarUno'])->add([new ItemPedidoMiddleware(), 'ParamsBorrarUno'])->add(new SocioMiddleware)->add(new AuthMiddleware); 
  });

  $app->group('/stats', function (RouteCollectorProxy $group) {
    $group->get('/cant_op_sector', \EstadisticasController::class . ':MostrarCantOperacionesPorSector');
    $group->get('/ingresos_por_usuario', \EstadisticasController::class . ':MostrarIngresosPorUsuario');
  });


$app->run();
