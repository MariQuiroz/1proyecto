
<!-- ============================================================== -->
<!-- Start Page Content here -->
<!-- ============================================================== -->

<div class="content-page">
    <div class="content">
        <!-- Start Content-->
        <div class="container-fluid overflow-auto" style="max-height: 500px;">    

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
                            <?php if($this->session->flashdata('error')): ?>
                                <div class="alert alert-danger" role="alert">
                                    <?php echo $this->session->flashdata('error'); ?>
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
                                    <label for="profesion" class="col-3 col-form-label">Profesión</label>
                                    <div class="col-9">
                                        <select name="profesion" id="profesion" class="form-control">
                                            <option value="">Seleccione su profesión</option>
                                            <option value="Estudiante Umss">Estudiante UMSS</option>
                                            <option value="Docente Umss">Docente UMSS</option>
                                            <option value="Investigador">Investigador</option>
                                            <option value="Publico en General">Público en General</option>
                                        </select>
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
                                
                                <?php if ($this->session->userdata('rol') === 'administrador'): ?>
                                <div class="form-group row mb-3">
                                    <label for="rol" class="col-3 col-form-label">Rol</label>
                                    <div class="col-9">
                                        <select name="rol" id="rol" class="form-control" required>
                                            <option value="">Seleccione el rol</option>
                                            <option value="administrador">Administrador</option>
                                            <option value="encargado">Encargado</option>
                                            <option value="lector">Lector</option>
                                        </select>
                                    </div>
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

   