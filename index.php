<?php
require __DIR__ . '/vendor/autoload.php';

use App\Auth\SessionManager;
use App\Factories\UsuarioFactory;
use App\Database\ConexionDB;

echo "<h1>🚍 Proyecto ONDA</h1>";

// ===========================
// SESIONES
// ===========================
if (SessionManager::estaLogueado()) {
    $usuario = SessionManager::getUsuario();
    echo "<p>Bienvenido, {$usuario['nombre']} ({$usuario['rol']})</p>";
    echo "<a href='logout.php'>Cerrar sesión</a>";
} else {
    echo "<p>No hay usuario en sesión.</p>";
    echo "<a href='login.php'>Iniciar sesión</a>";
}

// ===========================
// USO DE FACTORY
// ===========================
echo "<h2>Usuarios creados con Factory</h2>";

$u1 = UsuarioFactory::crearUsuario('admin', 'Ana', 'ana@mail.com');
$u2 = UsuarioFactory::crearUsuario('chofer', 'Carlos', 'carlos@mail.com');

echo $u1->mostrarInfo() . "<br>";
echo $u2->mostrarInfo() . "<br>";

// ===========================
// USO DE SINGLETON
// ===========================
echo "<h2>Conexión a la base de datos</h2>";

$db = ConexionDB::getInstancia()->getConexion();
$stmt = $db->query("SELECT NOW() as fecha_actual");
$row = $stmt->fetch(PDO::FETCH_ASSOC);

echo "✅ Conectado a la BD ONDA. Fecha/hora: " . $row['fecha_actual'];
