<?php
namespace App\Models;

class Usuario {
    private string $nombre;
    private string $email;
    private string $rol;

    public function __construct(string $nombre, string $email, string $rol) {
        $this->nombre = $nombre;
        $this->email = $email;
        $this->rol = $rol;
    }

    public function mostrarInfo(): string {
        return "👤 Usuario: {$this->nombre} ({$this->email}) – Rol: {$this->rol}";
    }
}
