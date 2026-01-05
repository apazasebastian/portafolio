#  Sistema de Reservas de Recintos Deportivos

Sistema web integral para la gestión de reservas de espacios deportivos municipales, desarrollado para la **Municipalidad de Arica**, Región de Arica y Parinacota, Chile.

##  Descripción

Este sistema permite a organizaciones deportivas y ciudadanos solicitar reservas de recintos deportivos municipales de forma simple y transparente, mientras proporciona a los administradores municipales herramientas completas para gestionar, aprobar y monitorear el uso de las instalaciones deportivas.

### Características Principales

-  **Calendario Público Interactivo**: Visualización en tiempo real de disponibilidad de recintos
-  **Gestión de Reservas**: Proceso completo desde solicitud hasta aprobación/rechazo
-  **Sistema de Roles**: Jefe de Recintos y Encargados de Recinto con permisos diferenciados
-  **Notificaciones Automáticas**: Emails de confirmación, aprobación, rechazo y cancelación
-  **Cancelación con Código**: Sistema seguro de cancelación mediante códigos únicos
-  **Gestión de Recintos**: Configuración flexible de horarios, capacidades y bloqueos
-  **Sistema de Incidencias**: Reporte y seguimiento de problemas post-uso
-  **Estadísticas y Reportes**: Dashboard con gráficos y exportación a Excel/PDF
-  **Auditoría Completa**: Trazabilidad total de todas las acciones administrativas
-  **Gestión de Eventos**: Publicación de noticias y eventos deportivos

##  Tecnologías Utilizadas

### Backend
- **Framework**: Laravel 11.x
- **Lenguaje**: PHP 8.2+
- **Base de Datos**: MySQL 8.0+ / MariaDB 10.3+
- **Autenticación**: Laravel Breeze
- **ORM**: Eloquent

### Frontend
- **Motor de Plantillas**: Blade
- **CSS Framework**: Tailwind CSS 3.x
- **JavaScript**: Alpine.js 3.x + Vanilla JS
- **Componentes**: HTML5 responsive

### Herramientas y Servicios
- **Control de Versiones**: Git
- **Gestor de Dependencias PHP**: Composer
- **Gestor de Dependencias JS**: NPM
- **Servidor Web**: Apache / Nginx
- **Email**: SMTP (configurable)

##  Requisitos del Sistema

- PHP >= 8.2
- MySQL >= 8.0 o MariaDB >= 10.3
- Composer >= 2.x
- Node.js >= 18.x
- NPM >= 9.x
- Extensiones PHP: OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON, BCMath, Fileinfo, GD

##  Instalación

### 1. Clonar el repositorio

```bash
git clone https://github.com/tu-usuario/sistema-reservas-arica.git
cd sistema-reservas-arica
```

### 2. Instalar dependencias de PHP

```bash
composer install
```

### 3. Instalar dependencias de Node.js

```bash
npm install
npm run build
```

### 4. Configurar variables de entorno

```bash
cp .env.example .env
```

Editar el archivo `.env` y configurar:

```env
APP_NAME="Sistema Reservas Arica"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=reservas_db
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu_email@gmail.com
MAIL_PASSWORD=tu_contraseña_app
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=reservas@muniarica.cl
MAIL_FROM_NAME="${APP_NAME}"
```

### 5. Generar clave de aplicación

```bash
php artisan key:generate
```

### 6. Ejecutar migraciones

```bash
php artisan migrate
```

### 7. Crear enlace simbólico para storage

```bash
php artisan storage:link
```

### 8. Crear usuario administrador

```bash
php artisan tinker
```

Dentro de tinker:

```php
\App\Models\User::create([
    'name' => 'Administrador',
    'email' => 'admin@muniarica.cl',
    'password' => bcrypt('contraseña_segura_aqui'),
    'role' => 'jefe_recintos',
    'activo' => true
]);
```

### 9. Iniciar servidor de desarrollo

```bash
php artisan serve
```

El sistema estará disponible en: `http://localhost:8000`

