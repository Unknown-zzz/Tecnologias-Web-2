<?php
declare(strict_types=1);

final class QrPaymentController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    // POST  index.php?r=qr/generate
    // Genera un token QR y lo devuelve como JSON.
    // Llamado desde el frontend (AJAX) cuando el usuario elige pago QR.
    // ─────────────────────────────────────────────────────────────
    public function generate(): void
    {
        header('Content-Type: application/json');

        if (!isLoggedIn() || empty($_SESSION['carrito'] ?? [])) {
            echo json_encode(['success' => false, 'error' => 'No autorizado o carrito vacío.']);
            exit;
        }

        $clientModel = new Client($this->db);
        $usuario     = $_SESSION['usuario'] ?? '';
        $cliente     = $clientModel->findByUsuario($usuario);

        if (!$cliente) {
            echo json_encode(['success' => false, 'error' => 'Cliente no encontrado. Completa tu perfil primero.']);
            exit;
        }

        $qrModel = new QrPayment($this->db);
        $token   = $qrModel->create($cliente['ci'], $_SESSION['carrito'], 5); // 5 minutos

        $baseUrl  = rtrim($this->config['app']['url'], '/');
        $scanUrl  = $baseUrl . '/index.php?r=qr/scan&token=' . $token;

        echo json_encode([
            'success'  => true,
            'token'    => $token,
            'scan_url' => $scanUrl,
        ]);
        exit;
    }

    // ─────────────────────────────────────────────────────────────
    // GET  index.php?r=qr/status&token=TOKEN
    // Retorna JSON con el estado actual del pago QR.
    // Llamado periódicamente por el frontend (polling).
    // ─────────────────────────────────────────────────────────────
    public function status(): void
    {
        header('Content-Type: application/json');

        $token = trim($_GET['token'] ?? '');
        if (empty($token)) {
            echo json_encode(['estado' => 'error', 'mensaje' => 'Token requerido.']);
            exit;
        }

        $qrModel = new QrPayment($this->db);
        $pago    = $qrModel->findByToken($token);

        if (!$pago) {
            echo json_encode(['estado' => 'error', 'mensaje' => 'Token inválido.']);
            exit;
        }

        // Auto-expirar si pasó el tiempo y sigue pendiente
        // Usar timestamps de servidor para mejor sincronización
        $ahora = time();
        $expira = (int)($pago['expira_en_timestamp'] ?? strtotime($pago['expires_at']));
        
        if ($pago['estado'] === 'pendiente' && $expira <= $ahora) {
            $qrModel->expirar($token);
            echo json_encode(['estado' => 'expirado', 'servidor_tiempo' => $ahora]);
            exit;
        }

        $respuesta = [
            'estado' => $pago['estado'],
            'servidor_tiempo' => $ahora,
            'expira_en' => $expira,
        ];

        if ($pago['estado'] === 'completado') {
            $respuesta['nroVenta']   = (int)$pago['nroVenta'];
            $respuesta['redirect']   = 'index.php?r=checkout/success&nro=' . $pago['nroVenta'];

            // Vaciar el carrito de la sesión al detectar pago completado
            if (!empty($_SESSION['carrito'])) {
                unset($_SESSION['carrito']);
            }
        }

        echo json_encode($respuesta);
        exit;
    }

    // ─────────────────────────────────────────────────────────────
    // GET  index.php?r=qr/scan&token=TOKEN
    // Página que se muestra al escanear el QR (dispositivo del cliente).
    // ─────────────────────────────────────────────────────────────
    public function scan(): void
    {
        $token = trim($_GET['token'] ?? '');

        if (empty($token)) {
            $this->redirect('home');
        }

        $qrModel = new QrPayment($this->db);
        $pago    = $qrModel->findByToken($token);

        $data = [
            'title' => 'Confirmar Pago QR',
            'token' => $token,
            'pago'  => $pago,
            'error' => null,
        ];

        if (!$pago) {
            $data['error'] = 'El código QR no es válido.';
            $this->render('checkout/qr_scan', $data);
            return;
        }

        if ($pago['estado'] === 'completado') {
            $data['error'] = '✅ Este pago ya fue procesado exitosamente.';
            $this->render('checkout/qr_scan', $data);
            return;
        }

        if ($pago['estado'] === 'expirado') {
            $data['error'] = 'Este código QR ha expirado. Genera uno nuevo desde el carrito.';
            $this->render('checkout/qr_scan', $data);
            return;
        }

        if ($pago['estado'] === 'confirmado') {
            $data['error'] = 'Este QR ya fue escaneado y está siendo procesado.';
            $this->render('checkout/qr_scan', $data);
            return;
        }

        // Verificar expiración usando timestamps del servidor
        $ahora = time();
        $expira = (int)($pago['expira_en_timestamp'] ?? strtotime($pago['expires_at']));
        
        if ($pago['estado'] === 'pendiente' && $expira <= $ahora) {
            $qrModel->expirar($token);
            $data['error'] = 'Este código QR ha expirado. Genera uno nuevo desde el carrito.';
            $this->render('checkout/qr_scan', $data);
            return;
        }

        // QR válido y pendiente → mostrar confirmación
        $this->render('checkout/qr_scan', $data);
    }

    // ─────────────────────────────────────────────────────────────
    // POST  index.php?r=qr/confirm
    // Confirma el pago desde la página de escaneo y procesa la compra.
    // ─────────────────────────────────────────────────────────────
    public function confirm(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('home');
        }

        $token = trim($_POST['token'] ?? '');

        if (empty($token)) {
            $this->redirect('home');
        }

        $qrModel = new QrPayment($this->db);

        // Intentar marcar como confirmado (solo válido si estaba pendiente y no expiró)
        if (!$qrModel->confirmarEscaneo($token)) {
            $pago = $qrModel->findByToken($token);
            $estado = $pago['estado'] ?? 'desconocido';
            $msg = match($estado) {
                'completado' => 'Este pago ya fue completado anteriormente.',
                'expirado'   => 'El QR ha expirado. Genera uno nuevo.',
                'confirmado' => 'El QR ya fue confirmado y está procesándose.',
                default      => 'El código QR no es válido o ya expiró.',
            };
            $this->render('checkout/qr_scan', [
                'title' => 'Error de Pago',
                'token' => $token,
                'pago'  => $pago,
                'error' => $msg,
            ]);
            return;
        }

        // Obtener los datos del pago para procesar la compra
        $pago    = $qrModel->findByToken($token);
        $carrito = json_decode($pago['carrito'], true);

        $productModel = new Product($this->db);
        $saleModel    = new Sale($this->db);
        $clientModel  = new Client($this->db);

        $cliente = $clientModel->findByCi($pago['ciCliente']);

        if (!$cliente) {
            $this->render('checkout/qr_scan', [
                'title' => 'Error',
                'token' => $token,
                'pago'  => $pago,
                'error' => 'No se encontró el cliente asociado al pago.',
            ]);
            return;
        }

        // Validar stock y construir items
        $items = [];
        $total = 0.0;

        foreach ($carrito as $sessionItem) {
            $productId = (int)$sessionItem['id'];
            $cantidad  = (int)$sessionItem['cantidad'];

            $product = $productModel->find($productId);
            if (!$product) {
                $this->render('checkout/qr_scan', [
                    'title' => 'Error',
                    'token' => $token,
                    'pago'  => $pago,
                    'error' => "Producto #{$productId} no encontrado. Contacta al soporte.",
                ]);
                return;
            }

            $stockActual = $productModel->getStock($productId);
            if ($stockActual < $cantidad) {
                $this->render('checkout/qr_scan', [
                    'title' => 'Error de Stock',
                    'token' => $token,
                    'pago'  => $pago,
                    'error' => "Stock insuficiente para '{$product['nombre']}'. Disponible: {$stockActual}.",
                ]);
                return;
            }

            if (!$productModel->updateStock($productId, $cantidad)) {
                $this->render('checkout/qr_scan', [
                    'title' => 'Error',
                    'token' => $token,
                    'pago'  => $pago,
                    'error' => "Error al actualizar el stock de '{$product['nombre']}'.",
                ]);
                return;
            }

            $subtotal = (float)$product['precio'] * $cantidad;
            $total   += $subtotal;

            $items[] = [
                'id'       => $productId,
                'cantidad' => $cantidad,
                'product'  => $product,
                'subtotal' => $subtotal,
            ];
        }

        // Crear la nota de venta
        try {
            $nroVenta = $saleModel->create($cliente['ci'], $items);
        } catch (Exception $e) {
            $this->render('checkout/qr_scan', [
                'title' => 'Error',
                'token' => $token,
                'pago'  => $pago,
                'error' => 'Error al registrar la venta: ' . $e->getMessage(),
            ]);
            return;
        }

        if (!$nroVenta) {
            $this->render('checkout/qr_scan', [
                'title' => 'Error',
                'token' => $token,
                'pago'  => $pago,
                'error' => 'Error interno al procesar la compra. Intenta nuevamente.',
            ]);
            return;
        }

        // Generar PDF de recibo
        $receiptDir = __DIR__ . '/../../storage/receipts';
        if (!is_dir($receiptDir)) {
            mkdir($receiptDir, 0777, true);
        }

        $filename     = sprintf('venta_%s_%s.pdf', $nroVenta, time());
        $relativePath = 'storage/receipts/' . $filename;
        $absolutePath = $receiptDir . DIRECTORY_SEPARATOR . $filename;

        $pdfGenerado = $saleModel->generateInvoicePdf($cliente, $nroVenta, $items, $total, $absolutePath);
        if ($pdfGenerado) {
            $saleModel->saveReportPath($nroVenta, $relativePath);
        }

        // Marcar QR como completado
        $qrModel->completar($token, $nroVenta);

        // Mostrar pantalla de éxito en el dispositivo que escaneó
        $this->render('checkout/qr_scan', [
            'title'     => '¡Pago Confirmado!',
            'token'     => $token,
            'pago'      => $pago,
            'error'     => null,
            'success'   => true,
            'nroVenta'  => $nroVenta,
        ]);
    }
}