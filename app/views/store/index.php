<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container py-5">
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="display-4 mb-3"><i class="bi bi-bag-check me-3"></i>Nuestros Productos</h1>
            <p class="lead text-muted">Descubre nuestra selección de productos de calidad</p>
        </div>
    </div>

    <div class="row g-4">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $p): ?>
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 product-card">
                        <?php if (!empty($p['imagen'])): ?>
                            <img src="resources/imagenes/<?= e($p['imagen']) ?>"
                                 class="card-img-top product-image-trigger" alt="<?= e($p['nombre']) ?>"
                                 data-bs-toggle="modal" data-bs-target="#productModal<?= (int)$p['cod'] ?>">
                        <?php else: ?>
                            <div class="product-image-placeholder" data-bs-toggle="modal" data-bs-target="#productModal<?= (int)$p['cod'] ?>">
                                <i class="bi bi-image fs-1"></i>
                            </div>
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title mb-2 product-card-title" data-bs-toggle="modal" data-bs-target="#productModal<?= (int)$p['cod'] ?>"><?= e($p['nombre']) ?></h5>
                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="bi bi-tag me-1"></i><?= e($p['categoria']) ?> •
                                    <i class="bi bi-building me-1"></i><?= e($p['marca']) ?>
                                </small>
                            </div>
                            <p class="card-text flex-grow-1 small text-muted">
                                <?= strlen($p['descripcion']) > 100 ? substr(e($p['descripcion']), 0, 100) . '...' : e($p['descripcion']) ?>
                            </p>
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <div>
                                    <strong class="fs-5 text-primary">Bs. <?= number_format((float)$p['precio'], 2) ?></strong>
                                    <?php if (!empty($p['stock']) && $p['stock'] > 0): ?>
                                        <small class="text-success d-block">
                                            <i class="bi bi-check-circle me-1"></i>En stock (<?= $p['stock'] ?>)
                                        </small>
                                    <?php elseif (!empty($p['stock']) && $p['stock'] == 0): ?>
                                        <small class="text-danger d-block">
                                            <i class="bi bi-x-circle me-1"></i>Agotado
                                        </small>
                                    <?php endif; ?>
                                </div>
                                <div class="d-flex gap-2 w-100">
                                    <?php if (!empty($p['stock']) && $p['stock'] > 0): ?>
                                        <button class="btn btn-success btn-sm add-to-cart w-100" data-id="<?= (int)$p['cod'] ?>" data-name="<?= e($p['nombre']) ?>">
                                            <i class="bi bi-cart-plus me-1"></i>Agregar
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-secondary btn-sm w-100" disabled>
                                            <i class="bi bi-cart-x me-1"></i>Agotado
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-box-seam fs-1 text-muted mb-3"></i>
                    <h4 class="text-muted">No hay productos disponibles</h4>
                    <p class="text-muted">Estamos trabajando para agregar más productos pronto.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Product Modals -->
<?php if (!empty($products)): ?>
    <?php foreach ($products as $p): ?>
        <div class="modal fade" id="productModal<?= (int)$p['cod'] ?>" tabindex="-1" aria-labelledby="productModalLabel<?= (int)$p['cod'] ?>" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="productModalLabel<?= (int)$p['cod'] ?>">
                            <i class="bi bi-box-seam me-2"></i><?= e($p['nombre']) ?>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <?php if (!empty($p['imagen'])): ?>
                                    <img src="resources/imagenes/<?= e($p['imagen']) ?>"
                                         class="img-fluid rounded product-image-modal" alt="<?= e($p['nombre']) ?>">
                                <?php else: ?>
                                    <div class="product-image-placeholder rounded product-image-modal modal-image-placeholder">
                                        <i class="bi bi-image fs-1"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <h4 class="text-primary mb-3">Bs. <?= number_format((float)$p['precio'], 2) ?></h4>

                                    <div class="mb-3">
                                        <small class="text-muted d-block">
                                            <i class="bi bi-tag me-1"></i><strong>Categoría:</strong> <?= e($p['categoria']) ?>
                                        </small>
                                        <small class="text-muted d-block">
                                            <i class="bi bi-building me-1"></i><strong>Marca:</strong> <?= e($p['marca']) ?>
                                        </small>
                                        <small class="text-muted d-block">
                                            <i class="bi bi-building-gear me-1"></i><strong>Industria:</strong> <?= e($p['industria']) ?>
                                        </small>
                                    </div>

                                    <div class="mb-3">
                                        <?php if (!empty($p['stock']) && $p['stock'] > 0): ?>
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i>En stock (<?= $p['stock'] ?> disponibles)
                                            </span>
                                        <?php elseif (!empty($p['stock']) && $p['stock'] == 0): ?>
                                            <span class="badge bg-danger">
                                                <i class="bi bi-x-circle me-1"></i>Agotado
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">
                                                <i class="bi bi-question-circle me-1"></i>Stock no disponible
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="mb-4">
                                        <h6 class="fw-bold">Descripción</h6>
                                        <p class="text-muted mb-0"><?= nl2br(e($p['descripcion'])) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i>Cerrar
                        </button>
                        <?php if (!empty($p['stock']) && $p['stock'] > 0): ?>
                            <a href="index.php?r=cart/add&id=<?= (int)$p['cod'] ?>" class="btn btn-success">
                                <i class="bi bi-cart-plus me-1"></i>Agregar al Carrito
                            </a>
                        <?php else: ?>
                            <button class="btn btn-secondary" disabled>
                                <i class="bi bi-cart-x me-1"></i>Producto Agotado
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php require __DIR__ . '/../layouts/footer.php'; ?>