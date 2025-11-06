<?php
// Crear una agencia y trayecto asociado
$agencia = new Agencia("Terminal Baltasar Brum", "Montevideo");
$agencia->guardar(); // Guarda en DB y asigna ID

$trayecto = new Trayecto("Montevideo", "Paysandú", "6 horas", 120.50);
$trayecto->setOmnibusId(1); // Asignar ómnibus existente
$agencia->agregarTrayecto($trayecto); // Relación
$trayecto->guardar(); // Guarda el trayecto con el ID de la agencia

// Listar trayectos con joins
$trayectos = Trayecto::listar();
print_r($trayectos);
?>
