<?php 
require __DIR__ . '/../layouts/header.php'; 
?>

<div class="container-fluid">
    <!-- Header con bienvenida -->
    <div class="section-header mt-3">
        <div>
            <h1 class="mb-1"><i class="bi bi-speedometer2 mr-2"></i>Panel de Administración</h1>
            <p class="text-muted mb-0">Bienvenido, <strong><?= e((string)($admin ?? 'Administrador')) ?></strong></p>
        </div>
        <a href="index.php?r=admin/logout" class="btn btn-danger"><i class="bi bi-box-arrow-right mr-1"></i>Cerrar Sesión</a>
    </div>

    <!-- Dashboard Cards -->
    <div class="row mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="dashboard-card">
                <div class="card-body">
                    <div class="dashboard-icon"><i class="bi bi-box-seam"></i></div>
                    <h5 class="card-title">Productos</h5>
                    <p class="card-text text-muted">Gestiona tu catálogo completo.</p>
                    <a href="index.php?r=admin/products" class="btn btn-primary btn-sm">Ver Productos</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="dashboard-card">
                <div class="card-body">
                    <div class="dashboard-icon"><i class="bi bi-tags"></i></div>
                    <h5 class="card-title">Marcas</h5>
                    <p class="card-text text-muted">Administra marcas disponibles.</p>
                    <a href="index.php?r=admin/brands" class="btn btn-primary btn-sm">Ver Marcas</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="dashboard-card">
                <div class="card-body">
                    <div class="dashboard-icon"><i class="bi bi-building"></i></div>
                    <h5 class="card-title">Industrias</h5>
                    <p class="card-text text-muted">Gestiona industrias.</p>
                    <a href="index.php?r=admin/industries" class="btn btn-primary btn-sm">Ver Industrias</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="dashboard-card">
                <div class="card-body">
                    <div class="dashboard-icon"><i class="bi bi-folder"></i></div>
                    <h5 class="card-title">Categorías</h5>
                    <p class="card-text text-muted">Organiza categorías.</p>
                    <a href="index.php?r=admin/categories" class="btn btn-primary btn-sm">Ver Categorías</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen Rápido -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header summary-card-header">
                    <h5 class="mb-0">Resumen Estadístico del Sistema</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="summary-item">
                                <span class="summary-icon"><i class="bi bi-box-seam"></i></span>
                                <div>
                                    <h4><?= count($products) ?></h4>
                                    <p>Productos activos</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="summary-item">
                                <span class="summary-icon"><i class="bi bi-tags"></i></span>
                                <div>
                                    <h4><?= count($brands ?? []) ?></h4>
                                    <p>Marcas registradas</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="summary-item">
                                <span class="summary-icon"><i class="bi bi-building"></i></span>
                                <div>
                                    <h4><?= count($industries ?? []) ?></h4>
                                    <p>Industrias configuradas</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="summary-item">
                                <span class="summary-icon"><i class="bi bi-folder"></i></span>
                                <div>
                                    <h4><?= count($categories ?? []) ?></h4>
                                    <p>Categorías creadas</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen de Ingresos -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header summary-card-header">
                    <h5 class="mb-0"><i class="bi bi-cash-stack mr-2"></i>Resumen de Ingresos</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="summary-item">
                                <span class="summary-icon"><i class="bi bi-receipt"></i></span>
                                <div>
                                    <h4><?= number_format($totalVentas) ?></h4>
                                    <p>Total de Ventas</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="summary-item">
                                <span class="summary-icon"><i class="bi bi-currency-dollar"></i></span>
                                <div>
                                    <h4>Bs. <?= number_format($totalIngresos, 2) ?></h4>
                                    <p>Ingresos Totales</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <a href="index.php?r=admin/sales" class="btn btn-secondary">
                            <i class="bi bi-eye mr-1"></i>Ver Todas las Ventas
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Información adicional -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="alert alert-info border-left-info text-center">
                <small><strong>Consejo:</strong> Utiliza los botones "Agregar" en cada sección para crear nuevos elementos rápidamente desde ventanas emergentes.</small>
            </div>
        </div>
    </div>
</div>

<?php 
require __DIR__ . '/../layouts/footer.php'; 
?>
