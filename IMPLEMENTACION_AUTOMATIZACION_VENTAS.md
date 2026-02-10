# 📊 Sistema de Automatización de Ventas - Documentación Completa

Bienvenido al Sistema Automatizado de Registro de Ventas para La Choza Náutica. Este documento te guiará a través de la implementación completa del sistema.

---

## 🎯 Descripción General del Sistema

El sistema automatiza completamente el registro de ventas online con:
- ✅ Formulario de checkout automático con validaciones
- ✅ Procesamiento automático de pedidos en BD
- ✅ Confirmación automática por email
- ✅ API REST para consultar datos de ventas
- ✅ Dashboard de reportes analíticos
- ✅ Tracking de productos más vendidos
- ✅ Análisis de clientes frecuentes

---

## 📦 Archivos Creados

### 1. **checkout.php** - Formulario de Checkout Automático
- Formulario amigable para recopilar datos del cliente
- Validaciones JavaScript en tiempo real
- Resumen automático del pedido
- Selección de métodos de pago
- **Ubicación:** Raíz del proyecto

### 2. **procesar_venta.php** - Backend de Procesamiento
- Procesa automáticamente los pedidos
- Valida todos los datos
- Guarda cliente y pedido en BD
- Envía email de confirmación automáticamente
- Responde en JSON para AJAX
- **Ubicación:** Raíz del proyecto

### 3. **confirmacion_pedido.php** - Página de Confirmación
- Muestra detalles del pedido
- Resumen financiero completo
- Información del cliente
- **Ubicación:** Raíz del proyecto

### 4. **api_ventas.php** - API REST de Consultas
- Endpoint para listar todas las ventas
- Detalles de venta específica
- Ventas por rango de fechas
- Estadísticas generales
- Productos más vendidos
- Clientes frecuentes
- Ingresos por mes
- **Ubicación:** Raíz del proyecto
- **Acceso:** GET /api_ventas.php?accion=...

### 5. **admin/dashboard_ventas.php** - Dashboard de Reportes
- Visualización de estadísticas en tarjetas
- Tabla de productos más vendidos
- Tabla de clientes frecuentes
- Historial de últimas ventas
- Gráfico de ingresos por mes (Chart.js)
- **Ubicación:** Carpeta admin/

### 6. **carrito.php** - Carrito Mejorado
- Interfaz renovada
- Botón directo a checkout
- Estilos CSS mejorados
- **Ubicación:** Raíz del proyecto (modificado)

---

## 🗄️ Estructura de Base de Datos

El sistema requiere las siguientes tablas. Verifica que existan en tu BD o crea las siguientes:

### Tabla: `clientes`
```sql
CREATE TABLE IF NOT EXISTS clientes (
    id_cliente INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    telefono VARCHAR(15),
    direccion TEXT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Tabla: `pedidos`
```sql
CREATE TABLE IF NOT EXISTS pedidos (
    id_pedido INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT NOT NULL,
    fecha_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_pago INT,
    id_estado INT DEFAULT 1,
    monto_total DECIMAL(10, 2) NOT NULL,
    notas TEXT,
    FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente),
    FOREIGN KEY (id_pago) REFERENCES metodos_pago(id_pago),
    FOREIGN KEY (id_estado) REFERENCES estadopedido(id_estado)
);
```

### Tabla: `detallepedido`
```sql
CREATE TABLE IF NOT EXISTS detallepedido (
    id_detalle INT AUTO_INCREMENT PRIMARY KEY,
    id_pedido INT NOT NULL,
    id_plato INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (id_pedido) REFERENCES pedidos(id_pedido),
    FOREIGN KEY (id_plato) REFERENCES platos(id_plato)
);
```

### Tabla: `platos` (ya debe existir)
```sql
CREATE TABLE IF NOT EXISTS platos (
    id_plato INT AUTO_INCREMENT PRIMARY KEY,
    nombre_plato VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10, 2) NOT NULL,
    imagen VARCHAR(100),
    activo TINYINT DEFAULT 1
);
```

### Tabla: `metodos_pago`
```sql
CREATE TABLE IF NOT EXISTS metodos_pago (
    id_pago INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL
);

