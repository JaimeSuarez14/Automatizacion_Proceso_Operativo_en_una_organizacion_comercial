<?php
/**
 * CONFIGURACIÓN DEL SISTEMA DE AUTOMATIZACIÓN DE VENTAS
 * 
 * Este archivo centraliza todas las configuraciones del sistema
 * Modifica los valores según tus necesidades
 */

// ========================================
// 1. CONFIGURACIÓN DE EMAIL
// ========================================
define('EMAIL_CONFIG', [
    'SMTP_HOST' => 'smtp.gmail.com',
    'SMTP_PORT' => 587,
    'SMTP_USER' => 'tu_email@gmail.com',      // CAMBIAR
    'SMTP_PASS' => 'tu_contraseña_app',       // CAMBIAR
    'EMAIL_FROM' => 'tu_email@gmail.com',     // CAMBIAR
    'EMPRESA_NOMBRE' => 'La Choza Náutica',
    'SMTP_SECURE' => 'tls'
]);

// ========================================
// 2. CONFIGURACIÓN DE VALIDACIÓN
// ========================================
define('VALIDACION_CONFIG', [
    'MIN_NOMBRE' => 3,
    'MAX_NOMBRE' => 100,
    'MIN_TELEFONO' => 7,
    'MIN_DIRECCION' => 10,
    'MAX_NOTAS' => 500,
    'REGEX_TELEFONO' => '/^\d{7,}$/'
]);

// ========================================
// 3. CONFIGURACIÓN DEL CARRITO
// ========================================
define('CARRITO_CONFIG', [
    'CANTIDAD_MINIMA' => 1,
    'CANTIDAD_MAXIMA' => 100,
    'TIMEOUT_SESION' => 3600 // 1 hora en segundos
]);

// ========================================
// 4. CONFIGURACIÓN DE MONEDA Y TAXES
// ========================================
define('DINERO_CONFIG', [
    'MONEDA' => 'S/.',
    'DECIMAL_PLACES' => 2,
    'TAXRATE' => 0.18, // 18% IGV
    'SIMBOLO' => 'S/.'
]);

// ========================================
// 5. CONFIGURACIÓN DE API
// ========================================
define('API_CONFIG', [
    'ENABLE_API' => true,
    'LIMITE_RESULTADOS_DEFAULT' => 10,
    'LIMITE_RESULTADOS_MAX' => 100,
    'CACHE_ENABLED' => false,
    'CACHE_TIME' => 300 // segundos
]);

// ========================================
// 6. CONFIGURACIÓN DE REPORTES
// ========================================
define('REPORTE_CONFIG', [
    'INCLUIR_CANCELADOS' => false,
    'FECHA_INICIO_DEFECTO' => '30 days ago', // strtotime compatible
    'GRAFICO_TIPO' => 'bar', // bar, line, pie
    'COLORES_GRADIENTE' => [
        '#667eea',
        '#764ba2',
        '#f093fb',
        '#f5576c'
    ]
]);

// ========================================
// 7. CONFIGURACIÓN DE SEGURIDAD
// ========================================
define('SEGURIDAD_CONFIG', [
    'ENABLE_CSRF' => false, // Cambiar a true en producción
    'REQUIRE_AUTH_API' => false, // Cambiar a true en producción
    'RATE_LIMIT' => 100, // peticiones por minuto
    'SESSION_TIMEOUT' => 3600 // segundos
]);

// ========================================
// 8. CONFIGURACIÓN DE NOTIFICACIONES
// ========================================
define('NOTIFICACION_CONFIG', [
    'ENVIAR_EMAIL_CLIENTE' => true,
    'ENVIAR_EMAIL_ADMIN' => true,
    'EMAIL_ADMIN' => 'admin@lachozanautica.com', // CAMBIAR
    'ENVIAR_SMS' => false, // Requiere configuración adicional
    'NUMERO_ADMIN_WHATSAPP' => '+51987654321' // CAMBIAR
]);

// ========================================
// 9. CONFIGURACIÓN DE BASE DE DATOS
// ========================================
define('BD_CONFIG', [
    'HOST' => 'localhost',
    'USER' => 'root',
    'PASS' => '',
    'NAME' => 'cevichería',
    'CHARSET' => 'utf8mb4',
    'TIMEOUT' => 10
]);

// ========================================
// 10. CONFIGURACIÓN DE ESTADO INICIAL
// ========================================
define('ESTADO_INICIAL_PEDIDO', 1); // 1 = Pendiente

// ========================================
// FUNCIONES AUXILIARES
// ========================================

/**
 * Obtener configuración de email
 */
function obtenerConfigEmail() {
    return EMAIL_CONFIG;
}

/**
 * Obtener símbolo de moneda
 */
function formatoMoneda($cantidad, $incluir_simbolo = true) {
    $formato = number_format($cantidad, DINERO_CONFIG['DECIMAL_PLACES']);
    return $incluir_simbolo ? DINERO_CONFIG['SIMBOLO'] . ' ' . $formato : $formato;
}

/**
 * Calcular IGV
 */
function calcularIGV($monto) {
    return $monto * DINERO_CONFIG['TAXRATE'];
}

/**
 * Validar moneda mínima
 */
function esMonedaValida($monto) {
    return $monto > 0 && is_numeric($monto);
}

/**
 * Validar días festivos (ejemplo simple)
 */
function esHoraLaboral() {
    $hora = date('H');
    $dia_semana = date('w'); // 0 = domingo, 6 = sábado
    
    // Asumir horario de 10 a 22, lun-dom
    return ($hora >= 10 && $hora < 22);
}

/**
 * Obtener tiempo estimado de entrega
 */
function obtenerTiempoEntrega() {
    $fecha_entrega = new DateTime();
    $fecha_entrega->add(new DateInterval('P1D')); // Agregar 1 día
    
    // Si es sábado o domingo, agregar un día más
    if (in_array($fecha_entrega->format('w'), [0, 6])) {
        $fecha_entrega->add(new DateInterval('P1D'));
    }
    
    return $fecha_entrega->format('Y-m-d H:i');
}

// ========================================
// CONFIGURACIÓN DE DESARROLLO vs PRODUCCIÓN
// ========================================

$ambiente = getenv('APP_ENV') ?: 'development';

if ($ambiente === 'production') {
    ini_set('display_errors', 0);
    error_reporting(0);
} else {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

// ========================================
// VERIFICACIÓN DE CONFIGURACIÓN
// ========================================

/**
 * Verificar que la configuración de Email esté completa
 */
function verificarConfigEmail() {
    $config = EMAIL_CONFIG;
    
    $campos_requeridos = ['SMTP_USER', 'SMTP_PASS', 'EMAIL_FROM'];
    $falta_configurar = [];
    
    foreach ($campos_requeridos as $campo) {
        if (strpos($config[$campo], 'tu_') === 0 || $config[$campo] === '') {
            $falta_configurar[] = $campo;
        }
    }
    
    return [
        'valido' => empty($falta_configurar),
        'falta_configurar' => $falta_configurar
    ];
}

?>
