<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Tienda Amiga') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="resources/styles.css">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php?r=home">
            <img src="<?= e(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\')) ?>/resources/logo/Logo.webp" alt="Tienda Amiga" class="navbar-logo me-2">
            <span>Tienda Amiga</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="index.php?r=home">Inicio</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?r=store">Tienda</a></li>
            </ul>
            <ul class="navbar-nav">
                <?php if (isLoggedIn()): ?>
                    <li class="nav-item"><a class="nav-link" href="index.php?r=account/purchases"><i class="bi bi-receipt me-1"></i>Mis Compras</a></li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link" href="#" id="cartBtn" data-bs-toggle="modal" data-bs-target="#cartModalWindow">
                        <i class="bi bi-cart3 me-1"></i>Carrito <span class="badge bg-danger"><?= count($_SESSION['carrito'] ?? []) ?></span>
                    </a>
                </li>
                <?php if (isLoggedIn()): ?>
                    <li class="nav-item"><a class="nav-link" href="index.php?r=logout"><i class="bi bi-box-arrow-right me-1"></i>Salir</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="index.php?r=login"><i class="bi bi-box-arrow-in-right me-1"></i>Iniciar Sesión</a></li>
                <?php endif; ?>
                <?php if (isAdmin()): ?>
                    <li class="nav-item"><a class="nav-link btn btn-outline-light ms-2" href="index.php?r=admin/dashboard"><i class="bi bi-speedometer2 me-1"></i>Panel Admin</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Mensajes flash -->
<?php if (!empty($_SESSION['flash_success'])): ?>
<div class="container mt-3">
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>
        <?= e($_SESSION['flash_success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
<?php unset($_SESSION['flash_success']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['flash_error'])): ?>
<div class="container mt-3">
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <?= e($_SESSION['flash_error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
<?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>

<!-- Modal del Carrito -->
<div class="modal fade" id="cartModalWindow" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header modal-header-theme">
                <h5 class="modal-title text-white"><i class="bi bi-cart3 me-2"></i>Carrito de Compras</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="cartModalContent">
                <p class="text-center text-muted">Cargando carrito...</p>
            </div>
        </div>
    </div>
</div>

<main class="container mt-4"></main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // === AGREGAR AL CARRITO (sin redirigir) ===
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    addToCartButtons.forEach(btn => {
        btn.addEventListener('click', async function(e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const originalText = this.innerHTML;
            
            try {
                const response = await fetch(`index.php?r=cart/add&id=${id}&ajax=1`);
                if (response.ok) {
                    // Actualizar badge
                    await updateBadge();
                    
                    // Feedback visual
                    this.classList.remove('btn-success');
                    this.classList.add('btn-primary');
                    this.innerHTML = '<i class="bi bi-check me-1"></i>Agregado';
                    
                    setTimeout(() => {
                        this.classList.remove('btn-primary');
                        this.classList.add('btn-success');
                        this.innerHTML = originalText;
                    }, 2000);
                } else if (response.status === 409) {
                    alert('No puedes agregar mas unidades. Ya alcanzaste el stock disponible.');
                }
            } catch (error) {
                console.error('Error adding to cart:', error);
            }
        });
    });

    // === MODAL DEL CARRITO ===
    const cartBtn = document.getElementById('cartBtn');
    const cartModalWindow = document.getElementById('cartModalWindow');
    const cartModalContent = document.getElementById('cartModalContent');
    let cartModal = null;

    if (cartModalWindow) {
        cartModal = new bootstrap.Modal(cartModalWindow);
    }

    async function loadCartContent() {
        try {
            const response = await fetch('index.php?r=cart/modal');
            if (!response.ok) {
                cartModalContent.innerHTML = '<p class="text-danger text-center">Error al cargar el carrito</p>';
                return;
            }
            
            const html = await response.text();
            cartModalContent.innerHTML = html;
            attachCartHandlers();
        } catch (error) {
            console.error('Error loading cart:', error);
            cartModalContent.innerHTML = '<p class="text-danger text-center">Error al cargar el carrito</p>';
        }
    }

    function attachCartHandlers() {
        const cartActions = cartModalContent.querySelectorAll('[data-cart-action]');
        cartActions.forEach(el => {
            el.addEventListener('click', async function(e) {
                e.preventDefault();
                const href = this.getAttribute('href');
                if (!href) return;
                
                try {
                    // Construir la URL correctamente
                    const separator = href.includes('?') ? '&' : '?';
                    const url = href + separator + 'ajax=1';
                    
                    const response = await fetch(url);
                    if (response.ok) {
                        await updateBadge();
                        await loadCartContent();
                    } else if (response.status === 409) {
                        alert('No puedes superar el stock disponible para este producto.');
                    } else {
                        console.error('Error response:', response.status);
                    }
                } catch (error) {
                    console.error('Error in cart action:', error);
                }
            });
        });
    }

    async function updateBadge() {
        try {
            const response = await fetch('index.php?r=cart/count&ajax=1');
            if (response.ok) {
                const count = await response.text();
                const badge = cartBtn.querySelector('.badge');
                if (badge) {
                    badge.textContent = count.trim();
                }
            }
        } catch (error) {
            console.error('Error updating badge:', error);
        }
    }

    cartBtn.addEventListener('click', function(e) {
        e.preventDefault();
        loadCartContent();
        if (cartModal) {
            cartModal.show();
        }
    });
});
</script>