-- Insertar métodos de pago
INSERT INTO metodos_pago (nombre) VALUES
('Efectivo'),
('Tarjeta de Crédito'),
('Tarjeta de Débito'),
('Transferencia Bancaria'),
('PayPal');
```

### Tabla: `estadopedido`
```sql
CREATE TABLE IF NOT EXISTS estadopedido (
    id_estado INT AUTO_INCREMENT PRIMARY KEY,
    descripcion VARCHAR(50) NOT NULL
);

-- Insertar estados
INSERT INTO estadopedido (descripcion) VALUES
('Pendiente'),
('En Preparación'),
('Listo para Entrega'),
('Entregado'),
('Cancelado');
```

### Tabla: `ventas` (Opcional - para registro histórico)
```sql
CREATE TABLE IF NOT EXISTS ventas (
    id_venta INT AUTO_INCREMENT PRIMARY KEY,
    id_pedido INT,
    id_cliente INT,
    fecha_venta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    monto_total DECIMAL(10, 2),
    estado VARCHAR(50),
    FOREIGN KEY (id_pedido) REFERENCES pedidos(id_pedido),
    FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente)
);
```

---

## 🔧 Instalación y Configuración

### Paso 1: Crear las Tablas en la Base de Datos
1. Abre phpMyAdmin o tu gestor de BD
2. Selecciona la BD `cevichería`
3. Copia y ejecuta cada sentencia SQL anterior

### Paso 2: Verificar Conexiones
Asegúrate que `db.php` esté correctamente configurado:
```php
$host = "localhost";
$dbname = "cevichería";
$user = "root";
$pass = "";
```

### Paso 3: Configurar Correo Electrónico
En `procesar_venta.php`, actualiza los datos de PHPMailer:
```php
$mail->Username = 'tu_email@gmail.com'; // Tu email
$mail->Password = 'tu_contraseña_app'; // Contraseña de aplicación
```

**Para Gmail:**
1. Activa la autenticación de dos factores
2. Genera una "Contraseña de Aplicación"
3. Usa esa contraseña en el código

### Paso 4: Actualizar Enlaces en Menu
En `menu.php` o donde esté el menú, el flujo debe ser:
```
Productos → Agregar al Carrito → Ver Carrito → Checkout → Procesamiento → Confirmación
```

---

## 🚀 Uso del Sistema

### Flujo de Cliente

1. **Cliente explora productos** en `menu.php`
2. **Agrega productos** al carrito (agregar_carrito.php)
3. **Ve el carrito** en `carrito.php`
4. **Hace clic en "Proceder a Checkout"**
5. **Completa formulario** en `checkout.php` con:
   - Nombre completo
   - Email
   - Teléfono
   - Dirección
   - Método de pago
   - Notas especiales (opcional)
6. **Sistema valida automáticamente** todos los datos
7. **Procesa el pedido** automáticamente
8. **Envía confirmación por email**
9. **Redirige a página de confirmación** con detalles

### Flujo de Administrador

1. **Ver Dashboard** en `admin/dashboard_ventas.php`
   - Estadísticas generales
   - Top productos
   - Clientes frecuentes
   - Últimas ventas
   - Gráfico de ingresos

2. **Consultar la API** en `api_ventas.php`
   - Listar todas las ventas
   - Ver detalles de una venta
   - Ventas por fechas
   - Estadísticas
   - Productos más vendidos

---

## 📡 Rutas y Endpoints de la API

### URL Base
```
http://localhost/choza2/api_ventas.php
```

### Endpoints Disponibles

#### 1. Listar Todas las Ventas
```
GET /api_ventas.php?accion=listar_ventas&pagina=1&limite=10
```
Parámetros:
- `pagina`: Número de página (default: 1)
- `limite`: Resultados por página (default: 10, máx: 100)

Respuesta:
```json
{
  "success": true,
  "total": 50,
  "pagina": 1,
  "limite": 10,
  "paginas_totales": 5,
  "data": [...]
}
```

#### 2. Detalles de Una Venta
```
GET /api_ventas.php?accion=venta_detalle&id=5
```
Parámetro:
- `id`: ID del pedido (obligatorio)

#### 3. Ventas por Rango de Fechas
```
GET /api_ventas.php?accion=ventas_por_fechas&desde=2025-01-01&hasta=2025-02-10
```
Parámetros:
- `desde`: Fecha inicio (formato: YYYY-MM-DD)
- `hasta`: Fecha fin (formato: YYYY-MM-DD)

#### 4. Estadísticas Generales
```
GET /api_ventas.php?accion=estadisticas
```
Respuesta incluye:
- Total de pedidos
- Total de ingresos
- Ingresos del día
- Total de clientes
- Pedidos pendientes
- Promedio de venta

#### 5. Productos Más Vendidos
```
GET /api_ventas.php?accion=productos_masvendidos
```
Top 5 productos con cantidad vendida e ingresos

#### 6. Clientes Frecuentes
```
GET /api_ventas.php?accion=clientes_frecuentes
```
Top 10 clientes con más pedidos

#### 7. Ingresos por Mes
```
GET /api_ventas.php?accion=ingresos_por_mes
```
Últimos 12 meses con ingresos y cantidad de pedidos

---

## 🎨 Personalización

### Cambiar Colores
- En `checkout.php`: Busca la sección `<style>`
- En `admin/dashboard_ventas.php`: Modifica los colores del gradiente

### Cambiar Validaciones
- Edita la función `validarFormulario()` en `checkout.php`
- Agrega o modifica expresiones regulares según necesites

### Cambiar Campos del Formulario
- Abre `checkout.php`
- Busca el formulario y agrega/modifica campos
- Actualiza validaciones en `procesar_venta.php`

---

## 🔒 Seguridad

### Recomendaciones

1. **Validaciones en Servidor**: El código ya valida en servidor
2. **Proteger Contraseña**: Nunca guardes contraseñas en claro
3. **HTTPS**: Usa SSL en producción
4. **SQL Injection**: Usamos prepared statements (seguro)
5. **CSRF**: Considera agregar tokens CSRF
6. **Rate Limiting**: Implementa límite de intentos


### Agregar Autenticación a la API (Opcional)
En `api_ventas.php`, descomenta:
```php
if (!isset($_SESSION['admin'])) {
    http_response_code(401);
    die(json_encode(['error' => 'No autorizado']));
}
```

---

## 📊 Ejemplos de Uso

### Obtener Estadísticas en JavaScript
```javascript
fetch('api_ventas.php?accion=estadisticas')
  .then(res => res.json())
  .then(data => {
    console.log('Total pedidos:', data.resumen.total_pedidos);
    console.log('Ingresos hoy:', data.resumen.ingresos_hoy);
  });
