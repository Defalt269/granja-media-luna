-- Base de datos para Granja Media Luna
CREATE DATABASE IF NOT EXISTS granja_media_luna;
USE granja_media_luna;

-- Tabla de productos
CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    cantidad_disponible INT NOT NULL DEFAULT 0
);

-- Tabla de clientes
CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    cedula VARCHAR(20) UNIQUE NOT NULL,
    direccion TEXT,
    telefono VARCHAR(20),
    correo VARCHAR(100)
);

-- Tabla de ventas
CREATE TABLE ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT NOT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_cliente) REFERENCES clientes(id)
);

-- Tabla de detalle de venta
CREATE TABLE detalle_venta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_venta INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_venta) REFERENCES ventas(id),
    FOREIGN KEY (id_producto) REFERENCES productos(id)
);

-- Tabla de usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_usuario VARCHAR(50) UNIQUE NOT NULL,
    contraseña VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'empleado') DEFAULT 'empleado'
);

-- Insertar datos de ejemplo
INSERT INTO productos (nombre, tipo, precio, cantidad_disponible) VALUES
('Huevo Blanco Grande', 'Blanco', 500.00, 100),
('Huevo Blanco Mediano', 'Blanco', 450.00, 150),
('Huevo Rojo Grande', 'Rojo', 550.00, 80),
('Huevo Rojo Mediano', 'Rojo', 500.00, 120);

INSERT INTO usuarios (nombre_usuario, contraseña, rol) VALUES
('admin', MD5('admin123'), 'admin'),
('empleado1', MD5('emp123'), 'empleado');