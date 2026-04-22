<?php if (!empty($items)): ?>
                    <div class="row">
                        <div class="col-lg-8 mb-4">
                            <div class="card">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0"><i class="bi bi-list-check me-2"></i>Productos en tu carrito</h5>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-custom mb-0">
                                            <thead>
                                                <tr>
                                                    <th class="border-0 ps-4 py-3 table-header-cell">Producto</th>
                                                    <th class="border-0 text-center py-3 table-header-cell">Cantidad</th>
                                                    <th class="border-0 text-center py-3 table-header-cell">Precio Unitario</th>
                                                    <th class="border-0 text-center py-3 table-header-cell">Subtotal</th>
                                                    <th class="border-0 text-center pe-4 py-3 table-header-cell">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-light">
                                                <?php foreach ($items as $row): ?>
                                                    <?php $stock = (int)($row['product']['stock'] ?? 0); ?>
                                                    <tr class="table-row-hover">
                                                        <td class="ps-4 py-3">
                                                            <div class="d-flex align-items-center">
                                                                <?php if (!empty($row['product']['imagen'])): ?>
                                                                    <img src="resources/imagenes/<?= e($row['product']['imagen']) ?>"
                                                                         class="cart-product-thumb shadow-sm me-3" alt="<?= e($row['product']['nombre']) ?>">
                                                                <?php else: ?>
                                                                    <div class="cart-product-placeholder shadow-sm me-3">
                                                                        <i class="bi bi-image text-muted"></i>
                                                                    </div>
                                                                <?php endif; ?>
                                                                <div>
                                                                    <h6 class="mb-0 text-dark fw-semibold"><?= e($row['product']['nombre']) ?></h6>
                                                                    <small class="text-muted d-block small-text-muted">Código: <?= (int)$row['product']['cod'] ?></small>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="text-center py-3">
                                                            <div class="d-flex align-items-center justify-content-center gap-2">
                                                                <a href="index.php?r=cart/quantity&id=<?= (int)$row['product']['cod'] ?>&op=dec"
                                                                   class="cart-qty-btn shadow-sm"
                                                                   data-cart-action="quantity" data-op="dec" data-id="<?= (int)$row['product']['cod'] ?>">
                                                                    <i class="bi bi-dash"></i>
                                                                </a>
                                                                <span class="cart-qty-value shadow-sm"><?= (int)$row['cantidad'] ?></span>
                                                                <?php if ($stock > 0 && (int)$row['cantidad'] < $stock): ?>
                                                                    <a href="index.php?r=cart/quantity&id=<?= (int)$row['product']['cod'] ?>&op=inc"
                                                                       class="cart-qty-btn shadow-sm"
                                                                       data-cart-action="quantity" data-op="inc" data-id="<?= (int)$row['product']['cod'] ?>">
                                                                        <i class="bi bi-plus"></i>
                                                                    </a>
                                                                <?php else: ?>
                                                                    <span class="cart-qty-btn shadow-sm disabled" title="Stock máximo alcanzado">
                                                                        <i class="bi bi-plus"></i>
                                                                    </span>
                                                                <?php endif; ?>
                                                            </div>
                                                            <?php if ($stock > 0): ?>
                                                                <small class="text-muted d-block mt-1">Stock disponible: <?= $stock ?></small>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="text-center fw-semibold py-3 price-tag">Bs. <?= number_format((float)$row['product']['precio'], 2) ?></td>
                                                        <td class="text-center fw-semibold py-3 price-tag">Bs. <?= number_format((float)$row['subtotal'], 2) ?></td>
                                                        <td class="text-center pe-4 py-3">
                                                            <a href="index.php?r=cart/remove&id=<?= (int)$row['product']['cod'] ?>"
                                                               class="btn btn-outline-danger btn-sm rounded-pill px-3 shadow-sm"
                                                               data-cart-action="remove" data-id="<?= (int)$row['product']['cod'] ?>">
                                                                <i class="bi bi-trash me-1"></i>Eliminar
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="card summary-card sticky-top shadow-lg">
                                <div class="card-header summary-card-header">
                                    <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Resumen del pedido</h5>
                                </div>
                                <div class="card-body summary-card-body">
                                    <div class="d-flex justify-content-between mb-3">
                                        <span class="text-muted">Subtotal:</span>
                                        <strong class="text-dark">Bs. <?= number_format((float)$total, 2) ?></strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-3">
                                        <span class="text-muted">Envío:</span>
                                        <strong class="text-success fw-semibold">Gratis</strong>
                                    </div>
                                    <hr class="border-secondary">
                                    <div class="d-flex justify-content-between mb-4">
                                        <span class="fs-5 text-muted">Total:</span>
                                        <strong class="fs-5 summary-total">Bs. <?= number_format((float)$total, 2) ?></strong>
                                    </div>

                                    <div class="d-grid gap-2">
                                        <a href="index.php?r=checkout" class="btn btn-gradient btn-lg rounded-pill py-3 shadow-sm">
                                            <i class="bi bi-credit-card me-2"></i>Proceder al Pago
                                        </a>
                                        <button type="button" class="btn btn-outline-secondary btn-lg rounded-pill py-3 fw-semibold" data-bs-dismiss="modal">
                                            <i class="bi bi-arrow-left me-2"></i>Seguir comprando
                                        </button>
                                        <a href="index.php?r=cart/clear" class="btn btn-outline-danger btn-lg rounded-pill py-3 fw-semibold" data-cart-action="clear">
                                            <i class="bi bi-trash me-2"></i>Vaciar carrito
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-cart-x fs-1 text-muted mb-4"></i>
                        <h4 class="text-muted">Tu carrito está vacío</h4>
                        <p class="text-muted mb-4">¡Agrega algunos productos para comenzar tu compra!</p>
                        <button type="button" class="btn btn-primary btn-lg" data-bs-dismiss="modal">
                            <i class="bi bi-shop me-2"></i>Seguir comprando
                        </button>
                    </div>
                <?php endif; ?>