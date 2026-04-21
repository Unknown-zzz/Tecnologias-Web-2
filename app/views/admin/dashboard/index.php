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

$dashboardOverview = [
    'productos' => count($products ?? []),
    'marcas' => count($brands ?? []),
    'industrias' => count($industries ?? []),
    'categorias' => count($categories ?? []),
];

$financeSummary = [
    'ventas' => (int)($totalVentas ?? 0),
    'ingresos' => (float)($totalIngresos ?? 0),
];
?>

<div class="container-fluid admin-dashboard">
    <!-- Header con bienvenida -->
    <div class="section-header mt-3">
        <div>
            <h1 class="mb-1"><i class="bi bi-speedometer2 mr-2"></i>Panel de Administración</h1>
            <p class="text-muted mb-0">Bienvenido, <strong><?= e((string)($admin ?? 'Administrador')) ?></strong></p>
        </div>
        <a href="index.php?r=admin/logout" class="btn btn-danger"><i class="bi bi-box-arrow-right mr-1"></i>Cerrar Sesión</a>
    </div>

    <!-- KPI Cards - Resumen Rápido -->
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm dashboard-kpi-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Total Productos</p>
                            <h3 class="mb-0 text-primary"><?= number_format($dashboardOverview['productos'] ?? 0) ?></h3>
                        </div>
                        <i class="bi bi-box-seam fs-3 text-primary opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm dashboard-kpi-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Total Marcas</p>
                            <h3 class="mb-0 text-success"><?= number_format($dashboardOverview['marcas'] ?? 0) ?></h3>
                        </div>
                        <i class="bi bi-tags fs-3 text-success opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm dashboard-kpi-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Total Ventas</p>
                            <h3 class="mb-0 text-warning"><?= number_format($financeSummary['ventas'] ?? 0) ?></h3>
                        </div>
                        <i class="bi bi-receipt fs-3 text-warning opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm dashboard-kpi-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Ingresos Totales</p>
                            <h3 class="mb-0 text-info">Bs. <?= number_format((float)($financeSummary['ingresos'] ?? 0), 2) ?></h3>
                        </div>
                        <i class="bi bi-cash-stack fs-3 text-info opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones Rápidas -->
    <div class="quick-actions mb-4 d-flex gap-2 flex-wrap">
        <a href="index.php?r=admin/products" class="btn btn-outline-primary btn-sm"><i class="bi bi-box-seam mr-1"></i>Productos</a>
        <a href="index.php?r=admin/brands" class="btn btn-outline-primary btn-sm"><i class="bi bi-tags mr-1"></i>Marcas</a>
        <a href="index.php?r=admin/industries" class="btn btn-outline-primary btn-sm"><i class="bi bi-building mr-1"></i>Industrias</a>
        <a href="index.php?r=admin/categories" class="btn btn-outline-primary btn-sm"><i class="bi bi-folder mr-1"></i>Categorías</a>
        <a href="index.php?r=admin/sales" class="btn btn-outline-primary btn-sm"><i class="bi bi-receipt mr-1"></i>Ventas</a>
    </div>

    <!-- Gráficos principales -->
    <div class="row mt-4">
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header summary-card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-graph-up-arrow mr-2"></i>Ingresos y Ventas (Últimos Meses)</h5>
                    <small class="text-light">Bs. y cantidad</small>
                </div>
                <div class="card-body">
                    <div class="chart-box chart-box-lg">
                        <canvas id="salesRevenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header summary-card-header">
                    <h5 class="mb-0"><i class="bi bi-pie-chart mr-2"></i>Productos por Categoría</h5>
                </div>
                <div class="card-body d-flex align-items-center">
                    <div class="chart-box chart-box-md w-100">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos secundarios -->
    <div class="row mt-4">
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header summary-card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-bar-chart-line mr-2"></i>Top Stock por Producto</h5>
                    <small class="text-light">Stock total actual: <?= number_format($totalStock) ?></small>
                </div>
                <div class="card-body">
                    <div class="chart-box chart-box-md">
                        <canvas id="stockChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header summary-card-header">
                    <h5 class="mb-0"><i class="bi bi-bar-chart mr-2"></i>Resumen General</h5>
                </div>
                <div class="card-body">
                    <div class="chart-box chart-box-md">
                        <canvas id="overviewChart"></canvas>
                    </div>
                </div>
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
    const dashboardOverview = <?= json_encode($dashboardOverview, JSON_UNESCAPED_UNICODE) ?>;
    const financeSummary = <?= json_encode($financeSummary, JSON_UNESCAPED_UNICODE) ?>;

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

    const overviewLabels = ['Productos', 'Marcas', 'Industrias', 'Categorias'];
    const overviewValues = [
        Number(dashboardOverview.productos || 0),
        Number(dashboardOverview.marcas || 0),
        Number(dashboardOverview.industrias || 0),
        Number(dashboardOverview.categorias || 0)
    ];

    if (overviewValues.some(value => value > 0)) {
        const overviewCtx = document.getElementById('overviewChart');
        new Chart(overviewCtx, {
            type: 'bar',
            data: {
                labels: overviewLabels,
                datasets: [{
                    label: 'Cantidad',
                    data: overviewValues,
                    backgroundColor: ['#3b82f6', '#22c55e', '#f59e0b', '#ef4444'],
                    borderRadius: 8,
                    maxBarThickness: 54
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
        safeMessage('overviewChart', 'No hay datos del sistema para mostrar en el resumen.');
    }

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
