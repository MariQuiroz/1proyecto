
		<h1>Lista de Estudiantes</h1>
        <br>
		<a href="<?php echo base_url(); ?>index.php/estudiante/curso">
        <button type="button" class="btn btn-warning">ver habilitados</button>
        </a>

		<br>

        
		<table class="table">
			<thead>
				<th>No.</th>
				<th>Nombre</th>
				<th>Primer Apellido</th>
				<th>Segundo Apellido</th>
				<th>Habilitar</th>
			</thead>
			<tbody>
				<?php
				$contador=1;
				foreach($alumnos->result() as $row)
				{
				?>
				<tr>
					<td><?php echo $contador; ?></td>
					<td><?php echo $row->nombre; ?></td>
					<td><?php echo $row->primerApellido; ?></td>
					<td><?php echo $row->segundoApellido; ?></td>
		
					<td>
<?php
	echo form_open_multipart("estudiante/habilitarbd");
?>
<input type="hidden" name="idestudiante" value="<?php echo $row->idEstudiante; ?>">
<button type="submit" class="btn btn-warning">Habilitar</button>
<?php
echo form_close();
?>
					</td>
				</tr>
				<?php
				$contador++;
				}
				?>
			</tbody>
		</table>