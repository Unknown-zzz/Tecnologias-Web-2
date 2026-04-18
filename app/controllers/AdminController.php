<?php
declare(strict_types=1);

final class AdminController extends Controller
{
    public function login(): void
    {
        if (isAdmin()) {
            $this->redirect('admin/dashboard');
        }

        $this->render('admin/auth/login', [
            'title' => 'Admin - Iniciar Sesión',
            'error' => $_SESSION['flash_error'] ?? null
        ]);
        unset($_SESSION['flash_error']);
    }

    public function authenticate(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/login');
        }

        $usuario  = trim($_POST['usuario'] ?? '');
        $password = $_POST['password'] ?? '';

        $accountModel = new Account($this->db);
        $user = $accountModel->findByUsuario($usuario);

        if ($user && $user['rol'] === 'admin' && password_verify($password, $user['password'])) {
            $_SESSION['usuario'] = $user['usuario'];
            $_SESSION['rol']     = 'admin';
            $this->redirect('admin/dashboard');
        }

        $_SESSION['flash_error'] = 'Credenciales de administrador incorrectas.';
        $this->redirect('admin/login');
    }

    public function dashboard(): void
    {
        requireAdmin();

        $productModel = new Product($this->db);
        $brandModel = new Brand($this->db);
        $industryModel = new Industry($this->db);
        $categoryModel = new Category($this->db);
        $saleModel = new Sale($this->db);

        $products = $productModel->allForAdmin();   
        $brands = $brandModel->all();
        $industries = $industryModel->all();
        $categories = $categoryModel->all();
        $totalVentas = $saleModel->getTotalVentas();
        $totalIngresos = $saleModel->getTotalIngresos();

        $this->render('admin/dashboard/index', [
            'title'         => 'Panel de Administración',
            'admin'         => $_SESSION['usuario'] ?? 'Administrador',
            'products'      => $products ?? [],
            'brands'        => $brands ?? [],
            'industries'    => $industries ?? [],
            'categories'    => $categories ?? [],
            'totalVentas'   => $totalVentas,
            'totalIngresos' => $totalIngresos
        ]);
    }

    public function products(): void
    {
        requireAdmin();

        $productModel = new Product($this->db);
        $brandModel = new Brand($this->db);
        $industryModel = new Industry($this->db);
        $categoryModel = new Category($this->db);

        $products = $productModel->allForAdmin();
        $brands = $brandModel->all();
        $industries = $industryModel->all();
        $categories = $categoryModel->all();

        $this->render('admin/products/index', [
            'title'       => 'Gestión de Productos',
            'products'    => $products ?? [],
            'brands'      => $brands ?? [],
            'industries'  => $industries ?? [],
            'categories'  => $categories ?? [],
            'message'     => $_SESSION['flash_message'] ?? null
        ]);
        unset($_SESSION['flash_message']);
    }

    public function create(): void
    {
        requireAdmin();

        $brandModel = new Brand($this->db);
        $industryModel = new Industry($this->db);
        $categoryModel = new Category($this->db);

        $this->render('admin/products/create', [
            'title'       => 'Agregar Producto',
            'brands'      => $brandModel->all(),
            'industries'  => $industryModel->all(),
            'categories'  => $categoryModel->all()
        ]);
    }

    public function store(): void
    {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/products');
        }

        $productModel = new Product($this->db);
        $uploadedImage = $this->handleProductImageUpload($_FILES['imagen'] ?? null);
        
        $data = [
            'nombre' => trim($_POST['nombre'] ?? ''),
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'precio' => (float)($_POST['precio'] ?? 0),
            'estado' => 'activo',
            'codMarca' => (int)($_POST['codMarca'] ?? 1),
            'codIndustria' => (int)($_POST['codIndustria'] ?? 1),
            'codCategoria' => (int)($_POST['codCategoria'] ?? 1),
            'stock' => (int)($_POST['stock'] ?? 0),
            'imagen' => $uploadedImage ?? ''
        ];

        if ($productModel->create($data)) {
            $_SESSION['flash_message'] = 'Producto creado exitosamente.';
            $this->redirect('admin/products');
        } else {
            $_SESSION['flash_error'] = 'Error al crear el producto.';
            $this->redirect('admin/products/create');
        }
    }

    public function edit(): void
    {
        requireAdmin();

        $cod = (int)($_GET['id'] ?? 0);
        if ($cod <= 0) {
            $this->redirect('admin/products');
        }

        $productModel = new Product($this->db);
        $product = $productModel->find($cod);

        if (!$product) {
            $_SESSION['flash_error'] = 'Producto no encontrado.';
            $this->redirect('admin/products');
        }

        $brandModel = new Brand($this->db);
        $industryModel = new Industry($this->db);
        $categoryModel = new Category($this->db);

        $this->render('admin/products/edit', [
            'title'       => 'Editar Producto',
            'product'     => $product,
            'brands'      => $brandModel->all(),
            'industries'  => $industryModel->all(),
            'categories'  => $categoryModel->all()
        ]);
    }

    public function update(): void
    {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/products');
        }

        $cod = (int)($_POST['cod'] ?? 0);
        if ($cod <= 0) {
            $this->redirect('admin/products');
        }

        $productModel = new Product($this->db);
        $product = $productModel->find($cod);

        if (!$product) {
            $_SESSION['flash_error'] = 'Producto no encontrado.';
            $this->redirect('admin/products');
        }

        $uploadedImage = $this->handleProductImageUpload($_FILES['imagen'] ?? null);
        $data = [
            'nombre' => trim($_POST['nombre'] ?? ''),
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'precio' => (float)($_POST['precio'] ?? 0),
            'estado' => 'activo',
            'codMarca' => (int)($_POST['codMarca'] ?? 1),
            'codIndustria' => (int)($_POST['codIndustria'] ?? 1),
            'codCategoria' => (int)($_POST['codCategoria'] ?? 1),
            'imagen_actual' => $product['imagen'],
            'imagen' => $uploadedImage ?? ($_POST['imagen_actual'] ?? $product['imagen'])
        ];

        if ($productModel->update($cod, $data)) {
            $newStock = (int)($_POST['stock'] ?? 0);
            $currentStock = (int)($product['stock'] ?? 0);
            if ($newStock != $currentStock) {
                $difference = $newStock - $currentStock;
                $productModel->addStock($cod, $difference);
            }

            $_SESSION['flash_message'] = 'Producto actualizado exitosamente.';
            $this->redirect('admin/products');
        } else {
            $_SESSION['flash_error'] = 'Error al actualizar el producto.';
            $this->redirect('admin/products/edit&id=' . $cod);
        }
    }

    private function handleProductImageUpload(?array $file): ?string
    {
        if (empty($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $allowedTypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
        ];

        $extension = $allowedTypes[$file['type']] ?? null;
        if ($extension === null) {
            return null;
        }

        $filename = uniqid('prod_', true) . '.' . $extension;
        $targetDir = __DIR__ . '/../../resources/imagenes';

        if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
            return null;
        }

        $targetPath = $targetDir . DIRECTORY_SEPARATOR . $filename;
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            return null;
        }

        return $filename;
    }

    public function delete(): void
    {
        requireAdmin();

        $cod = (int)($_GET['id'] ?? 0);
        if ($cod <= 0) {
            $this->redirect('admin/products');
        }

        $productModel = new Product($this->db);
        
        if ($productModel->delete($cod)) {
            $_SESSION['flash_message'] = 'Producto eliminado exitosamente.';
        } else {
            $_SESSION['flash_error'] = 'Error al eliminar el producto.';
        }

        $this->redirect('admin/products');
    }

    // ===== MARCAS =====
    public function brands(): void
    {
        requireAdmin();

        $brandModel = new Brand($this->db);
        $brands = $brandModel->all();

        $this->render('admin/brands/index', [
            'title'   => 'Gestión de Marcas',
            'brands'  => $brands ?? [],
            'message' => $_SESSION['flash_message'] ?? null
        ]);
        unset($_SESSION['flash_message']);
    }

    public function createBrand(): void
    {
        requireAdmin();

        $this->render('admin/brands/create', [
            'title' => 'Agregar Marca'
        ]);
    }

    public function storeBrand(): void
    {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/brands');
        }

        $nombre = trim($_POST['nombre'] ?? '');
        if (empty($nombre)) {
            $_SESSION['flash_error'] = 'El nombre de la marca es obligatorio.';
            $this->redirect('admin/brands/create');
        }

        $brandModel = new Brand($this->db);
        if ($brandModel->create($nombre)) {
            $_SESSION['flash_message'] = 'Marca creada exitosamente.';
            $this->redirect('admin/brands');
        } else {
            $_SESSION['flash_error'] = 'Error al crear la marca.';
            $this->redirect('admin/brands/create');
        }
    }

    public function editBrand(): void
    {
        requireAdmin();

        $cod = (int)($_GET['id'] ?? 0);
        if ($cod <= 0) {
            $this->redirect('admin/brands');
        }

        $brandModel = new Brand($this->db);
        $brand = $brandModel->find($cod);

        if (!$brand) {
            $_SESSION['flash_error'] = 'Marca no encontrada.';
            $this->redirect('admin/brands');
        }

        $this->render('admin/brands/edit', [
            'title' => 'Editar Marca',
            'brand' => $brand
        ]);
    }

    public function updateBrand(): void
    {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/brands');
        }

        $cod = (int)($_POST['cod'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');

        if ($cod <= 0 || empty($nombre)) {
            $_SESSION['flash_error'] = 'Datos inválidos.';
            $this->redirect('admin/brands');
        }

        $brandModel = new Brand($this->db);
        if ($brandModel->update($cod, $nombre)) {
            $_SESSION['flash_message'] = 'Marca actualizada exitosamente.';
            $this->redirect('admin/brands');
        } else {
            $_SESSION['flash_error'] = 'Error al actualizar la marca.';
            $this->redirect('admin/brands/edit&id=' . $cod);
        }
    }

    public function deleteBrand(): void
    {
        requireAdmin();

        $cod = (int)($_GET['id'] ?? 0);
        if ($cod <= 0) {
            $this->redirect('admin/brands');
        }

        $brandModel = new Brand($this->db);
        if ($brandModel->delete($cod)) {
            $_SESSION['flash_message'] = 'Marca eliminada exitosamente.';
        } else {
            $_SESSION['flash_error'] = 'Error al eliminar la marca.';
        }

        $this->redirect('admin/brands');
    }

    // ===== INDUSTRIAS =====
    public function industries(): void
    {
        requireAdmin();

        $industryModel = new Industry($this->db);
        $industries = $industryModel->all();

        $this->render('admin/industries/index', [
            'title'       => 'Gestión de Industrias',
            'industries'  => $industries ?? [],
            'message'     => $_SESSION['flash_message'] ?? null
        ]);
        unset($_SESSION['flash_message']);
    }

    public function createIndustry(): void
    {
        requireAdmin();

        $this->render('admin/industries/create', [
            'title' => 'Agregar Industria'
        ]);
    }

    public function storeIndustry(): void
    {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/industries');
        }

        $nombre = trim($_POST['nombre'] ?? '');
        if (empty($nombre)) {
            $_SESSION['flash_error'] = 'El nombre de la industria es obligatorio.';
            $this->redirect('admin/industries/create');
        }

        $industryModel = new Industry($this->db);
        if ($industryModel->create($nombre)) {
            $_SESSION['flash_message'] = 'Industria creada exitosamente.';
            $this->redirect('admin/industries');
        } else {
            $_SESSION['flash_error'] = 'Error al crear la industria.';
            $this->redirect('admin/industries/create');
        }
    }

    public function editIndustry(): void
    {
        requireAdmin();

        $cod = (int)($_GET['id'] ?? 0);
        if ($cod <= 0) {
            $this->redirect('admin/industries');
        }

        $industryModel = new Industry($this->db);
        $industry = $industryModel->find($cod);

        if (!$industry) {
            $_SESSION['flash_error'] = 'Industria no encontrada.';
            $this->redirect('admin/industries');
        }

        $this->render('admin/industries/edit', [
            'title'    => 'Editar Industria',
            'industry' => $industry
        ]);
    }

    public function updateIndustry(): void
    {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/industries');
        }

        $cod = (int)($_POST['cod'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');

        if ($cod <= 0 || empty($nombre)) {
            $_SESSION['flash_error'] = 'Datos inválidos.';
            $this->redirect('admin/industries');
        }

        $industryModel = new Industry($this->db);
        if ($industryModel->update($cod, $nombre)) {
            $_SESSION['flash_message'] = 'Industria actualizada exitosamente.';
            $this->redirect('admin/industries');
        } else {
            $_SESSION['flash_error'] = 'Error al actualizar la industria.';
            $this->redirect('admin/industries/edit&id=' . $cod);
        }
    }

    public function deleteIndustry(): void
    {
        requireAdmin();

        $cod = (int)($_GET['id'] ?? 0);
        if ($cod <= 0) {
            $this->redirect('admin/industries');
        }

        $industryModel = new Industry($this->db);
        if ($industryModel->delete($cod)) {
            $_SESSION['flash_message'] = 'Industria eliminada exitosamente.';
        } else {
            $_SESSION['flash_error'] = 'Error al eliminar la industria.';
        }

        $this->redirect('admin/industries');
    }

    // ===== CATEGORÍAS =====
    public function categories(): void
    {
        requireAdmin();

        $categoryModel = new Category($this->db);
        $categories = $categoryModel->all();

        $this->render('admin/categories/index', [
            'title'      => 'Gestión de Categorías',
            'categories' => $categories ?? [],
            'message'    => $_SESSION['flash_message'] ?? null
        ]);
        unset($_SESSION['flash_message']);
    }

    public function createCategory(): void
    {
        requireAdmin();

        $this->render('admin/categories/create', [
            'title' => 'Agregar Categoría'
        ]);
    }

    public function storeCategory(): void
    {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/categories');
        }

        $nombre = trim($_POST['nombre'] ?? '');
        if (empty($nombre)) {
            $_SESSION['flash_error'] = 'El nombre de la categoría es obligatorio.';
            $this->redirect('admin/categories/create');
        }

        $categoryModel = new Category($this->db);
        if ($categoryModel->create($nombre)) {
            $_SESSION['flash_message'] = 'Categoría creada exitosamente.';
            $this->redirect('admin/categories');
        } else {
            $_SESSION['flash_error'] = 'Error al crear la categoría.';
            $this->redirect('admin/categories/create');
        }
    }

    public function editCategory(): void
    {
        requireAdmin();

        $cod = (int)($_GET['id'] ?? 0);
        if ($cod <= 0) {
            $this->redirect('admin/categories');
        }

        $categoryModel = new Category($this->db);
        $category = $categoryModel->find($cod);

        if (!$category) {
            $_SESSION['flash_error'] = 'Categoría no encontrada.';
            $this->redirect('admin/categories');
        }

        $this->render('admin/categories/edit', [
            'title'    => 'Editar Categoría',
            'category' => $category
        ]);
    }

    public function updateCategory(): void
    {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/categories');
        }

        $cod = (int)($_POST['cod'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');

        if ($cod <= 0 || empty($nombre)) {
            $_SESSION['flash_error'] = 'Datos inválidos.';
            $this->redirect('admin/categories');
        }

        $categoryModel = new Category($this->db);
        if ($categoryModel->update($cod, $nombre)) {
            $_SESSION['flash_message'] = 'Categoría actualizada exitosamente.';
            $this->redirect('admin/categories');
        } else {
            $_SESSION['flash_error'] = 'Error al actualizar la categoría.';
            $this->redirect('admin/categories/edit&id=' . $cod);
        }
    }

    public function deleteCategory(): void
    {
        requireAdmin();

        $cod = (int)($_GET['id'] ?? 0);
        if ($cod <= 0) {
            $this->redirect('admin/categories');
        }

        $categoryModel = new Category($this->db);
        if ($categoryModel->delete($cod)) {
            $_SESSION['flash_message'] = 'Categoría eliminada exitosamente.';
        } else {
            $_SESSION['flash_error'] = 'Error al eliminar la categoría.';
        }

        $this->redirect('admin/categories');
    }

    // ===== VENTAS =====
    public function sales(): void
    {
        requireAdmin();

        $saleModel = new Sale($this->db);
        $sales = $saleModel->all();
        $totalVentas = $saleModel->getTotalVentas();
        $totalIngresos = $saleModel->getTotalIngresos();

        $this->render('admin/sales/index', [
            'title'         => 'Gestión de Ventas',
            'sales'         => $sales ?? [],
            'totalVentas'   => $totalVentas,
            'totalIngresos' => $totalIngresos,
            'message'       => $_SESSION['flash_message'] ?? null,
            'error'         => $_SESSION['flash_error'] ?? null
        ]);
        unset($_SESSION['flash_message'], $_SESSION['flash_error']);
    }

    public function showSale(): void
    {
        requireAdmin();

        $nro = (int)($_GET['id'] ?? 0);
        if ($nro <= 0) {
            $this->redirect('admin/sales');
        }

        $saleModel = new Sale($this->db);
        $sale = $saleModel->find($nro);

        if (!$sale) {
            $_SESSION['flash_error'] = 'Venta no encontrada.';
            $this->redirect('admin/sales');
        }

        $this->render('admin/sales/show', [
            'title' => 'Detalle de Venta #' . $nro,
            'sale'  => $sale
        ]);
    }

    public function logout(): void
    {
        session_destroy();
        $this->redirect('admin/login');
    }
}