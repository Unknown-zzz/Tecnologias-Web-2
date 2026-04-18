<?php require __DIR__ . '/../layouts/header.php'; ?>
<div class="container-fluid">
    <!-- Header con bienvenida y botón agregar -->
    <div class="section-header mt-3">
        <h2><i class="bi bi-tags mr-2"></i>Gestión de Marcas</h2>
        <div class="btn-group">
            <button class="btn btn-success" data-toggle="modal" data-target="#brandModal"><i class="bi bi-plus-lg mr-1"></i>Agregar Marca</button>
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

    <!-- Tabla de marcas -->
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
                <?php if (!empty($brands)): ?>
                    <?php foreach ($brands as $brand): ?>
                        <tr>
                            <td>
                                <strong class="text-secondary"><?= (int) $brand['cod'] ?></strong>
                            </td>
                            <td>
                                <span class="badge badge-secondary"><?= e($brand['nombre']) ?></span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-primary" onclick="editBrand(<?= (int) $brand['cod'] ?>, '<?= e($brand['nombre']) ?>')">
                                        <i class="bi bi-pencil-square mr-1"></i>Editar
                                    </button>
                                    <a href="index.php?r=admin/brands/delete&id=<?= (int) $brand['cod'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Deseas eliminar esta marca?')">
                                        <i class="bi bi-trash mr-1"></i>Eliminar
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center py-5">
                            <p class="text-muted mb-3">No hay marcas disponibles.</p>
                            <button class="btn btn-success" data-toggle="modal" data-target="#brandModal">
                                <i class="bi bi-plus-circle mr-1"></i>Crear la primera marca
                            </button>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="total-counter">
        Total de marcas: <strong><?= count($brands ?? []) ?></strong>
    </div>
</div>

<!-- Modal para crear/Editar Marca -->
<div class="modal fade" id="brandModal" tabindex="-1" role="dialog" aria-labelledby="brandModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="brandModalLabel"><i class="bi bi-tags mr-1"></i>Agregar nueva marca</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="index.php?r=admin/brands/store" id="brandForm">
                <input type="hidden" name="cod" id="brandCod" value="">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="brandNombre" class="form-label">Nombre de la marca <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="brandNombre" name="nombre" required autofocus placeholder="Ej: Samsung, LG, Sony...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success" id="brandSubmitBtn">Guardar Marca</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editBrand(cod, nombre) {
    document.getElementById('brandModalLabel').textContent = 'Editar Marca';
    document.getElementById('brandCod').value = cod;
    document.getElementById('brandNombre').value = nombre;
    document.getElementById('brandSubmitBtn').textContent = 'Actualizar Marca';
    document.getElementById('brandForm').action = 'index.php?r=admin/brands/update';
    $('#brandModal').modal('show');
}

// Reset modal when closed
$('#brandModal').on('hidden.bs.modal', function () {
    document.getElementById('brandForm').reset();
    document.getElementById('brandModalLabel').textContent = 'Agregar nueva marca';
    document.getElementById('brandCod').value = '';
    document.getElementById('brandSubmitBtn').textContent = 'Guardar Marca';
    document.getElementById('brandForm').action = 'index.php?r=admin/brands/store';
});

// Auto-abrir modal si viene desde create
$(document).ready(function() {
    <?php if (isset($_GET['modal']) && $_GET['modal'] === 'create'): ?>
        $('#brandModal').modal('show');
    <?php endif; ?>
    
    <?php if (isset($_GET['modal']) && $_GET['modal'] === 'edit' && !empty($_GET['id'])): ?>
        var editId = <?= (int)$_GET['id'] ?>;
        var foundBrand = null;
        <?php foreach ($brands as $brand): ?>
            if (<?= (int)$brand['cod'] ?> === editId) {
                foundBrand = {cod: <?= (int)$brand['cod'] ?>, nombre: '<?= addslashes(e($brand['nombre'])) ?>'};
            }
        <?php endforeach; ?>
        if (foundBrand) {
            editBrand(foundBrand.cod, foundBrand.nombre);
        }
    <?php endif; ?>
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

