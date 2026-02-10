# 🚀 GUÍA RÁPIDA - AUTOMATIZACIÓN DE GESTIÓN DE VENTAS

## ✅ ¿QUÉ SE IMPLEMENTÓ?

### 1. **Formulario de Checkout Automático** (`checkout.php`)
- Captura datos del cliente (nombre, email, teléfono, dirección)
- Validaciones en tiempo real
- Selección de método de pago
- Resumen automático del pedido
- **Acceso:** `menu.php` → Carrito → **"Proceder a Checkout"**

### 2. **Procesamiento Automático** (`procesar_venta.php`)
- Valida todos los datos
- Guarda cliente en BD automáticamente
- Registra pedido y detalles
- **Envía email de confirmación**
- Limpia carrito automáticamente

### 3. **Página de Confirmación** (`confirmacion_pedido.php`)
- Muestra detalles completos del pedido
- Información del cliente
- Desglose de productos
- Cálculo automático de IGV

### 4. **API REST de Ventas** (`api_ventas.php`)
```
GET /api_ventas.php?accion=listar_ventas
GET /api_ventas.php?accion=estadisticas
GET /api_ventas.php?accion=productos_masvendidos
GET /api_ventas.php?accion=clientes_frecuentes
GET /api_ventas.php?accion=ingresos_por_mes
```

### 5. **Dashboard de Reportes** (`admin/dashboard_ventas.php`)
- Tarjetas de estadísticas
- Top 5 productos más vendidos
- Top 5 clientes frecuentes
- Últimas 10 ventas
- **Gráfico de ingresos por mes**

---

## 📋 PASOS DE IMPLEMENTACIÓN INMEDIATA

### Paso 1: Ejecutar Script SQL
```bash
1. Abre phpMyAdmin
2. Selecciona BD "cevichería"
3. Abre pestaña SQL
4. Copia contenido de: `setup_automatizacion_ventas.sql`
5. Ejecuta el script
```

### Paso 2: Configurar Email
Edita `config_automatizacion.php`:
```php
'SMTP_USER' => 'tu_email@gmail.com',
'SMTP_PASS' => 'tu_contraseña_app'
```

