<?php
include 'php/conexion.php';

// Función para obtener todos los clientes
function obtenerClientes() {
    global $conn;
    $sql = "SELECT * FROM clientes ORDER BY nombre";
    $result = $conn->query($sql);
    return $result;
}

// Función para agregar cliente
function agregarCliente($nombre, $cedula, $direccion, $telefono, $correo) {
    global $conn;
    $sql = "INSERT INTO clientes (nombre, cedula, direccion, telefono, correo) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $nombre, $cedula, $direccion, $telefono, $correo);
    return $stmt->execute();
}

// Función para actualizar cliente
function actualizarCliente($id, $nombre, $cedula, $direccion, $telefono, $correo) {
    global $conn;
    $sql = "UPDATE clientes SET nombre=?, cedula=?, direccion=?, telefono=?, correo=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $nombre, $cedula, $direccion, $telefono, $correo, $id);
    return $stmt->execute();
}

// Función para eliminar cliente
function eliminarCliente($id) {
    global $conn;
    $sql = "DELETE FROM clientes WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// Procesar acciones POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['agregar'])) {
        agregarCliente($_POST['nombre'], $_POST['cedula'], $_POST['direccion'], $_POST['telefono'], $_POST['correo']);
    } elseif (isset($_POST['actualizar'])) {
        actualizarCliente($_POST['id'], $_POST['nombre'], $_POST['cedula'], $_POST['direccion'], $_POST['telefono'], $_POST['correo']);
    } elseif (isset($_POST['eliminar'])) {
        eliminarCliente($_POST['id']);
    }
    header("Location: clientes.php");
    exit();
}

$clientes = obtenerClientes();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes - Granja Media Luna</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <h1>Granja Media Luna</h1>
        <p>Gestión de Clientes</p>
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
            <h2>Registrar Nuevo Cliente</h2>
            <form id="formCliente" method="POST">
                <div class="form-group">
                    <label for="nombre">Nombre Completo:</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                <div class="form-group">
                    <label for="cedula">Cédula/NIT:</label>
                    <input type="text" id="cedula" name="cedula" required>
                </div>
                <div class="form-group">
                    <label for="direccion">Dirección:</label>
                    <textarea id="direccion" name="direccion" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono:</label>
                    <input type="tel" id="telefono" name="telefono">
                </div>
                <div class="form-group">
                    <label for="correo">Correo Electrónico:</label>
                    <input type="email" id="correo" name="correo">
                </div>
                <button type="submit" name="agregar">Registrar Cliente</button>
            </form>
        </section>

        <section>
            <h2>Lista de Clientes</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Cédula</th>
                        <th>Dirección</th>
                        <th>Teléfono</th>
                        <th>Correo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $clientes->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($row['cedula']); ?></td>
                        <td><?php echo htmlspecialchars($row['direccion']); ?></td>
                        <td><?php echo htmlspecialchars($row['telefono']); ?></td>
                        <td><?php echo htmlspecialchars($row['correo']); ?></td>
                        <td>
                            <button onclick="editarCliente(<?php echo $row['id']; ?>, '<?php echo addslashes($row['nombre']); ?>', '<?php echo addslashes($row['cedula']); ?>', '<?php echo addslashes($row['direccion']); ?>', '<?php echo addslashes($row['telefono']); ?>', '<?php echo addslashes($row['correo']); ?>')">Editar</button>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="eliminar" onclick="return confirm('¿Está seguro de eliminar este cliente?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>

        <!-- Formulario oculto para edición -->
        <section id="editarForm" style="display: none;">
            <h2>Editar Cliente</h2>
            <form id="formEditar" method="POST">
                <input type="hidden" id="editId" name="id">
                <div class="form-group">
                    <label for="editNombre">Nombre Completo:</label>
                    <input type="text" id="editNombre" name="nombre" required>
                </div>
                <div class="form-group">
                    <label for="editCedula">Cédula/NIT:</label>
                    <input type="text" id="editCedula" name="cedula" required>
                </div>
                <div class="form-group">
                    <label for="editDireccion">Dirección:</label>
                    <textarea id="editDireccion" name="direccion" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label for="editTelefono">Teléfono:</label>
                    <input type="tel" id="editTelefono" name="telefono">
                </div>
                <div class="form-group">
                    <label for="editCorreo">Correo Electrónico:</label>
                    <input type="email" id="editCorreo" name="correo">
                </div>
                <button type="submit" name="actualizar">Actualizar Cliente</button>
                <button type="button" onclick="cancelarEdicion()">Cancelar</button>
            </form>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Granja Media Luna. Todos los derechos reservados.</p>
    </footer>

    <script src="js/validaciones.js"></script>
    <script>
        function editarCliente(id, nombre, cedula, direccion, telefono, correo) {
            document.getElementById('editId').value = id;
            document.getElementById('editNombre').value = nombre;
            document.getElementById('editCedula').value = cedula;
            document.getElementById('editDireccion').value = direccion;
            document.getElementById('editTelefono').value = telefono;
            document.getElementById('editCorreo').value = correo;
            document.getElementById('editarForm').style.display = 'block';
            document.getElementById('editarForm').scrollIntoView();
        }

        function cancelarEdicion() {
            document.getElementById('editarForm').style.display = 'none';
        }
    </script>
</body>
</html>