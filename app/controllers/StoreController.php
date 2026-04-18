<?php
declare(strict_types=1);

final class StoreController extends Controller
{
    public function index(): void
    {
        $productModel = new Product($this->db);
        $products = $productModel->allActive();

        $this->render('store/index', [
            'title' => 'Tienda en Línea',
            'products' => $products
        ]);
    }

    public function purchases(): void
    {
        requireLogin();

        $clientModel = new Client($this->db);
        $client = $clientModel->findByUsuario($_SESSION['usuario']);

        if (!$client) {
            $_SESSION['flash_error'] = 'No se pudo localizar su cuenta de cliente.';
            $this->redirect('home');
            return;
        }

        $saleModel = new Sale($this->db);
        $sales = $saleModel->getSalesByClientCi($client['ci']);

        $this->render('store/purchases', [
            'title' => 'Mis Compras',
            'sales' => $sales
        ]);
    }
}