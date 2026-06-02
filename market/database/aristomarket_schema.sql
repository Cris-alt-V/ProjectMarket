-- Crear base de datos
CREATE DATABASE AristoMarket;
\c AristoMarket;

-- Tabla Usuarios
CREATE TABLE Usuarios (
    id_usuario SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(100) UNIQUE NOT NULL,
    contraseña VARCHAR(255) NOT NULL,
    tipo_usuario VARCHAR(20) CHECK (tipo_usuario IN ('cliente','vendedor'))
);

-- Tabla Clientes
CREATE TABLE Clientes (
    id_cliente INT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    direccion VARCHAR(150),
    telefono VARCHAR(20),
    FOREIGN KEY (id_cliente) REFERENCES Usuarios(id_usuario)
);

-- Tabla Vendedores
CREATE TABLE Vendedores (
    id_vendedor INT PRIMARY KEY,
    nombre_negocio VARCHAR(100) NOT NULL,
    descripcion TEXT,
    ubicacion VARCHAR(150),
    FOREIGN KEY (id_vendedor) REFERENCES Usuarios(id_usuario)
);

-- Tabla Productos
CREATE TABLE Productos (
    id_producto SERIAL PRIMARY KEY,
    id_vendedor INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    categoria VARCHAR(80) NOT NULL DEFAULT 'General',
    precio DECIMAL(10,2) NOT NULL,
    stock INT DEFAULT 0,
    imagen_url VARCHAR(255),
    FOREIGN KEY (id_vendedor) REFERENCES Vendedores(id_vendedor)
);

-- Tabla Pedidos
CREATE TABLE Pedidos (
    id_pedido SERIAL PRIMARY KEY,
    id_cliente INT NOT NULL,
    fecha DATE NOT NULL,
    estado VARCHAR(20) CHECK (estado IN ('pendiente','enviado','entregado')),
    FOREIGN KEY (id_cliente) REFERENCES Clientes(id_cliente)
);

-- Tabla DetallePedido
CREATE TABLE DetallePedido (
    id_detalle SERIAL PRIMARY KEY,
    id_pedido INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_pedido) REFERENCES Pedidos(id_pedido),
    FOREIGN KEY (id_producto) REFERENCES Productos(id_producto)
);

-- Tabla Ventas
CREATE TABLE Ventas (
    id_venta SERIAL PRIMARY KEY,
    id_pedido INT NOT NULL,
    id_cliente INT NOT NULL,
    fecha_venta DATE NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    metodo_pago VARCHAR(20) CHECK (metodo_pago IN ('contra entrega','simulado')),
    FOREIGN KEY (id_pedido) REFERENCES Pedidos(id_pedido),
    FOREIGN KEY (id_cliente) REFERENCES Clientes(id_cliente)
);

-- Tabla Reseñas
CREATE TABLE Reseñas (
    id_reseña SERIAL PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_producto INT NOT NULL,
    puntuacion INT CHECK (puntuacion BETWEEN 1 AND 5),
    comentario TEXT,
    fecha DATE NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario),
    FOREIGN KEY (id_producto) REFERENCES Productos(id_producto)
);
