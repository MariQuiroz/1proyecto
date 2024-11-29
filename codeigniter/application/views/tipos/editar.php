<!-- Start Page Content here -->
<div class="content-page">
    <div class="content">
        <!-- Start Content-->
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">Editar Tipo</h4>
                            <p class="text-muted font-13 mb-4">
                                Modifique el nombre del tipo de publicación.
                            </p>

                            <?php if($this->session->flashdata('error')): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <?php echo $this->session->flashdata('error'); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>

                            <?php echo form_open('tipos/editarbd', ['id' => 'formEditarTipo', 'class' => 'needs-validation']); ?>
                                <input type="hidden" name="idTipo" value="<?php echo $tipo->idTipo; ?>">
                                <div class="mb-3">
                                    <label for="nombreTipo" class="form-label">Nombre del Tipo</label>
                                    <input type="text" 
                                           class="form-control <?php echo form_error('nombreTipo') ? 'is-invalid' : ''; ?>" 
                                           id="nombreTipo" 
                                           name="nombreTipo" 
                                           value="<?php echo set_value('nombreTipo', $tipo->nombreTipo); ?>"
                                           pattern="^[A-ZÁÉÍÓÚÜÑa-záéíóúüñ0-9\s]+$"
                                           maxlength="100"
                                           minlength="2"
                                           required>
                                    <?php if(form_error('nombreTipo')): ?>
                                        <div class="invalid-feedback">
                                            <?php echo form_error('nombreTipo'); ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="invalid-feedback">
                                            Por favor ingrese un nombre válido (solo letras, números y espacios).
                                        </div>
                                    <?php endif; ?>
                                    <small class="form-text text-muted">
                                        El nombre debe tener entre 2 y 100 caracteres, y solo puede contener letras, números y espacios.
                                    </small>
                                </div>
                                <div class="mb-3">
                                    <button type="submit" class="btn btn-primary">Actualizar Tipo</button>
                                    <a href="<?php echo site_url('tipos'); ?>" class="btn btn-secondary" id="btnCancelar">Cancelar</a>
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
(function() {
    'use strict';
    
    // Validación del formulario
    var form = document.getElementById('formEditarTipo');
    var nombreInput = document.getElementById('nombreTipo');
    
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    });

    // Validación en tiempo real del campo nombre
    nombreInput.addEventListener('input', function() {
        var valor = this.value;
        var regex = /^[A-ZÁÉÍÓÚÜÑa-záéíóúüñ0-9\s]+$/;
        
        if (valor.length < 2) {
            this.setCustomValidity('El nombre debe tener al menos 2 caracteres');
        } else if (valor.length > 100) {
            this.setCustomValidity('El nombre no puede exceder los 100 caracteres');
        } else if (!regex.test(valor)) {
            this.setCustomValidity('Solo se permiten letras, números y espacios');
        } else {
            this.setCustomValidity('');
        }
    });

    // Confirmación al cancelar
    document.getElementById('btnCancelar').addEventListener('click', function(e) {
        var formChanged = false;
        var originalValue = '<?php echo htmlspecialchars($tipo->nombreTipo); ?>';
        
        if (nombreInput.value !== originalValue) {
            formChanged = true;
        }

        if (formChanged) {
            if (!confirm('¿Estás seguro de que deseas cancelar? Los cambios no guardados se perderán.')) {
                e.preventDefault();
            }
        }
    });
})();
</script>