<?php
require_once "ventaRepository.php";

class JsonVentaRepository implements VentaRepository {
    private string $archivo = __DIR__ . '/ventas.json';

    public function guardar(DTO $venta): bool {
        $fp = fopen($this->archivo, 'c+'); //c+ lo crea si no existe y no lo borra si ya existe
        flock($fp, LOCK_EX); //bloquea el archivo, si llegan dos envios juntos el segundo espera

        $contenido = stream_get_contents($fp);
        $ventas = $contenido === '' ? [] : json_decode($contenido, true);

        $clave = 'VENTA-' . $venta->ventaId; //misma referencia q ya usamos

        if (isset($ventas[$clave])) { //ya estaba, no se guarda de nuevo
            flock($fp, LOCK_UN);
            fclose($fp);
            return false;
        }

        $productos = [];
        foreach ($venta->productos as $info) {
            $productos[] = [
                "codigo"         => $info->codigo,
                "cantidad"       => $info->cantidad,
                "precioUnitario" => $info->precioUnitario
            ];
        }

        $ventas[$clave] = [
            "ventaId"  => $venta->ventaId,
            "fecha"    => $venta->fecha,
            "cajeroId" => $venta->cajeroId,
            "estado"   => "pendiente", //los otros estados los vemos en reintentos
            "intentos" => 0,
            "productos" => $productos
        ];

        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, json_encode($ventas, JSON_PRETTY_PRINT));
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);
        return true;
    }

    public function sumarIntento($ventaId): int {
        $intentos = 0;
        $this->modificar($ventaId, function ($venta) use (&$intentos) {
            $venta['intentos']++;
            $intentos = $venta['intentos'];
            return $venta;
        });
        return $intentos;
    }

    public function cambiarEstado($ventaId, string $estado): void {
        $this->modificar($ventaId, function ($venta) use ($estado) {
            $venta['estado'] = $estado;
            return $venta;
        });
    }

    //abre el archivo, cambia una sola venta y lo vuelve a guardar (con el mismo bloqueo q usa guardar)
    private function modificar($ventaId, callable $cambio): void {
        $fp = fopen($this->archivo, 'c+');
        flock($fp, LOCK_EX);

        $contenido = stream_get_contents($fp);
        $ventas = $contenido === '' ? [] : json_decode($contenido, true);
        $clave = 'VENTA-' . $ventaId;

        if (isset($ventas[$clave])) {
            $ventas[$clave] = $cambio($ventas[$clave]);
            ftruncate($fp, 0);
            rewind($fp);
            fwrite($fp, json_encode($ventas, JSON_PRETTY_PRINT));
            fflush($fp);
        }

        flock($fp, LOCK_UN);
        fclose($fp);
    }
    public function obtenerPendientes(): array {
        return $this->obtenerPorEstado('pendiente');
    }

    public function obtenerPorEstado(string $estado): array{
        if (!file_exists($this->archivo)){
            return [];
        }
    $contenido = file_get_contents($this->archivo);
    $ventas = json_decode($contenido, true) ?? [];
    
    $resultado = [];
    foreach ($ventas as $data) {
        if(($data['estado'] ?? '') === $estado) {
            $resultado[] = $data;
        }
    }
    return $resultado;
}
}
?>
