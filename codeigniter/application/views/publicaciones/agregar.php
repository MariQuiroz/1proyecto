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
                            <h4 class="header-title">Agregar Nueva Publicación</h4>
                            <p class="text-muted font-13 mb-4">
                                Complete el formulario para agregar una nueva publicación a la hemeroteca.
                            </p>
                            <?php if ($this->session->flashdata('error')): ?>
                                <div class="alert alert-danger">
                                    <?php echo $this->session->flashdata('error'); ?>
                                </div>
                            <?php endif; ?>

                            <?php echo form_open_multipart('publicaciones/agregarbd', ['id' => 'formPublicacion', 'class' => 'needs-validation', 'novalidate' => '']); ?>
                                <div class="mb-3">
                                    <label for="titulo" class="form-label">Título</label>
                                    <input type="text" class="form-control" id="titulo" name="titulo" required 
                                           minlength="3" maxlength="255" placeholder="Ingrese el título">
                                    <div class="invalid-feedback" id="titulo-error">
                                        El título debe tener entre 3 y 255 caracteres.
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="idEditorial" class="form-label">Editorial</label>
                                    <select class="form-control" id="idEditorial" name="idEditorial" required>
                                        <option value="">Seleccione una editorial</option>
                                        <?php foreach ($editoriales as $editorial): ?>
                                            <option value="<?php echo $editorial->idEditorial; ?>"><?php echo $editorial->nombreEditorial; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">
                                        Debe seleccionar una editorial.
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="idTipo" class="form-label">Tipo</label>
                                    <select class="form-control" id="idTipo" name="idTipo" required>
                                        <option value="">Seleccione un tipo</option>
                                        <?php foreach ($tipos as $tipo): ?>
                                            <option value="<?php echo $tipo->idTipo; ?>"><?php echo $tipo->nombreTipo; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">
                                        Debe seleccionar un tipo.
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="fechaPublicacion" class="form-label">Fecha de Publicación</label>
                                    <input type="date" class="form-control" id="fechaPublicacion" name="fechaPublicacion" required
                                           max="<?php echo date('Y-m-d'); ?>">
                                    <div class="invalid-feedback">
                                        La fecha de publicación es requerida y no puede ser posterior a hoy.
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="numeroPaginas" class="form-label">Número de Páginas</label>
                                    <input type="number" class="form-control" id="numeroPaginas" name="numeroPaginas" 
                                           placeholder="Número de páginas" min="1" max="1000">
                                    <div class="invalid-feedback">
                                        El número de páginas debe estar entre 1 y 1000.
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="descripcion" class="form-label">Descripción</label>
                                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3" 
                                              maxlength="500" placeholder="Ingrese una descripción"></textarea>
                                    <div class="text-muted">
                                        <small class="caracteres-restantes">500 caracteres restantes</small>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="ubicacionFisica" class="form-label">Ubicación Física</label>
                                    <select class="form-control" id="ubicacionFisica" name="ubicacionFisica" required>
                                        <option value="">Seleccione una ubicación</option>
                                        <?php foreach ($ubicaciones as $key => $value): ?>
                                            <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">
                                        Debe seleccionar una ubicación física.
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="portada" class="form-label">Portada</label>
                                    <input type="file" class="form-control" id="portada" name="portada" 
                                           accept="image/jpeg,image/png,image/gif">
                                    <div class="invalid-feedback" id="portada-error">
                                        Por favor seleccione una imagen válida (JPG, PNG o GIF, máximo 2MB).
                                    </div>
                                    <small class="form-text text-muted">Formatos permitidos: JPG, JPEG, PNG, GIF. Tamaño máximo: 2MB.</small>
                                </div>

                                <button type="submit" class="btn btn-primary">Agregar Publicación</button>
                                <a href="<?php echo site_url('publicaciones'); ?>" class="btn btn-secondary">Cancelar</a>
                            <?php echo form_close(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- container -->
    </div> <!-- content -->
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formPublicacion');
    const descripcion = document.getElementById('descripcion');
    const caracteresRestantes = document.querySelector('.caracteres-restantes');

    // Validación de la fecha de publicación
    const fechaPublicacion = document.getElementById('fechaPublicacion');
    const maxDate = new Date().toISOString().split('T')[0];
    fechaPublicacion.setAttribute('max', maxDate);

    // Contador de caracteres para la descripción
    descripcion.addEventListener('input', function() {
        const restantes = 500 - this.value.length;
        caracteresRestantes.textContent = `${restantes} caracteres restantes`;
    });

    // Validación del archivo de portada
    const portada = document.getElementById('portada');
    portada.addEventListener('change', function() {
        const file = this.files[0];
        const portadaError = document.getElementById('portada-error');
        
        if (file) {
            // Validar tipo de archivo
            const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
            if (!validTypes.includes(file.type)) {
                this.classList.add('is-invalid');
                portadaError.textContent = 'El archivo debe ser una imagen (JPG, PNG o GIF)';
                this.value = '';
                return;
            }

            // Validar tamaño (2MB = 2 * 1024 * 1024 bytes)
            if (file.size > 2 * 1024 * 1024) {
                this.classList.add('is-invalid');
                portadaError.textContent = 'El archivo no debe superar los 2MB';
                this.value = '';
                return;
            }

            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        }
    });

    // Validación del título
    const titulo = document.getElementById('titulo');
    titulo.addEventListener('input', function() {
        const tituloError = document.getElementById('titulo-error');
        if (this.value.length < 3) {
            this.classList.add('is-invalid');
            tituloError.textContent = 'El título debe tener al menos 3 caracteres';
        } else if (this.value.length > 255) {
            this.classList.add('is-invalid');
            tituloError.textContent = 'El título no debe superar los 255 caracteres';
        } else {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        }
    });

    // Validación del número de páginas
    const numeroPaginas = document.getElementById('numeroPaginas');
    numeroPaginas.addEventListener('input', function() {
        const valor = parseInt(this.value);
        if (valor < 1 || valor > 1000) {
            this.classList.add('is-invalid');
        } else {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        }
    });

    // Validación al enviar el formulario
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }

        const camposRequeridos = form.querySelectorAll('select[required], input[required]');
        camposRequeridos.forEach(campo => {
            if (!campo.value) {
                campo.classList.add('is-invalid');
            }
        });

        form.classList.add('was-validated');
    });

    // Validación de selects al cambiar
    const selects = form.querySelectorAll('select');
    selects.forEach(select => {
        select.addEventListener('change', function() {
            if (this.value) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
            }
        });
    });
});
function convertToUpperCase(input) {
    input.value = input.value.toUpperCase();
}

