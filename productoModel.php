<?php

class producto{
    public $codigo;
    public $cantidad;
    public $precioUnitario;

 public function __construct($codigo,$cantidad,$precioUnitario) {
    $this-> codigo = $codigo;
    $this-> cantidad = $cantidad;
    $this-> precioUnitario = $precioUnitario;
}
}
?>