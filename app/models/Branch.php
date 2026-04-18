<?php
declare(strict_types=1);

final class Branch
{
    public function __construct(private PDO $db) {}

    public function all(): array
    {
        $stmt = $this->db->prepare("CALL sp_sucursal_all()");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function find(int $cod): ?array
    {
        $stmt = $this->db->prepare("CALL sp_sucursal_find(:cod)");
        $stmt->execute(['cod' => $cod]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare("CALL sp_sucursal_create(:nombre, :direccion, :telefono)");
        return $stmt->execute([
            'nombre' => trim($data['nombre']),
            'direccion' => trim($data['direccion']),
            'telefono' => $data['telefono'] ?? ''
        ]);
    }

    public function update(int $cod, array $data): bool
    {
        $stmt = $this->db->prepare("CALL sp_sucursal_update(:cod, :nombre, :direccion, :telefono)");
        return $stmt->execute([
            'cod' => $cod,
            'nombre' => trim($data['nombre']),
            'direccion' => trim($data['direccion']),
            'telefono' => $data['telefono'] ?? ''
        ]);
    }

    public function delete(int $cod): bool
    {
        $stmt = $this->db->prepare("CALL sp_sucursal_delete(:cod)");
        return $stmt->execute(['cod' => $cod]);
    }
}