<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12 text-center section-title">
            <h1 class="display-4"><i class="bi bi-receipt-cutoff me-3"></i>Mis Compras</h1>
            <p class="lead text-muted">Consulta tus pedidos realizados y descarga tus facturas de compra.</p>
        </div>
    </div>

    <?php if (!empty($sales)): ?>
        <div class="accordion" id="purchaseAccordion">
            <?php foreach ($sales as $index => $sale): ?>
                <div class="accordion-item mb-3">
                    <h2 class="accordion-header" id="heading<?= e($sale['nro']) ?>">
                        <button class="accordion-button <?= $index !== 0 ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= e($sale['nro']) ?>" aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>" aria-controls="collapse<?= e($sale['nro']) ?>">
                            <div class="d-flex justify-content-between align-items-center w-100">
                                <div>
                                    <strong>Pedido #<?= e($sale['nro']) ?></strong>
                                    <span class="text-muted ms-3"><?= e($sale['fecha']) ?></span>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-primary"><?= count($sale['detalles']) ?> artículos</span>
                                    <div class="text-muted">Bs. <?= number_format($sale['total'], 2) ?></div>
                                </div>
                            </div>
                        </button>
                    </h2>
                    <div id="collapse<?= e($sale['nro']) ?>" class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>" aria-labelledby="heading<?= e($sale['nro']) ?>" data-bs-parent="#purchaseAccordion">
                        <div class="accordion-body p-4">
                            <div class="row mb-4">
                                <div class="col-md-8">
                                    <p class="mb-2"><strong>Fecha:</strong> <?= e($sale['fecha']) ?></p>
                                    <p class="mb-0"><strong>Total:</strong> Bs. <?= number_format($sale['total'], 2) ?></p>
                                </div>
                                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                    <?php if (!empty($sale['rutaInforme'])): ?>
                                        <a href="<?= e($sale['rutaInforme']) ?>" class="btn btn-sm btn-primary" download>
                                            <i class="bi bi-file-earmark-pdf-fill me-1"></i>Descargar factura
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Producto</th>
                                            <th class="text-center">Cantidad</th>
                                            <th class="text-end">Precio unitario</th>
                                            <th class="text-end">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($sale['detalles'] as $detalle): ?>
                                            <tr>
                                                <td><?= e($detalle['producto']) ?></td>
                                                <td class="text-center"><?= (int)$detalle['cant'] ?></td>
                                                <td class="text-end">Bs. <?= number_format($detalle['precioUnitario'], 2) ?></td>
                                                <td class="text-end">Bs. <?= number_format($detalle['subtotal'], 2) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card text-center py-5">
                    <div class="card-body">
                        <i class="bi bi-basket fs-1 text-muted mb-3"></i>
                        <h4 class="text-muted">Aún no has realizado compras</h4>
                        <p class="text-muted mb-4">Visita la tienda y agrega productos al carrito para ver tus compras aquí.</p>
                        <a href="index.php?r=store" class="btn btn-primary btn-lg">
                            <i class="bi bi-shop me-2"></i>Ir a la Tienda
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>