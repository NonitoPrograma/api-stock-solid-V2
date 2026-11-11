<?php
require_once "stockConnector.php";
class ErpStockConnector implements StockConnector {
    public function registrarMovimiento(array $movimiento): bool {
        $opciones = [
            'http' => [
                'method'        => 'POST',
                'header'        => 'Content-Type: application/json',
                'content'       => json_encode($movimiento),
                'ignore_errors' => true,
            ]
        ];
        $contexto  = stream_context_create($opciones);
        $respuesta = @file_get_contents('http://localhost:3000/api/stock/movimiento', false, $contexto);
//esto hace de q si no hubo conexion, devuelva falso, pero si hubo devuelve true.
        if ($respuesta === false) {
            return false;
        }
            return true;
    }
    //REQUIRED 
}
?>