##  Estructura del Proyecto

```
├── app/
│   ├── Http/
│   │   ├── Controllers/          # Controladores
│   │   │   ├── Admin/            # Controladores administrativos
│   │   │   └── ...
│   │   └── Middleware/           # Middleware personalizado
│   └── Models/                   # Modelos Eloquent
│       ├── Recinto.php
│       ├── Reserva.php
│       ├── Incidencia.php
│       ├── AuditLog.php
│       └── ...
├── database/
│   └── migrations/               # Migraciones de base de datos
├── resources/
│   └── views/                    # Vistas Blade
│       ├── admin/                # Vistas administrativas
│       ├── emails/               # Plantillas de emails
│       └── ...
├── routes/
│   └── web.php                   # Definición de rutas
└── public/                       # Archivos públicos
```

##  Base de Datos

### Tablas Principales

- **users**: Usuarios administrativos del sistema
- **recintos**: Espacios deportivos disponibles
- **reservas**: Solicitudes y reservas confirmadas
- **incidencias**: Reportes de problemas post-uso
- **audit_logs**: Registro de auditoría completo
- **eventos**: Noticias y eventos deportivos
- **password_reset_tokens**: Tokens de recuperación de contraseña

##  Roles y Permisos

### Jefe de Recintos
- Acceso completo al sistema
- Gestionar todos los recintos
- Aprobar/rechazar todas las reservas
- Ver y gestionar todas las incidencias
- Acceso completo a auditoría
- Ver estadísticas globales
- Gestionar eventos
- Exportar reportes

### Encargado de Recinto
- Ver reservas de su recinto asignado
- Aprobar/rechazar reservas de su recinto
- Gestionar incidencias de su recinto
- Ver estadísticas de su recinto
- Exportar datos de su recinto

### Público (sin autenticación)
- Ver calendario de disponibilidad
- Solicitar nuevas reservas
- Cancelar reservas con código
- Ver eventos públicos

##  Configuración de Producción

### 1. Variables de entorno

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://reservas.muniarica.cl
```

### 2. Optimización

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer install --optimize-autoloader --no-dev
```

### 3. Permisos

