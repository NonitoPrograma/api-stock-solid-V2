<?php
header ("Content-Type: application/json; charset=UTF-8");
//INSTANCIAR TODAS LAS CLSES Q DIPARAS 
//SEGUIR EL ORDEN DE LAS INSTANCIASIONES 
//ULTIMA LINEA LLAMADA AL METODO DE CONTROLER 
require_once "movimientoController.php";
require_once "LogStockConnector.php";
require_once "erpStockConnector.php";
require_once "jsonVentaRepository.php";
require_once "sistemaA2Adapter.php";
require_once "SistemaCEstadisticasConnector.php";

$conector   = new LogStockConnector();   // para probar
// $conector = new ErpStockConnector();
$repositorio = new JsonVentaRepository();
$estadisticas  = new SistemaCEstadisticasConnector();
$service    = new movimientoService($conector, $repositorio, $estadisticas);
$controller = new movimientoController($service, new SistemaA2Adapter());

echo json_encode($controller->formato());
?>
