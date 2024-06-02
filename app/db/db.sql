CREATE TABLE Usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(30) NOT NULL,
    password VARCHAR(255) NOT NULL,
    fecha_baja date DEFAULT NULL,
    nombre VARCHAR(30) NOT NULL,
    fecha_ingreso DATETIME NOT NULL,
    rol_empleado ENUM( 'socio','bartender', 'cervecero', 'cocinero', 'mozo') NOT NULL
);


CREATE TABLE Productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('TragoVino', 'PlatoPrincipal', 'Postre', 'Cerveza') NOT NULL,
    nombre VARCHAR(50) NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    fecha_baja date DEFAULT NULL
);


CREATE TABLE Pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(5) NOT NULL UNIQUE,
    id_mesa INT NOT NULL,
    estado ENUM('pendiente', 'en preparación', 'listo para servir') NOT NULL,
    foto VARCHAR(255),
    tiempo_estimado DATETIME,
    fecha_hora DATETIME NOT NULL,
    FOREIGN KEY (id_mesa) REFERENCES Mesa(id)
);


CREATE TABLE EmpleadosPedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_pedido INT NOT NULL,
    id_empleado INT NOT NULL,
    estado ENUM('pendiente', 'en preparación', 'listo para servir') NOT NULL,
    FOREIGN KEY (id_pedido) REFERENCES Pedido(id),
    FOREIGN KEY (id_empleado) REFERENCES Empleado(id)
);
CREATE TABLE ItemsPedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_pedido INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL,
    estado ENUM('pendiente', 'en preparación', 'listo para servir') NOT NULL,
    id_empleado INT NOT NULL,
    FOREIGN KEY (id_pedido) REFERENCES Pedido(id),
    FOREIGN KEY (id_producto) REFERENCES Producto(id),
    FOREIGN KEY (id_empleado) REFERENCES Empleado(id)
);


CREATE TABLE Mesas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(5) NOT NULL UNIQUE,
    estado ENUM('con cliente esperando pedido', 'con cliente comiendo', 'con cliente pagando', 'cerrada') NOT NULL,
    total_pedido DECIMAL(10, 2) DEFAULT 0 -- Opcional, se puede calcular dinámicamente
);


CREATE TABLE Encuestas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_pedido INT NOT NULL,
    mesa_puntuacion INT NOT NULL,
    restaurante_puntuacion INT NOT NULL,
    mozo_puntuacion INT NOT NULL,
    cocinero_puntuacion INT NOT NULL,
    comentario VARCHAR(66),
    FOREIGN KEY (id_pedido) REFERENCES Pedido(id)
);

