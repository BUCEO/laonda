<?php
require_once 'Database.php';

class Agencia {
    private $id;
    private $nombre;
    private $ciudad;
    private $trayectos = []; // Relación con Trayecto

    // Constructor
    public function __construct($nombre = null, $ciudad = null) {
        $this->nombre = $nombre;
        $this->ciudad = $ciudad;
    }

    // --- CRUD --- //
    public function guardar() {
        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO agencia (nombre, ciudad) VALUES (?, ?)");
        $result = $stmt->execute([$this->nombre, $this->ciudad]);
        $this->id = $db->lastInsertId(); // Asigna el ID generado
        return $result;
    }

    public static function listar() {
        $db = Database::getInstance();
        return $db->query("SELECT * FROM agencia")->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function buscarPorId($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM agencia WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizar($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE agencia SET nombre = ?, ciudad = ? WHERE id = ?");
        return $stmt->execute([$this->nombre, $this->ciudad, $id]);
    }

    public static function eliminar($id) {
        $db = Database::getInstance();
        $db->beginTransaction();
        try {
            // Primero elimina trayectos asociados
            $stmt = $db->prepare("DELETE FROM trayecto WHERE agencia_id = ?");
            $stmt->execute([$id]);
            
            // Luego elimina la agencia
            $stmt = $db->prepare("DELETE FROM agencia WHERE id = ?");
            $stmt->execute([$id]);
            
            $db->commit();
            return true;
        } catch (PDOException $e) {
            $db->rollBack();
            return false;
        }
    }

    // --- Relaciones --- //
    public function agregarTrayecto(Trayecto $trayecto) {
        $trayecto->setAgenciaId($this->id);
        $this->trayectos[] = $trayecto;
    }

    // ... Getters & Setters
}
?>
