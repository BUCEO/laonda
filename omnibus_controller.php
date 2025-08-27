<?php
require_once '../../classes/Omnibus.php';

// Crear
if ($_POST['action'] == 'crear') {
    $omnibus = new Omnibus($_POST['modelo'], $_POST['anio'], $_POST['estado']);
    if ($omnibus->guardar()) {
        echo "✅ Ómnibus creado!";
    }
}

// Listar
$omnibuses = Omnibus::listar();
print_r($omnibuses);

// Eliminar
Omnibus::eliminar($_GET['id']);
?>
