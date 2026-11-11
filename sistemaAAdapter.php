<?php
require_once "ventaAdapter.php";

class SistemaAAdapter implements VentaAdapter {
    public function adaptar($json): ?DTO {
        //esta validacion antes estaba en el controller, ahora cada sistema valida lo suyo
        if ($json === null
            || !isset($json->ventaId, $json->fecha, $json->cajeroId, $json->productos)
            || !is_array($json->productos)) {
            return null;
        }
        return new DTO($json->ventaId, $json->fecha, $json->cajeroId, $json->productos);
    }
}
?>
