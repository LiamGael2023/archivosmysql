# Sistema de Gestión de Archivos

Sistema completo de gestión de archivos similar a OwnCloud, desarrollado en PHP con arquitectura MVC y MySQL.

## Características

- **Gestión de Carpetas**: Crear carpetas con estructura jerárquica ilimitada
- **Gestión de Archivos**: Subir, renombrar, eliminar y descargar archivos de cualquier tipo
- **Metakeys**: Sistema de metadatos/etiquetas para organizar y clasificar archivos
- **Árbol de Carpetas**: Visualización jerárquica de toda la estructura de carpetas
- **Búsqueda Avanzada**: Buscar archivos por nombre o metakeys
- **Filtrado**: Filtrar archivos por metakeys específicos
- **Ordenamiento**: Ordenar archivos por ID, nombre, fecha de creación o tamaño (ASC/DESC)
- **Compartir**: Generar enlaces públicos para compartir archivos o carpetas
- **Interfaz Moderna**: Diseño profesional con Tabler.io, completamente responsive
- **Drag & Drop**: Arrastra y suelta archivos para subirlos fácilmente
- **Responsive Design**: Optimizado para móviles, tablets y desktop

## Tecnologías

- PHP 7.4+
- MySQL 5.7+
- PDO para conexión a base de datos
- Arquitectura MVC
- **Tabler.io** - Framework UI moderno basado en Bootstrap 5
- **Tabler Icons** - Conjunto de iconos SVG
- HTML5, CSS3, JavaScript vanilla
- Diseño responsive con breakpoints optimizados

## Estructura del Proyecto

```
archivosmysql/
├── app/
│   ├── controllers/      # Controladores MVC
│   │   ├── HomeController.php
│   │   ├── FileController.php
│   │   ├── FolderController.php
│   │   └── ShareController.php
│   ├── models/           # Modelos de datos
│   │   ├── Database.php
│   │   ├── File.php
│   │   ├── Folder.php
│   │   ├── MetaKey.php
│   │   └── SharedLink.php
│   └── views/            # Vistas HTML
│       ├── home.php
│       ├── shared_folder.php
│       └── shared_links.php
├── config/
│   └── config.php        # Configuración del sistema
├── public/               # Directorio público
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── main.js
│   ├── index.php         # Punto de entrada
│   └── .htaccess
├── uploads/              # Archivos subidos
├── database.sql          # Script de base de datos
├── .htaccess
└── README.md
```

## Instalación

### Requisitos

- Servidor web Apache con mod_rewrite habilitado
- PHP 7.4 o superior
- MySQL 5.7 o superior
- Extensión PDO de PHP habilitada

### Pasos de Instalación

1. **Clonar o descargar el proyecto**
   ```bash
   git clone https://github.com/tu-usuario/archivosmysql.git
   cd archivosmysql
   ```

2. **Configurar permisos**
   ```bash
   chmod -R 755 .
   chmod -R 777 uploads/
   ```

3. **Crear la base de datos**
   - Acceder a MySQL:
     ```bash
     mysql -u root -p
     ```
   - Importar el script:
     ```sql
     source database.sql
     ```
   - O desde línea de comandos:
     ```bash
     mysql -u root -p < database.sql
     ```

4. **Configurar la conexión a la base de datos**
   - Editar el archivo `config/config.php`
   - Actualizar las credenciales de la base de datos:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_NAME', 'filemanager_db');
     define('DB_USER', 'tu_usuario');
     define('DB_PASS', 'tu_contraseña');
     ```
   - Actualizar la URL base:
     ```php
     define('BASE_URL', 'http://localhost/archivosmysql/public');
     ```

5. **Configurar el servidor web**

   **Opción A: Apache**
   - Asegurarse de que mod_rewrite está habilitado
   - El DocumentRoot debe apuntar al directorio `public/`
   - Los archivos `.htaccess` ya están configurados

   **Opción B: PHP Built-in Server (desarrollo)**
   ```bash
   cd public
   php -S localhost:8000
   ```

6. **Acceder a la aplicación**
   - Abrir el navegador en: `http://localhost/archivosmysql/public`
   - O si usas el servidor PHP: `http://localhost:8000`

## Uso

### Crear Carpetas

1. Click en el botón "📁 Nueva Carpeta"
2. Ingresar el nombre de la carpeta
3. La carpeta se creará en la ubicación actual

### Subir Archivos

1. Click en el botón "📤 Subir Archivo"
2. Seleccionar el archivo
3. (Opcional) Agregar metakeys para clasificar el archivo
4. Click en "Subir"

### Agregar Metakeys

Los metakeys son etiquetas que ayudan a organizar y buscar archivos:

1. En la tabla de archivos, click en "+ Agregar" en la columna Metakeys
2. Ingresar el nombre del metakey (ej: "categoría", "proyecto", "estado")
3. (Opcional) Ingresar un valor (ej: "importante", "2024", "completado")

### Buscar Archivos

**Búsqueda por texto:**
- Usar el campo de búsqueda en la parte superior derecha
- Busca en nombres de archivos y metakeys

**Filtrar por metakey:**
- Usar el selector "Filtrar por metakey"
- Seleccionar un metakey específico
- (Opcional) Ingresar un valor específico

