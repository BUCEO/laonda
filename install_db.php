<?php
require __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Configuración desde .env
$host = $_ENV['DB_HOST'];
$dbname = $_ENV['DB_NAME'];
$user = $_ENV['DB_USER'];
$password = $_ENV['DB_PASSWORD'];
$models = explode(',', str_replace('"', '', $_ENV['DB_MODELS']));

// Crear conexión root para crear DB
try {
    $conn = new PDO("mysql:host=$host", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("CREATE DATABASE IF NOT EXISTS $dbname");
    $conn->exec("GRANT ALL PRIVILEGES ON $dbname.* TO '$user'@'$host'");
    echo "✅ Base de datos creada\n";
} catch (PDOException $e) {
    die("❌ Error al crear DB: " . $e->getMessage());
}

// Crear tabla `omnibus`
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $conn->exec("
        CREATE TABLE IF NOT EXISTS omnibus (
            id INT AUTO_INCREMENT PRIMARY KEY,
            modelo ENUM(" . $_ENV['DB_MODELS'] . ") NOT NULL,
            anio INT(4) NOT NULL,
            estado ENUM('activo', 'vintage', 'baja') DEFAULT 'vintage'
        )
    ");
    echo "✅ Tabla 'omnibus' creada\n";
} catch (PDOException $e) {
    die("❌ Error al crear tabla: " . $e->getMessage());
}

// Insertar datos de prueba
try {
    $stmt = $conn->prepare("INSERT INTO omnibus (modelo, anio) VALUES (?, ?)");
    foreach ($models as $modelo) {
        $stmt->execute([trim($modelo), rand(1980, 1990)]);
    }
    echo "✅ Datos de prueba insertados\n";
} catch (PDOException $e) {
    die("❌ Error al insertar datos: " . $e->getMessage());
}
