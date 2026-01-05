# Discount Logger Module for Magento 2

## Descripcion

Este modulo implementa un sistema de logging personalizado que intercepta el proceso de guardado del carrito de compras. Su objetivo es detectar el uso de un cupon especifico ("SAVE10") y registrar los detalles de la transaccion (subtotal, total y descuento) en un archivo de log dedicado, separado del flujo principal de Magento.

## Funcionalidades
- **Intercepcion de Repositorio:** Utiliza un Plugin en "Magento\Quote\Api\CartRepositoryInterface" para asegurar la captura de datos tanto en frontend como en API.
- **Log Personalizado:** Implementa VirtualTypes para escribir en "var/log/custom_discount.log", manteniendo limpios los logs del sistema (system.log).
- **Logica Condicional:** Filtra estrictamente por el codigo de cupon configurado en el archivo di.xml.
- **Calculo de Datos:** Registra fecha, subtotal, gran total y el valor del descuento aplicado.

## Requisitos
- Magento 2.3.x o superior.
- PHP 7.4 o superior.

## Instalacion
1. Copiar la carpeta "Vendor" dentro del directorio "app/code/" de su instalacion de Magento.
2. Ejecutar los comandos de habilitacion y despliegue:

   bin/magento module:enable Vendor_DiscountLogger
   bin/magento setup:upgrade
   bin/magento setup:di:compile
   bin/magento cache:flush

## Pruebas

1. Agregue un producto al carrito de compras.
2. Aplique el cupon SAVE10 en el checkout.
3. Verifique el archivo de log generado para confirmar el registro ejecutando:

   tail -f var/log/custom_discount.log

## Estructura Tecnica

- **etc/di.xml:** Configuracion de inyeccion de dependencias, definicion del Logger virtual y configuracion del argumento del cupon ("SAVE10").
- **Plugin/LogDiscountCoupon.php:** Logica principal que intercepta el metodo save del repositorio de cotizaciones.

## Autor
Jorge Alvarado - Ingeniero de Sistemas / Desarrollador PHP
