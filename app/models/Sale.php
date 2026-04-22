<?php
declare(strict_types=1);

use Dompdf\Dompdf;
use Dompdf\Options;

final class Sale
{
    public function __construct(private PDO $db) {}

    public function create(string $ciCliente, array $items): ?int
    {
        $this->db->beginTransaction();

        try {
            // Crear nota de venta usando output parameter
            $nroVenta = 0;
            $stmtVenta = $this->db->prepare("CALL sp_venta_create(:ciCliente, @nro)");
            $stmtVenta->execute(['ciCliente' => $ciCliente]);
            
            // Obtener el valor del output parameter
            $result = $this->db->query("SELECT @nro as nro")->fetch();
            $nroVenta = (int)($result['nro'] ?? 0);

            if ($nroVenta <= 0) {
                throw new Exception('Error al crear la nota de venta');
            }

            // Agregar detalles de la venta
            $stmtDetalle = $this->db->prepare("CALL sp_venta_detail_add(:nro, :cod, :cant, :precio)");

            foreach ($items as $item) {
                if (empty($item['id']) || empty($item['cantidad']) || empty($item['product']['precio'])) {
                    throw new Exception('Datos incompletos del producto');
                }
                
                $result = $stmtDetalle->execute([
                    'nro' => $nroVenta,
                    'cod' => (int)$item['id'],
                    'cant' => (int)$item['cantidad'],
                    'precio' => (float)$item['product']['precio']
                ]);
                
                if (!$result) {
                    throw new Exception('Error al insertar detalle de venta');
                }
            }

            $this->db->commit();
            return $nroVenta;
        } catch (Exception $e) {
            error_log('Sale::create error: ' . $e->getMessage());
            $this->db->rollBack();
            return null;
        }
    }

    public function saveReportPath(int $nroVenta, string $rutaInforme): bool
    {
        $stmt = $this->db->prepare("CALL sp_venta_set_report_path(:nro, :ruta)");
        return $stmt->execute(['nro' => $nroVenta, 'ruta' => $rutaInforme]);
    }

