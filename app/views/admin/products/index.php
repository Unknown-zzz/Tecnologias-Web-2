<?php require __DIR__ . '/../layouts/header.php'; ?>
<div class="container-fluid">
    <!-- Header con bienvenida y botón agregar -->
    <div class="section-header mt-3">
        <h2><i class="bi bi-box-seam mr-2"></i>Gestión de Productos</h2>
        <div class="btn-group">
            <button class="btn btn-success" data-toggle="modal" data-target="#productModal"><i class="bi bi-plus-lg mr-1"></i>Agregar Producto</button>
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

    <!-- Tabla de productos -->
    <div class="table-container">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th style="width: 8%;">ID</th>
                    <th style="width: 15%;">Nombre</th>
                    <th style="width: 12%;">Marca</th>
                    <th style="width: 20%;">Descripción</th>
                    <th style="width: 10%;">Precio</th>
                    <th style="width: 10%;">Stock</th>
                    <th style="width: 15%;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><strong class="text-secondary"><?= (int) $product['cod'] ?></strong></td>
                            <td>
                                <strong><?= e($product['nombre']) ?></strong>
                            </td>
                            <td>
                                <span class="badge badge-secondary"><?= e($product['marca'] ?? 'N/A') ?></span>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <?= substr(e((string) $product['descripcion']), 0, 40) ?>
                                    <?php if (strlen($product['descripcion']) > 40): ?>...<?php endif; ?>
                                </small>
                            </td>
                            <td>
                                <span class="badge badge-success" style="font-size: 0.95rem;">
                                    Bs. <?= number_format((float) $product['precio'], 2) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ((int)($product['stock'] ?? 0) > 0): ?>
                                    <span class="badge badge-success"><?= (int) ($product['stock'] ?? 0) ?></span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Sin stock</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-primary" onclick="editProduct(<?= (int) $product['cod'] ?>, '<?= addslashes(e($product['nombre'])) ?>', '<?= addslashes(e((string)$product['descripcion'])) ?>', <?= (float) $product['precio'] ?>, <?= (int) ($product['stock'] ?? 0) ?>, '<?= addslashes(e((string) $product['imagen'])) ?>', <?= (int) $product['codMarca'] ?>, <?= (int) $product['codIndustria'] ?>, <?= (int) $product['codCategoria'] ?>)">
                                        <i class="bi bi-pencil-square mr-1"></i>Editar
                                    </button>
                                    <a href="index.php?r=admin/products/delete&id=<?= (int) $product['cod'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Deseas eliminar este producto?')">
                                        <i class="bi bi-trash mr-1"></i>Eliminar
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <p class="text-muted mb-3">No hay productos disponibles.</p>
                            <button class="btn btn-success" data-toggle="modal" data-target="#productModal"><i class="bi bi-plus-circle mr-1"></i>Crear el primer producto</button>
                                Crear el primer producto
                            </button>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="total-counter">
        Total de productos: <strong><?= count($products ?? []) ?></strong>
    </div>
</div>

