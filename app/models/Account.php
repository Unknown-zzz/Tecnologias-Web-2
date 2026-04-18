<?php
declare(strict_types=1);

final class Account
{
    public function __construct(private PDO $db) {}

    public function findByUsuario(string $usuario): ?array
    {
        $stmt = $this->db->prepare("CALL sp_cuenta_find(:usuario)");
        $stmt->execute(['usuario' => $usuario]);
        return $stmt->fetch() ?: null;
    }

    public function create(string $usuario, string $password, string $rol = 'cliente'): bool
    {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("CALL sp_cuenta_create(:usuario, :password, :rol)");
        return $stmt->execute([
            'usuario' => $usuario,
            'password' => $hashed,
            'rol' => $rol
        ]);
    }
}