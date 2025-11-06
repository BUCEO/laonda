<?php
require __DIR__ . '/vendor/autoload.php';

use App\Auth\SessionManager;

SessionManager::logout();

echo "👋 Sesión cerrada correctamente.";
echo "<br><a href='index.php'>Volver al inicio</a>";
