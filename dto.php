<?php
require_once "productoModel.php";

class DTO {
    public $ventaId;
    public $fecha;
    public $cajeroId;
    public $productos = [];

    public function __construct($ventaId, $fecha, $cajeroId, array $productos) {
        $this->ventaId  = $ventaId;
        $this->fecha    = $fecha;
        $this->cajeroId = $cajeroId;
        foreach ($productos as $info) {
            $precio = isset($info->precioUnitario) ? (float)$info->precioUnitario : 0.0;
            $this->productos[] = new producto($info->codigo, (int)$info->cantidad, $precio);
        }
    }
    public function getCantidadTotalProductos(): int {
    $total = 0;
    foreach ($this->productos as $prod){
        $total += $prod->cantidad;
    }
    return $total;
}

public function getImporteTotal(): float {
    $total = 0.0;
    foreach ($this->productos as $prod) {
        $total += ($prod->cantidad * $prod->precioUnitario);
    }
    return $total;
  }
}


?>