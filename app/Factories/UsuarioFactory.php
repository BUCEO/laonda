<?php
namespace App\Factories;

use App\Models\Usuario;

class UsuarioFactory {
    public static function crearUsuario(string $tipo, string $nombre, string $email): Usuario {
        switch (strtolower($tipo)) {
            case "admin":
                return new Usuario($nombre, $email, "Administrador");
            case "cliente":
                return new Usuario($nombre, $email, "Cliente");
            case "chofer":
                return new Usuario($nombre, $email, "Chofer");
            default:
                throw new \InvalidArgumentException("Tipo de usuario no válido: {$tipo}");
        }
    }
}
