<br><br>
<h1>Modificar Estudiantes</h1>
<br>

<?php
foreach ($infoestudiante->result() as $row)
{
?>
<?php
echo form_open_multipart("estudiante/modificarbd");
?>
<input type="hidden" class="form-control" name="idestudiante" value="<?php echo $row->idEstudiante; ?>">
<input type="text" class="form-control" name="nombre" placeholder="Escribe nombre" value="<?php echo $row->nombre; ?>"required>
<input type="text" class="form-control" name="apellido1" placeholder="Escribe apellido paterno" minlength="3" maxlength="20" value="<?php echo $row->primerApellido; ?>" required>
<input type="text" class="form-control" name="apellido2" placeholder="Escribe apellido materno" value="<?php echo $row->segundoApellido; ?>" required>
<input type="number" min="0" max="100" class="form-control" name="nota" placeholder="Escribe nota" value="<?php echo $row->nota; ?>" required>
<button type="submit" class="btn btn-success">Modificar estudiante</button>

<?php
echo form_close();
}
?>