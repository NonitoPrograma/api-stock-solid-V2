<?php
require_once "stockConnector.php";
require_once "dto.php";
require_once "ventaRepository.php";
require_once "EstadisticasConnector.php";

class movimientoService {
    private StockConnector $conector;
    private VentaRepository $repositorio;
    private ?EstadisticasConnector $estadisticasConnector;

    const MAX_INTENTOS = 3;

    public function __construct(
        StockConnector $conector, 
        VentaRepository $repositorio,
        ?EstadisticasConnector $estadisticasConnector = null
    ) {
        $this->conector = $conector;
        $this->repositorio = $repositorio;
        $this->estadisticasConnector = $estadisticasConnector;
    }

    public function procesarVenta(DTO $venta): bool {
        // Idempotencia: Si ya estaba guardada previamente, no descuenta stock de nuevo
        if (!$this->repositorio->guardar($venta)) {
            return true;
        }

        // 1. Envío al ERP B (Stock)
        $this->enviar($venta);

        // 2. Envío al Sistema C (Estadísticas) de forma aislada
        $this->notificarEstadisticas($venta);

        return true;
    }

    public function enviar(DTO $venta): bool {
        $movimiento = $this->cambioFormato($venta);
        $ok = $this->conector->registrarMovimiento($movimiento);

        $intentos = $this->repositorio->sumarIntento($venta->ventaId);
        if ($ok) {
            $this->repositorio->cambiarEstado($venta->ventaId, 'procesada');
        } elseif ($intentos >= self::MAX_INTENTOS) {
            $this->repositorio->cambiarEstado($venta->ventaId, 'fallida');
        }
        return $ok;
    }

    private function notificarEstadisticas(DTO $venta): void {
        if ($this->estadisticasConnector !== null) {
            try {
                $this->estadisticasConnector->registrarEstadistica($venta);
            } catch (Throwable $e) {
                error_log("Error al notificar al Sistema C: " . $e->getMessage());
            }
        }
    }

    private function cambioFormato(DTO $venta): array {
        $stock = [];
        foreach ($venta->productos as $info) {
            $stock[] = [
                "codigoProducto" => $info->codigo,
                "cantidad"       => $info->cantidad
            ];
        }

        return [
            "tipoMovimiento" => 'SALIDA',
            "fecha"          => $venta->fecha,
            "referencia"     => 'VENTA-' . $venta->ventaId,
            "productos"      => $stock
        ];
    }
}
?>