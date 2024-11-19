<!-- ============================================================== -->
<!-- Start Page Content here -->
<!-- ============================================================== -->

<div class="content-page">
    <div class="content">
        <div class="container-fluid">    

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="page-title">Agregar Nuevo Usuario</h4>
                    </div>
                </div>
            </div>     
            <!-- end page title --> 

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <?php if ($this->session->flashdata('error')): ?>
                                <div class="alert alert-danger" role="alert">
                                    <?= htmlspecialchars($this->session->flashdata('error')); ?>
                                </div>
                            <?php endif; ?>

                            <?php echo form_open_multipart('usuarios/agregarbd', array('class' => 'form-horizontal')); ?>
                                <div class="form-group row mb-3">
                                    <label for="nombres" class="col-3 col-form-label">Nombres</label>
                                    <div class="col-9">
                                        <input type="text" name="nombres" id="nombres" class="form-control" placeholder="Ingrese sus nombres" required>
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label for="apellidoPaterno" class="col-3 col-form-label">Apellido Paterno</label>
                                    <div class="col-9">
                                        <input type="text" name="apellidoPaterno" id="apellidoPaterno" class="form-control" placeholder="Ingrese su apellido paterno" required>
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label for="apellidoMaterno" class="col-3 col-form-label">Apellido Materno</label>
                                    <div class="col-9">
                                        <input type="text" name="apellidoMaterno" id="apellidoMaterno" class="form-control" placeholder="Ingrese su apellido materno">
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label for="carnet" class="col-3 col-form-label">Carnet</label>
                                    <div class="col-9">
                                        <input type="text" name="carnet" id="carnet" class="form-control" placeholder="Ingrese su carnet" required>
                                    </div>
                                </div>
                                
                                <div class="form-group row mb-3">
                                    <label for="fechaNacimiento" class="col-3 col-form-label">Fecha de Nacimiento</label>
                                    <div class="col-9">
                                        <input type="date" name="fechaNacimiento" id="fechaNacimiento" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label for="sexo" class="col-3 col-form-label">Sexo</label>
                                    <div class="col-9">
                                        <select name="sexo" id="sexo" class="form-control">
                                            <option value="">Seleccione su sexo</option>
                                            <option value="M">Masculino</option>
                                            <option value="F">Femenino</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label for="email" class="col-3 col-form-label">Email</label>
                                    <div class="col-9">
                                        <input type="email" name="email" id="email" class="form-control" placeholder="Ingrese su email" required>
                                    </div>
                                </div>
                                <!-- Campo de profesión dinámico según rol -->


<?php if($es_admin): ?>
<div class="form-group">
    <label for="rol">Rol</label>
    <select class="form-control" id="rol" name="rol" required onchange="toggleProfesionField()">
        <option value="">Seleccione un rol</option>
        <option value="administrador">Administrador</option>
        <option value="encargado">Encargado</option>
        <option value="lector">Lector</option>
    </select>
    <?php echo form_error('rol', '<div class="text-danger">', '</div>'); ?>
</div>
<div class="form-group">
    <label for="profesion">Profesión</label>
    <?php if ($this->input->post('rol') == 'lector' || (!$es_admin)): ?>
        <!-- Para lectores: mostrar select con opciones predefinidas -->
        <?php echo form_dropdown('profesion', $profesiones_lector, set_value('profesion'), 'class="form-control" required'); ?>
    <?php else: ?>
        <!-- Para admin/encargado: campo de texto -->
        <input type="text" 
               class="form-control" 
               id="profesion" 
               name="profesion" 
               value="<?php echo set_value('profesion'); ?>"
               required>
    <?php endif; ?>
    <?php echo form_error('profesion', '<div class="text-danger">', '</div>'); ?>
</div>

                                <?php else: ?>
                                <input type="hidden" name="rol" value="lector">
                                <?php endif; ?>

                                <div class="form-group row mb-3">
                                    <div class="col-9 offset-3">
                                        <p class="text-muted">El nombre de usuario y la contraseña se generarán automáticamente y se enviarán por correo electrónico al nuevo usuario.</p>
                                    </div>
                                </div>
                                
                                <div class="form-group row mb-3">
                                    <div class="col-9 offset-3">
                                        <button type="submit" class="btn btn-primary">Agregar Usuario</button>
                                        <a href="<?php echo site_url('usuarios/mostrar'); ?>" class="btn btn-secondary">Cancelar</a>
                                    </div>
                                </div>
                            <?php echo form_close(); ?>
                        </div> <!-- end card-body -->
                    </div> <!-- end card -->
                </div> <!-- end col -->
            </div> <!-- end row -->
<!-- ============================================================== -->
<!-- End Page content -->
<!-- ============================================================== -->
        </div>   
    </div> <!-- container -->
</div> <!-- content -->
<script>
function toggleProfesionField() {
    var rolSelect = document.getElementById('rol');
    var profesionDiv = document.getElementById('profesion').parentElement;
    var profesionInput = document.getElementById('profesion');
    var profesionSelect = document.createElement('select');
    
    if (rolSelect.value === 'lector') {
        // Convertir a select con opciones predefinidas
        profesionSelect.className = 'form-control';
        profesionSelect.id = 'profesion';
        profesionSelect.name = 'profesion';
        profesionSelect.required = true;
        
        // Agregar opciones
        var opciones = <?php echo json_encode($profesiones_lector); ?>;
        for (var valor in opciones) {
            var option = document.createElement('option');
            option.value = valor;
            option.text = opciones[valor];
            profesionSelect.appendChild(option);
        }
        
        profesionInput.parentNode.replaceChild(profesionSelect, profesionInput);
    } else {
        // Convertir a input text
        var inputText = document.createElement('input');
        inputText.type = 'text';
        inputText.className = 'form-control';
        inputText.id = 'profesion';
        inputText.name = 'profesion';
        inputText.required = true;
        
        if (document.getElementById('profesion').tagName === 'SELECT') {
            document.getElementById('profesion').parentNode.replaceChild(inputText, document.getElementById('profesion'));
        }
    }
}

// Ejecutar al cargar la página para establecer el estado inicial
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('rol')) {
        toggleProfesionField();
    }
});
</script>