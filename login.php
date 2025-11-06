<?php
require __DIR__ . '/vendor/autoload.php';

use App\Auth\SessionManager;
use App\Factories\UsuarioFactory;

$nombre = "Ana";
$email = "ana@mail.com";
$tipo = "admin";

$usuario = UsuarioFactory::crearUsuario($tipo, $nombre, $email);

SessionManager::login([
    'nombre' => $nombre,
    'email'  => $email,
    'rol'    => $tipo
]);

echo "✅ Usuario logueado: " . $usuario->mostrarInfo();
echo "<br><a href='index.php'>Ir al inicio</a>";
