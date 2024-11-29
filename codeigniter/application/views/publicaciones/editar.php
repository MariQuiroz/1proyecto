<!-- ============================================================== -->
<!-- Start Page Content here -->
<!-- ============================================================== -->

<div class="content-page">
    <div class="content">
        <!-- Start Content-->
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">Editar Publicación</h4>
                            <p class="text-muted font-13 mb-4">
                                Modifique los campos necesarios para actualizar la información de la publicación.
                            </p>

                            <?php if ($this->session->flashdata('success')): ?>
                                <div class="alert alert-success">
                                    <?php echo $this->session->flashdata('success'); ?>
                                </div>
                            <?php elseif ($this->session->flashdata('error')): ?>
                                <div class="alert alert-danger">
                                    <?php echo $this->session->flashdata('error'); ?>
                                </div>
                            <?php endif; ?>

                            <?php echo form_open_multipart('publicaciones/modificarbd'); ?>
                                <input type="hidden" name="idPublicacion" value="<?php echo $publicacion->idPublicacion; ?>">
                                <div class="mb-3">
                                    <label for="titulo" class="form-label">Título</label>
                                    <input type="text" class="form-control" id="titulo" name="titulo" value="<?php echo htmlspecialchars($publicacion->titulo); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="idEditorial" class="form-label">Editorial</label>
                                    <select class="form-control" id="idEditorial" name="idEditorial" required>
                                        <option value="">Seleccione una editorial</option>
                                        <?php foreach ($editoriales as $editorial): ?>
                                            <option value="<?php echo $editorial->idEditorial; ?>" <?php echo ($publicacion->idEditorial == $editorial->idEditorial) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($editorial->nombreEditorial); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="idTipo" class="form-label">Tipo</label>
                                    <select class="form-control" id="idTipo" name="idTipo" required>
                                        <option value="">Seleccione un tipo</option>
                                        <?php foreach ($tipos as $tipo): ?>
                                            <option value="<?php echo $tipo->idTipo; ?>" <?php echo ($publicacion->idTipo == $tipo->idTipo) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($tipo->nombreTipo); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="fechaPublicacion" class="form-label">Fecha de Publicación</label>
                                    <input type="date" class="form-control" id="fechaPublicacion" name="fechaPublicacion" value="<?php echo $publicacion->fechaPublicacion; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="numeroPaginas" class="form-label">Número de Páginas</label>
                                    <input type="number" class="form-control" id="numeroPaginas" name="numeroPaginas" value="<?php echo $publicacion->numeroPaginas; ?>" min="1">
                                </div>
                                <div class="mb-3">
                                    <label for="descripcion" class="form-label">Descripción</label>
                                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?php echo htmlspecialchars($publicacion->descripcion); ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="ubicacionFisica" class="form-label">Ubicación Física</label>
                                    <select class="form-control" id="ubicacionFisica" name="ubicacionFisica" required>
                                        <option value="">Seleccione una ubicación</option>
                                        <?php foreach ($ubicaciones as $key => $value): ?>
                                            <option value="<?php echo $key; ?>" <?php echo ($publicacion->ubicacionFisica == $key) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($value); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="portada" class="form-label">Portada</label>
                                    <?php if (!empty($publicacion->portada)): ?>
                                        <img src="<?php echo base_url('uploads/portadas/' . $publicacion->portada); ?>" alt="Portada actual" class="img-thumbnail mb-2" style="max-width: 200px;">
                                        <p>Portada actual: <?php echo htmlspecialchars($publicacion->portada); ?></p>
                                    <?php endif; ?>
                                    <input type="file" class="form-control" id="portada" name="portada" accept="image/jpeg,image/png,image/gif">
                                    <small class="form-text text-muted">Formatos permitidos: JPG, JPEG, PNG, GIF. Tamaño máximo: 2MB. Deje este campo vacío si no desea cambiar la portada.</small>
                                </div>
                                <button type="submit" class="btn btn-primary">Actualizar Publicación</button>
                                <a href="<?php echo site_url('publicaciones'); ?>" class="btn btn-secondary">Cancelar</a>
                            <?php echo form_close(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- container -->
    </div> <!-- content -->
</div>

<!-- ============================================================== -->
<!-- End Page content -->
<!-- ============================================================== -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const maxDate = new Date().toISOString().split('T')[0];
    
    // Función para convertir texto a mayúsculas
    function convertToUpperCase(input) {
        input.value = input.value.toUpperCase();
    }

    // Validación y conversión a mayúsculas para el título
    const titulo = document.getElementById('titulo');
    titulo.addEventListener('input', function() {
        convertToUpperCase(this);
        if (this.value.length < 3) {
            this.classList.add('is-invalid');
            this.setCustomValidity('El título debe tener al menos 3 caracteres');
        } else if (this.value.length > 255) {
            this.classList.add('is-invalid');
            this.setCustomValidity('El título no debe exceder los 255 caracteres');
        } else {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
            this.setCustomValidity('');
        }
        this.reportValidity();
    });

    // Validación de la fecha de publicación
    const fechaPublicacion = document.getElementById('fechaPublicacion');
    fechaPublicacion.setAttribute('max', maxDate);
    fechaPublicacion.addEventListener('change', function() {
        if (this.value > maxDate) {
            this.classList.add('is-invalid');
            this.setCustomValidity('La fecha no puede ser posterior a hoy');
        } else {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
            this.setCustomValidity('');
        }
        this.reportValidity();
    });

    // Validación del número de páginas
    const numeroPaginas = document.getElementById('numeroPaginas');
    numeroPaginas.addEventListener('input', function() {
        const valor = parseInt(this.value);
        if (valor < 1 || valor > 1000) {
            this.classList.add('is-invalid');
            this.setCustomValidity('El número de páginas debe estar entre 1 y 1000');
        } else {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
            this.setCustomValidity('');
        }
        this.reportValidity();
    });

    // Validación y conversión a mayúsculas para la descripción
    const descripcion = document.getElementById('descripcion');
    const maxDescLength = 500;
    descripcion.setAttribute('maxlength', maxDescLength);
    descripcion.addEventListener('input', function() {
        convertToUpperCase(this);
        const remaining = maxDescLength - this.value.length;
        if (!this.nextElementSibling || !this.nextElementSibling.classList.contains('char-count')) {
            const countDiv = document.createElement('div');
            countDiv.className = 'char-count text-muted small';
            this.parentNode.insertBefore(countDiv, this.nextSibling);
        }
        this.nextElementSibling.textContent = `${remaining} caracteres restantes`;
    });

    // Validación de selects
    const selects = document.querySelectorAll('select');
    selects.forEach(select => {
        select.addEventListener('change', function() {
            if (this.value) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
                this.setCustomValidity('');
            } else {
                this.classList.add('is-invalid');
                this.setCustomValidity('Este campo es requerido');
            }
            this.reportValidity();
        });
    });

    // Validación de la portada
    const portada = document.getElementById('portada');
    portada.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const fileType = file.type;
            const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
            const maxSize = 2 * 1024 * 1024; // 2MB

            if (!validTypes.includes(fileType)) {
                this.classList.add('is-invalid');
                this.setCustomValidity('Solo se permiten archivos JPG, PNG o GIF');
                this.value = '';
            } else if (file.size > maxSize) {
                this.classList.add('is-invalid');
                this.setCustomValidity('El archivo no debe superar los 2MB');
                this.value = '';
            } else {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
                this.setCustomValidity('');
            }
            this.reportValidity();
        }
    });

    // Validación del formulario antes de enviar
    form.addEventListener('submit', function(event) {
        // Convertir todos los campos de texto a mayúsculas antes de enviar
        const textInputs = form.querySelectorAll('input[type="text"], textarea');
        textInputs.forEach(input => {
            input.value = input.value.toUpperCase();
        });

        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();

            // Mostrar mensajes de error para campos inválidos
            const invalidInputs = form.querySelectorAll(':invalid');
            invalidInputs.forEach(input => {
                input.classList.add('is-invalid');
            });
        }

        form.classList.add('was-validated');
    });
});
</script>