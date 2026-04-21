<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Administracion - Tienda Amiga') ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="resources/admin-styles.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container-fluid">
        <div class="d-flex align-items-center">
            <a class="navbar-brand me-3" href="index.php?r=admin/dashboard">
                <img src="resources/logo/Logo.webp" alt="Tienda Amiga" class="admin-navbar-logo me-2">Tienda Amiga Admin
            </a>
            <span class="text-white-50 small d-none d-lg-inline">Gestión centralizada de productos, ventas y catálogo</span>
        </div>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#adminNav" aria-controls="adminNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="adminNav">
            <ul class="navbar-nav mx-auto">
                <?php if (isAdmin()): ?>
                    <li class="nav-item"><a class="nav-link" href="index.php?r=admin/dashboard"><i class="bi bi-grid-fill me-1"></i>Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php?r=admin/products"><i class="bi bi-box-seam me-1"></i>Productos</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php?r=admin/brands"><i class="bi bi-tags-fill me-1"></i>Marcas</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php?r=admin/categories"><i class="bi bi-card-list me-1"></i>Categorías</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php?r=admin/industries"><i class="bi bi-buildings me-1"></i>Industrias</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php?r=admin/sales"><i class="bi bi-receipt me-1"></i>Ventas</a></li>
                <?php endif; ?>
            </ul>

            <ul class="navbar-nav ms-auto align-items-center">
                <?php if (isAdmin()): ?>
                    <li class="nav-item me-2">
                        <a class="btn btn-outline-light btn-sm" href="index.php?r=home">
                            <i class="bi bi-browser-safari me-1"></i>Ver Tienda
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="index.php?r=admin/logout">
                            <i class="bi bi-box-arrow-right me-1"></i>Cerrar Sesión
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="index.php?r=login"><i class="bi bi-box-arrow-in-right me-1"></i>Iniciar Sesión</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php?r=home"><i class="bi bi-browser-safari me-1"></i>Ver Tienda</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

