<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0 checkout-card">
                <div class="card-header checkout-card-header text-center">
                    <h2 class="mb-0"><i class="bi bi-credit-card-2-back-fill me-3"></i>Proceso de Pago</h2>
                </div>
                <div class="card-body p-5">

                    <div class="checkout-alert shadow-sm mb-4">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-info-circle fs-4 me-3 text-muted"></i>
                            <div>
                                <h6 class="alert-heading mb-1">Elige tu método de pago</h6>
                                <p class="mb-0">Puedes completar la compra directamente o usar el pago por código QR.</p>
                            </div>
                        </div>
                    </div>

                    <!-- ══════════════════════════════════════════════
                         SELECTOR DE MÉTODO DE PAGO
                    ══════════════════════════════════════════════ -->
                    <div class="row mb-4 g-3" id="paymentMethodSelector">
                        <!-- Pago Directo -->
                        <div class="col-md-6">
                            <div class="card h-100 checkout-info-card border-2 payment-method-card active"
                                 id="cardDirect" onclick="selectMethod('direct')" style="cursor:pointer;">
                                <div class="card-body text-center py-4">
                                    <i class="bi bi-shield-check fs-1 text-muted mb-3"></i>
                                    <h6 class="card-title fw-bold">Pago Directo</h6>
                                    <p class="card-text small text-muted">Completa tu compra al instante</p>
                                    <div class="mt-2">
                                        <span class="badge bg-success" id="badgeDirect">✓ Seleccionado</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Pago por QR -->
                        <div class="col-md-6">
                            <div class="card h-100 checkout-info-card border-2 payment-method-card"
                                 id="cardQr" onclick="selectMethod('qr')" style="cursor:pointer;">
                                <div class="card-body text-center py-4">
                                    <i class="bi bi-qr-code fs-1 text-muted mb-3"></i>
                                    <h6 class="card-title fw-bold">Pago por QR</h6>
                                    <p class="card-text small text-muted">Escanea con tu teléfono</p>
                                    <div class="mt-2">
                                        <span class="badge bg-secondary d-none" id="badgeQr">✓ Seleccionado</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ══════════════════════════════════════════════
                         PANEL: PAGO DIRECTO
                    ══════════════════════════════════════════════ -->
                    <div id="panelDirect">
                        <form method="POST" action="index.php?r=checkout/process">
                            <div class="text-center">
                                <button type="submit" class="btn btn-gradient btn-lg rounded-pill px-5 py-3 shadow-sm">
                                    <i class="bi bi-check2-circle me-2 fs-5"></i>
                                    <span class="fw-bold">Completar Compra</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- ══════════════════════════════════════════════
                         PANEL: PAGO POR QR
                    ══════════════════════════════════════════════ -->
                    <div id="panelQr" class="d-none">

                        <!-- Paso 1: Generar QR -->
                        <div id="qrStep1" class="text-center">
                            <p class="text-muted mb-4">
                                Genera tu código QR único. Tienes <strong>5 minutos</strong> para escanearlo
                                con tu teléfono y confirmar el pago.
                            </p>
                            <button class="btn btn-dark btn-lg rounded-pill px-5 py-3 shadow-sm" onclick="generarQr()">
                                <i class="bi bi-qr-code-scan me-2 fs-5"></i>
                                <span class="fw-bold">Generar Código QR</span>
                            </button>
                        </div>

                        <!-- Paso 2: Mostrar QR + Polling -->
                        <div id="qrStep2" class="d-none">
                            <div class="text-center mb-3">
                                <h5 class="fw-bold">Escanea el código QR</h5>
                                <p class="text-muted small">
                                    Abre la cámara de tu teléfono y apunta al código.
                                    La compra se confirmará automáticamente.
                                </p>
                            </div>

                            <!-- QR Image generado con qrcode.js -->
                            <div class="d-flex justify-content-center mb-3">
                                <div class="p-3 border rounded-3 shadow-sm bg-white" style="display:inline-block;">
                                    <div id="qrCodeCanvas"></div>
                                </div>
                            </div>

                            <!-- Barra de tiempo restante -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted"><i class="bi bi-clock me-1"></i>Tiempo restante</small>
                                    <small id="qrTimerLabel" class="fw-semibold">05:00</small>
                                </div>
                                <div style="height:8px;border-radius:4px;background:#e9ecef;overflow:hidden;">
                                    <div id="qrTimerBar" style="height:100%;width:100%;background:linear-gradient(90deg,#28a745,#20c997);transition:width 1s linear;"></div>
                                </div>
                            </div>

                            <!-- Estado del pago -->
                            <div id="qrStatusBox" class="alert alert-info d-flex align-items-center gap-2">
                                <div class="spinner-border spinner-border-sm text-info" role="status"></div>
                                <span>Esperando escaneo del código QR...</span>
                            </div>

                            <!-- Botón de cancelar / volver a generar -->
                            <div class="text-center mt-2">
                                <button class="btn btn-sm btn-outline-secondary rounded-pill px-4" onclick="cancelarQr()">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i>Cancelar y regenerar
                                </button>
                            </div>
                        </div>

                        <!-- Paso 3: Pago confirmado (solo informativo, el redirect es automático) -->
                        <div id="qrStep3" class="d-none text-center py-3">
                            <i class="bi bi-check-circle-fill text-success" style="font-size:4rem;"></i>
                            <h4 class="mt-3 fw-bold text-success">¡Pago Confirmado!</h4>
                            <p class="text-muted">Redirigiendo a tu comprobante...</p>
                            <div class="spinner-border text-success mt-2" role="status"></div>
                        </div>

                    </div>
                    <!-- fin panelQr -->

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

<!-- ══════════════════════════════════════════════════════════
     LIBRERÍA QR CODE (sin Composer, desde CDN)