```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 4. Servidor Web (Nginx)

```nginx
server {
    listen 80;
    server_name reservas.muniarica.cl;
    root /var/www/sistema-reservas/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 5. SSL con Let's Encrypt

```bash
sudo certbot --nginx -d reservas.muniarica.cl
```

##  Funcionalidades Detalladas

### Gestión de Reservas

1. **Solicitud Pública**:
   - Selección de recinto en calendario interactivo
   - Formulario con validación de RUT chileno
   - Verificación de disponibilidad en tiempo real
   - Control de solapamiento de horarios
   - Aceptación de reglamento obligatoria

2. **Proceso de Aprobación**:
   - Dashboard administrativo con reservas pendientes
   - Aprobación con generación automática de código de cancelación
   - Rechazo con registro de motivo
   - Notificaciones automáticas por email

3. **Cancelación**:
   - Formulario público con código único
   - Verificación de datos antes de cancelar
   - Liberación automática del horario
   - Registro en auditoría

### Gestión de Recintos

- Nombre, descripción y capacidad máxima
- Horarios de apertura y cierre configurables
- Días completos cerrados (ej: "Cerrado los lunes")
- Bloqueos específicos por fecha (ej: "15/01/2025 de 10:00 a 14:00 - Mantenimiento")
- Carga de imagen representativa
- Activación/desactivación temporal

### Sistema de Incidencias

- Tipos: Problema post-uso, Daño, Otro
- Descripción detallada del problema
- Estados: Reportada, En revisión, Resuelta
- Vinculación directa con la reserva
- Notificaciones a encargados

### Auditoría y Trazabilidad

Registro automático de:
- Todas las acciones de aprobación/rechazo
- Cancelaciones (por admin o usuario)
- Creación y edición de recintos
- Gestión de eventos e incidencias
- Exportaciones de datos
- Inicio y cierre de sesión
- IP, user agent y timestamp de cada acción

### Estadísticas y Reportes

- Gráficos de reservas por recinto
- Análisis por deporte practicado
- Top organizaciones más activas
- Tendencias mensuales y anuales
- Tasa de aprobación y ocupación
- Exportación a Excel y PDF
- Reportes históricos por organización

##  Seguridad

-  Autenticación con Laravel Breeze
-  Hash bcrypt para contraseñas
-  Protección CSRF en todos los formularios
-  Validación exhaustiva de entrada
-  Eloquent ORM previene SQL injection
-  Escape automático XSS en Blade
-  Auditoría con IP y user agent
-  Control de acceso basado en roles
-  Códigos de cancelación únicos e irrepetibles

##  Notificaciones por Email

El sistema envía emails automáticos en:

-  **Reserva Aprobada**: Confirmación con código de cancelación
-  **Reserva Rechazada**: Notificación con motivo
-  **Reserva Cancelada**: Confirmación de cancelación
-  **Nueva Solicitud**: Alerta a administradores
-  **Recuperación de Contraseña**: Link de restablecimiento

##  Testing

```bash
# Ejecutar tests
php artisan test

# Con cobertura
php artisan test --coverage
```

## 🛠️ Mantenimiento

### Comandos Útiles

```bash
# Limpiar cachés
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Regenerar cachés (producción)
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ver información del sistema
php artisan about

# Migrar base de datos
php artisan migrate

# Rollback de migraciones
php artisan migrate:rollback
```

### Tareas Programadas

- **Diario**: Backup de base de datos
- **Semanal**: Limpieza de logs, revisión de auditoría
- **Mensual**: Actualización de dependencias, limpieza de archivos temporales

##  Solución de Problemas

### No se envían correos

**Problema**: Los emails no llegan a los destinatarios

**Solución**: 
1. Verificar configuración `MAIL_*` en `.env`
2. Para Gmail, usar "Contraseñas de aplicación"
3. Probar envío con: `php artisan tinker` → `Mail::raw('Test', function($m) { $m->to('email@test.com')->subject('Test'); });`

### Error 500 al acceder

**Problema**: Error interno del servidor

**Solución**:
1. Revisar logs: `storage/logs/laravel.log`
2. Verificar permisos: `chmod -R 755 storage bootstrap/cache`
3. Limpiar cachés: `php artisan config:clear`

### Las imágenes no se muestran

**Problema**: Imágenes de recintos no cargan

**Solución**:
1. Crear enlace simbólico: `php artisan storage:link`
2. Verificar permisos en `storage/app/public/`

### Error de conexión a BD

**Problema**: No conecta con MySQL

**Solución**:
1. Verificar que MySQL esté corriendo: `sudo systemctl status mysql`
2. Revisar credenciales en `.env`
3. Probar conexión: `mysql -u usuario -p`

##  Licencia

Este proyecto ha sido desarrollado para la **Municipalidad de Arica** como software a medida para la gestión de recintos deportivos municipales.

##  Contribución

Este es un proyecto de software municipal. 


##  Capturas de Pantalla

### Calendario Público
<img width="1506" height="761" alt="image" src="https://github.com/user-attachments/assets/0a54e3a3-612f-44ba-a496-6633130f352d" />


### Panel Administrativo
<img width="1605" height="742" alt="image" src="https://github.com/user-attachments/assets/1bec5627-7a21-47cb-ba4e-cb72fe8e773f" />

### Gestión de Reservas
<img width="1505" height="775" alt="image" src="https://github.com/user-attachments/assets/f63bf111-e10e-4c5b-b604-b8f395b07248" />


### Estadísticas
<img width="1523" height="767" alt="image" src="https://github.com/user-attachments/assets/b541411f-a290-463c-b6b8-70c252b33245" />

<img width="1502" height="808" alt="image" src="https://github.com/user-attachments/assets/476387d6-6caa-47d8-8787-730851afdbe8" />


