<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">Agregar Nueva Editorial</h4>
                            <p class="text-muted font-13 mb-4">
                                Complete el formulario para agregar una nueva editorial. Los campos marcados con (*) son obligatorios.
                            </p>

                            <?php if(validation_errors()): ?>
                                <div class="alert alert-danger">
                                    <?php echo validation_errors(); ?>
                                </div>
                            <?php endif; ?>

                            <?php echo form_open('editoriales/agregarbd', ['id' => 'formEditorial', 'class' => 'needs-validation', 'novalidate' => '']); ?>
                                <div class="mb-3">
                                    <label for="nombreEditorial" class="form-label">Nombre de la Editorial (*)</label>
                                    <input type="text" 
                                           class="form-control <?php echo form_error('nombreEditorial') ? 'is-invalid' : ''; ?>" 
                                           id="nombreEditorial" 
                                           name="nombreEditorial" 
                                           value="<?php echo set_value('nombreEditorial'); ?>" 
                                           required
                                           minlength="2"
                                           maxlength="100"
                                           placeholder="Ingrese el nombre de la editorial">
                                    <div class="invalid-feedback" id="nombreEditorialError">
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
                                    <a href="<?php echo site_url('editoriales'); ?>" class="btn btn-secondary ms-2">
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
    const form = document.getElementById('formEditorial');
    const nombreEditorial = document.getElementById('nombreEditorial');
    const errorDiv = document.getElementById('nombreEditorialError');
    const charCount = document.getElementById('charCount');
    const submitBtn = document.getElementById('btnSubmit');
    let isValid = false;

    function actualizarContador() {
        const restantes = 100 - nombreEditorial.value.length;
        charCount.textContent = restantes;
        if (restantes < 0) {
            charCount.classList.add('text-danger');
        } else {
            charCount.classList.remove('text-danger');
        }
    }

    function validarNombre(valor) {
    // Eliminar solo caracteres realmente no permitidos, manteniendo espacios y acentos
    valor = valor.replace(/[^A-ZÁÉÍÓÚÜÑa-záéíóúüñ0-9\s]/g, '');
    
    // Reemplazar múltiples espacios con uno solo
    valor = valor.replace(/\s+/g, ' ');
    
    return valor;
}

function validarCampo() {
    const valor = nombreEditorial.value;
    isValid = true;

    if (!valor.trim()) {
        errorDiv.textContent = 'El nombre de la editorial es obligatorio.';
        isValid = false;
    } else if (valor.trim().length < 2) {
        errorDiv.textContent = 'El nombre debe tener al menos 2 caracteres.';
        isValid = false;
    } else if (valor.length > 100) {
        errorDiv.textContent = 'El nombre no puede exceder los 100 caracteres.';
        isValid = false;
    } else if (!/^[A-ZÁÉÍÓÚÜÑa-záéíóúüñ0-9\s]+$/.test(valor)) {
        errorDiv.textContent = 'El nombre solo puede contener letras, números y espacios.';
        isValid = false;
    }

    // Evento input para validación en tiempo real
    nombreEditorial.addEventListener('input', function() {
        this.value = validarNombre(this.value);
        validarCampo();
    });

    // Validación al perder el foco
    nombreEditorial.addEventListener('blur', function() {
        this.value = this.value.trim();
        validarCampo();
    });

    // Validación al enviar el formulario
    form.addEventListener('submit', function(event) {
        if (!validarCampo()) {
            event.preventDefault();
            event.stopPropagation();
            nombreEditorial.focus();
        } else {
            // Asegurar que el valor esté en mayúsculas antes de enviar
            nombreEditorial.value = nombreEditorial.value.toUpperCase();
        }
        form.classList.add('was-validated');
    });

    // Deshabilitar el botón submit si hay errores
    nombreEditorial.addEventListener('input', function() {
        submitBtn.disabled = !isValid;
    });

    // Inicializar contador
    actualizarContador();
});
</script>