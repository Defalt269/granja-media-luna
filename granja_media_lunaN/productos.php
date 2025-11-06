<?php
include 'php/conexion.php';

// Función para obtener todos los productos
function obtenerProductos() {
    global $conn;
    $sql = "SELECT * FROM productos ORDER BY nombre";
    $result = $conn->query($sql);
    return $result;
}

// Función para agregar producto
function agregarProducto($nombre, $tipo, $precio, $cantidad) {
    global $conn;
    $sql = "INSERT INTO productos (nombre, tipo, precio, cantidad_disponible) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdi", $nombre, $tipo, $precio, $cantidad);
    return $stmt->execute();
}

// Función para actualizar producto
function actualizarProducto($id, $nombre, $tipo, $precio, $cantidad) {
    global $conn;
    $sql = "UPDATE productos SET nombre=?, tipo=?, precio=?, cantidad_disponible=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdii", $nombre, $tipo, $precio, $cantidad, $id);
    return $stmt->execute();
}

// Función para eliminar producto
function eliminarProducto($id) {
    global $conn;
    $sql = "DELETE FROM productos WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// Procesar acciones POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['agregar'])) {
        agregarProducto($_POST['nombre'], $_POST['tipo'], $_POST['precio'], $_POST['cantidad']);
    } elseif (isset($_POST['actualizar'])) {
        actualizarProducto($_POST['id'], $_POST['nombre'], $_POST['tipo'], $_POST['precio'], $_POST['cantidad']);
    } elseif (isset($_POST['eliminar'])) {
        eliminarProducto($_POST['id']);
    }
    header("Location: productos.php");
    exit();
}

$productos = obtenerProductos();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos - Granja Media Luna</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <h1>Granja Media Luna</h1>
        <p>Gestión de Productos</p>
    </header>

    <nav>
        <ul>
            <li><a href="index.html">Inicio</a></li>
            <li><a href="productos.php">Productos</a></li>
            <li><a href="clientes.php">Clientes</a></li>
            <li><a href="facturacion.php">Facturación</a></li>
            <li><a href="admin.php">Administración</a></li>
            <li><a href="contacto.html">Contacto</a></li>
        </ul>
    </nav>

    <main>
        <section>
            <h2>Agregar Nuevo Producto</h2>
            <form id="formProducto" method="POST">
                <div class="form-group">
                    <label for="nombre">Nombre del Producto:</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                <div class="form-group">
                    <label for="tipo">Tipo:</label>
                    <select id="tipo" name="tipo" required>
                        <option value="">Seleccionar tipo</option>
                        <option value="Blanco">Blanco</option>
                        <option value="Rojo">Rojo</option>
                        <option value="criollo">Criollo</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="precio">Precio por Unidad:</label>
                    <input type="number" id="precio" name="precio" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="cantidad">Cantidad Disponible:</label>
                    <input type="number" id="cantidad" name="cantidad" required>
                </div>
                <button type="submit" name="agregar">Agregar Producto</button>
            </form>
        </section>

        <section>
            <h2>Lista de Productos</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $productos->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($row['tipo']); ?></td>
                        <td>$<?php echo number_format($row['precio'], 2); ?></td>
                        <td><?php echo $row['cantidad_disponible']; ?></td>
                        <td>
                            <button onclick="editarProducto(<?php echo $row['id']; ?>, '<?php echo addslashes($row['nombre']); ?>', '<?php echo $row['tipo']; ?>', <?php echo $row['precio']; ?>, <?php echo $row['cantidad_disponible']; ?>)">Editar</button>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="eliminar" onclick="return confirm('¿Está seguro de eliminar este producto?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>

        <!-- Formulario oculto para edición -->
        <section id="editarForm" style="display: none;">
            <h2>Editar Producto</h2>
            <form id="formEditar" method="POST">
                <input type="hidden" id="editId" name="id">
                <div class="form-group">
                    <label for="editNombre">Nombre del Producto:</label>
                    <input type="text" id="editNombre" name="nombre" required>
                </div>
                <div class="form-group">
                    <label for="editTipo">Tipo:</label>
                    <select id="editTipo" name="tipo" required>
                        <option value="Blanco">Blanco</option>
                        <option value="Rojo">Rojo</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="editPrecio">Precio por Unidad:</label>
                    <input type="number" id="editPrecio" name="precio" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="editCantidad">Cantidad Disponible:</label>
                    <input type="number" id="editCantidad" name="cantidad" required>
                </div>
                <button type="submit" name="actualizar">Actualizar Producto</button>
                <button type="button" onclick="cancelarEdicion()">Cancelar</button>
            </form>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Granja Media Luna. Todos los derechos reservados.</p>
    </footer>

    <script src="js/validaciones.js"></script>
    <script>
        function editarProducto(id, nombre, tipo, precio, cantidad) {
            document.getElementById('editId').value = id;
            document.getElementById('editNombre').value = nombre;
            document.getElementById('editTipo').value = tipo;
            document.getElementById('editPrecio').value = precio;
            document.getElementById('editCantidad').value = cantidad;
            document.getElementById('editarForm').style.display = 'block';
            document.getElementById('editarForm').scrollIntoView();
        }

        function cancelarEdicion() {
            document.getElementById('editarForm').style.display = 'none';
        }
    </script>
</body>
</html>