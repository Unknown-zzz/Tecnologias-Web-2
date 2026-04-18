<?php
declare(strict_types=1);

final class ProductController extends Controller
{
    public function show(): void
    {
        $cod = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($cod <= 0) $this->redirect('home');

        $productModel = new Product($this->db);
        $product = $productModel->find($cod);

        if (!$product) $this->redirect('home');

        $this->render('store/product', [
            'title' => $product['nombre'],
            'product' => $product
        ]);
    }
}