<!-- Modal para crear/Editar Producto -->
<div class="modal fade" id="productModal" tabindex="-1" role="dialog" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white;">
                <h5 class="modal-title" id="productModalLabel"><i class="bi bi-box-seam mr-1"></i>Agregar nuevo producto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="index.php?r=admin/products/store" id="productForm" enctype="multipart/form-data">
                <input type="hidden" name="cod" id="productCod" value="">
                
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <div class="form-group">
                        <label for="productNombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="productNombre" name="nombre" required placeholder="Ej: Samsung Galaxy S21">
                    </div>

                    <div class="form-group">
                        <label for="productDescripcion" class="form-label">Descripción <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="productDescripcion" name="descripcion" rows="3" required placeholder="Describe el producto..."></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="productPrecio" class="form-label">Precio ($) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" id="productPrecio" name="precio" required placeholder="0.00">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="productStock" class="form-label">Stock <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="productStock" name="stock" required placeholder="0">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="productImagen" class="form-label">Imagen del producto</label>
                        <input type="file" class="form-control-file" id="productImagen" name="imagen" accept="image/*">
                        <input type="hidden" name="imagen_actual" id="productImagenActual" value="">
                        <small id="productImagenInfo" class="form-text text-muted">Selecciona un archivo para subir. Si editas y no seleccionas uno, se mantiene la imagen actual.</small>
                    </div>

                    <hr>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="productMarca" class="form-label">Marca <span class="text-danger">*</span></label>
                            <select class="form-control" id="productMarca" name="codMarca" required>
                                <option value="">-- Selecciona marca --</option>
                                <?php foreach ($brands as $brand): ?>
                                    <option value="<?= (int) $brand['cod'] ?>"><?= e($brand['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="productIndustria" class="form-label">Industria <span class="text-danger">*</span></label>
                            <select class="form-control" id="productIndustria" name="codIndustria" required>
                                <option value="">-- Selecciona industria --</option>
                                <?php foreach ($industries as $industry): ?>
                                    <option value="<?= (int) $industry['cod'] ?>"><?= e($industry['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="productCategoria" class="form-label">Categoría <span class="text-danger">*</span></label>
                        <select class="form-control" id="productCategoria" name="codCategoria" required>
                            <option value="">-- Selecciona categoría --</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= (int) $category['cod'] ?>"><?= e($category['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success" id="productSubmitBtn">Guardar Producto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editProduct(cod, nombre, descripcion, precio, stock, imagen, marca, industria, categoria) {
    document.getElementById('productModalLabel').textContent = 'Editar Producto';
    document.getElementById('productCod').value = cod;
    document.getElementById('productNombre').value = nombre;
    document.getElementById('productDescripcion').value = descripcion;
    document.getElementById('productPrecio').value = precio;
    document.getElementById('productStock').value = stock;
    document.getElementById('productImagenActual').value = imagen;
    document.getElementById('productMarca').value = marca;
    document.getElementById('productIndustria').value = industria;
    document.getElementById('productCategoria').value = categoria;
    document.getElementById('productSubmitBtn').textContent = 'Actualizar Producto';
    document.getElementById('productForm').action = 'index.php?r=admin/products/update';

    var imageInfo = document.getElementById('productImagenInfo');
    if (imageInfo) {
        imageInfo.textContent = imagen ? 'Imagen actual: ' + imagen + '. Deja vacío para conservarla.' : 'No hay imagen actual. Selecciona un archivo para subir.';
    }

    $('#productModal').modal('show');
}

// Reset modal when closed
$('#productModal').on('hidden.bs.modal', function () {
    document.getElementById('productForm').reset();
    document.getElementById('productModalLabel').textContent = 'Agregar nuevo producto';
    document.getElementById('productCod').value = '';
    document.getElementById('productImagenActual').value = '';
    var imageInfo = document.getElementById('productImagenInfo');
    if (imageInfo) {
        imageInfo.textContent = 'Selecciona un archivo para subir. Si editas y no seleccionas uno, se mantiene la imagen actual.';
    }
    document.getElementById('productSubmitBtn').textContent = 'Guardar Producto';
    document.getElementById('productForm').action = 'index.php?r=admin/products/store';
});

// Auto-abrir modal si viene desde create
$(document).ready(function() {
    <?php if (isset($_GET['modal']) && $_GET['modal'] === 'create'): ?>
        $('#productModal').modal('show');
    <?php endif; ?>
    
    <?php if (isset($_GET['modal']) && $_GET['modal'] === 'edit' && !empty($_GET['id'])): ?>
        var editId = <?= (int)$_GET['id'] ?>;
        var foundProduct = null;
        <?php foreach ($products as $product): ?>
            if (<?= (int)$product['cod'] ?> === editId) {
                foundProduct = {
                    cod: <?= (int)$product['cod'] ?>,
                    nombre: '<?= addslashes(e($product['nombre'])) ?>',
                    descripcion: '<?= addslashes(e((string)$product['descripcion'])) ?>',
                    precio: <?= (float) $product['precio'] ?>,
                    stock: <?= (int) ($product['stock'] ?? 0) ?>,
                    imagen: '<?= addslashes(e((string) $product['imagen'])) ?>',
                    marca: <?= (int) $product['codMarca'] ?>,
                    industria: <?= (int) $product['codIndustria'] ?>,
                    categoria: <?= (int) $product['codCategoria'] ?>
                };
            }
        <?php endforeach; ?>
        if (foundProduct) {
            editProduct(foundProduct.cod, foundProduct.nombre, foundProduct.descripcion, foundProduct.precio, foundProduct.stock, foundProduct.imagen, foundProduct.marca, foundProduct.industria, foundProduct.categoria);
        }
    <?php endif; ?>
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