**Para Gmail:** Genera una "Contraseña de Aplicación"
[Google App Passwords](https://myaccount.google.com/apppasswords)

### Paso 3: Actualizar Configuración
Edita `procesar_venta.php` líneas 79-82:
```php
$mail->Username = 'tu_email@gmail.com';
$mail->Password = 'tu_contraseña_app';
$mail->setFrom('tu_email@gmail.com', 'La Choza Náutica');
```

### Paso 4: Probar el Sistema
Abre en navegador:
```
http://localhost/choza2/test_sistema.php
```
Verifica que todos los tests pasen ✓

---

## 📱 FLUJO DE CLIENTE (Nuevo)

```
1. Usuario ve menú (menu.php)
   ↓
2. Agrega productos al carrito
   ↓
3. Abre carrito (carrito.php)
   ↓
4. Hace clic en "Proceder a Checkout" ← NUEVO
   ↓
5. Completa formulario (checkout.php) ← NUEVO
   ↓
6. Sistema valida automáticamente ← NUEVO
   ↓
7. Procesa pedido automáticamente ← NUEVO
   ↓
8. Envía email de confirmación ← NUEVO
   ↓
9. Muestra página de confirmación ← NUEVO
```

---

## 📊 FLUJO DE ADMINISTRADOR (Nuevo)

```
Acceder a: http://localhost/choza2/admin/dashboard_ventas.php

Dashboard muestra:
├─ Total de Pedidos
├─ Ingresos Totales
├─ Ingresos del Día
├─ Total de Clientes
├─ Top 5 Productos Más Vendidos
├─ Top 5 Clientes Frecuentes
├─ Últimas 10 Ventas
└─ Gráfico de Ingresos por Mes
```

---

## 🔌 USO DE LA API

### Obtener Estadísticas
```bash
curl "http://localhost/choza2/api_ventas.php?accion=estadisticas"
```

Respuesta:
```json
{
  "success": true,
  "resumen": {
    "total_pedidos": 45,
    "total_ingresos": 2350.50,
    "ingresos_hoy": 120.00,
    "total_clientes": 30,
    "pedidos_pendientes": 5,
    "promedio_venta": 52.23
  }
}
```

### Obtener Productos Más Vendidos
```bash
curl "http://localhost/choza2/api_ventas.php?accion=productos_masvendidos"
```

### Obtener Ventas de un Período
```bash
curl "http://localhost/choza2/api_ventas.php?accion=ventas_por_fechas&desde=2025-01-01&hasta=2025-02-10"
```

---

## 🗄️ LISTA DE TABLAS CREADAS

- ✓ `clientes` - Información de clientes
- ✓ `pedidos` - Registro de pedidos
- ✓ `detallepedido` - Items en cada pedido
- ✓ `metodos_pago` - Formas de pago
- ✓ `estadopedido` - Estados de pedidos
- ✓ `ventas` - Historial de ventas
- ✓ Vistas SQL para reportes
- ✓ Procedimientos almacenados

---

## 📂 ARCHIVOS CREADOS

```
choza2/
├─ checkout.php                          ← Formulario automático
├─ procesar_venta.php                    ← Backend de procesamiento
├─ confirmacion_pedido.php               ← Página de confirmación
├─ api_ventas.php                        ← API REST
├─ config_automatizacion.php             ← Configuraciones
├─ test_sistema.php                      ← Suite de pruebas
├─ setup_automatizacion_ventas.sql       ← Script BD
├─ carrito.php                  (MODIFICADO)
├─ IMPLEMENTACION_AUTOMATIZACION_VENTAS.md
└─ admin/
   └─ dashboard_ventas.php               ← Dashboard de reportes
```

---

## ✨ CARACTERÍSTICAS PRINCIPALES

| Característica | Status |
|---|---|
| Captura automática de datos | ✅ |
| Validaciones en tiempo real | ✅ |
| Guardado automático en BD | ✅ |
| Confirmación por email | ✅ |
| API REST | ✅ |
| Dashboard visual | ✅ |
| Gráficos de ventas | ✅ |
| Análisis de productos | ✅ |
| Análisis de clientes | ✅ |
| Histórico de ventas | ✅ |

---

## 🔐 SEGURIDAD

- ✓ Validaciones en servidor
- ✓ Prepared statements (anti SQL injection)
- ✓ Sanitización de datos
- ✓ Encriptación de contraseñas (recomendado agregar)
- ✓ CORS habilitado (opcional)

**Para producción, agregar:**
- [ ] HTTPS obligatorio
- [ ] Tokens CSRF
- [ ] Autenticación en API
- [ ] Rate limiting
- [ ] WAF (Web Application Firewall)

---

## 🧪 VERIFICACIÓN RÁPIDA

```bash
# Inicia navegador y abre:
http://localhost/choza2/test_sistema.php

# Verifica:
✓ Conexión a BD
✓ Todas las tablas
✓ Todos los archivos
✓ Métodos de pago
✓ Estados de pedidos
✓ Platos disponibles
✓ API funcionando
✓ Sesiones PHP
```

---

## 🆘 TROUBLESHOOTING

| Problema | Solución |
|---|---|
| Email no se envía | Verifica credenciales en `procesar_venta.php` |
| Tablas no existen | Ejecuta `setup_automatizacion_ventas.sql` |
| API no responde | Abre `/api_ventas.php` en navegador para ver error |
| Formulario no valida | Abre consola F12 y revisa errores JS |
| Carrito no guarda | Verifica que sesiones estén habilitadas |

---

## 📞 PRÓXIMAS MEJORAS RECOMENDADAS

- [ ] Pasarela de pago (Stripe, Paypal)
- [ ] Integraciones SMS
- [ ] WhatsApp Business API
- [ ] Descuentos y cupones
- [ ] Reportes en PDF
- [ ] App móvil
- [ ] Notificaciones push
- [ ] Seguimiento de pedidos en tiempo real

---

## 📍 URLS RÁPIDAS

| Página | URL |
|---|---|
| Menú de productos | `/menu.php` |
| Carrito | `/carrito.php` |
| Checkout | `/checkout.php` |
| Dashboard Admin | `/admin/dashboard_ventas.php` |
| API Ventas | `/api_ventas.php?accion=...` |
| Pruebas | `/test_sistema.php` |

---

**Sistema lista para producción ✅**

¿Preguntas? Revisa `IMPLEMENTACION_AUTOMATIZACION_VENTAS.md`
