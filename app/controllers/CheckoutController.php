<?php
declare(strict_types=1);

final class CheckoutController extends Controller
{
    public function index(): void
    {
        if (empty($_SESSION['carrito'] ?? [])) $this->redirect('cart');
        if (!isLoggedIn()) $this->redirect('login');

        $this->render('checkout/index', ['title' => 'Finalizar Compra']);
    }

    public function process(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['carrito'] ?? [])) {
            $this->redirect('cart');
        }

        $productModel = new Product($this->db);
        $saleModel = new Sale($this->db);
        $clientModel = new Client($this->db);

        $usuario = $_SESSION['usuario'] ?? '';
        if (empty($usuario)) {
            $_SESSION['flash_error'] = 'Debes iniciar sesión para comprar.';
            $this->redirect('login');
        }

        $cliente = $clientModel->findByUsuario($usuario);
        if (!$cliente) {
            $_SESSION['flash_error'] = 'Cliente no encontrado. Por favor, completa tu perfil primero.';
            $this->redirect('cart');
        }

        // Reconstruir los detalles del carrito desde la sesión
        $items = [];
        $total = 0.0;

        foreach ($_SESSION['carrito'] as $sessionItem) {
            $productId = (int)$sessionItem['id'];
            $cantidad = (int)$sessionItem['cantidad'];

            $product = $productModel->find($productId);
            if ($product === null) {
                $_SESSION['flash_error'] = 'Producto ' . $productId . ' no encontrado.';
                $this->redirect('cart');
            }

            $currentStock = $productModel->getStock($productId);
            if ($currentStock < $cantidad) {
                $_SESSION['flash_error'] = 'Stock insuficiente para ' . e($product['nombre']) . '. Disponible: ' . $currentStock;
                $this->redirect('cart');
            }

            if (!$productModel->updateStock($productId, $cantidad)) {
                $_SESSION['flash_error'] = 'Error al actualizar el stock de ' . e($product['nombre']);
                $this->redirect('cart');
            }

            $subtotal = (float)$product['precio'] * $cantidad;
            $total += $subtotal;

            $items[] = [
                'id' => $productId,
                'cantidad' => $cantidad,
                'product' => $product,
                'subtotal' => $subtotal
            ];
        }

        try {
            $nroVenta = $saleModel->create($cliente['ci'], $items);
        } catch (Exception $e) {
            $_SESSION['flash_error'] = 'Error al procesar la compra: ' . $e->getMessage();
            $this->redirect('cart');
        }

        if ($nroVenta) {
            $receiptDir = __DIR__ . '/../../storage/receipts';
            if (!is_dir($receiptDir) && !mkdir($receiptDir, 0777, true) && !is_dir($receiptDir)) {
                error_log('No se pudo crear el directorio de recibos: ' . $receiptDir);
            }

            $filename = sprintf('venta_%s_%s.pdf', $nroVenta, time());
            $relativePath = 'storage/receipts/' . $filename;
            $absolutePath = $receiptDir . DIRECTORY_SEPARATOR . $filename;

            $reportCreated = $saleModel->generateInvoicePdf($cliente, $nroVenta, $items, $total, $absolutePath);
            if ($reportCreated) {
                $saleModel->saveReportPath($nroVenta, $relativePath);
            } else {
                error_log('No se pudo generar el informe PDF para la venta ' . $nroVenta);
            }

            unset($_SESSION['carrito']);
            $this->redirect('checkout/success&nro=' . $nroVenta);
        }

        $_SESSION['flash_error'] = 'Error al procesar la compra. Intente nuevamente.';
        $this->redirect('cart');
    }

    public function success(): void
    {
        $nro = (int)($_GET['nro'] ?? 0);
        if ($nro <= 0) {
            $this->redirect('home');
        }

        $saleModel = new Sale($this->db);
        $sale = $saleModel->find($nro);

        if (!$sale) {
            $this->redirect('home');
        }

        $this->render('checkout/success', ['title' => 'Compra Exitosa', 'sale' => $sale]);
    }
}