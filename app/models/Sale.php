<?php
declare(strict_types=1);

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
        $content = $this->buildPdfDocument($cliente, $nroVenta, $items, $total);
        return file_put_contents($savePath, $content) !== false;
    }

    private function buildPdfDocument(array $cliente, int $nroVenta, array $items, float $total): string
    {
        $title = 'RECIBO DE VENTA #' . $nroVenta;
        $date = date('Y-m-d H:i:s');
        $customerName = trim(($cliente['nombres'] ?? '') . ' ' . ($cliente['apPaterno'] ?? '') . ' ' . ($cliente['apMaterno'] ?? ''));
        $customerEmail = $cliente['correo'] ?? '';
        $customerAddress = $cliente['direccion'] ?? '';
        $customerPhone = $cliente['nroCelular'] ?? '';

        $logoPath = __DIR__ . '/../../resources/logo/LogoInvertido.png';
        $logoObject = $this->buildPdfLogoImageObject($logoPath);
        $hasLogo = $logoObject !== null;

        $streamLines = [];

        if ($hasLogo) {
            $streamLines[] = 'q';
            $streamLines[] = '120 0 0 70 40 980 cm';
            $streamLines[] = '/Im1 Do';
            $streamLines[] = 'Q';
            $startY = 860;
        } else {
            $startY = 980;
        }

        $streamLines[] = 'BT';
        $streamLines[] = '/F1 18 Tf';
        $streamLines[] = '40 ' . $startY . ' Td';
        $streamLines[] = '(' . $this->escapePdfString($title) . ') Tj';
        $streamLines[] = '0 -24 Td';
        $streamLines[] = '/F1 11 Tf';
        $streamLines[] = '(' . $this->escapePdfString('Ecommerce Pro') . ') Tj';
        $streamLines[] = '0 -18 Td';
        $streamLines[] = '(' . $this->escapePdfString('Fecha: ' . $date) . ') Tj';
        $streamLines[] = '0 -18 Td';
        $streamLines[] = '(' . $this->escapePdfString('Cliente: ' . $customerName) . ') Tj';
        $streamLines[] = '0 -18 Td';
        $streamLines[] = '(' . $this->escapePdfString('CI: ' . ($cliente['ciCliente'] ?? '')) . ') Tj';

        if ($customerEmail !== '') {
            $streamLines[] = '0 -18 Td';
            $streamLines[] = '(' . $this->escapePdfString('Email: ' . $customerEmail) . ') Tj';
        }

        if ($customerAddress !== '') {
            $streamLines[] = '0 -18 Td';
            $streamLines[] = '(' . $this->escapePdfString('Dirección: ' . $customerAddress) . ') Tj';
        }

        if ($customerPhone !== '') {
            $streamLines[] = '0 -18 Td';
            $streamLines[] = '(' . $this->escapePdfString('Teléfono: ' . $customerPhone) . ') Tj';
        }

        $streamLines[] = '0 -28 Td';
        $streamLines[] = '/F1 12 Tf';
        $streamLines[] = '(' . $this->escapePdfString('Detalle de compra:') . ') Tj';
        $streamLines[] = '0 -20 Td';
        $streamLines[] = '/F1 10 Tf';

        foreach ($items as $item) {
            $productName = $item['product']['nombre'] ?? 'Producto';
            $quantity = (int)($item['cantidad'] ?? 0);
            $unitPrice = number_format((float)($item['product']['precio'] ?? 0), 2, '.', ',');
            $subtotal = number_format((float)($item['subtotal'] ?? ($quantity * (float)($item['product']['precio'] ?? 0))), 2, '.', ',');
            $line = sprintf('%s x%d @ Bs.%s = Bs.%s', $productName, $quantity, $unitPrice, $subtotal);
            $streamLines[] = '(' . $this->escapePdfString($line) . ') Tj';
            $streamLines[] = '0 -16 Td';
        }

        $streamLines[] = '/F1 12 Tf';
        $streamLines[] = '0 -24 Td';
        $streamLines[] = '(' . $this->escapePdfString('TOTAL: Bs. ' . number_format($total, 2, '.', ',')) . ') Tj';
        $streamLines[] = 'ET';

        $stream = implode("\n", $streamLines);

        $objects = [];
        $objects[1] = "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";
        $objects[2] = "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n";
        $pageResources = '<< /Font << /F1 5 0 R >>';

        if ($hasLogo) {
            $pageResources .= ' /XObject << /Im1 6 0 R >>';
        }

        $pageResources .= ' >>';
        $objects[3] = "3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 420 1080] /Contents 4 0 R /Resources " . $pageResources . " >>\nendobj\n";
        $objects[4] = "4 0 obj\n<< /Length " . strlen($stream) . " >>\nstream\n" . $stream . "endstream\nendobj\n";
        $objects[5] = "5 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n";

        if ($hasLogo) {
            $objects[6] = $logoObject;
        }

        $pdf = "%PDF-1.4\n";
        $offsets = [];
        $position = strlen($pdf);

        foreach ($objects as $object) {
            $offsets[] = $position;
            $pdf .= $object;
            $position += strlen($object);
        }

        $xref = "xref\n";
        $xref .= "0 " . (count($objects) + 1) . "\n";
        $xref .= "0000000000 65535 f \r\n";

        foreach ($offsets as $offset) {
            $xref .= sprintf("%010d 00000 n \r\n", $offset);
        }

        $xrefOffset = $position;

        $trailer  = "trailer\n";
        $trailer .= "<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n";
        $trailer .= "startxref\n";
        $trailer .= $xrefOffset . "\n";
        $trailer .= "%%EOF\n";

        return $pdf . $xref . $trailer;
    }

    private function buildPdfLogoImageObject(string $logoPath): ?string
    {
        if (!file_exists($logoPath)) {
            return null;
        }

        if (function_exists('imagecreatefrompng') && function_exists('imagejpeg')) {
            $image = @imagecreatefrompng($logoPath);
            if ($image !== false) {
                $width = imagesx($image);
                $height = imagesy($image);

                ob_start();
                imagejpeg($image, null, 85);
                $jpegData = ob_get_clean();
                imagedestroy($image);

                if ($jpegData !== false && $jpegData !== '') {
                    $length = strlen($jpegData);
                    return "6 0 obj\n<< /Type /XObject /Subtype /Image /Width " . $width . " /Height " . $height . " /ColorSpace /DeviceRGB /BitsPerComponent 8 /Filter /DCTDecode /Length " . $length . " >>\nstream\n" . $jpegData . "\nendstream\nendobj\n";
                }
            }
        }

        return $this->buildPdfLogoObjectFromPng($logoPath);
    }

    private function buildPdfLogoObjectFromPng(string $logoPath): ?string
    {
        $png = file_get_contents($logoPath);
        if ($png === false || substr($png, 0, 8) !== "\x89PNG\r\n\x1a\n") {
            return null;
        }

        $offset = 8;
        $width = 0;
        $height = 0;
        $bitDepth = 0;
        $colorType = 0;
        $idatData = '';

        while ($offset + 8 <= strlen($png)) {
            $lengthData = substr($png, $offset, 4);
            if ($lengthData === false) {
                break;
            }

            $length = unpack('N', $lengthData)[1];
            $type = substr($png, $offset + 4, 4);
            $chunkData = substr($png, $offset + 8, $length);

            if ($type === 'IHDR') {
                $values = unpack('Nwidth/Nheight/CbitDepth/CcolorType/Ccompression/Cfilter/Cinterlace', $chunkData);
                $width = $values['width'];
                $height = $values['height'];
                $bitDepth = $values['bitDepth'];
                $colorType = $values['colorType'];

                if ($bitDepth !== 8 || $values['compression'] !== 0 || $values['filter'] !== 0 || $values['interlace'] !== 0) {
                    return null;
                }

                if (!in_array($colorType, [2, 6], true)) {
                    return null;
                }
            } elseif ($type === 'IDAT') {
                $idatData .= $chunkData;
            } elseif ($type === 'IEND') {
                break;
            }

            $offset += 12 + $length;
        }

        if ($width <= 0 || $height <= 0 || $idatData === '') {
            return null;
        }

        $decoded = @gzuncompress($idatData);
        if ($decoded === false) {
            return null;
        }

        $bytesPerPixel = $colorType === 6 ? 4 : 3;
        $rowLength = $bytesPerPixel * $width;
        $pos = 0;
        $prevRow = str_repeat("\0", $rowLength);
        $pixelData = '';

        for ($row = 0; $row < $height; $row++) {
            if ($pos >= strlen($decoded)) {
                return null;
            }
            $filterType = ord($decoded[$pos]);
            $pos++;
            $rowBytes = substr($decoded, $pos, $rowLength);
            $pos += $rowLength;

            if (strlen($rowBytes) !== $rowLength) {
                return null;
            }

            $decodedRow = $this->decodePngFilter($filterType, $rowBytes, $prevRow, $bytesPerPixel);
            if ($decodedRow === null) {
                return null;
            }

            if ($colorType === 6) {
                $rgbRow = '';
                for ($i = 0; $i < strlen($decodedRow); $i += 4) {
                    $red = ord($decodedRow[$i]);
                    $green = ord($decodedRow[$i + 1]);
                    $blue = ord($decodedRow[$i + 2]);
                    $alpha = ord($decodedRow[$i + 3]);

                    $alphaRatio = $alpha / 255;
                    $red = (int)round($red * $alphaRatio + 255 * (1 - $alphaRatio));
                    $green = (int)round($green * $alphaRatio + 255 * (1 - $alphaRatio));
                    $blue = (int)round($blue * $alphaRatio + 255 * (1 - $alphaRatio));

                    $rgbRow .= chr($red) . chr($green) . chr($blue);
                }
                $pixelData .= chr(0) . $rgbRow;
                $prevRow = $decodedRow;
            } else {
                $pixelData .= chr(0) . $decodedRow;
                $prevRow = $decodedRow;
            }
        }

        $compressed = gzcompress($pixelData);
        $length = strlen($compressed);

        return "6 0 obj\n<< /Type /XObject /Subtype /Image /Width " . $width . " /Height " . $height . " /ColorSpace /DeviceRGB /BitsPerComponent 8 /Filter /FlateDecode /DecodeParms << /Predictor 15 /Colors 3 /BitsPerComponent 8 /Columns " . $width . " >> /Length " . $length . " >>\nstream\n" . $compressed . "\nendstream\nendobj\n";
    }

    private function decodePngFilter(int $filterType, string $rowBytes, string $prevRow, int $bytesPerPixel): ?string
    {
        $length = strlen($rowBytes);
        $result = '';

        if ($filterType === 0) {
            return $rowBytes;
        }

        for ($i = 0; $i < $length; $i++) {
            $x = ord($rowBytes[$i]);
            $a = $i >= $bytesPerPixel ? ord($result[$i - $bytesPerPixel]) : 0;
            $b = ord($prevRow[$i]);
            $c = $i >= $bytesPerPixel ? ord($prevRow[$i - $bytesPerPixel]) : 0;

            switch ($filterType) {
                case 1:
                    $value = ($x + $a) & 0xFF;
                    break;
                case 2:
                    $value = ($x + $b) & 0xFF;
                    break;
                case 3:
                    $value = ($x + floor(($a + $b) / 2)) & 0xFF;
                    break;
                case 4:
                    $p = $a + $b - $c;
                    $pa = abs($p - $a);
                    $pb = abs($p - $b);
                    $pc = abs($p - $c);
                    if ($pa <= $pb && $pa <= $pc) {
                        $pr = $a;
                    } elseif ($pb <= $pc) {
                        $pr = $b;
                    } else {
                        $pr = $c;
                    }
                    $value = ($x + $pr) & 0xFF;
                    break;
                default:
                    return null;
            }

            $result .= chr($value);
        }

        return $result;
    }

    private function escapePdfString(string $text): string
    {
        $text = $this->normalizeTextForPdf($text);
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }

    private function normalizeTextForPdf(string $text): string
    {
        $replacements = [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
            'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U',
            'ñ' => 'n', 'Ñ' => 'N', 'ü' => 'u', 'Ü' => 'U'
        ];

        $text = strtr($text, $replacements);
        $converted = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $text);
        return $converted !== false ? $converted : $text;
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
}