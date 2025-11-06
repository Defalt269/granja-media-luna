<?php
session_start();
include 'php/conexion.php';

// Función para verificar login
function verificarLogin($usuario, $password) {
    global $conn;
    $sql = "SELECT * FROM usuarios WHERE nombre_usuario = ? AND contraseña = MD5(?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $usuario, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Función para obtener estadísticas
function obtenerEstadisticas() {
    global $conn;
    $stats = [];

    // Total productos
    $result = $conn->query("SELECT COUNT(*) as total FROM productos");
    $stats['productos'] = $result->fetch_assoc()['total'];

    // Total clientes
    $result = $conn->query("SELECT COUNT(*) as total FROM clientes");
    $stats['clientes'] = $result->fetch_assoc()['total'];

    // Total ventas del mes
    $result = $conn->query("SELECT COUNT(*) as total FROM ventas WHERE MONTH(fecha) = MONTH(CURRENT_DATE()) AND YEAR(fecha) = YEAR(CURRENT_DATE())");
    $stats['ventas_mes'] = $result->fetch_assoc()['total'];

    // Ingresos del mes
    $result = $conn->query("SELECT SUM(total) as total FROM ventas WHERE MONTH(fecha) = MONTH(CURRENT_DATE()) AND YEAR(fecha) = YEAR(CURRENT_DATE())");
    $stats['ingresos_mes'] = $result->fetch_assoc()['total'] ?? 0;

    return $stats;
}

// Procesar login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $usuario = verificarLogin($_POST['usuario'], $_POST['password']);
    if ($usuario) {
        $_SESSION['usuario'] = $usuario;
        header("Location: admin.php");
        exit();
    } else {
        $error_login = "Usuario o contraseña incorrectos";
    }
}

// Procesar logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit();
}

// Verificar si está logueado
$logueado = isset($_SESSION['usuario']);
$stats = $logueado ? obtenerEstadisticas() : null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración - Granja Media Luna</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <h1>Granja Media Luna</h1>
        <p>Panel Administrativo</p>
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
        <?php if (!$logueado): ?>
        <!-- Formulario de Login -->
        <section>
            <h2>Iniciar Sesión</h2>
            <?php if (isset($error_login)): ?>
            <p style="color: red;"><?php echo $error_login; ?></p>
            <?php endif; ?>
            <form method="POST">
                <div class="form-group">
                    <label for="usuario">Usuario:</label>
                    <input type="text" id="usuario" name="usuario" required>
                </div>
                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" name="login">Iniciar Sesión</button>
            </form>
            <p><small>Usuario de prueba: admin / admin123</small></p>
        </section>
        <?php else: ?>
        <!-- Panel Administrativo -->
        <section>
            <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']['nombre_usuario']); ?> <a href="?logout=1" style="float: right;">Cerrar Sesión</a></h2>

            <div class="estadisticas">
                <div class="stat">
                    <h3>Total Productos</h3>
                    <p><?php echo $stats['productos']; ?></p>
                </div>
                <div class="stat">
                    <h3>Total Clientes</h3>
                    <p><?php echo $stats['clientes']; ?></p>
                </div>
                <div class="stat">
                    <h3>Ventas del Mes</h3>
                    <p><?php echo $stats['ventas_mes']; ?></p>
                </div>
                <div class="stat">
                    <h3>Ingresos del Mes</h3>
                    <p>$<?php echo number_format($stats['ingresos_mes'], 2); ?></p>
                </div>
            </div>
        </section>

        <section>
            <h2>Enlaces Rápidos</h2>
            <ul>
                <li><a href="productos.php">Gestionar Productos</a></li>
                <li><a href="clientes.php">Gestionar Clientes</a></li>
                <li><a href="facturacion.php">Generar Facturas</a></li>
                <li><a href="reportes.php">Ver Reportes</a></li>
            </ul>
        </section>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2024 Granja Media Luna. Todos los derechos reservados.</p>
    </footer>

    <script src="js/validaciones.js"></script>
</body>
</html>

<style>
.estadisticas {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-top: 2rem;
}

.stat {
    background-color: #f9f9f9;
    padding: 1rem;
    border-radius: 5px;
    text-align: center;
    border: 1px solid #ddd;
}

.stat h3 {
    margin-bottom: 0.5rem;
    color: #2E7D32;
}

.stat p {
    font-size: 2rem;
    font-weight: bold;
    color: #4CAF50;
}
</style>