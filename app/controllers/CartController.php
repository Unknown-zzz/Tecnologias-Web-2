<?php
declare(strict_types=1);

final class CartController extends Controller
{
    public function index(): void
    {
        $_SESSION['carrito'] ??= [];
        $productModel = new Product($this->db);
        $items = [];
        $total = 0.0;

        foreach ($_SESSION['carrito'] as $item) {
            $product = $productModel->find((int)$item['id']);
            if ($product === null) continue;

            $qty = (int)$item['cantidad'];
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
        $items = [];
        $total = 0.0;

        foreach ($_SESSION['carrito'] as $item) {
            $product = $productModel->find((int)$item['id']);
            if ($product === null) continue;

            $qty = (int)$item['cantidad'];
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
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
                echo 'ERROR';
                return;
            }
            $this->redirect('cart');
        }

        $found = false;
        foreach ($_SESSION['carrito'] as &$item) {
            if ((int)$item['id'] === $id) {
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

            if ($op === 'inc') {
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