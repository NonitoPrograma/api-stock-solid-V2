<?php

interface StockConnector {
    public function registrarMovimiento(array $movimiento): bool;
}
?>