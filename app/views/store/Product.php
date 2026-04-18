<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="row">
    <div class="col-md-6">
        <?php if (!empty($product['imagen'])): ?>
            <img src="resources/imagenes/<?= e($product['imagen']) ?>" 
                 class="img-fluid rounded shadow product-image-modal" alt="<?= e($product['nombre']) ?>">
        <?php else: ?>
            <div class="product-image-placeholder text-white d-flex align-items-center justify-content-center product-image-placeholder-large">
                Sin imagen disponible
            </div>
        <?php endif; ?>
    </div>
    
    <div class="col-md-6">
        <h1><?= e($product['nombre']) ?></h1>
        <p class="text-muted">
            <?= e($product['categoria']) ?> • <?= e($product['marca']) ?> • <?= e($product['industria']) ?>
        </p>
        
        <h3 class="text-success mb-4">Bs. <?= number_format((float)$product['precio'], 2) ?></h3>
        
        <p><?= e($product['descripcion']) ?></p>
        
        <div class="mt-4">
            <a href="index.php?r=cart/add&id=<?= (int)$product['cod'] ?>" class="btn btn-lg btn-success">
                <i class="bi bi-cart-plus"></i> Agregar al Carrito
            </a>
            <a href="index.php?r=store" class="btn btn-lg btn-secondary ms-2">Volver a la Tienda</a>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>