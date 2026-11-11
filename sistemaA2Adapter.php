<?php
require_once "ventaAdapter.php";

class SistemaA2Adapter implements VentaAdapter {
    public function adaptar($json): ?DTO {
        if ($json === null
            || !isset($json->id, $json->timestamp, $json->items)
            || !is_array($json->items)) {
            return null;
        }

        $productos = [];
        foreach ($json->items as $item) {
            if (!isset($item->sku, $item->units)) {
                return null;
            }
            //lo dejo con los nombres de A (codigo, cantidad) para no tocar el DTO
            $productos[] = (object)[
                "codigo"         => $item->sku,
                "cantidad"       => $item->units,
                "precioUnitario" => 0.0 //A2 no manda precio, falta decidir q hacer con esto (NOTA, LE PUSE 0.0 PARA QUE SEA MAS PRACTICO)
            ];
        }

        //A2 tampoco manda cajeroId, por ahora va null
        return new DTO($json->id, $json->timestamp, null, $productos);
    }
}
?>