### Ordenar Archivos

Usar los selectores de ordenamiento:
- **Por**: ID, Nombre, Fecha de Creación, Tamaño
- **Dirección**: Ascendente (ASC) o Descendente (DESC)

### Compartir Archivos/Carpetas

1. Click en el botón "🔗" junto al archivo o carpeta
2. Especificar cuántos días será válido el enlace (0 = sin expiración)
3. Copiar el enlace generado
4. El enlace puede ser compartido públicamente

**Nota:** Los enlaces de archivos permiten descargarlos directamente. Los enlaces de carpetas muestran su contenido.

### Renombrar

- Click en el botón "✏️" junto al archivo o carpeta
- Ingresar el nuevo nombre
- Confirmar

### Eliminar

- Click en el botón "🗑️" junto al archivo o carpeta
- Confirmar la eliminación
- **Advertencia:** Eliminar una carpeta elimina todo su contenido

## Base de Datos

### Tablas

**folders** - Almacena las carpetas
- `id`: Identificador único
- `name`: Nombre de la carpeta
- `parent_id`: ID de la carpeta padre (NULL para raíz)
- `created_at`: Fecha de creación
- `updated_at`: Fecha de actualización

**files** - Almacena los archivos
- `id`: Identificador único
- `name`: Nombre del archivo
- `original_name`: Nombre original del archivo
- `file_path`: Ruta del archivo en el servidor
- `extension`: Extensión del archivo
- `size`: Tamaño en bytes
- `mime_type`: Tipo MIME
- `folder_id`: ID de la carpeta contenedora
- `created_at`: Fecha de creación
- `updated_at`: Fecha de actualización

**meta_keys** - Almacena los metadatos
- `id`: Identificador único
- `entity_type`: Tipo de entidad ('file' o 'folder')
- `entity_id`: ID de la entidad
- `meta_key`: Nombre del metakey
- `meta_value`: Valor del metakey
- `created_at`: Fecha de creación

**shared_links** - Almacena los enlaces compartidos
- `id`: Identificador único
- `token`: Token único de 64 caracteres
- `entity_type`: Tipo de entidad ('file' o 'folder')
- `entity_id`: ID de la entidad
- `is_active`: Estado del enlace (1=activo, 0=inactivo)
- `expires_at`: Fecha de expiración (NULL = sin expiración)
- `created_at`: Fecha de creación

## API/Rutas

### Archivos
- `POST /file/upload` - Subir archivo
- `POST /file/rename` - Renombrar archivo
- `POST /file/delete` - Eliminar archivo
- `GET /file/download?id={id}` - Descargar archivo
- `POST /file/add-meta` - Agregar metakey
- `POST /file/delete-meta` - Eliminar metakey

### Carpetas
- `POST /folder/create` - Crear carpeta
- `POST /folder/rename` - Renombrar carpeta
- `POST /folder/delete` - Eliminar carpeta

### Compartir
- `POST /share/create` - Crear enlace compartido
- `GET /share?token={token}` - Ver contenido compartido
- `POST /share/delete` - Eliminar enlace
- `GET /share/list?entity_type={type}&entity_id={id}` - Listar enlaces

### Navegación
- `GET /` - Página principal
- `GET /?folder={id}` - Ver carpeta específica
- `GET /?search={query}` - Buscar archivos
- `GET /?filter_key={key}&filter_value={value}` - Filtrar por metakey
- `GET /?order={field}&dir={ASC|DESC}` - Ordenar archivos

## Seguridad

- Validación de extensiones de archivo
- Protección contra inyección SQL usando PDO prepared statements
- Protección de directorios sensibles mediante .htaccess
- Tokens únicos para enlaces compartidos
- Validación de datos de entrada

## Configuración Avanzada

### Cambiar tamaño máximo de subida

Editar `config/config.php`:
```php
define('MAX_UPLOAD_SIZE', 100 * 1024 * 1024); // 100MB
```

Y `public/.htaccess`:
```apache
php_value upload_max_filesize 100M
php_value post_max_size 100M
```

### Cambiar zona horaria

Editar `config/config.php`:
```php
date_default_timezone_set('America/Mexico_City');
```

## Troubleshooting

### Error de conexión a la base de datos
- Verificar credenciales en `config/config.php`
- Asegurarse de que MySQL está corriendo
- Verificar que la base de datos existe

### Archivos no se suben
- Verificar permisos del directorio `uploads/` (debe ser 777)
- Verificar configuración de `upload_max_filesize` en PHP
- Revisar logs de Apache/PHP

### URLs no funcionan (404)
- Verificar que mod_rewrite está habilitado
- Asegurarse de que los archivos `.htaccess` existen
- Verificar la configuración de `BASE_URL` en `config/config.php`

### Estilos no se cargan
- Verificar que la ruta en `BASE_URL` es correcta
- Verificar permisos de lectura en `public/css/` y `public/js/`

## Licencia

Este proyecto es de código abierto y está disponible bajo la licencia MIT.

## Autor

Desarrollado como sistema de gestión de archivos empresarial.

## Contribuciones

Las contribuciones son bienvenidas. Por favor, crear un pull request o abrir un issue para sugerencias.
