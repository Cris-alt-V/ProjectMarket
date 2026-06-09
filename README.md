Se debe clonar el repositorio de la rama testdb, el commit mas reciente.

## Configuración Inicial del Entorno

Siga estos pasos para configurar el entorno local y ejecutar las migraciones de la base de datos:

### 1. Configuración del Archivo de Entorno
Cree o edite el archivo `.env` en la raíz del proyecto e introduzca sus credenciales de acceso local. Asegúrese de especificar el nombre correcto de la base de datos:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5436
DB_DATABASE=AristoMarket
DB_USERNAME=market_user
DB_PASSWORD=market123


2. Creación de la Base de Datos
​Antes de proceder, acceda a su gestor de bases de datos en este caso postgresql y cree únicamente la base de datos vacía con el nombre especificado en el archivo de configuración:
​Nombre: AristoMarket
​Nota: No es necesario crear tablas manualmente; el framework se encargará de estructurarlas.
​3. Ejecución de Migraciones
​Una vez verificada la conexión, ejecute el siguiente comando en la terminal para construir la estructura de la base de datos desde cero:

luego en postgre cree el usuario market_user

Creacion del usuario market_user:
-- Crear usuario con contraseña
CREATE USER market_user WITH PASSWORD 'market123';

-- Darle permisos sobre la base de datos AristoMarket
GRANT ALL PRIVILEGES ON DATABASE "AristoMarket" TO market_user;

-- Conectarse a la base de datos
\c AristoMarket

-- Darle permisos sobre todas las tablas y secuencias
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO market_user;
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO market_user;

Se debe haber creado antes la bd y ejecutado la migracion

php artisan migrate:fresh

## Instalación de dependencias para chat en tiempo real

1. Instalar Node.js y npm.
2. Crear carpeta `server-chat` dentro de `ProjectMarket`.
3. Ejecutar `npm init -y`.
4. Instalar dependencias:
   - `npm install express socket.io pg`
   - `npm install nodemon --save-dev` (opcional para desarrollo).
5. Iniciar servidor con `node index.js` o `nodemon index.js`.

### Configuración del servidor de chat

La carpeta `server-chat` ya contiene el código del servidor WebSocket. Antes de iniciarlo, configura las variables de entorno necesarias para conectarlo a Postgres:

```env
PORT=3001
CLIENT_ORIGIN=http://localhost:8000
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=AristoMarket
DB_USERNAME=market_user
DB_PASSWORD=market123
```

También puedes usar `DATABASE_URL` si prefieres una sola cadena de conexión.

### Uso

- Ejecutar las migraciones de Laravel para crear o actualizar la tabla `messages`:

```bash
cd market
php artisan migrate
```

- Ejecutar el servidor WebSocket en la carpeta `server-chat`:

```bash
cd ../server-chat
npm install
npm start
```

- Abrir el proyecto en navegador y acceder al apartado "Mensajes".
- Probar el chat en tiempo real entre comprador y vendedor.
