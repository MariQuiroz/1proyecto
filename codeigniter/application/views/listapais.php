
		<h1>Lista de paises</h1>
        <br>

        <a href="<?php echo base_url(); ?>index.php/pais/agregar">
        <button type="button" class="btn btn-primary">Agregar pais</button>
        </a>
		<table class="table">
			<thead>
				<th>No.</th>
				<th>Pais</th>
				<th>Superficie</th>
				<th>Poblacion</th>
				<th>Modificar</th>
				<th>Eliminar</th>
			</thead>
			<tbody>
				<?php
				$contador=1;
				foreach($country->result() as $row)
				{
				?>
				<tr>
					<td><?php echo $contador; ?></td>
					<td><?php echo $row->pais; ?></td>
					<td><?php echo $row->superficie; ?></td>
					<td><?php echo $row->poblacion; ?></td>
					<td>
<?php
	echo form_open_multipart("pais/modificar");
?>
<input type="hidden" name="idpais" value="<?php echo $row->idPais; ?>">
<button type="submit" class="btn btn-success">Modificar</button>
<?php
echo form_close();
?>
					</td>
					<td>
<?php
	echo form_open_multipart("pais/eliminarbd");
?>
<input type="hidden" name="idpais" value="<?php echo $row->idPais; ?>">
<button type="submit" class="btn btn-danger">Eliminar</button>
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