    public function generateInvoicePdf(array $cliente, int $nroVenta, array $items, float $total, string $savePath): bool
    {
        if (!class_exists(Dompdf::class)) {
            error_log('Dompdf no esta disponible. Ejecuta: composer install');
            return false;
        }

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($this->buildInvoiceHtml($cliente, $nroVenta, $items, $total), 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return file_put_contents($savePath, $dompdf->output()) !== false;
    }

    private function buildInvoiceHtml(array $cliente, int $nroVenta, array $items, float $total): string
    {
        $date = date('d/m/Y H:i:s');
        $customerName = trim(($cliente['nombres'] ?? '') . ' ' . ($cliente['apPaterno'] ?? '') . ' ' . ($cliente['apMaterno'] ?? ''));

        $rowsHtml = '';
        foreach ($items as $item) {
            $productName = (string)($item['product']['nombre'] ?? 'Producto');
            $quantity = (int)($item['cantidad'] ?? 0);
            $unitPrice = number_format((float)($item['product']['precio'] ?? 0), 2, '.', ',');
            $subtotal = number_format((float)($item['subtotal'] ?? ($quantity * (float)($item['product']['precio'] ?? 0))), 2, '.', ',');

            $rowsHtml .= '<tr>'
                . '<td>' . $this->e($productName) . '</td>'
                . '<td class="text-center">' . $quantity . '</td>'
                . '<td class="text-right">Bs. ' . $unitPrice . '</td>'
                . '<td class="text-right">Bs. ' . $subtotal . '</td>'
                . '</tr>';
        }

        $logoHtml = $this->buildBrandLogoHtml();

        return '<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 24px; }
        body { font-family: DejaVu Sans, sans-serif; color: #1d1d1f; font-size: 12px; }
        .header { width: 100%; margin-bottom: 18px; }
        .header td { vertical-align: middle; }
        .logo-wrap { width: 170px; }
        .logo-wrap svg { display: block; width: 170px; height: 44px; }
        .brand { text-align: right; }
        .brand h1 { margin: 0; font-size: 22px; color: #1f4f46; }
        .brand p { margin: 2px 0 0; font-size: 11px; color: #666; }
        .box { border: 1px solid #d9d9d9; border-radius: 4px; padding: 10px; margin-bottom: 14px; }
        .box p { margin: 3px 0; }
        .table { width: 100%; border-collapse: collapse; }
        .table th { background: #1f4f46; color: #fff; padding: 8px; font-size: 11px; text-align: left; }
        .table td { border-bottom: 1px solid #ececec; padding: 8px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total { margin-top: 12px; text-align: right; font-size: 15px; font-weight: 700; color: #1f4f46; }
        .footer { margin-top: 18px; font-size: 10px; color: #777; text-align: center; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td>' . $logoHtml . '</td>
            <td class="brand">
                <h1>Tienda Amiga</h1>
                <p>Factura / Recibo de Venta</p>
                <p>Nro Venta: ' . $nroVenta . '</p>
                <p>Fecha: ' . $this->e($date) . '</p>
            </td>
        </tr>
    </table>

    <div class="box">
        <p><strong>Cliente:</strong> ' . $this->e($customerName) . '</p>
        <p><strong>CI:</strong> ' . $this->e((string)($cliente['ciCliente'] ?? '')) . '</p>
        <p><strong>Correo:</strong> ' . $this->e((string)($cliente['correo'] ?? '')) . '</p>
        <p><strong>Telefono:</strong> ' . $this->e((string)($cliente['nroCelular'] ?? '')) . '</p>
        <p><strong>Direccion:</strong> ' . $this->e((string)($cliente['direccion'] ?? '')) . '</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Producto</th>
                <th class="text-center">Cant.</th>
                <th class="text-right">P. Unit</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>' . $rowsHtml . '</tbody>
    </table>

    <div class="total">TOTAL: Bs. ' . number_format($total, 2, '.', ',') . '</div>
    <div class="footer">Gracias por su compra. Comprobante generado por Tienda Amiga.</div>
</body>
</html>';
    }

    private function buildBrandLogoHtml(): string
    {
        return '<div class="logo-wrap">'
            . '<svg viewBox="0 0 360 90" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Logo Tienda Amiga">'
            . '<rect x="1" y="1" width="358" height="88" rx="12" fill="#1f4f46"/>'
            . '<circle cx="42" cy="45" r="20" fill="#f1b84b"/>'
            . '<path d="M34 45h16M42 37v16" stroke="#1f4f46" stroke-width="4" stroke-linecap="round"/>'
            . '<text x="74" y="53" font-size="28" font-family="DejaVu Sans, sans-serif" font-weight="700" fill="#ffffff">Tienda Amiga</text>'
            . '</svg>'
            . '</div>';
    }

    private function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    public function all(): array
    {
        $stmt = $this->db->prepare("CALL sp_venta_all()");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function find(int $nro): ?array
    {
        // Obtener datos de la venta
        $stmtVenta = $this->db->prepare("CALL sp_venta_find(:nro)");
        $stmtVenta->execute(['nro' => $nro]);
        $venta = $stmtVenta->fetch();
        $stmtVenta->closeCursor();
        
        if ($venta) {
            // Obtener detalles de la venta
            $stmtDetalle = $this->db->prepare("CALL sp_venta_details(:nro)");
            $stmtDetalle->execute(['nro' => $nro]);
            $venta['detalles'] = $stmtDetalle->fetchAll();
            $stmtDetalle->closeCursor();
            
            // Calcular total
            $venta['total'] = array_reduce($venta['detalles'], function($carry, $item) {
                return $carry + ($item['cant'] * $item['precioUnitario']);
            }, 0);
        }
        
        return $venta ?: null;
    }

    public function getSalesByClientCi(string $ciCliente): array
    {
        $stmt = $this->db->prepare(
            "SELECT nv.nro, nv.fecha, nv.rutaInforme, dnv.codProducto, dnv.cant, dnv.precioUnitario, p.nombre AS producto " .
            "FROM NotaVenta nv " .
            "INNER JOIN DetalleNotaVenta dnv ON nv.nro = dnv.nroNotaVenta " .
            "INNER JOIN Producto p ON dnv.codProducto = p.cod " .
            "WHERE nv.ciCliente = :ciCliente " .
            "ORDER BY nv.fecha DESC, nv.nro DESC"
        );
        $stmt->execute(['ciCliente' => $ciCliente]);
        $rows = $stmt->fetchAll();

        $sales = [];
        foreach ($rows as $row) {
            $nro = (int)$row['nro'];
            if (!isset($sales[$nro])) {
                $sales[$nro] = [
                    'nro' => $nro,
                    'fecha' => $row['fecha'],
                    'rutaInforme' => $row['rutaInforme'],
                    'total' => 0.0,
                    'detalles' => []
                ];
            }

            $subtotal = (float)$row['cant'] * (float)$row['precioUnitario'];
            $sales[$nro]['total'] += $subtotal;
            $sales[$nro]['detalles'][] = [
                'codProducto' => (int)$row['codProducto'],
                'producto' => $row['producto'],
                'cant' => (int)$row['cant'],
                'precioUnitario' => (float)$row['precioUnitario'],
                'subtotal' => $subtotal,
            ];
        }

        return array_values($sales);
    }

    public function getTotalVentas(): int
    {
        $stmt = $this->db->prepare("CALL sp_venta_total_count()");
        $stmt->execute();
        $result = $stmt->fetch();
        return (int)($result['total'] ?? 0);
    }

    public function getTotalIngresos(): float
    {
        $stmt = $this->db->prepare("CALL sp_venta_total_ingresos()");
        $stmt->execute();
        $result = $stmt->fetch();
        return (float)($result['total'] ?? 0);
    }

    public function getSalesAndRevenueByMonth(int $limit = 6): array
    {
        $safeLimit = max(1, min($limit, 24));

        $sql = "SELECT DATE_FORMAT(nv.fecha, '%Y-%m') AS periodo, " .
               "COUNT(DISTINCT nv.nro) AS ventas, " .
               "COALESCE(SUM(dnv.cant * dnv.precioUnitario), 0) AS ingresos " .
               "FROM NotaVenta nv " .
               "LEFT JOIN DetalleNotaVenta dnv ON dnv.nroNotaVenta = nv.nro " .
               "GROUP BY DATE_FORMAT(nv.fecha, '%Y-%m') " .
               "ORDER BY periodo DESC " .
               "LIMIT " . $safeLimit;

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        $rows = array_reverse($rows);

        return array_map(static function (array $row): array {
            $label = (string)($row['periodo'] ?? '');
            if (preg_match('/^(\d{4})-(\d{2})$/', $label, $match)) {
                $monthNames = [
                    '01' => 'Ene', '02' => 'Feb', '03' => 'Mar', '04' => 'Abr',
                    '05' => 'May', '06' => 'Jun', '07' => 'Jul', '08' => 'Ago',
                    '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dic'
                ];
                $label = ($monthNames[$match[2]] ?? $match[2]) . ' ' . $match[1];
            }

            return [
                'periodo' => $label,
                'ventas' => (int)($row['ventas'] ?? 0),
                'ingresos' => (float)($row['ingresos'] ?? 0),
            ];
        }, $rows);
    }

    public function getTopProductos(int $limite = 7): array
    {
        $stmt = $this->db->prepare("CALL sp_productos_top_vendidos(:limite)");
        $stmt->execute(['limite' => max(1, min($limite, 50))]);
        return $stmt->fetchAll();
    }
}