══════════════════════════════════════════════════════════ -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<script>
/* ─── Estado ─────────────────────────────────────────── */
let metodoActual  = 'direct';
let qrTokenActual = null;
let pollingHandle = null;
let timerHandle   = null;
let segsRestantes = 300;
let qrInstance    = null;

/* ─── Selección de método ─────────────────────────────── */
function selectMethod(method) {
    metodoActual = method;

    document.getElementById('cardDirect').classList.toggle('border-dark', method === 'direct');
    document.getElementById('cardQr').classList.toggle('border-dark', method === 'qr');

    document.getElementById('badgeDirect').classList.toggle('d-none', method !== 'direct');
    document.getElementById('badgeQr').classList.toggle('d-none', method !== 'qr');

    document.getElementById('panelDirect').classList.toggle('d-none', method !== 'direct');
    document.getElementById('panelQr').classList.toggle('d-none', method !== 'qr');

    // Cancelar polling activo si el usuario cambia de método
    if (method === 'direct') detenerPolling();
}

/* ─── Generar QR ──────────────────────────────────────── */
async function generarQr() {
    try {
        const resp = await fetch('index.php?r=qr/generate', { method: 'POST' });
        const data = await resp.json();

        if (!data.success) {
            alert('Error: ' + (data.error || 'No se pudo generar el QR.'));
            return;
        }

        qrTokenActual = data.token;

        // Mostrar el paso 2
        document.getElementById('qrStep1').classList.add('d-none');
        document.getElementById('qrStep2').classList.remove('d-none');

        // Limpiar canvas anterior
        const canvas = document.getElementById('qrCodeCanvas');
        canvas.innerHTML = '';

        // Generar imagen QR
        qrInstance = new QRCode(canvas, {
            text:           data.scan_url,
            width:          220,
            height:         220,
            colorDark:      '#1a1a2e',
            colorLight:     '#ffffff',
            correctLevel:   QRCode.CorrectLevel.H,
        });

        // Iniciar contador
        segsRestantes = 300;
        iniciarTimer();

        // Iniciar polling
        pollingHandle = setInterval(verificarEstadoQr, 2500);

    } catch (err) {
        alert('Error de red al generar el QR. Inténtalo de nuevo.');
        console.error(err);
    }
}

/* ─── Timer ───────────────────────────────────────────── */
function iniciarTimer() {
    if (timerHandle) clearInterval(timerHandle);

    timerHandle = setInterval(() => {
        segsRestantes--;
        const m   = String(Math.floor(segsRestantes / 60)).padStart(2, '0');
        const s   = String(segsRestantes % 60).padStart(2, '0');
        const pct = Math.max(0, Math.round((segsRestantes / 300) * 100));

        document.getElementById('qrTimerLabel').textContent = `${m}:${s}`;
        document.getElementById('qrTimerBar').style.width   = pct + '%';

        if (pct < 30) {
            document.getElementById('qrTimerBar').style.background = 'linear-gradient(90deg,#dc3545,#ffc107)';
        }

        if (segsRestantes <= 0) {
            clearInterval(timerHandle);
            detenerPolling();
            mostrarEstado('expirado');
        }
    }, 1000);
}

/* ─── Polling de estado ───────────────────────────────── */
async function verificarEstadoQr() {
    if (!qrTokenActual) return;

    try {
        const resp = await fetch(`index.php?r=qr/status&token=${qrTokenActual}`);
        const data = await resp.json();

        mostrarEstado(data.estado, data);

        if (data.estado === 'completado') {
            detenerPolling();
            document.getElementById('qrStep2').classList.add('d-none');
            document.getElementById('qrStep3').classList.remove('d-none');

            // Redirigir a la página de éxito
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 2000);
        }

        if (data.estado === 'expirado' || data.estado === 'error') {
            detenerPolling();
        }

    } catch (err) {
        console.error('Error de polling:', err);
    }
}

/* ─── Mostrar estado en el badge ─────────────────────── */
function mostrarEstado(estado, data = {}) {
    const box = document.getElementById('qrStatusBox');
    const estados = {
        pendiente:  { cls: 'alert-info',    icon: '<div class="spinner-border spinner-border-sm text-info" role="status"></div>', msg: 'Esperando escaneo del código QR...' },
        confirmado: { cls: 'alert-warning',  icon: '<div class="spinner-border spinner-border-sm text-warning" role="status"></div>', msg: 'QR escaneado. Procesando pago...' },
        completado: { cls: 'alert-success',  icon: '<i class="bi bi-check-circle-fill text-success fs-5"></i>', msg: '¡Pago confirmado! Redirigiendo...' },
        expirado:   { cls: 'alert-danger',   icon: '<i class="bi bi-clock-history text-danger fs-5"></i>', msg: 'El QR ha expirado. Genera uno nuevo.' },
        error:      { cls: 'alert-danger',   icon: '<i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>', msg: 'Error al verificar el pago.' },
    };

    const cfg = estados[estado] ?? estados.error;
    box.className = `alert ${cfg.cls} d-flex align-items-center gap-2`;
    box.innerHTML = `${cfg.icon}<span>${cfg.msg}</span>`;
}

/* ─── Cancelar y resetear ────────────────────────────── */
function cancelarQr() {
    detenerPolling();
    qrTokenActual = null;
    document.getElementById('qrStep2').classList.add('d-none');
    document.getElementById('qrStep3').classList.add('d-none');
    document.getElementById('qrStep1').classList.remove('d-none');
}

function detenerPolling() {
    if (pollingHandle) { clearInterval(pollingHandle); pollingHandle = null; }
    if (timerHandle)   { clearInterval(timerHandle);   timerHandle   = null; }
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>