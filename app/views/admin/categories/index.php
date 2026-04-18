<?php require __DIR__ . '/../layouts/header.php'; ?>
<div class="container-fluid">
    <!-- Header con bienvenida y botón agregar -->
    <div class="section-header mt-3">
        <h2><i class="bi bi-folder mr-2"></i>Gestión de Categorías</h2>
        <div class="btn-group">
            <button class="btn btn-success" data-toggle="modal" data-target="#categoryModal"><i class="bi bi-plus-lg mr-1"></i>Agregar Categoría</button>
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

    <!-- Tabla de categorías -->
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
                <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <td>
                                <strong class="text-secondary"><?= (int) $category['cod'] ?></strong>
                            </td>
                            <td>
                                <span class="badge badge-secondary"><?= e($category['nombre']) ?></span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-primary" onclick="editCategory(<?= (int) $category['cod'] ?>, '<?= e($category['nombre']) ?>')">
                                        <i class="bi bi-pencil-square mr-1"></i>Editar
                                    </button>
                                    <a href="index.php?r=admin/categories/delete&id=<?= (int) $category['cod'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Deseas eliminar esta categoría?')">
                                        <i class="bi bi-trash mr-1"></i>Eliminar
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center py-5">
                            <p class="text-muted mb-3">No hay categorías disponibles.</p>
                            <button class="btn btn-success" data-toggle="modal" data-target="#categoryModal">
                                <i class="bi bi-plus-circle mr-1"></i>Crear la primera categoría
                            </button>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="total-counter">
        Total de categorías: <strong><?= count($categories ?? []) ?></strong>
    </div>
</div>

<!-- Modal para crear/editar categoría -->
<div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryModalLabel"><i class="bi bi-folder mr-1"></i>Agregar nueva categoría</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="index.php?r=admin/categories/store" id="categoryForm">
                <input type="hidden" name="cod" id="categoryCod" value="">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="categoryNombre" class="form-label">Nombre de la categoría <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="categoryNombre" name="nombre" required autofocus placeholder="Ej: Electrónica, Ropa, Hogar...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success" id="categorySubmitBtn">Guardar Categoría</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editCategory(cod, nombre) {
    document.getElementById('categoryModalLabel').textContent = 'Editar Categoría';
    document.getElementById('categoryCod').value = cod;
    document.getElementById('categoryNombre').value = nombre;
    document.getElementById('categorySubmitBtn').textContent = 'Actualizar Categoría';
    document.getElementById('categoryForm').action = 'index.php?r=admin/categories/update';
    $('#categoryModal').modal('show');
}

// Reset modal when closed
$('#categoryModal').on('hidden.bs.modal', function () {
    document.getElementById('categoryForm').reset();
    document.getElementById('categoryModalLabel').textContent = 'Agregar nueva categoría';
    document.getElementById('categoryCod').value = '';
    document.getElementById('categorySubmitBtn').textContent = 'Guardar Categoría';
    document.getElementById('categoryForm').action = 'index.php?r=admin/categories/store';
});

// Auto-abrir modal si viene desde create
$(document).ready(function() {
    <?php if (isset($_GET['modal']) && $_GET['modal'] === 'create'): ?>
        $('#categoryModal').modal('show');
    <?php endif; ?>
    
    <?php if (isset($_GET['modal']) && $_GET['modal'] === 'edit' && !empty($_GET['id'])): ?>
        var editId = <?= (int)$_GET['id'] ?>;
        var foundCategory = null;
        <?php foreach ($categories as $category): ?>
            if (<?= (int)$category['cod'] ?> === editId) {
                foundCategory = {cod: <?= (int)$category['cod'] ?>, nombre: '<?= addslashes(e($category['nombre'])) ?>'};
            }
        <?php endforeach; ?>
        if (foundCategory) {
            editCategory(foundCategory.cod, foundCategory.nombre);
        }
    <?php endif; ?>
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

