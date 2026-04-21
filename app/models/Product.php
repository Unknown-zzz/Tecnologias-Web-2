<?php
declare(strict_types=1);

final class Product
{
    public function __construct(private PDO $db) {}

    public function allActive(): array
    {
        $stmt = $this->db->prepare(<<<'SQL'
SELECT p.*, m.nombre AS marca, cat.nombre AS categoria, i.nombre AS industria, COALESCE(dps.stock, 0) AS stock
FROM Producto p
LEFT JOIN Marca m ON p.codMarca = m.cod
LEFT JOIN Categoria cat ON p.codCategoria = cat.cod
LEFT JOIN Industria i ON p.codIndustria = i.cod
LEFT JOIN DetalleProductoSucursal dps ON p.cod = dps.codProducto AND dps.codSucursal = 1
WHERE p.estado = 'activo'
ORDER BY p.cod DESC
SQL
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function allForAdmin(): array
    {
        $stmt = $this->db->prepare(<<<'SQL'
SELECT p.*, m.nombre AS marca, cat.nombre AS categoria, i.nombre AS industria, COALESCE(dps.stock, 0) AS stock
FROM Producto p
LEFT JOIN Marca m ON p.codMarca = m.cod
LEFT JOIN Categoria cat ON p.codCategoria = cat.cod
LEFT JOIN Industria i ON p.codIndustria = i.cod
LEFT JOIN DetalleProductoSucursal dps ON p.cod = dps.codProducto AND dps.codSucursal = 1
ORDER BY p.cod DESC
SQL
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function find(int $cod): ?array
    {
        $stmt = $this->db->prepare(<<<'SQL'
SELECT p.*, m.nombre AS marca, cat.nombre AS categoria, i.nombre AS industria
FROM Producto p
LEFT JOIN Marca m ON p.codMarca = m.cod
LEFT JOIN Categoria cat ON p.codCategoria = cat.cod
LEFT JOIN Industria i ON p.codIndustria = i.cod
WHERE p.cod = :cod
SQL
        );
        $stmt->execute(['cod' => $cod]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare(<<<'SQL'
INSERT INTO Producto (nombre, descripcion, precio, imagen, estado, codMarca, codIndustria, codCategoria)
VALUES (TRIM(:nombre), TRIM(:descripcion), :precio, :imagen, :estado, :codMarca, :codIndustria, :codCategoria)
SQL
        );

        $success = $stmt->execute([
            'nombre' => trim($data['nombre']),
            'descripcion' => trim($data['descripcion']),
            'precio' => (float)$data['precio'],
            'imagen' => $data['imagen'] ?? '',
            'estado' => $data['estado'] ?? 'activo',
            'codMarca' => (int)$data['codMarca'],
            'codIndustria' => (int)$data['codIndustria'],
            'codCategoria' => (int)$data['codCategoria']
        ]);

        if (!$success) {
            return false;
        }

        $codProducto = (int)$this->db->lastInsertId();
        $stock = (int)($data['stock'] ?? 0);

        if ($stock > 0) {
            $stmt = $this->db->prepare(
                "INSERT INTO DetalleProductoSucursal (codProducto, codSucursal, stock) VALUES (:codProducto, :codSucursal, :stock)"
            );
            $stmt->execute([
                'codProducto' => $codProducto,
                'codSucursal' => 1,
                'stock' => $stock
            ]);
        }

        return true;
    }

    public function update(int $cod, array $data): bool
    {
        $stmt = $this->db->prepare(<<<'SQL'
UPDATE Producto SET
    nombre = TRIM(:nombre),
    descripcion = TRIM(:descripcion),
    precio = :precio,
    imagen = :imagen,
    estado = :estado,
    codMarca = :codMarca,
    codIndustria = :codIndustria,
    codCategoria = :codCategoria
WHERE cod = :cod
SQL
        );

        return $stmt->execute([
            'cod' => $cod,
            'nombre' => trim($data['nombre']),
            'descripcion' => trim($data['descripcion']),
            'precio' => (float)$data['precio'],
            'imagen' => $data['imagen'] ?? '',
            'estado' => $data['estado'] ?? 'activo',
            'codMarca' => (int)$data['codMarca'],
            'codIndustria' => (int)$data['codIndustria'],
            'codCategoria' => (int)$data['codCategoria']
        ]);
    }

    public function delete(int $cod): bool
    {
        $stmt = $this->db->prepare("DELETE FROM Producto WHERE cod = :cod");
        return $stmt->execute(['cod' => $cod]);
    }

    public function addStock(int $codProducto, int $stock, int $codSucursal = 1): bool
    {
        $stmt = $this->db->prepare(<<<'SQL'
INSERT INTO DetalleProductoSucursal (codProducto, codSucursal, stock)
VALUES (:codProducto, :codSucursal, :stock)
ON DUPLICATE KEY UPDATE stock = :stock_update
SQL
        );
        return $stmt->execute([
            'codProducto' => $codProducto,
            'codSucursal' => $codSucursal,
            'stock' => $stock,
            'stock_update' => $stock
        ]);
    }

    public function getStock(int $codProducto, int $codSucursal = 1): int
    {
        $stmt = $this->db->prepare("SELECT stock FROM DetalleProductoSucursal 
                                    WHERE codProducto = :codProducto AND codSucursal = :codSucursal");
        $stmt->execute(['codProducto' => $codProducto, 'codSucursal' => $codSucursal]);
        return (int)($stmt->fetchColumn() ?? 0);
    }

    public function updateStock(int $codProducto, int $cantidad, int $codSucursal = 1): bool
    {
        $currentStock = $this->getStock($codProducto, $codSucursal);
        if ($currentStock < $cantidad) {
            return false; // Stock insuficiente
        }

        $stmt = $this->db->prepare("UPDATE DetalleProductoSucursal SET stock = stock - :cantidad 
                                    WHERE codProducto = :codProducto AND codSucursal = :codSucursal");
        return $stmt->execute([
            'cantidad' => $cantidad,
            'codProducto' => $codProducto,
            'codSucursal' => $codSucursal
        ]);
    }
}