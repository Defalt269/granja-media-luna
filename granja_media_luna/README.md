# Granja Media Luna - Sistema Web de Gestión

## Descripción
Aplicativo web para la comercialización y facturación de huevos de la Granja Media Luna, con módulo de gestión administrativa.

## Tecnologías Utilizadas
- **Frontend:** HTML5, CSS3, JavaScript
- **Backend:** PHP
- **Base de Datos:** MySQL (XAMPP)

## Estructura del Proyecto
```
/granja_media_luna/
├── index.html          # Página de inicio
├── productos.php       # Gestión de productos (CRUD)
├── clientes.php        # Gestión de clientes (CRUD)
├── facturacion.php     # Sistema de facturación
├── admin.php           # Panel administrativo
├── contacto.html       # Página de contacto
├── css/
│   └── styles.css      # Estilos CSS
├── js/
│   └── validaciones.js # Validaciones JavaScript
├── php/
│   └── conexion.php    # Conexión a base de datos
└── sql/
    └── granja_media_luna.sql # Script de base de datos
```

## Instalación y Configuración

### 1. Requisitos Previos
- XAMPP instalado y ejecutándose
- Navegador web moderno

### 2. Configuración de la Base de Datos
1. Abrir phpMyAdmin en XAMPP
2. Crear base de datos: `granja_media_luna`
3. Importar el archivo `sql/granja_media_luna.sql`

### 3. Configuración del Servidor
1. Copiar la carpeta `granja_media_luna` al directorio `htdocs` de XAMPP
2. Acceder desde el navegador: `http://localhost/granja_media_luna/`

## Funcionalidades

### Gestión de Productos
- Agregar, editar, eliminar productos
- Control de inventario
- Precios por unidad

### Gestión de Clientes
- Registro de nuevos clientes
- Consulta y edición de datos
- Información de contacto

### Sistema de Facturación
- Generación de facturas
- Cálculo automático de totales
- Registro en base de datos
- Actualización de inventario

### Panel Administrativo
- Inicio de sesión seguro
- Estadísticas del negocio
- Control de acceso restringido

### Validaciones
- Validación de formularios con JavaScript
- Validación de datos en servidor con PHP
- Mensajes de error informativos

## Usuarios de Prueba
- **Administrador:** usuario: `admin`, contraseña: `admin123`
- **Empleado:** usuario: `empleado1`, contraseña: `emp123`

## Características Técnicas
- Diseño responsive
- Interfaz amigable
- Seguridad básica con sesiones
- Transacciones seguras en base de datos
- Validaciones cliente y servidor

## Desarrollo
Este proyecto cumple con todos los requisitos especificados:
- Estructura semántica HTML5
- Estilos CSS3 con tema agrícola
- Funcionalidad JavaScript para cálculos dinámicos
- Backend PHP con MySQL
- Sistema de gestión completo

## Notas
- Asegurarse de que XAMPP esté ejecutándose antes de usar la aplicación
- Las imágenes de la granja pueden agregarse en la carpeta `img/`
- El sistema está optimizado para funcionar en entornos locales con XAMPP