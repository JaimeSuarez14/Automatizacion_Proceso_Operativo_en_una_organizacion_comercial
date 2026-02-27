# Choza - Sistema de Pedidos en Línea (Proyecto)
![JavaScript](https://img.shields.io/badge/JavaScript-ES6%2B-yellow?logo=javascript&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8%2B-blue?logo=php&logoColor=white)
![Bootstrap](https://img.shields.io/badge/bootstrap-5%2B-blue?logo=bootstrap&logoColor=white)

## Descripción

Proyecto web para la gestión y realización de pedidos de comida en línea. Permite a usuarios navegar el menú, añadir platos al carrito, registrarse/iniciar sesión y realizar pedidos. Incluye un panel administrativo para gestionar platos y pedidos, y envía notificaciones por correo.

## Partes principales del proyecto

- **Frontend público:** páginas y vistas para clientes: `index.php`, `menu.php`, `carrito.php`, `registro.php`, `login.php`.
- **Carrito y proceso de pedido:** `agregar_carrito.php`, `carrito.php`, `send_order_email.php`.
- **Autenticación y registro:** `proceso_registro.php`, `login.php`, `logout.php`.
- **Panel administrativo:** carpeta `admin/` con gestión de platos y pedidos (`admin_platos.php`, `admin_pedidos.php`, `agregar_plato.php`, `editar_plato.php`, etc.).
- **Conexión a base de datos:** `db.php` centraliza la conexión y consultas a la base de datos.
- **Recursos y assets:** carpetas `assets/`, `img/`, `icons/`, `style.css`, `script.js` para estilos, imágenes y scripts.
- **Correo electrónico:** `PHPMailer/` integrado para enviar confirmaciones o notificaciones desde `send_order_email.php`.

## Tecnologías y herramientas

- **PHP**: Lógica del servidor y renderizado de páginas.
- **MySQL (o MariaDB)**: Base de datos para usuarios, platos y pedidos (configurable desde `db.php`).
- **PHPMailer**: Envío de correos SMTP para notificaciones.
- **HTML / CSS / JavaScript**: Interfaz de usuario, estilos y comportamiento en el navegador.
- **XAMPP (Windows)**: Entorno recomendado para ejecutar localmente (Apache + PHP + MySQL).

## Instalación y uso (rápido)

1. Copiar la carpeta del proyecto a la carpeta `htdocs` de XAMPP (por ejemplo `c:\xampp\htdocs\choza2`).
2. Crear una base de datos MySQL y ejecutar las consultas necesarias para crear las tablas (usuarios, platos, pedidos). (Si no hay script SQL incluido, crear las tablas según el flujo de la app.)
3. Configurar la conexión a la base de datos en `db.php` (host, usuario, contraseña, nombre de BD).
4. Configurar credenciales de correo en `PHPMailer` o en `send_order_email.php` si usa SMTP.
5. Iniciar Apache y MySQL desde el panel de XAMPP y abrir con el comando "php -S localhost:8080" en el navegador.

## Notas y recomendaciones

- Revisar y asegurar la sanitización de entradas de usuario al trabajar con la base de datos.
- Proteger las rutas administrativas con validación de sesión/roles.
- Ajustar configuración SMTP en `PHPMailer` antes de enviar correos reales.

Si quieres, puedo añadir un script SQL de ejemplo, mejorar la documentación de instalación o generar instrucciones detalladas para desplegar en un servidor.