```

### Consultar Productos Más Vendidos
```javascript
fetch('api_ventas.php?accion=productos_masvendidos')
  .then(res => res.json())
  .then(data => {
    data.productos.forEach(p => {
      console.log(p.nombre_plato + ': ' + p.cantidad_vendida + ' unidades');
    });
  });
```

---

## 🐛 Solución de Problemas

### Problema: "No autorizado" en API
**Solución:** Descomenta la verificación de sesión en `api_ventas.php` o inicia sesión primero

### Problema: Errores de BD
**Solución:** Verifica que todas las tablas existan con exactamente los mismos nombres

### Problema: Correos no se envían
**Solución:** 
- Verifica credenciales de Gmail
- Usa contraseña de aplicación (no contraseña de cuenta)
- Activa autenticación de dos factores

### Problema: Validaciones no funcionan
**Solución:** 
- Abre consola JavaScript (F12)
- Verifica que no haya errores JS
- Limpia caché del navegador

---

## 📈 Próximas Mejoras

- [ ] Integración de pasarela de pago (Stripe, Paypal)
- [ ] Notificaciones en tiempo real
- [ ] Sistemas de descuentos y cupones
- [ ] Reportes avanzados en PDF
- [ ] App móvil
- [ ] Notificaciones SMS
- [ ] Integración con redes sociales

---

## 📞 Soporte

Para reportar bugs o solicitar mejoras:
1. Verifica la consola del navegador (F12)
2. Verifica los logs del servidor
3. Revisa que todos los archivos estén en su lugar correcto

---

**Última actualización:** Febrero 2025  
**Versión:** 1.0  
**Estado:** Listo para producción
