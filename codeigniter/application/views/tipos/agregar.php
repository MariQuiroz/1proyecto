<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">Agregar Nuevo Tipo</h4>
                            <p class="text-muted font-13 mb-4">
                                Complete el formulario para agregar un nuevo tipo de publicación. Los campos marcados con (*) son obligatorios.
                            </p>

                            <?php if($this->session->flashdata('error')): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <?php echo $this->session->flashdata('error'); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>

                            <?php echo form_open('tipos/agregarbd', ['id' => 'formTipo', 'class' => 'needs-validation', 'novalidate' => '']); ?>
                                <div class="mb-3">
                                    <label for="nombreTipo" class="form-label">Nombre del Tipo (*)</label>
                                    <input type="text" 
                                           class="form-control <?php echo form_error('nombreTipo') ? 'is-invalid' : ''; ?>" 
                                           id="nombreTipo" 
                                           name="nombreTipo" 
                                           value="<?php echo set_value('nombreTipo'); ?>" 
                                           required
                                           minlength="2"
                                           maxlength="100"
                                           placeholder="Ingrese el nombre del tipo">
                                    <div class="invalid-feedback" id="nombreTipoError">
                                        Por favor ingrese un nombre válido (2-100 caracteres).
                                    </div>
                                    <div class="text-muted small mt-1">
                                        <span id="charCount">100</span> caracteres restantes
                                    </div>
                                    <small class="form-text text-muted">
                                        El nombre puede contener letras, números, acentos y espacios.
                                    </small>
                                </div>

                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary" id="btnSubmit">
                                        <i class="fe-save me-1"></i> Guardar
                                    </button>
                                    <a href="<?php echo site_url('tipos'); ?>" class="btn btn-secondary ms-2">
                                        <i class="fe-x me-1"></i> Cancelar
                                    </a>
                                </div>
                            <?php echo form_close(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formTipo');
    const nombreTipo = document.getElementById('nombreTipo');
    const errorDiv = document.getElementById('nombreTipoError');
    const charCount = document.getElementById('charCount');
    const submitBtn = document.getElementById('btnSubmit');
    let isValid = false;

    function actualizarContador() {
        const restantes = 100 - nombreTipo.value.length;
        charCount.textContent = restantes;
        if (restantes < 0) {
            charCount.classList.add('text-danger');
        } else {
            charCount.classList.remove('text-danger');
        }
    }

    function validarNombre(valor) {
        // Permitir espacios, letras con acentos y números
        valor = valor.replace(/[^A-ZÁÉÍÓÚÜÑa-záéíóúüñ0-9\s]/g, '');
        
        // Reemplazar múltiples espacios con uno solo
        valor = valor.replace(/\s+/g, ' ');
        
        return valor;
    }

    function validarCampo() {
        const valor = nombreTipo.value;
        isValid = true;

        if (!valor.trim()) {
            errorDiv.textContent = 'El nombre del tipo es obligatorio.';
            isValid = false;
        } else if (valor.trim().length < 2) {
            errorDiv.textContent = 'El nombre debe tener al menos 2 caracteres.';
            isValid = false;
        } else if (valor.length > 100) {
            errorDiv.textContent = 'El nombre no puede exceder los 100 caracteres.';
            isValid = false;
        }

        if (!isValid) {
            nombreTipo.classList.add('is-invalid');
            nombreTipo.classList.remove('is-valid');
        } else {
            nombreTipo.classList.remove('is-invalid');
            nombreTipo.classList.add('is-valid');
            errorDiv.textContent = '';
        }

        actualizarContador();
        submitBtn.disabled = !isValid;
        return isValid;
    }

    // Evento input para validación en tiempo real
    nombreTipo.addEventListener('input', function() {
        this.value = validarNombre(this.value);
        validarCampo();
    });

    // Validación al perder el foco
    nombreTipo.addEventListener('blur', function() {
        this.value = this.value.trim();
        validarCampo();
    });

    // Validación al enviar el formulario
    form.addEventListener('submit', function(event) {
        if (!validarCampo()) {
            event.preventDefault();
            event.stopPropagation();
            nombreTipo.focus();
        } else {
            // Asegurar que el valor esté en mayúsculas antes de enviar
            nombreTipo.value = nombreTipo.value.toUpperCase();
        }
        form.classList.add('was-validated');
    });

    // Inicializar contador
    actualizarContador();
});
</script>