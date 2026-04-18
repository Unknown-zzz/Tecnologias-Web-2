<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <div class="text-center mb-5">
                        <i class="bi bi-person-plus fs-1 text-primary mb-3"></i>
                        <h2 class="card-title fw-bold">Registro de Cliente</h2>
                        <p class="text-muted">Crea tu cuenta para comenzar a comprar</p>
                    </div>

                    <?php if (!empty($error ?? '')): ?>
                        <div class="alert alert-danger d-flex align-items-center">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <?= e($error) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="index.php?r=register/process">
                        <!-- Información Personal -->
                        <div class="mb-4">
                            <h5 class="fw-bold mb-3"><i class="bi bi-person-lines-fill me-2"></i>Información Personal</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">CI</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                                        <input type="text" name="ci" class="form-control" required
                                               placeholder="Número de CI">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Usuario</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                                        <input type="text" name="usuario" class="form-control" required
                                               placeholder="Nombre de usuario">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-semibold">Nombres</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                                        <input type="text" name="nombres" class="form-control" required
                                               placeholder="Tus nombres">
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-semibold">Apellido Paterno</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                                        <input type="text" name="apPaterno" class="form-control" required
                                               placeholder="Apellido paterno">
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-semibold">Apellido Materno</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                                        <input type="text" name="apMaterno" class="form-control"
                                               placeholder="Apellido materno (opcional)">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información de Contacto -->
                        <div class="mb-4">
                            <h5 class="fw-bold mb-3"><i class="bi bi-envelope-fill me-2"></i>Información de Contacto</h5>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Correo Electrónico</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" name="correo" class="form-control" required
                                           placeholder="tu@email.com">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Dirección</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                        <input type="text" name="direccion" class="form-control" required
                                               placeholder="Tu dirección completa">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Celular</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-phone"></i></span>
                                        <input type="text" name="nroCelular" class="form-control" required
                                               placeholder="Número de celular">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Seguridad -->
                        <div class="mb-4">
                            <h5 class="fw-bold mb-3"><i class="bi bi-shield-lock-fill me-2"></i>Seguridad</h5>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Contraseña</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-key"></i></span>
                                    <input type="password" name="password" class="form-control" required
                                           placeholder="Crea una contraseña segura">
                                </div>
                                <small class="text-muted">Mínimo 6 caracteres</small>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                            <i class="bi bi-pencil-square me-2"></i>Crear Cuenta
                        </button>
                    </form>

                    <div class="text-center">
                        <p class="mb-0 text-muted">¿Ya tienes cuenta?</p>
                        <a href="index.php?r=login" class="btn btn-outline-primary">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Inicia sesión aquí
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>