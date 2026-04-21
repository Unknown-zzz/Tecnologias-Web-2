<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <img src="resources/logo/Logo.webp" alt="Tienda Amiga" class="auth-brand-logo mb-3">
                        <div class="auth-brand-name mb-2">Tienda Amiga</div>
                        <h2 class="card-title fw-bold">Iniciar Sesión</h2>
                        <p class="text-muted">Ingresa tus credenciales para acceder</p>
                    </div>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger d-flex align-items-center">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <?= e($error) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="index.php?r=authenticate">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Usuario</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" name="usuario" class="form-control form-control-lg" required
                                       placeholder="Ingresa tu usuario">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-key"></i></span>
                                <input type="password" name="password" class="form-control form-control-lg" required
                                       placeholder="Ingresa tu contraseña">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Ingresar
                        </button>
                    </form>

                    <div class="text-center">
                        <p class="mb-0 text-muted">¿No tienes cuenta?</p>
                        <a href="index.php?r=register" class="btn btn-outline-primary">
                            <i class="bi bi-person-plus me-1"></i>Regístrate aquí
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>