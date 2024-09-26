
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Agregar Nuevo Usuario</h4>
                    
                    <?php if($this->session->flashdata('error')): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $this->session->flashdata('error'); ?>
                        </div>
                    <?php endif; ?>

                    <?php echo form_open_multipart('usuarios/agregarbd'); ?>
                        <div class="form-group">
                            <label for="nombres">Nombres</label>
                            <input type="text" name="nombres" id="nombres" class="form-control" placeholder="Ingrese sus nombres" required>
                        </div>
                        <div class="form-group">
                            <label for="apellidoPaterno">Apellido Paterno</label>
                            <input type="text" name="apellidoPaterno" id="apellidoPaterno" class="form-control" placeholder="Ingrese su apellido paterno" required>
                        </div>
                        <div class="form-group">
                            <label for="apellidoMaterno">Apellido Materno</label>
                            <input type="text" name="apellidoMaterno" id="apellidoMaterno" class="form-control" placeholder="Ingrese su apellido materno">
                        </div>
                        <div class="form-group">
                            <label for="carnet">Carnet</label>
                            <input type="text" name="carnet" id="carnet" class="form-control" placeholder="Ingrese su carnet" required>
                        </div>
                        <div class="form-group">
                            <label for="profesion">Profesión</label>
                            <select name="profesion" id="profesion" class="form-control">
                                <option value="">Seleccione su profesión</option>
                                <option value="Estudiante Umss">Estudiante UMSS</option>
                                <option value="Docente Umss">Docente UMSS</option>
                                <option value="Investigador">Investigador</option>
                                <option value="Publico en General">Público en General</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="fechaNacimiento">Fecha de Nacimiento</label>
                            <input type="date" name="fechaNacimiento" id="fechaNacimiento" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="sexo">Sexo</label>
                            <select name="sexo" id="sexo" class="form-control">
                                <option value="">Seleccione su sexo</option>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="Ingrese su email" required>
                        </div>
                        <div class="form-group">
                            <label for="username">Nombre de Usuario</label>
                            <input type="text" name="username" id="username" class="form-control" placeholder="Ingrese su nombre de usuario" required>
                        </div>
                        <?php if ($this->session->userdata('rol') === 'administrador'): ?>
                        <div class="form-group">
                            <label for="rol">Rol</label>
                            <select name="rol" id="rol" class="form-control" required>
                                <option value="">Seleccione el rol</option>
                                <option value="administrador">Administrador</option>
                                <option value="encargado">Encargado</option>
                                <option value="lector">Lector</option>
                            </select>
                        </div>
                        <?php else: ?>
                        <input type="hidden" name="rol" value="lector">
                        <?php endif; ?>
                        <div class="form-group">
                            <label for="foto">Foto de Perfil</label>
                            <input type="file" name="foto" id="foto" class="form-control-file">
                        </div>
                        <button type="submit" class="btn btn-primary">Agregar Usuario</button>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>
</div>