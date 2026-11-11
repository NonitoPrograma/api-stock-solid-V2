<?php
// reintentar.php
require_once "jsonVentaRepository.php";
require_once "erpStockConnector.php";
require_once "movimientoService.php";

$repositorio = new JsonVentaRepository();
$conector = new ErpStockConnector();
$service = new movimientoService($conector, $repositorio);

$pendientes = $repositorio->obtenerPendientes();

echo "Iniciando reintento de " . count($pendientes) . " ventas pendientes...\n";

foreach ($pendientes as $ventaData) {
    if ($ventaData['intentos'] >= movimientoService::MAX_INTENTOS) {
        continue;
    }

    $productos = [];
    foreach ($ventaData['productos'] as $p) {
        $productos[] = (object)[
            'codigo'         => $p['codigo'],
            'cantidad'       => $p['cantidad'],
            'precioUnitario' => $p['precioUnitario'] ?? 0
        ];
    }
    
    $dto = new DTO($ventaData['ventaId'], $ventaData['fecha'], $ventaData['cajeroId'], $productos);

    echo "Procesando venta ID: {$dto->ventaId} (Intento " . ($ventaData['intentos'] + 1) . ")...\n";
    $exito = $service->enviar($dto);

    if ($exito) {
        echo " -> Venta {$dto->ventaId} procesada exitosamente en ERP B.\n";
    } else {
        echo " -> Fallo al enviar venta {$dto->ventaId}.\n";
    }
}
?>