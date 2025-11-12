<?php
include 'php/conexion.php';

// Función para obtener productos disponibles
function obtenerProductosDisponibles() {
    global $conn;
    $sql = "SELECT * FROM productos WHERE cantidad_disponible >0 ORDER BY nombre";
    $result = $conn->query($sql);
    return $result;
}

// Función para obtener clientes
function obtenerClientes() {
    global $conn;
    $sql = "SELECT id, nombre, cedula FROM clientes ORDER BY nombre";
    $result = $conn->query($sql);
    return $result;
}

// Función para registrar venta
function registrarVenta($id_cliente, $productos) {
    global $conn;
    $conn->begin_transaction();

    try {
        // Calcular total
        $total = 0;
        foreach ($productos as $producto) {
            $sql = "SELECT precio FROM productos WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $producto['id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $total += $row['precio'] * $producto['cantidad'];
        }

        // Insertar venta
        $sql = "INSERT INTO ventas (id_cliente, total) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("id", $id_cliente, $total);
        $stmt->execute();
        $id_venta = $conn->insert_id;

        // Insertar detalle de venta y actualizar inventario
        foreach ($productos as $producto) {
            $sql = "SELECT precio FROM productos WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $producto['id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $subtotal = $row['precio'] * $producto['cantidad'];
            
            $sql = "INSERT INTO detalle_venta (id_venta, id_producto, cantidad, subtotal) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iiid", $id_venta, $producto['id'], $producto['cantidad'], $subtotal);
            $stmt->execute();

            // Actualizar inventario
            $sql = "UPDATE productos SET cantidad_disponible = cantidad_disponible - ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $producto['cantidad'], $producto['id']);
            $stmt->execute();
        }

        $conn->commit();
        return $id_venta;
    } catch (Exception $e) {
        $conn->rollback();
        return false;
    }
}

// Procesar formulario de venta
$id_venta_generada = null;
$error_stock = null;
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['generar_factura'])) {
    $productos_seleccionados = [];
    if (isset($_POST['productos'])) {
        foreach ($_POST['productos'] as $index => $producto_id) {
            if (!empty($_POST['cantidades'][$index])) {
                $productos_seleccionados[] = [
                    'id' => $producto_id,
                    'cantidad' => $_POST['cantidades'][$index]
                ];
            }
        }
    }

    // Validar stock disponible
    if (!empty($productos_seleccionados)) {
        foreach ($productos_seleccionados as $producto) {
            $sql = "SELECT nombre, cantidad_disponible FROM productos WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $producto['id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            if ($row && $producto['cantidad'] > $row['cantidad_disponible']) {
                $error_stock = "Error: La cantidad ingresada para " . htmlspecialchars($row['nombre']) . " (" . $producto['cantidad'] . ") supera el stock disponible (" . $row['cantidad_disponible'] . ").";
                break;
            }
        }
    }

    if (!empty($productos_seleccionados) && !empty($_POST['cliente']) && !$error_stock) {
        $id_venta_generada = registrarVenta($_POST['cliente'], $productos_seleccionados);
    }
}

$productos = obtenerProductosDisponibles();
$clientes = obtenerClientes();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facturación - Granja Media Luna</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <h1>Granja Media Luna</h1>
        <p>Sistema de Facturación</p>
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
            <h2>Generar Nueva Factura</h2>
            
            <?php if ($error_stock): ?>
            <div style="background-color: #f8d7da; color: #721c24; padding: 12px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                <strong>⚠️ Error:</strong> <?php echo $error_stock; ?>
            </div>
            <?php endif; ?>
            
            <form id="formFactura" method="POST">
                <div class="form-group">
                    <label for="cliente">Seleccionar Cliente:</label>
                    <select id="cliente" name="cliente" required>
                        <option value="">Seleccionar cliente</option>
                        <?php while($cliente = $clientes->fetch_assoc()): ?>
                        <option value="<?php echo $cliente['id']; ?>"><?php echo htmlspecialchars($cliente['nombre']) . ' - ' . htmlspecialchars($cliente['cedula']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div id="productosContainer">
                    <h3>Seleccionar Productos</h3>
                    <div class="producto-item">
                        <select name="productos[]" required>
                            <option value="">Seleccionar producto</option>
                            <?php
                            $productos->data_seek(0); // Reiniciar puntero
                            while($producto = $productos->fetch_assoc()):
                            ?>
                            <option value="<?php echo $producto['id']; ?>" data-precio="<?php echo $producto['precio']; ?>">
                                <?php echo htmlspecialchars($producto['nombre']) . ' - $' . number_format($producto['precio'], 2) . ' (Disponible: ' . $producto['cantidad_disponible'] . ')'; ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                        <input type="number" name="cantidades[]" placeholder="Cantidad" min="1" required>
                        <button type="button" onclick="removerProducto(this)">Remover</button>
                    </div>
                </div>

                <button type="button" onclick="agregarProducto()">Agregar Otro Producto</button>

                <div class="form-group">
                    <label>Total: $<span id="total">0.00</span></label>
                </div>

                <button type="submit" name="generar_factura">Generar Factura</button>
            </form>
        </section>

        <?php if ($id_venta_generada): ?>
        <section>
            <h2>Factura Generada Exitosamente</h2>
            <p><strong>ID de Venta:</strong> <?php echo $id_venta_generada; ?></p>
            <p>La factura ha sido registrada en el sistema.</p>
            <button onclick="window.open('imprimir_factura.php?id=<?php echo $id_venta_generada; ?>', '_blank')">Ver/Imprimir Factura</button>
        </section>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2024 Granja Media Luna. Todos los derechos reservados.</p>
    </footer>

    <script src="js/validaciones.js"></script>
    <script>
        let productoIndex = 1;

        function agregarProducto() {
            const container = document.getElementById('productosContainer');
            const productoItem = document.querySelector('.producto-item').cloneNode(true);
            productoItem.querySelector('input[type="number"]').value = '';
            container.appendChild(productoItem);
            actualizarTotal();
        }

        function removerProducto(button) {
            if (document.querySelectorAll('.producto-item').length > 1) {
                button.parentElement.remove();
                actualizarTotal();
            }
        }

        function actualizarTotal() {
            let total = 0;
            const productos = document.querySelectorAll('.producto-item');
            productos.forEach(item => {
                const select = item.querySelector('select');
                const cantidad = item.querySelector('input[type="number"]').value;
                if (select.value && cantidad) {
                    const precio = parseFloat(select.selectedOptions[0].getAttribute('data-precio'));
                    total += precio * parseInt(cantidad);
                }
            });
            document.getElementById('total').textContent = total.toFixed(2);
        }

        // Event listeners para actualizar total en tiempo real
        document.addEventListener('change', function(e) {
            if (e.target.name === 'productos[]' || e.target.name === 'cantidades[]') {
                actualizarTotal();
            }
        });

        document.addEventListener('input', function(e) {
            if (e.target.name === 'cantidades[]') {
                actualizarTotal();
            }
        });
    </script>
</body>
</html>
