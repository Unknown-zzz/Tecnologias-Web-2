<?php
declare(strict_types=1);

final class Category
{
    public function __construct(private PDO $db) {}

    public function all(): array
    {
        $stmt = $this->db->prepare("CALL sp_categoria_all()");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function find(int $cod): ?array
    {
        $stmt = $this->db->prepare("CALL sp_categoria_find(:cod)");
        $stmt->execute(['cod' => $cod]);
        return $stmt->fetch() ?: null;
    }

    public function create(string $nombre): bool
    {
        $stmt = $this->db->prepare("CALL sp_categoria_create(:nombre)");
        return $stmt->execute(['nombre' => trim($nombre)]);
    }

    public function update(int $cod, string $nombre): bool
    {
        $stmt = $this->db->prepare("CALL sp_categoria_update(:cod, :nombre)");
        return $stmt->execute(['cod' => $cod, 'nombre' => trim($nombre)]);
    }

    public function delete(int $cod): bool
    {
        $stmt = $this->db->prepare("CALL sp_categoria_delete(:cod)");
        return $stmt->execute(['cod' => $cod]);
    }
}