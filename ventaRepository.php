<?php
require_once "dto.php";

interface VentaRepository {
    public function guardar(DTO $venta): bool; //devuelve false si esa venta ya estaba guardada
    public function sumarIntento($ventaId): int; //suma 1 a los intentos y devuelve cuantos lleva
    public function cambiarEstado($ventaId, string $estado): void; //pendiente, procesada o fallida
    public function obtenerPendientes(): array;
    public function obtenerPorEstado(string $estado): array;
}
?>
