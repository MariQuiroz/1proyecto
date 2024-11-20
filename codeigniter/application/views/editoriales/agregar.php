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

                            <?php echo form_open('editoriales/agregarbd', ['id' => 'formEditorial', 'class' => 'needs-validation']); ?>
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
                                    <div class="invalid-feedback">
                                        Por favor ingrese un nombre válido (2-100 caracteres).
                                    </div>
                                    <small class="form-text text-muted">
                                        El nombre puede contener letras, números, acentos y espacios.
                                    </small>
                                </div>

                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary">
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
    const nombreEditorial = document.getElementById('nombreEditorial');
    
    nombreEditorial.addEventListener('input', function() {
        let valor = this.value;
        
        // Permitir letras, números, acentos y espacios
        valor = valor.replace(/[^a-zA-Z0-9áéíóúÁÉÍÓÚüÜñÑ\s]/g, '');
        
        // Eliminar espacios múltiples
        valor = valor.replace(/\s+/g, ' ');
        
        this.value = valor;

        // Validación de longitud
        if (valor.length < 2) {
            this.setCustomValidity('El nombre debe tener al menos 2 caracteres');
        } else if (valor.length > 100) {
            this.setCustomValidity('El nombre no puede exceder los 100 caracteres');
        } else {
            this.setCustomValidity('');
        }
    });

    // Prevenir envío del formulario si hay errores
    const form = document.getElementById('formEditorial');
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    });
});
</script>