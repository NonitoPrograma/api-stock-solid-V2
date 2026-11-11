<?php
require_once "dto.php";

interface EstadisticasConnector {
    public function registrarEstadistica(DTO $venta): bool;
}
?>