<?php
require_once 'Database.php';

class Omnibus {
    private $id;
    private $modelo;
    private $anio;
    private $estado;

    // Constructor
    public function __construct($modelo = null, $anio = null, $estado = 'vintage') {
        $this->modelo = $modelo;
        $this->anio = $anio;
        $this->estado = $estado;
    }

    // --- CRUD --- //

    // Crear
    public function guardar() {
        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO omnibus (modelo, anio, estado) VALUES (?, ?, ?)");
        return $stmt->execute([$this->modelo, $this->anio, $this->estado]);
    }

    // Leer (Todos)
    public static function listar() {
        $db = Database::getInstance();
        return $db->query("SELECT * FROM omnibus")->fetchAll(PDO::FETCH_ASSOC);
    }

    // Leer (Por ID)
    public static function buscarPorId($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM omnibus WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualizar
    public function actualizar($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE omnibus SET modelo = ?, anio = ?, estado = ? WHERE id = ?");
        return $stmt->execute([$this->modelo, $this->anio, $this->estado, $id]);
    }

    // Eliminar
    public static function eliminar($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM omnibus WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // --- Getters & Setters --- //
    public function getModelo() { return $this->modelo; }
    public function setModelo($modelo) { $this->modelo = $modelo; }
    // ... (implementar para anio y estado)
}
?>
