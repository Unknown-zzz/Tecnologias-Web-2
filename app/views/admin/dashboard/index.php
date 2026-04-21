<?php 
require __DIR__ . '/../layouts/header.php'; 

$chartData = $dashboardCharts ?? [
    'salesByMonth' => [],
    'productsByCategory' => [],
    'stockByProduct' => [],
    'totalStock' => 0,
];

$salesByMonth = $chartData['salesByMonth'] ?? [];
$productsByCategory = $chartData['productsByCategory'] ?? [];
$stockByProduct = $chartData['stockByProduct'] ?? [];
$totalStock = (int)($chartData['totalStock'] ?? 0);
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

    <!-- Graficos Chart.js -->
    <div class="row mt-4">
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header summary-card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-graph-up-arrow mr-2"></i>Ingresos y Ventas (Ultimos meses)</h5>
                    <small class="text-muted">Bs. y cantidad</small>
                </div>
                <div class="card-body">
                    <canvas id="salesRevenueChart" height="120"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header summary-card-header">
                    <h5 class="mb-0"><i class="bi bi-pie-chart mr-2"></i>Productos por Categoría</h5>
                </div>
                <div class="card-body d-flex align-items-center">
                    <canvas id="categoryChart" height="220"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-1">
        <div class="col-12 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header summary-card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-bar-chart-line mr-2"></i>Top Stock por Producto</h5>
                    <small class="text-muted">Stock total actual: <?= number_format($totalStock) ?></small>
                </div>
                <div class="card-body">
                    <canvas id="stockChart" height="95"></canvas>
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

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
(() => {
    const salesByMonth = <?= json_encode($salesByMonth, JSON_UNESCAPED_UNICODE) ?>;
    const productsByCategory = <?= json_encode($productsByCategory, JSON_UNESCAPED_UNICODE) ?>;
    const stockByProduct = <?= json_encode($stockByProduct, JSON_UNESCAPED_UNICODE) ?>;

    const currencyFormatter = (value) => {
        const n = Number(value || 0);
        return 'Bs. ' + n.toLocaleString('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    };

    const safeMessage = (canvasId, message) => {
        const el = document.getElementById(canvasId);
        if (!el) return;
        const parent = el.parentElement;
        if (!parent) return;
        parent.innerHTML = '<div class="text-center text-muted py-5">' + message + '</div>';
    };

    if (salesByMonth.length > 0) {
        const salesCtx = document.getElementById('salesRevenueChart');
        new Chart(salesCtx, {
            type: 'bar',
            data: {
                labels: salesByMonth.map(item => item.periodo),
                datasets: [
                    {
                        type: 'line',
                        label: 'Ventas',
                        yAxisID: 'y1',
                        data: salesByMonth.map(item => item.ventas),
                        borderColor: '#1f77b4',
                        backgroundColor: '#1f77b4',
                        tension: 0.35,
                        pointRadius: 4,
                        pointHoverRadius: 5
                    },
                    {
                        label: 'Ingresos (Bs.)',
                        yAxisID: 'y',
                        data: salesByMonth.map(item => item.ingresos),
                        backgroundColor: 'rgba(40, 167, 69, 0.65)',
                        borderColor: '#28a745',
                        borderWidth: 1,
                        borderRadius: 8,
                        maxBarThickness: 40
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'top' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                if (context.dataset.yAxisID === 'y') {
                                    return context.dataset.label + ': ' + currencyFormatter(context.raw);
                                }
                                return context.dataset.label + ': ' + Number(context.raw || 0).toLocaleString('es-BO');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        position: 'left',
                        ticks: {
                            callback: (value) => 'Bs. ' + Number(value).toLocaleString('es-BO')
                        },
                        title: {
                            display: true,
                            text: 'Ingresos'
                        }
                    },
                    y1: {
                        beginAtZero: true,
                        position: 'right',
                        grid: { drawOnChartArea: false },
                        ticks: {
                            precision: 0
                        },
                        title: {
                            display: true,
                            text: 'Ventas'
                        }
                    }
                }
            }
        });
    } else {
        safeMessage('salesRevenueChart', 'Aun no hay ventas para graficar ingresos.');
    }

    const categoryLabels = Object.keys(productsByCategory || {});
    if (categoryLabels.length > 0) {
        const categoryCtx = document.getElementById('categoryChart');
        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: categoryLabels,
                datasets: [{
                    label: 'Productos',
                    data: categoryLabels.map(label => productsByCategory[label]),
                    backgroundColor: ['#007bff', '#20c997', '#ffc107', '#fd7e14', '#6f42c1', '#e83e8c', '#17a2b8', '#6610f2'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    } else {
        safeMessage('categoryChart', 'No hay categorias con productos para mostrar.');
    }

    if (stockByProduct.length > 0) {
        const stockCtx = document.getElementById('stockChart');
        new Chart(stockCtx, {
            type: 'bar',
            data: {
                labels: stockByProduct.map(item => item.nombre),
                datasets: [{
                    label: 'Stock',
                    data: stockByProduct.map(item => item.stock),
                    backgroundColor: 'rgba(23, 162, 184, 0.7)',
                    borderColor: '#17a2b8',
                    borderWidth: 1,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                }
            }
        });
    } else {
        safeMessage('stockChart', 'No hay productos para mostrar stock.');
    }
})();
</script>

<?php 
require __DIR__ . '/../layouts/footer.php'; 
?>
