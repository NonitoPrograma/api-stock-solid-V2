<?php
require_once "stockConnector.php";
class LogStockConnector implements StockConnector {
    public function registrarMovimiento(array $movimiento): bool {
        file_put_contents('stock.log', json_encode($movimiento) . PHP_EOL, FILE_APPEND);//recordatorios: php_eol, salto de linea para q cada venta quede separada. file_append: agrega al final del archivo las cosas en vez de borrarlas
        return true;
    }
}//REQURED
?>