<br><br>
<h1>Agregar Estudiantes</h1>
<br>

<?php
echo form_open_multipart("estudiante/agregarbd");
?>

<input type="text" class="form-control" name="nombre" placeholder="Escribe nombre" required>
<input type="text" class="form-control" name="apellido1" placeholder="Escribe apellido paterno" minlength="3" maxlength="20" required>
<input type="text" class="form-control" name="apellido2" placeholder="Escribe apellido materno" required>
<input type="number" min="0" max="100" class="form-control" name="nota" placeholder="Escribe nota" required>
<button type="submit" class="btn btn-success">Agregar estudiante</button>

<?php
echo form_close();
?>
