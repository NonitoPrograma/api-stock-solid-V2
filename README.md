====================================================================
  GUIA DE EJECUCION Y PRUEBAS - INTEGRACION API STOCK (SOLID)
====================================================================

--- PASO 0: PREPARAR EL ENTORNO LIMPIO (OPCIONAL PARA DEMO) ---

1. Abrir 'ventas.json' y dejarlo únicamente con: {}

2. Abrir 'stock.log' y borrar todo su contenido (dejarlo vacío).


--------------------------------------------------------------------
--- PASO 1: LEVANTAR EL SERVIDOR DE PHP (CONSOLA 1) ---
--------------------------------------------------------------------

1. Abrir el Explorador de Windows en la carpeta de tu proyecto.
2. Hacer clic en la barra de direcciones de arriba, escribir 'cmd' y presionar Enter.
3. En la consola que se abre, ejecutar:

    C:\xampp\php\php.exe -S localhost:3000

* NOTA: Dejar esta consola abierta todo el tiempo corriendo el servidor.

--------------------------------------------------------------------
--- PASO 2: EJECUTAR LAS PRUEBAS (CONSOLA 2) ---
--------------------------------------------------------------------
1. Abrir una SEGUNDA ventana de CMD parada en la carpeta del proyecto.
2. Copiar y pegar los siguientes comandos uno a uno:

[PRUEBA 1: Venta desde Sistema A (index.php)]

curl -X POST http://localhost:3000/index.php -H "Content-Type: application/json" -d "{\"ventaId\":154872,\"fecha\":\"2026-09-24T16:20:35-03:00\",\"cajeroId\":42,\"productos\":[{\"codigo\":\"7791234567890\",\"cantidad\":2,\"precioUnitario\":1250.50}]}"


-> Respuesta esperada: {"ok":true,"referencia":"VENTA-154872"}
-> Verificación: Agrega registro en 'ventas.json' y 1 línea en 'stock.log'.


[PRUEBA 2: Validar Idempotencia (Reenviar misma venta)]

curl -X POST http://localhost:3000/index.php -H "Content-Type: application/json" -d "{\"ventaId\":154872,\"fecha\":\"2026-09-24T16:20:35-03:00\",\"cajeroId\":42,\"productos\":[{\"codigo\":\"7791234567890\",\"cantidad\":2,\"precioUnitario\":1250.50}]}"


-> Respuesta esperada: {"ok":true,"referencia":"VENTA-154872"}
-> Verificación: NO debe sumar una segunda línea en 'stock.log' (se mantiene en 1).



[PRUEBA 3: Venta desde Sistema A2 (indexA2.php)]

curl -X POST http://localhost:3000/indexA2.php -H "Content-Type: application/json" -d "{\"id\":\"200500\",\"timestamp\":\"2026-09-24T16:20:35-03:00\",\"items\":[{\"sku\":\"7791234567890\",\"units\":3}]}"


-> Respuesta esperada: {"ok":true,"referencia":"VENTA-200500"}
-> Verificación: El adaptador procesa el JSON de A2 y guarda 'VENTA-200500' en 'ventas.json'.



[PRUEBA 4: Script de Reintentos CLI]

C:\xampp\php\php.exe reintentar.php

-> Respuesta esperada: Iniciando reintento de 0 ventas pendientes...
--------------------------------------------------------------------
