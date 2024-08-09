
		<h1>Lista de Estudiantes</h1>
        <br>
		<a href="<?php echo base_url(); ?>index.php/estudiante/deshabilitados">
        <button type="button" class="btn btn-warning">ver deshabilitados</button>
        </a>

		<br>

        <a href="<?php echo base_url(); ?>index.php/estudiante/agregar">
        <button type="button" class="btn btn-primary">Agregar estudiante</button>
        </a>
		<table class="table">
			<thead>
				<th>No.</th>
				<th>Nombre</th>
				<th>Primer Apellido</th>
				<th>Segundo Apellido</th>
				<th>Nota</th>
				<th>Modificar</th>
				
			</thead>
			<tbody>
				<?php
				$contador=1;
				foreach($personas->result() as $row)
				{
				?>
				<tr>
					<td><?php echo $contador; ?></td>
					<td><?php echo $row->ciNit; ?></td>
					<td><?php echo $row->razonSocial; ?></td>
					<td><?php echo $row->estado; ?></td>
					<td><?php echo $row->fechaRegistro; ?></td>
					<td><?php echo $row->fechaActualizacion; ?></td>
					
					
				</tr>
				<?php
				$contador++;
				}
				?>
			</tbody>
		</table>