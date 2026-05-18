## Configuración Inicial del Entorno

Siga estos pasos para configurar el entorno local y ejecutar las migraciones de la base de datos:

### 1. Configuración del Archivo de Entorno
Cree o edite el archivo `.env` en la raíz del proyecto e introduzca sus credenciales de acceso local. Asegúrese de especificar el nombre correcto de la base de datos:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=AristoMarket
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña

2. Creación de la Base de Datos
​Antes de proceder, acceda a su gestor de bases de datos (ej. phpMyAdmin, DBeaver, Laragon) y cree únicamente la base de datos vacía con el nombre especificado en el archivo de configuración:
​Nombre: AristoMarket
​Nota: No es necesario crear tablas manualmente; el framework se encargará de estructurarlas.
​3. Ejecución de Migraciones
​Una vez verificada la conexión, ejecute el siguiente comando en la terminal para construir la estructura de la base de datos desde cero:

php artisan migrate:fresh
