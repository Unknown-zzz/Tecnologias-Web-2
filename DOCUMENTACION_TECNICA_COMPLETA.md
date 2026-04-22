# Documentacion Tecnica Completa - Tienda Amiga (TW2)

## 1. Resumen del sistema
Aplicacion e-commerce en arquitectura MVC con PHP y MySQL.
Incluye:
- autenticacion de usuarios
- tienda y carrito
- checkout directo
- checkout por QR
- panel administrativo
- emision de factura PDF

## 2. Stack y componentes usados
- Backend: PHP 8.x
- Base de datos: MySQL/MariaDB
- Patron: MVC (carpetas app/controllers, app/models, app/views)
- Frontend: Bootstrap 5 + Bootstrap Icons + JS nativo
- QR en cliente: qrcode.js via CDN
- PDF: Dompdf (instalado por Composer)

## 3. Dependencias del proyecto
### 3.1 Composer
Paquetes instalados:
- dompdf/dompdf v3.x
- dompdf/php-font-lib
- dompdf/php-svg-lib
- masterminds/html5
- sabberworm/php-css-parser

Archivos generados por Composer:
- composer.json
- composer.lock
- vendor/

### 3.2 Extensiones PHP requeridas
Minimas recomendadas para el proyecto actual:
- pdo
- pdo_mysql
- json
- mbstring
- iconv
- zlib
- session

Extensiones utiles adicionales:
- openssl (tokens y seguridad)
- curl
- fileinfo

## 4. Configuracion principal
La aplicacion actualmente lee configuracion desde:
- config/config.php

El archivo .env se usa como documento tecnico de referencia:
- .env

## 5. Base de datos y SQL usados
Orden recomendado:
1. crear esquema y datos base con sql/ecommerce (1).sql
2. cargar procedimientos almacenados con sql/stored_procedures.sql
3. asegurar tabla de pagos QR con sql/qr_payment_migration.sql (si aplica)

Stored procedures clave:
- sp_cuenta_find, sp_cuenta_create
- sp_producto_all_active, sp_producto_find
- sp_venta_create, sp_venta_details, sp_venta_all
- sp_venta_set_report_path
- sp_productos_top_vendidos

## 6. Modulo PDF (Factura)
### 6.1 Implementacion actual
La factura PDF se genera con Dompdf desde:
- app/models/Sale.php

Metodo principal:
- generateInvoicePdf(...)

### 6.2 Branding solicitado
La factura muestra marca "Tienda Amiga" con logo vectorial/textual integrado en el HTML del PDF.
Este enfoque evita dependencia de ext-gd para render de imagenes y mantiene compatibilidad en entornos donde GD no esta habilitado.

### 6.3 Flujo de generacion
1. checkout crea la venta
2. se arma HTML de factura
3. Dompdf renderiza PDF A4 portrait
4. se guarda en storage/receipts
5. se guarda la ruta en BD con sp_venta_set_report_path

Controladores que disparan PDF:
- app/controllers/CheckoutController.php
- app/controllers/QrPaymentController.php

## 7. Modulo QR (Pago por QR)
### 7.1 Backend
- app/controllers/QrPaymentController.php
- app/models/QrPayment.php

Estados del pago QR:
- pendiente
- confirmado
- completado
- expirado

### 7.2 Frontend
- app/views/checkout/index.php
- qrcode.js por CDN para dibujar el QR
- polling al endpoint qr/status para detectar cambios de estado

### 7.3 Datos en BD
Tabla:
- qr_pagos

Campos clave:
- token
- ciCliente
- carrito (json)
- estado
- expires_at
- nroVenta

## 8. Rutas principales
- index.php?r=home
- index.php?r=cart
- index.php?r=checkout
- index.php?r=admin/login
- index.php?r=admin/dashboard

Rutas QR:
- index.php?r=qr/generate
- index.php?r=qr/status
- index.php?r=qr/scan
- index.php?r=qr/confirm

## 9. Estructura de archivos importantes
- index.php
- config/config.php
- app/models/Sale.php
- app/models/QrPayment.php
- app/controllers/CheckoutController.php
- app/controllers/QrPaymentController.php
- app/views/checkout/index.php
- app/views/checkout/qr_scan.php
- storage/receipts/

## 10. Instalacion rapida
1. Colocar proyecto en htdocs
2. Crear base ecommerce e importar SQL base
3. Importar procedimientos
4. Instalar dependencias:
   - composer install
5. Verificar config/config.php
6. Levantar:
   - php -S 127.0.0.1:8000

## 11. Notas operativas
- Si falta un SP, el home o checkout puede fallar.
- Si no existe vendor/autoload.php, Dompdf no cargara.
- El logo de factura se dibuja como marca vectorial/textual de Tienda Amiga (sin requerir GD).
- Recibos generados quedan en storage/receipts.

## 12. Checklist de verificacion
- [ ] Home carga sin errores SQL
- [ ] Checkout directo crea venta y PDF
- [ ] Pago QR genera token y confirma compra
- [ ] PDF contiene logo y texto Tienda Amiga
- [ ] Ruta de PDF queda guardada en BD
