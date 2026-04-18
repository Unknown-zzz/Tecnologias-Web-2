<?php require __DIR__ . '/../layouts/header.php'; ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="section-header mt-3">
        <h2><i class="bi bi-receipt mr-2"></i>Detalle de Venta #<?= (int) $sale['nro'] ?></h2>
        <div class="btn-group">
            <a href="index.php?r=admin/sales" class="btn btn-secondary"><i class="bi bi-arrow-left mr-1"></i>Volver</a>
            <a href="index.php?r=admin/dashboard" class="btn btn-primary"><i class="bi bi-speedometer2 mr-1"></i>Panel</a>
        </div>
    </div>

    <!-- Información de la venta -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-info-circle mr-2"></i>Información de la Venta</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Número de Venta:</strong> #<?= (int) $sale['nro'] ?></p>
                            <p><strong>Fecha:</strong> <?= date('d/m/Y H:i:s', strtotime($sale['fecha'])) ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Cliente:</strong> <?= e($sale['nombres'] . ' ' . $sale['apPaterno'] . ' ' . $sale['apMaterno']) ?></p>
                            <p><strong>CI:</strong> <?= e($sale['ciCliente']) ?></p>
                            <p><strong>Correo:</strong> <?= e($sale['correo'] ?? 'N/A') ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h4 class="mb-0">Total</h4>
                    <h2 class="mb-0">Bs. <?= number_format($sale['total'], 2) ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Detalles de productos -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-box-seam mr-2"></i>Productos Vendidos</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 10%;">Código</th>
                            <th style="width: 40%;">Producto</th>
                            <th style="width: 15%;">Cantidad</th>
                            <th style="width: 15%;">Precio Unit.</th>
                            <th style="width: 20%;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($sale['detalles'])): ?>
                            <?php foreach ($sale['detalles'] as $detalle): ?>
                                <tr>
                                    <td><strong class="text-secondary">#<?= (int) $detalle['codProducto'] ?></strong></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if (!empty($detalle['imagen'])): ?>
                                                <img src="resources/imagenes/<?= e($detalle['imagen']) ?>" 
                                                     alt="<?= e($detalle['producto']) ?>" 
                                                     class="product-thumb mr-3">
                                            <?php endif; ?>
                                            <strong><?= e($detalle['producto']) ?></strong>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary badge-lg"><?= (int) $detalle['cant'] ?></span>
                                    </td>
                                    <td>
                                        <strong>Bs. <?= number_format($detalle['precioUnitario'], 2) ?></strong>
                                    </td>
                                    <td>
                                        <strong class="text-success">Bs. <?= number_format($detalle['cant'] * $detalle['precioUnitario'], 2) ?></strong>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <div class="empty-state">
                                        <i class="bi bi-exclamation-triangle display-4 text-muted mb-3"></i>
                                        <h5 class="text-muted">No hay detalles disponibles</h5>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <?php if (!empty($sale['detalles'])): ?>
                        <tfoot>
                            <tr class="table-active">
                                <td colspan="4" class="text-right"><strong>TOTAL:</strong></td>
                                <td><strong class="text-success h5">Bs. <?= number_format($sale['total'], 2) ?></strong></td>
                            </tr>
                        </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>
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

.card {
    border: none;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 1.5rem;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    padding: 1rem 1.5rem;
}

.card-body {
    padding: 1.5rem;
}

.product-thumb {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 4px;
    border: 1px solid #dee2e6;
}

.badge-lg {
    font-size: 0.9rem;
    padding: 0.5rem 0.75rem;
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

.table tfoot td {
    border-top: 2px solid #dee2e6;
    font-weight: bold;
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
    
    .card-body .row > div {
        margin-bottom: 1rem;
    }
}
</style>

<?php require __DIR__ . '/../layouts/footer.php'; ?>