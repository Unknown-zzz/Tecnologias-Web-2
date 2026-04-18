<?php
declare(strict_types=1);

final class Client
{
    public function __construct(private PDO $db) {}

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare("CALL sp_cliente_create(:ci, :nombres, :apPaterno, :apMaterno, :correo, :direccion, :nroCelular, :usuarioCuenta)");
        return $stmt->execute($data);
    }

    public function findByCi(string $ci): ?array
    {
        $stmt = $this->db->prepare("CALL sp_cliente_find(:ci)");
        $stmt->execute(['ci' => $ci]);
        return $stmt->fetch() ?: null;
    }

    public function findByUsuario(string $usuario): ?array
    {
        $stmt = $this->db->prepare("SELECT c.* FROM Cliente c 
                                    INNER JOIN Cuenta cu ON c.usuarioCuenta = cu.usuario 
                                    WHERE cu.usuario = :usuario LIMIT 1");
        $stmt->execute(['usuario' => $usuario]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return is_array($result) ? $result : null;
    }
}