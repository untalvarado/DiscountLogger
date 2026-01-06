# Vendor_DiscountLogger para Magento 2
Este modulo implementa funcionalidades personalizadas para el registro de auditoria de cupones de descuento especificos y extiende la API de GraphQL para exponer datos simulados de fidelidad del cliente.

## Funcionalidades Principales
### 1. Registro de Cupones (Coupon Logger)

Intercepta el proceso de guardado del carrito de compras (Quote). Si el cupon aplicado es **SAVE10**, registra los detalles de la transaccion en un archivo de log dedicado.
- **Archivo de Log:** var/log/custom_discount.log
- **Datos registrados:** Fecha, Subtotal, Grand Total y Monto del Descuento.
- **Logica de Deduplicacion:** Se implemento una validacion comparando getOrigData('coupon_code') para asegurar que el registro solo ocurra cuando el cupon cambia o se aplica por primera vez, evitando entradas duplicadas durante el ciclo de vida del carrito.

### 2. Comando de Consola (CLI)
Proporciona una herramienta de linea de comandos para visualizar rapidamente las ultimas entradas del log de descuentos sin necesidad de acceder al servidor via FTP/SSH.
- **Comando:** bin/magento discountlog:tail
- **Accion:** Muestra las ultimas 5 lineas del archivo custom_discount.log.

### 3. Extension GraphQL (Loyalty Points)
Extiende el esquema Customer para incluir un campo de puntos de fidelidad.
- **Campo:** loyalty_points (Int)
- **Logica (Mock):** Retorna el ID del cliente multiplicado por 10.
- **Seguridad:** Requiere que el usuario tenga una sesion activa (Token de cliente). Si es invitado (Guest), lanza una excepcion de autorizacion.

## Instalacion
1. Copiar los archivos del modulo en app/code/Vendor/DiscountLogger.
2. Ejecutar los comandos de instalacion:

   bin/magento module:enable Vendor_DiscountLogger
   bin/magento setup:upgrade
   bin/magento setup:di:compile
   bin/magento cache:flush

## Uso y Pruebas

Para validar el correcto funcionamiento del modulo, siga estos pasos:

### A. Prueba del Logger (Backend)

1. Navegue al Checkout de la tienda como cliente registrado o invitado.
2. Aplique el cupon SAVE10.
3. Verifique que se ha creado/actualizado el archivo en var/log/custom_discount.log con la informacion de la orden.

### B. Prueba del CLI (Consola)

Ejecute desde la raiz del proyecto Magento para ver los registros generados en el paso anterior:
bin/magento discountlog:tail
*Resultado esperado:* Debe imprimir en consola las ultimas 5 lineas del registro de auditoria.

### C. Prueba de GraphQL (Headless)
Realice una peticion POST al endpoint /graphql utilizando un cliente como Postman o Altair.
Nota: Debe incluir un Header de Autorizacion con el token del cliente (Bearer token).

Query:

query {
  customer {
    firstname
    email
    loyalty_points
  }
}

Respuesta Esperada:

{
  "data": {
    "customer": {
      "firstname": "NombreCliente",
      "email": "cliente@email.com",
      "loyalty_points": 450
    }
  }
}


## Estructura del Modulo

A continuacion se detalla la ubicacion de los archivos fisicos creados para este modulo:

app/code/Vendor/DiscountLogger/
|-- Console/
|   |-- Command/
|       |-- ShowDiscountLogCommand.php   (Logica del comando CLI)
|-- Model/
|   |-- Resolver/
|       |-- LoyaltyPoints.php            (Logica del campo GraphQL)
|-- Plugin/
|   |-- LogDiscountCoupon.php            (Interceptor del Quote Save)
|-- etc/
|   |-- di.xml                           (Inyeccion de dependencias y config)
|   |-- module.xml                       (Declaracion del modulo)
|   |-- schema.graphqls                  (Esquema extendido de GraphQL)
|-- registration.php                     (Registro del componente)
|-- README.md                            (Documentacion)
