<?php
require_once 'Database.php';

class Trayecto {
    private $id;
    private $origen;
    private $destino;
    private $duracion;
    private $precio;
    private $omnibusId;
    private $agenciaId;

    public function __construct($origen, $destino, $duracion, $precio, $omnibusId = null, $agenciaId = null) {
        $this->origen = $origen;
        $this->destino = $destino;
        $this->duracion = $duracion;
        $this->precio = $precio;
        $this->omnibusId = $omnibusId;
        $this->agenciaId = $agenciaId;
    }

    // --- CRUD --- //
    public function guardar() {
        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO trayecto (origen, destino, duracion, precio, omnibus_id, agencia_id) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $this->origen, $this->destino, $this->duracion, 
            $this->precio, $this->omnibusId, $this->agenciaId
        ]);
    }

    public static function listar() {
        $db = Database::getInstance();
        return $db->query("
            SELECT t.*, a.nombre as agencia_nombre, o.modelo as omnibus_modelo 
            FROM trayecto t
            LEFT JOIN agencia a ON t.agencia_id = a.id
            LEFT JOIN omnibus o ON t.omnibus_id = o.id
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function buscarPorId($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT t.*, a.nombre as agencia_nombre 
            FROM trayecto t
            LEFT JOIN agencia a ON t.agencia_id = a.id
            WHERE t.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizar($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            UPDATE trayecto 
            SET origen = ?, destino = ?, duracion = ?, precio = ?, omnibus_id = ?, agencia_id = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            $this->origen, $this->destino, $this->duracion, 
            $this->precio, $this->omnibusId, $this->agenciaId, $id
        ]);
    }

    public static function eliminar($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM trayecto WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // --- Getters & Setters --- //
    public function setAgenciaId($id) { $this->agenciaId = $id; }
    public function setOmnibusId($id) { $this->omnibusId = $id; }
    // ... (implementar los demás)
}
?>
