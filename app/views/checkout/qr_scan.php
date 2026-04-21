<?php
/**
 * Vista: checkout/qr_scan.php
 * Página que se muestra al dispositivo que escanea el QR.
 * No usa el layout principal para ser más liviana en móvil.
 *
 * Variables disponibles:
 *   $token    string   - Token del pago
 *   $pago     array    - Registro de la BD (puede ser null)
 *   $error    string   - Mensaje de error (puede ser null)
 *   $success  bool     - true si el pago fue completado en esta solicitud
 *   $nroVenta int      - Número de venta (solo si $success = true)
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Pago QR') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: #f0f4f8; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .scan-card { max-width: 460px; width: 100%; border-radius: 20px; box-shadow: 0 8px 32px rgba(0,0,0,0.13); }
        .scan-header { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); border-radius: 20px 20px 0 0; }
        .btn-confirm { background: linear-gradient(135deg, #28a745, #218838); border: none; font-size: 1.1rem; padding: 14px 40px; border-radius: 50px; }
        .btn-confirm:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(40,167,69,0.4); }
        .total-badge { font-size: 1.5rem; font-weight: 700; color: #28a745; }
        .item-list { background: #f8f9fa; border-radius: 12px; }
        .timer-bar { height: 6px; border-radius: 3px; background: #e9ecef; overflow: hidden; }
        .timer-fill { height: 100%; background: linear-gradient(90deg, #28a745, #ffc107); transition: width 1s linear; }
    </style>
</head>
<body>

<div class="scan-card card border-0 m-3">
    <!-- Header -->
    <div class="scan-header text-white text-center p-4">
        <img src="resources/logo/Logo.png" alt="Logo" style="height:48px;" class="mb-2">
        <h4 class="mb-0 fw-bold">Ecommerce Pro</h4>
        <small class="opacity-75">Confirmar Pago QR</small>
    </div>

    <div class="card-body p-4">

        <?php if (!empty($error)): ?>
            <!-- Estado de error / ya usado / expirado -->
            <div class="text-center py-3">
                <?php if (str_starts_with($error, '✅')): ?>
                    <i class="bi bi-check-circle-fill text-success" style="font-size:4rem;"></i>
                <?php else: ?>
                    <i class="bi bi-x-circle-fill text-danger" style="font-size:4rem;"></i>
                <?php endif; ?>
                <h5 class="mt-3"><?= e($error) ?></h5>
            </div>

        <?php elseif (!empty($success)): ?>
            <!-- Pago completado exitosamente -->
            <div class="text-center py-3">
                <div class="mb-3">
                    <i class="bi bi-check-circle-fill text-success" style="font-size:5rem;"></i>
                </div>
                <h3 class="fw-bold text-success">¡Pago Confirmado!</h3>
                <p class="text-muted">Tu compra fue registrada correctamente.</p>
                <div class="alert alert-success rounded-3 mt-3">
                    <strong>Venta #<?= (int)($nroVenta ?? 0) ?></strong><br>
                    <small>Puedes cerrar esta ventana.</small>
                </div>
            </div>

        <?php elseif ($pago && $pago['estado'] === 'pendiente'): ?>
            <!-- Formulario de confirmación -->

            <?php
                // Calcular tiempo restante
                $segsRestantes = max(0, strtotime($pago['expires_at']) - time());
                $totalSegs     = 300; // 5 minutos
                $porcentaje    = min(100, (int)(($segsRestantes / $totalSegs) * 100));
                $carritoDatos  = json_decode($pago['carrito'], true);
                
                // Obtener detalles de productos
                require_once __DIR__ . '/../../models/Product.php';
                $productModel = new Product($db);
                $totalCarrito = 0;
            ?>

            <h5 class="fw-bold mb-1">Resumen del pedido</h5>
            <div class="item-list p-3 mb-3">
                <?php if (is_array($carritoDatos) && count($carritoDatos) > 0): ?>
                    <?php foreach ($carritoDatos as $item): ?>
                        <?php
                            $productId = (int)($item['id'] ?? 0);
                            $cantidad = (int)($item['cantidad'] ?? 1);
                            if ($productId > 0) {
                                $product = $productModel->find($productId);
                                if ($product) {
                                    $subtotal = (float)$product['precio'] * $cantidad;
                                    $totalCarrito += $subtotal;
                                    $nombre = $product['nombre'];
                                    $precio = $product['precio'];
                                } else {
                                    continue;
                                }
                            } else {
                                $nombre = 'Producto #' . $productId;
                                $precio = 0;
                            }
                        ?>
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div>
                                <span class="fw-semibold small"><?= e($nombre) ?></span><br>
                                <span class="text-muted" style="font-size:.8rem;">Cant: <?= $cantidad ?></span>
                            </div>
                            <span class="small">
                                Bs. <?= number_format((float)$precio * $cantidad, 2) ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <small>No hay items en el carrito</small>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if ($totalCarrito > 0): ?>
            <div class="mb-3">
                <div class="d-flex justify-content-between fw-bold">
                    <span>Total:</span>
                    <span class="text-success">Bs. <?= number_format($totalCarrito, 2) ?></span>
                </div>
            </div>
            <?php endif; ?>

            <div class="mb-3">
                <div class="d-flex justify-content-between mb-1">
                    <small class="text-muted">Tiempo restante</small>
                    <small id="timerLabel"><?= gmdate('i:s', $segsRestantes) ?></small>
                </div>
                <div class="timer-bar">
                    <div class="timer-fill" id="timerFill" style="width: <?= $porcentaje ?>%;"></div>
                </div>
            </div>

            <form method="POST" action="index.php?r=qr/confirm">
                <input type="hidden" name="token" value="<?= e($token) ?>">
                <div class="d-grid">
                    <button type="submit" class="btn btn-confirm text-white fw-bold">
                        <i class="bi bi-check2-circle me-2"></i>Confirmar Pago
                    </button>
                </div>
            </form>

            <script>
                const token = '<?= e($token) ?>';
                const label = document.getElementById('timerLabel');
                const fill  = document.getElementById('timerFill');
                const form = document.querySelector('form');
                let clientTime = <?= time() ?>; // Tiempo del servidor al cargar la página
                let serverExpireTime = <?= (int)strtotime($pago['expires_at']) ?>;
                const totalSegs = 300; // 5 minutos

                // Polling al servidor cada 2 segundos para sincronización exacta
                const pollInterval = setInterval(() => {
                    fetch(`index.php?r=qr/status&token=${token}`)
                        .then(r => r.json())
                        .then(data => {
                            if (data.estado === 'completado') {
                                clearInterval(pollInterval);
                                location.href = data.redirect;
                                return;
                            }
                            
                            if (data.estado === 'expirado') {
                                clearInterval(pollInterval);
                                label.textContent = '00:00';
                                fill.style.width = '0%';
                                fill.style.background = '#dc3545';
                                form.innerHTML = '<div class="alert alert-danger text-center">El QR ha expirado. Genera uno nuevo.</div>';
                                return;
                            }

                            // Usar tiempos del servidor para sincronización exacta
                            if (data.servidor_tiempo && data.expira_en) {
                                clientTime = data.servidor_tiempo;
                                serverExpireTime = data.expira_en;
                            }
                        })
                        .catch(e => console.error('Error en polling:', e));
                }, 2000); // Polling cada 2 segundos

                // Actualizar display cada segundo basado en tiempos del servidor
                const displayInterval = setInterval(() => {
                    const ahora = clientTime + Math.floor((Date.now() - performance.now()) / 1000 - clientTime);
                    const segsRestantes = Math.max(0, serverExpireTime - ahora);
                    
                    const m = String(Math.floor(segsRestantes / 60)).padStart(2, '0');
                    const s = String(segsRestantes % 60).padStart(2, '0');
                    label.textContent = `${m}:${s}`;
                    
                    const pct = Math.round((segsRestantes / totalSegs) * 100);
                    fill.style.width = pct + '%';
                    
                    if (pct < 30) {
                        fill.style.background = 'linear-gradient(90deg, #dc3545, #ffc107)';
                    }
                    
                    if (segsRestantes === 0) {
                        clearInterval(displayInterval);
                        form.innerHTML = '<div class="alert alert-danger text-center">El QR ha expirado. Genera uno nuevo.</div>';
                    }
                }, 1000);

                // Limpiar intervalos cuando se cierre la página
                window.addEventListener('beforeunload', () => {
                    clearInterval(pollInterval);
                    clearInterval(displayInterval);
                });
            </script>

        <?php else: ?>
            <div class="text-center py-3">
                <i class="bi bi-question-circle text-warning" style="font-size:3rem;"></i>
                <p class="mt-2 text-muted">Estado desconocido. El QR podría haber expirado.</p>
            </div>
        <?php endif; ?>

    </div>

    <div class="card-footer text-center text-muted small py-3" style="border-radius:0 0 20px 20px;">
        Ecommerce Pro &mdash; Pago seguro
    </div>
</div>

</body>
</html>