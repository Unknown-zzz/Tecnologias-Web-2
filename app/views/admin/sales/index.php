<?php require __DIR__ . '/../layouts/header.php'; ?>
<div class="container-fluid">
    <!-- Header con bienvenida y botón agregar -->
    <div class="section-header mt-3">
        <h2><i class="bi bi-receipt mr-2"></i>Gestión de Ventas</h2>
        <div class="btn-group">
            <a href="index.php?r=admin/dashboard" class="btn btn-secondary"><i class="bi bi-speedometer2 mr-1"></i>Panel</a>
        </div>
    </div>

    <!-- Estadísticas de ventas -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-graph-up"></i> Total de Ventas</h5>
                    <h3 class="mb-0"><?= number_format($totalVentas) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-cash"></i> Ingresos Totales</h5>
                    <h3 class="mb-0">Bs. <?= number_format($totalIngresos, 2) ?></h3>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= e($message) ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= e($error) ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- Tabla de ventas -->
    <div class="table-container">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th style="width: 8%;">Nro</th>
                    <th style="width: 15%;">Fecha</th>
                    <th style="width: 25%;">Cliente</th>
                    <th style="width: 15%;">Total</th>
                    <th style="width: 12%;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($sales)): ?>
                    <?php foreach ($sales as $sale): ?>
                        <tr>
                            <td><strong class="text-secondary">#<?= (int) $sale['nro'] ?></strong></td>
                            <td>
                                <small class="text-muted">
                                    <?= date('d/m/Y H:i', strtotime($sale['fecha'])) ?>
                                </small>
                            </td>
                            <td>
                                <strong><?= e($sale['nombres'] . ' ' . $sale['apPaterno'] . ' ' . $sale['apMaterno']) ?></strong>
                                <br>
                                <small class="text-muted">CI: <?= e($sale['ciCliente']) ?></small>
                            </td>
                            <td>
                                <strong class="text-success">Bs. <?= number_format($sale['total'], 2) ?></strong>
                            </td>
                            <td>
                                <a href="index.php?r=admin/sales/show&id=<?= (int) $sale['nro'] ?>" 
                                   class="btn btn-sm btn-info" 
                                   title="Ver Detalles">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <div class="empty-state">
                                <i class="bi bi-receipt-x display-4 text-muted mb-3"></i>
                                <h5 class="text-muted">No hay ventas registradas</h5>
                                <p class="text-muted">Las ventas aparecerán aquí cuando se realicen compras.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #e9ecef;
}

.section-header h2 {
    margin: 0;
    color: #495057;
}

.btn-group .btn {
    margin-left: 0.5rem;
}

.table-container {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow: hidden;
}

.table th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    color: #495057;
    padding: 1rem;
}

.table td {
    padding: 1rem;
    vertical-align: middle;
}

.card {
    border: none;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card-body {
    padding: 1.5rem;
}

.empty-state {
    padding: 3rem 0;
}

.empty-state i {
    opacity: 0.5;
}

@media (max-width: 768px) {
    .section-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .btn-group {
        width: 100%;
        display: flex;
        justify-content: flex-end;
    }
}
</style>

<?php require __DIR__ . '/../layouts/footer.php'; ?>