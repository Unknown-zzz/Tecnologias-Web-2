# Tienda en Linea MVC (PHP + MySQL + Bootstrap)

## Estructura

- app/core: Base de datos, controlador base y helpers.
- app/models: Modelos Product y User.
- app/controllers: Controladores para tienda, carrito, checkout y admin.
- app/views: Vistas separadas por modulo.
- resources/imagenes: Imagenes de productos.
- sql/schema.sql: Script de base de datos.

## Instalacion en XAMPP

1. Crea la base de datos ejecutando el script `sql/ecommerce (1).sql` en phpMyAdmin.
2. Luego importa los procedimientos almacenados con `sql/stored_procedures.sql`.
3. Verifica credenciales en config/config.php (por defecto root sin contrasena).
4. Abre en navegador:
   - http://localhost/Tecnologia%20web%202/index.php?r=home
4. Acceso admin:
   - Usuario: administrador
   - Password: 12345
   - URL: http://localhost/Tecnologia%20web%202/index.php?r=admin/login

## Rutas principales

- Tienda: index.php?r=home
- Carrito: index.php?r=cart
- Pago: index.php?r=checkout
- Admin dashboard: index.php?r=admin/dashboard
- Gestion productos: index.php?r=admin/products
