<?php
declare(strict_types=1);

final class Account
{
    public function __construct(private PDO $db) {}

    public function findByUsuario(string $usuario): ?array
    {
        try {
            $stmt = $this->db->prepare("CALL sp_cuenta_find(:usuario)");
            $stmt->execute(['usuario' => $usuario]);
        } catch (PDOException $e) {
            if (!$this->isMissingProcedureError($e, 'sp_cuenta_find')) {
                throw $e;
            }

            $stmt = $this->db->prepare("SELECT * FROM Cuenta WHERE usuario = :usuario LIMIT 1");
            $stmt->execute(['usuario' => $usuario]);
        }

        return $stmt->fetch() ?: null;
    }

    public function create(string $usuario, string $password, string $rol = 'cliente'): bool
    {
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $this->db->prepare("CALL sp_cuenta_create(:usuario, :password, :rol)");
            return $stmt->execute([
                'usuario' => $usuario,
                'password' => $hashed,
                'rol' => $rol
            ]);
        } catch (PDOException $e) {
            if (!$this->isMissingProcedureError($e, 'sp_cuenta_create')) {
                throw $e;
            }

            $stmt = $this->db->prepare("INSERT INTO Cuenta (usuario, password, rol) VALUES (:usuario, :password, :rol)");
            return $stmt->execute([
                'usuario' => $usuario,
                'password' => $hashed,
                'rol' => $rol
            ]);
        }
    }

    private function isMissingProcedureError(PDOException $e, string $procedureName): bool
    {
        $message = $e->getMessage();
        return str_contains($message, '1305') && str_contains($message, $procedureName);
    }
}