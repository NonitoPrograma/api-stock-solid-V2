<?php
require_once "EstadisticasConnector.php";

class SistemaCEstadisticasConnector implements EstadisticasConnector {
    private string $url;

    public function __construct(string $url = "http://localhost:4000/api/estadisticas") {
        $this->url = $url;
    }

    public function registrarEstadistica(DTO $venta): bool {
        $payload = [
            "ventaId"           => (int)$venta->ventaId,
            "cantidadProductos" => $venta->getCantidadTotalProductos(),
            "importeTotal"      => $venta->getImporteTotal()
        ];

        $opciones = [
            'http' => [
                'method'        => 'POST',
                'header'        => "Content-Type: application/json\r\n",
                'content'       => json_encode($payload),
                'timeout'       => 2,
                'ignore_errors' => true,
            ]
        ];

        $contexto = stream_context_create($opciones);
        $respuesta = @file_get_contents($this->url, false, $contexto);

        return $respuesta !== false;
    }
}
?>