<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card cart-card">
                <div class="card-body p-5">
                    <div class="d-flex align-items-center mb-4">
                        <div class="me-4 success-icon-box">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <div>
                            <h1 class="h2 mb-1">¡Compra Exitosa!</h1>
                            <p class="mb-0 text-muted">Tu pedido fue procesado y la factura está lista para descargar.</p>
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="border rounded p-4 h-100">
                                <h5 class="mb-3">Datos del cliente</h5>
                                <p class="mb-1"><strong>Nombre:</strong> <?= e(trim($sale['nombres'] . ' ' . $sale['apPaterno'] . ' ' . $sale['apMaterno'])) ?></p>
                                <p class="mb-1"><strong>CI:</strong> <?= e($sale['ciCliente']) ?></p>
                                <p class="mb-1"><strong>Email:</strong> <?= e($sale['correo']) ?></p>
                                <?php if (!empty($sale['direccion'])): ?>
                                    <p class="mb-1"><strong>Dirección:</strong> <?= e($sale['direccion']) ?></p>
                                <?php endif; ?>
                                <?php if (!empty($sale['nroCelular'])): ?>
                                    <p class="mb-1"><strong>Teléfono:</strong> <?= e($sale['nroCelular']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-4 h-100">
                                <h5 class="mb-3">Resumen de compra</h5>
                                <p class="mb-1"><strong>Venta #:</strong> <?= e($sale['nro']) ?></p>
                                <p class="mb-1"><strong>Fecha:</strong> <?= e($sale['fecha']) ?></p>
                                <p class="mb-0"><strong>Total:</strong> Bs. <?= number_format($sale['total'], 2) ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive mb-4">
                        <table class="table table-striped">
                            <thead>
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
                                        <td class="text-end">Bs. <?= number_format($detalle['cant'] * $detalle['precioUnitario'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
                        <?php if (!empty($sale['rutaInforme'])): ?>
                            <a href="<?= e($sale['rutaInforme']) ?>" class="btn btn-primary btn-lg" download>
                                <i class="bi bi-file-earmark-pdf-fill me-2"></i>Descargar factura PDF
                            </a>
                        <?php else: ?>
                            <div class="alert alert-warning mb-0">
                                La factura sigue procesándose. Si no se genera el PDF automáticamente, contacta con soporte.
                            </div>
                        <?php endif; ?>

                        <div class="btn-group">
                            <a href="index.php?r=store" class="btn btn-outline-primary">
                                <i class="bi bi-shop me-2"></i>Seguir Comprando
                            </a>
                            <a href="index.php?r=home" class="btn btn-secondary">
                                <i class="bi bi-house me-2"></i>Volver al Inicio
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
