# Guía de Instalación Rápida

## Instalación en 5 Minutos

### 1. Crear la Base de Datos

```bash
mysql -u root -p < database.sql
```

O manualmente en MySQL:
```sql
CREATE DATABASE filemanager_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE filemanager_db;
source database.sql;
```

### 2. Configurar la Aplicación

```bash
# Copiar el archivo de configuración de ejemplo
cp config/config.example.php config/config.php

# Editar la configuración
nano config/config.php
```

Actualizar estas líneas:
```php
define('DB_USER', 'tu_usuario_mysql');
define('DB_PASS', 'tu_contraseña_mysql');
define('BASE_URL', 'http://tu-dominio.com/archivosmysql/public');
```

### 3. Configurar Permisos

```bash
chmod -R 755 .
chmod -R 777 uploads/
```

### 4. Probar la Aplicación

**Opción A: Servidor PHP integrado (desarrollo)**
```bash
cd public
php -S localhost:8000
```
Abrir: http://localhost:8000

**Opción B: Apache**
- Configurar VirtualHost apuntando a `/ruta/archivosmysql/public`
- Asegurarse de que mod_rewrite está habilitado: `a2enmod rewrite`
- Reiniciar Apache: `service apache2 restart`

## Verificación

Si todo está bien configurado, deberías ver:
- La página principal del gestor de archivos
- El árbol de carpetas en el sidebar (con la carpeta "Root")
- Botones para crear carpetas y subir archivos

## Problemas Comunes

### "Error de conexión a la base de datos"
- Verificar credenciales en `config/config.php`
- Verificar que MySQL está corriendo: `service mysql status`

### "Página en blanco"
- Verificar permisos de archivos
- Revisar logs de Apache: `tail -f /var/log/apache2/error.log`
- Habilitar display_errors en config.php

### "Estilos no se cargan"
- Verificar BASE_URL en config.php
- Verificar que mod_rewrite está habilitado

### "No se pueden subir archivos"
- Verificar permisos de `uploads/`: debe ser 777
- Verificar configuración PHP: `php -i | grep upload`

## Siguiente Paso

Leer el README.md completo para conocer todas las características.
