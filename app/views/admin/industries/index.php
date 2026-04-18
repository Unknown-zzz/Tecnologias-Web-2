<?php require __DIR__ . '/../layouts/header.php'; ?>
<div class="container-fluid">
    <!-- Header con bienvenida y botón agregar -->
    <div class="section-header mt-3">
        <h2><i class="bi bi-building mr-2"></i>Gestión de Industrias</h2>
        <div class="btn-group">
            <button class="btn btn-success" data-toggle="modal" data-target="#industryModal"><i class="bi bi-plus-lg mr-1"></i>Agregar Industria</button>
            <a href="index.php?r=admin/dashboard" class="btn btn-secondary"><i class="bi bi-speedometer2 mr-1"></i>Panel</a>
        </div>
    </div>

    <?php if (!empty($message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= e($message) ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- Tabla de industrias -->
    <div class="table-container">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th style="width: 15%;">ID</th>
                    <th style="width: 60%;">Nombre</th>
                    <th style="width: 25%;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($industries)): ?>
                    <?php foreach ($industries as $industry): ?>
                        <tr>
                            <td>
                                <strong class="text-secondary"><?= (int) $industry['cod'] ?></strong>
                            </td>
                            <td>
                                <span class="badge badge-secondary"><?= e($industry['nombre']) ?></span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-primary" onclick="editIndustry(<?= (int) $industry['cod'] ?>, '<?= e($industry['nombre']) ?>')">
                                        <i class="bi bi-pencil-square mr-1"></i>Editar
                                    </button>
                                    <a href="index.php?r=admin/industries/delete&id=<?= (int) $industry['cod'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Deseas eliminar esta industria?')">
                                        <i class="bi bi-trash mr-1"></i>Eliminar
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center py-5">
                            <p class="text-muted mb-3">No hay industrias disponibles.</p>
                            <button class="btn btn-success" data-toggle="modal" data-target="#industryModal">
                                <i class="bi bi-plus-circle mr-1"></i>Crear la primera industria
                            </button>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="total-counter">
        Total de industrias: <strong><?= count($industries ?? []) ?></strong>
    </div>
</div>

<!-- Modal para crear/Editar Industria -->
<div class="modal fade" id="industryModal" tabindex="-1" role="dialog" aria-labelledby="industryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="industryModalLabel"><i class="bi bi-building mr-1"></i>Agregar nueva industria</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="index.php?r=admin/industries/store" id="industryForm">
                <input type="hidden" name="cod" id="industryCod" value="">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="industryNombre" class="form-label">Nombre de la industria <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="industryNombre" name="nombre" required autofocus placeholder="Ej: Tecnología, Moda, Alimentación...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success" id="industrySubmitBtn">Guardar Industria</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editIndustry(cod, nombre) {
    document.getElementById('industryModalLabel').textContent = 'Editar Industria';
    document.getElementById('industryCod').value = cod;
    document.getElementById('industryNombre').value = nombre;
    document.getElementById('industrySubmitBtn').textContent = 'Actualizar Industria';
    document.getElementById('industryForm').action = 'index.php?r=admin/industries/update';
    $('#industryModal').modal('show');
}

// Reset modal when closed
$('#industryModal').on('hidden.bs.modal', function () {
    document.getElementById('industryForm').reset();
    document.getElementById('industryModalLabel').textContent = 'Agregar nueva industria';
    document.getElementById('industryCod').value = '';
    document.getElementById('industrySubmitBtn').textContent = 'Guardar Industria';
    document.getElementById('industryForm').action = 'index.php?r=admin/industries/store';
});

// Auto-abrir modal si viene desde create
$(document).ready(function() {
    <?php if (isset($_GET['modal']) && $_GET['modal'] === 'create'): ?>
        $('#industryModal').modal('show');
    <?php endif; ?>
    
    <?php if (isset($_GET['modal']) && $_GET['modal'] === 'edit' && !empty($_GET['id'])): ?>
        var editId = <?= (int)$_GET['id'] ?>;
        var foundIndustry = null;
        <?php foreach ($industries as $industry): ?>
            if (<?= (int)$industry['cod'] ?> === editId) {
                foundIndustry = {cod: <?= (int)$industry['cod'] ?>, nombre: '<?= addslashes(e($industry['nombre'])) ?>'};
            }
        <?php endforeach; ?>
        if (foundIndustry) {
            editIndustry(foundIndustry.cod, foundIndustry.nombre);
        }
    <?php endif; ?>
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

