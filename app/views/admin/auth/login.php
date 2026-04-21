<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container py-5" style="max-width: 540px;">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <img src="resources/logo/Logo.webp" alt="Tienda Amiga" class="admin-auth-logo mb-3">
                <div class="admin-brand-title">Tienda Amiga</div>
                <h2 class="h4 mt-2 mb-1">Acceso de Administracion</h2>
                <p class="text-muted mb-0">Ingresa tus credenciales para administrar la tienda</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger mt-3"><?= e($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="index.php?r=admin/authenticate" class="mt-4">
                <div class="form-group">
                    <label for="usuario">Usuario</label>
                    <input type="text" class="form-control" id="usuario" name="usuario" required>
                </div>
                <div class="form-group">
                    <label for="password">Contrasena</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Iniciar Sesion</button>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

