<?php require __DIR__ . '/../layouts/header.php'; ?>
<div class="container mt-5" style="max-width: 500px;">
    <h2 class="text-center">Inicio de Sesion - Administracion</h2>
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
<?php require __DIR__ . '/../layouts/footer.php'; ?>

