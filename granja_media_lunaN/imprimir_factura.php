<?php
include_once 'php/conexion.php';

// Vista separada para imprimir una factura por su ID (GET param: id)
$id_venta = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_venta <= 0) {
    http_response_code(400);
    echo "<p>ID de factura no válido.</p>";
    exit;
}

// Obtener datos de la venta
$sql = "SELECT * FROM ventas WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_venta);
$stmt->execute();
$result = $stmt->get_result();
$venta = $result->fetch_assoc();

if (!$venta) {
    http_response_code(404);
    echo "<p>Factura no encontrada.</p>";
    exit;
}

// Obtener cliente
$id_cliente = $venta['id_cliente'];
$sql = "SELECT id, nombre, cedula FROM clientes WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_cliente);
$stmt->execute();
$result = $stmt->get_result();
$cliente = $result->fetch_assoc();

// Obtener detalle de la venta con información del producto
$sql = "SELECT dv.id_producto, dv.cantidad, dv.subtotal, p.nombre AS producto_nombre, p.precio AS producto_precio
        FROM detalle_venta dv
        LEFT JOIN productos p ON dv.id_producto = p.id
        WHERE dv.id_venta = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_venta);
$stmt->execute();
$detalle = $stmt->get_result();

// Fecha: intenta usar campo 'fecha' o 'created_at' si existen
$fecha = null;
if (isset($venta['fecha'])) $fecha = $venta['fecha'];
elseif (isset($venta['created_at'])) $fecha = $venta['created_at'];
else $fecha = date('Y-m-d H:i:s');

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura #<?php echo htmlspecialchars($venta['id']); ?></title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; margin: 20px; color: #222 }
        .invoice { max-width: 800px; margin: 0 auto; border: 1px solid #ddd; padding: 20px }
        h1, h2 { margin: 0 0 8px }
        .meta { margin-bottom: 12px }
        table { width: 100%; border-collapse: collapse; margin-top: 12px }
        table th, table td { border: 1px solid #ccc; padding: 8px; text-align: left }
        table th { background: #f5f5f5 }
        .totals { text-align: right; margin-top: 12px }
        .no-print { margin-top: 12px }
        @media print {
            body*{ visibility: hidden
             }
            .invoice, .invoice *{ visibility: visible
            }
            .invoice{
                position: absolute;
                left: 0;
                top: 0;
            }

            .no-print { display: none !important}
            body { margin: 0 }
            .invoice { border: none; padding: 0; }
        }
    </style>
</head>
<body>
    <div class="invoice">
        <header>
            <h1>Granja Media Luna</h1>
            <div class="meta">
                <strong>Factura #<?php echo htmlspecialchars($venta['id']); ?></strong><br>
                Fecha: <?php echo htmlspecialchars($fecha); ?>
            </div>
        </header>

        <section>
            <h2>Datos del cliente</h2>
            <p>
                <strong>Nombre:</strong> <?php echo htmlspecialchars($cliente['nombre'] ?? 'N/A'); ?><br>
                <strong>Cédula:</strong> <?php echo htmlspecialchars($cliente['cedula'] ?? 'N/A'); ?>
            </p>
        </section>

        <section>
            <h2>Detalle</h2>
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $suma = 0;
                    if ($detalle->num_rows > 0) {
                        while ($row = $detalle->fetch_assoc()) {
                            $nombreP = $row['producto_nombre'] ?? 'Producto #' . $row['id_producto'];
                            $precio = isset($row['producto_precio']) ? number_format($row['producto_precio'], 2) : number_format(($row['subtotal'] / max(1, $row['cantidad'])), 2);
                            $cantidad = (int)$row['cantidad'];
                            $subtotal = number_format($row['subtotal'], 2);
                            $suma += floatval($row['subtotal']);
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($nombreP) . "</td>";
                            echo "<td>$" . htmlspecialchars($precio) . "</td>";
                            echo "<td>" . htmlspecialchars($cantidad) . "</td>";
                            echo "<td>$" . htmlspecialchars($subtotal) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan=4>Sin items</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <div class="totals">
                <p><strong>Total:</strong> $<?php echo number_format($venta['total'], 2); ?></p>
            </div>
        </section>

        <div class="no-print">
            <button onclick="window.print()">Imprimir</button>
            <button onclick="window.close()">Cerrar</button>
        </div>
    </div>

    <script>
        // Forzar apertura del diálogo de impresión al cargar
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>