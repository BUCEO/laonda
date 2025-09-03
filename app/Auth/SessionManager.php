<?php
namespace App\Auth;

class SessionManager {
    public static function start(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function login(array $userData): void {
        self::start();
        $_SESSION['usuario'] = $userData;
    }

    public static function getUsuario(): ?array {
        self::start();
        return $_SESSION['usuario'] ?? null;
    }

    public static function estaLogueado(): bool {
        self::start();
        return isset($_SESSION['usuario']);
    }

    public static function logout(): void {
        self::start();
        session_unset();
        session_destroy();
    }
}
