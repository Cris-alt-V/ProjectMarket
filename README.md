## Indicaciones de instacion del proyecto:
## clonar el repositorio
git clone https://github.com/Cris-alt-V/ProjectMarket.git
cd ProjectMarket
git checkout chatdeusuarios
## Asegurarse de tener una version de php 8.0 o superior para descargar composer
composer install
## Asegurarse de tener Node.js superior a 18 junto a npm
npm install

## correr laravel y ver la visualizacion:
php artisan serve

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


Nombre del proyecto:

Aristo Market

El proyecto Aristo Market es una plataforma de comercio electrónico diseñada para facilitar la interacción entre compradores y vendedores dentro de un entorno digital moderno. Su desarrollo integra funcionalidades clave como el sistema de reseñas con calificaciones en estrellas, la aplicación de cupones promocionales que generan descuentos, el cálculo automático de impuestos en las compras y un apartado visual mejorado para el registro e inicio de sesión. Además, se han realizado pruebas que garantizan la persistencia de datos, la correcta validación de entradas y la actualización dinámica de la información en tiempo real, lo que convierte a Aristo Market en un sistema robusto y confiable que refleja las características esenciales de un e‑commerce actual.

Saul Antonio Amaya Umanzor SMSS017024.  

Francisco Javier Hernández Aguirre SMSS068924.  

Yerovi Josué Martínez Gómez SMSS030924.  

Diego Steven Montoya Castro SMSS054024.  

Cristian Alexis Velásquez Hernández SMSS024224.

============BASE DE DATOS==============

Para este proyecto se empleo el gestor de base de datos "Postgres sql"

Se empleo este gestor gracias a su capacidad de crear modelos relacionales, cosa que para el objetivo del proyecto se adapta
de manera perfecta permitiendo gestionar de forma excelente los datos. Como por ejemplo el tema de que negocios tiene un vendedor
, que producto tiene en cada negocio y otros tipos de relaciones. Ademas que postgres es muy fiable para manejar datos y para crear modelos de base de datos escalables como lo puede ser la de este proyecto al querer expandirlo a futuro
