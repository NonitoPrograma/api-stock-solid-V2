<?php
require_once "movimientoService.php";
require_once "ventaAdapter.php";

class movimientoController {
    private movimientoService $service;
    private VentaAdapter $adapter;

    public function __construct(movimientoService $service, VentaAdapter $adapter) {
        $this->service = $service;
        $this->adapter = $adapter;
    }


    public function formato() {
        $movimiento = json_decode(file_get_contents('php://input'));

        $venta = $this->adapter->adaptar($movimiento); //el adapter lo pasa a nuestro formato

        if ($venta === null) {
            http_response_code(400);
            return ["error" => "JSON invalido o incompleto"];
        }


        if (!$this->service->procesarVenta($venta)) {
            http_response_code(502);
            return ["error" => "No se pudo registrar en el sistema B"];
        }

        return ["ok" => true, "referencia" => "VENTA-" . $venta->ventaId];
    }
}
