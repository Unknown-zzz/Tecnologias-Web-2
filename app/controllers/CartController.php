<?php
declare(strict_types=1);

final class CartController extends Controller
{
    private function syncCartWithStock(Product $productModel): void
    {
        $_SESSION['carrito'] ??= [];

        foreach ($_SESSION['carrito'] as $index => $item) {
            $productId = (int)($item['id'] ?? 0);
            $quantity = max(1, (int)($item['cantidad'] ?? 1));

            $product = $productModel->find($productId);
            if ($product === null) {
                unset($_SESSION['carrito'][$index]);
                continue;
            }

            $stock = $productModel->getStock($productId);
            if ($stock <= 0) {
                unset($_SESSION['carrito'][$index]);
                continue;
            }

            if ($quantity > $stock) {
                $_SESSION['carrito'][$index]['cantidad'] = $stock;
            }
        }

        $_SESSION['carrito'] = array_values($_SESSION['carrito']);
    }

    public function index(): void
    {
        $_SESSION['carrito'] ??= [];
        $productModel = new Product($this->db);
        $this->syncCartWithStock($productModel);
        $items = [];
        $total = 0.0;

        foreach ($_SESSION['carrito'] as $item) {
            $product = $productModel->find((int)$item['id']);
            if ($product === null) continue;

            $qty = (int)$item['cantidad'];
            $product['stock'] = $productModel->getStock((int)$item['id']);
            $subtotal = (float)$product['precio'] * $qty;
            $total += $subtotal;

            $items[] = [
                'product' => $product,
                'cantidad' => $qty,
                'subtotal' => $subtotal
            ];
        }

        $this->render('cart/index', [
            'title' => 'Carrito de Compras',
            'items' => $items,
            'total' => $total
        ]);
    }

    public function modal(): void
    {
        $_SESSION['carrito'] ??= [];
        $productModel = new Product($this->db);
        $this->syncCartWithStock($productModel);
        $items = [];
        $total = 0.0;

        foreach ($_SESSION['carrito'] as $item) {
            $product = $productModel->find((int)$item['id']);
            if ($product === null) continue;

            $qty = (int)$item['cantidad'];
            $product['stock'] = $productModel->getStock((int)$item['id']);
            $subtotal = (float)$product['precio'] * $qty;
            $total += $subtotal;

            $items[] = [
                'product' => $product,
                'cantidad' => $qty,
                'subtotal' => $subtotal
            ];
        }

        $this->render('cart/modal', [
            'items' => $items,
            'total' => $total
        ]);
    }

    public function count(): void
    {
        $_SESSION['carrito'] ??= [];
        echo count($_SESSION['carrito']);
    }

    public function add(): void
    {
        $_SESSION['carrito'] ??= [];
        $productModel = new Product($this->db);
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
                echo 'ERROR';
                return;
            }
            $this->redirect('cart');
        }

        $product = $productModel->find($id);
        if ($product === null) {
            if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
                http_response_code(404);
                echo 'NOT_FOUND';
                return;
            }
            $this->redirect('cart');
        }

        $stock = $productModel->getStock($id);
        if ($stock <= 0) {
            if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
                http_response_code(409);
                echo 'OUT_OF_STOCK';
                return;
            }
            $_SESSION['flash_error'] = 'No hay stock disponible para este producto.';
            $this->redirect('cart');
        }

        $found = false;
        foreach ($_SESSION['carrito'] as &$item) {
            if ((int)$item['id'] === $id) {
                if ((int)$item['cantidad'] >= $stock) {
                    if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
                        http_response_code(409);
                        echo 'STOCK_LIMIT';
                        return;
                    }
                    $_SESSION['flash_error'] = 'No puedes agregar mas unidades de las disponibles en stock.';
                    $this->redirect('cart');
                }

                $item['cantidad']++;
                $found = true;
                break;
            }
        }
        unset($item);

        if (!$found) {
            $_SESSION['carrito'][] = ['id' => $id, 'cantidad' => 1];
        }

        if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
            echo 'OK';
            return;
        }

        $this->redirect('cart');
    }

    public function remove(): void
    {
        $_SESSION['carrito'] ??= [];
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        $_SESSION['carrito'] = array_values(array_filter(
            $_SESSION['carrito'],
            fn($item) => (int)$item['id'] !== $id
        ));

        if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
            echo 'OK';
            return;
        }

        $this->redirect('cart');
    }

    public function quantity(): void
    {
        $_SESSION['carrito'] ??= [];
        $productModel = new Product($this->db);
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $op = $_GET['op'] ?? '';

        if ($id <= 0 || !in_array($op, ['inc', 'dec'])) {
            if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
                echo 'ERROR';
                return;
            }
            $this->redirect('cart');
        }

        foreach ($_SESSION['carrito'] as $index => &$item) {
            if ((int)$item['id'] !== $id) continue;

            $stock = $productModel->getStock($id);
            if ($stock <= 0) {
                unset($_SESSION['carrito'][$index]);
                break;
            }

            if ($op === 'inc') {
                if ((int)$item['cantidad'] >= $stock) {
                    if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
                        http_response_code(409);
                        echo 'STOCK_LIMIT';
                        return;
                    }
                    $_SESSION['flash_error'] = 'No puedes superar el stock disponible.';
                    $this->redirect('cart');
                }

                $item['cantidad']++;
            } elseif ($op === 'dec') {
                $item['cantidad']--;
                if ($item['cantidad'] <= 0) {
                    unset($_SESSION['carrito'][$index]);
                }
            }
            break;
        }
        unset($item);

        $_SESSION['carrito'] = array_values($_SESSION['carrito'] ?? []);
        if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
            echo 'OK';
            return;
        }

        $this->redirect('cart');
    }

    public function clear(): void
    {
        $_SESSION['carrito'] = [];
        if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
            echo 'OK';
            return;
        }
        $this->redirect('cart');
    }
}