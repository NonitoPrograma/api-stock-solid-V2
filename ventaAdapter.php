<?php
require_once "dto.php";

interface VentaAdapter {
    public function adaptar($json): ?DTO; //recibe el json como lo manda el sistema y devuelve nuestro DTO, o null si viene mal
}
?>
