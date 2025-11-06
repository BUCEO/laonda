// ============= SCRIPT DE INICIALIZACIÓN =============
// Archivo: api.php
<?php   
require_once 'controllers/HistoriaController.php';

if (basename($_SERVER['PHP_SELF']) === 'api.php') {
    $controller = new HistoriaController();
    $controller->handleRequest();
}
?>