// Convertir a mayúsculas los campos de texto mientras el usuario escribe
const camposTexto = ['titulo', 'descripcion'];
camposTexto.forEach(campo => {
    const elemento = document.getElementById(campo);
    if (elemento) {
        elemento.addEventListener('input', function() {
            convertToUpperCase(this);
            
            // Actualizar contador de caracteres para descripción
            if (campo === 'descripcion') {
                const restantes = 500 - this.value.length;
                document.querySelector('.caracteres-restantes').textContent = 
                    `${restantes} caracteres restantes`;
            }
        });
    }
});

// Convertir a mayúsculas antes de enviar el formulario
form.addEventListener('submit', function(event) {
    if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
    } else {
        // Convertir todos los campos de texto a mayúsculas antes de enviar
        camposTexto.forEach(campo => {
            const elemento = document.getElementById(campo);
            if (elemento) {
                elemento.value = elemento.value.toUpperCase();
            }
        });
    }

    form.classList.add('was-validated');
});

// Convertir la ubicación física a mayúsculas al cambiar
const ubicacionFisica = document.getElementById('ubicacionFisica');
if (ubicacionFisica) {
    ubicacionFisica.addEventListener('change', function() {
        this.value = this.value.toUpperCase();
    });
}
</script>

<!-- ============================================================== -->
<!-- End Page content -->
<!-- ============================================================== -->