<?php

declare(strict_types=1);

final class User
{
    public function __construct(private PDO $db)
    {
    }

    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM usuarios WHERE usuario = :usuario LIMIT 1');
        $stmt->execute(['usuario' => $username]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    public function verifyPassword(string $inputPassword, string $storedPassword): bool
    {
        if (password_get_info($storedPassword)['algo']) {
            return password_verify($inputPassword, $storedPassword);
        }

        return hash_equals($storedPassword, $inputPassword);
    }
}
