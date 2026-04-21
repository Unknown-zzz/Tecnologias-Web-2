<?php
declare(strict_types=1);

final class QrPayment
{
    public function __construct(private PDO $db) {}

    /**
     * Crea un nuevo token QR y guarda el estado del carrito.
     * Retorna el token único generado.
     */
    public function create(string $ciCliente, array $carrito, int $minutosValido = 5): string
{
    $token       = bin2hex(random_bytes(32));
    $carritoJson = json_encode($carrito, JSON_UNESCAPED_UNICODE);

    $stmt = $this->db->prepare(
        "INSERT INTO qr_pagos (token, ciCliente, carrito, expires_at)
         VALUES (:token, :ci, :carrito, DATE_ADD(NOW(), INTERVAL {$minutosValido} MINUTE))"
    );
    $stmt->execute([
        'token'   => $token,
        'ci'      => $ciCliente,
        'carrito' => $carritoJson,
    ]);

    return $token;
}

    /**
     * Busca un pago QR por su token. Retorna null si no existe.
     * Calcula si está vigente usando comparación de timestamps.
     */
    public function findByToken(string $token): ?array
{
    $stmt = $this->db->prepare(
        "SELECT *, 
                UNIX_TIMESTAMP(expires_at) as expira_en_timestamp,
                UNIX_TIMESTAMP(NOW()) as ahora_timestamp,
                (UNIX_TIMESTAMP(expires_at) > UNIX_TIMESTAMP(NOW())) AS vigente
         FROM qr_pagos WHERE token = :token LIMIT 1"
    );
    $stmt->execute(['token' => $token]);
    $row = $stmt->fetch();
    return $row !== false ? $row : null;
}

    /**
     * Marca el QR como 'confirmado' (fue escaneado).
     * Solo funciona si estaba 'pendiente' y no expiró.
     * Retorna true si se actualizó, false si ya no era válido.
     */
    public function confirmarEscaneo(string $token): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE qr_pagos
             SET estado = 'confirmado'
             WHERE token = :token
               AND estado = 'pendiente'
               AND expires_at > NOW()"
        );
        $stmt->execute(['token' => $token]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Marca el pago como 'completado' y guarda el nro de venta.
     */
    public function completar(string $token, int $nroVenta): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE qr_pagos
             SET estado = 'completado', nroVenta = :nro
             WHERE token = :token
               AND estado = 'confirmado'"
        );
        $stmt->execute(['token' => $token, 'nro' => $nroVenta]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Marca el QR como expirado manualmente.
     */
    public function expirar(string $token): void
    {
        $stmt = $this->db->prepare(
            "UPDATE qr_pagos SET estado = 'expirado'
             WHERE token = :token AND estado = 'pendiente'"
        );
        $stmt->execute(['token' => $token]);
    }

    /**
     * Verifica si el token sigue vigente (pendiente + no expirado).
     * Usa timestamps de servidor para garantizar sincronización.
     */
    public function esValido(string $token): bool
    {
        $pago = $this->findByToken($token);
        if (!$pago) return false;
        
        // Verificar que esté pendiente y que los timestamps indiquen que NO ha expirado
        $ahora = time();
        $expira = (int)$pago['expira_en_timestamp'];
        
        return $pago['estado'] === 'pendiente' && $expira > $ahora;
    }
}