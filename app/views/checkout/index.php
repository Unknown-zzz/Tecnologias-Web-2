<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0 checkout-card">
                <div class="card-header checkout-card-header text-center">
                    <h2 class="mb-0"><i class="bi bi-credit-card-2-back-fill me-3"></i>Proceso de Pago</h2>
                </div>
                <div class="card-body p-5">
                    <div class="checkout-alert shadow-sm">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-info-circle fs-4 me-3 text-muted"></i>
                            <div>
                                <h6 class="alert-heading mb-1">Pago Simulado</h6>
                                <p class="mb-0">Este es un proceso de pago simulado para fines demostrativos. En un entorno real, aquí se integraría una pasarela de pagos como PayPal, Stripe o MercadoPago.</p>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card checkout-info-card border-0 mb-3">
                                <div class="card-body text-center">
                                    <i class="bi bi-shield-check fs-1 text-muted mb-3"></i>
                                    <h6 class="card-title">Pago Seguro</h6>
                                    <p class="card-text small text-muted">Tus datos están protegidos con encriptación SSL</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card checkout-info-card border-0 mb-3">
                                <div class="card-body text-center">
                                    <i class="bi bi-truck fs-1 text-muted mb-3"></i>
                                    <h6 class="card-title">Envío Gratis</h6>
                                    <p class="card-text small text-muted">Entrega en 3-5 días hábiles</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="index.php?r=checkout/process">
                        <div class="text-center">
                            <button type="submit" class="btn btn-gradient btn-lg rounded-pill px-5 py-3 shadow-sm">
                                <i class="bi bi-check2-circle me-2 fs-5"></i>
                                <span class="fw-bold">Completar Compra</span>
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <small class="text-muted">
                            Al completar tu compra, aceptas nuestros
                            <a href="#" class="checkout-link">términos y condiciones</a>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
