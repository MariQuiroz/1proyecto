<br><br>
<h1>Modificar Paises</h1>
<br>

<?php
foreach ($infopais->result() as $row)
{
?>
<?php
echo form_open_multipart("pais/modificarbd");
?>
<input type="hidden" class="form-control" name="idpais" value="<?php echo $row->idPais; ?>">
<input type="text" class="form-control" name="pais" placeholder="Escribe nombre" value="<?php echo $row->pais; ?>"required>
<input type="number" class="form-control" name="superficie" placeholder="Escribe superficie" value="<?php echo $row->superficie; ?>" required>
<input type="number" class="form-control" name="poblacion" placeholder="Escribe poblacion" value="<?php echo $row->poblacion; ?>" required>
<button type="submit" class="btn btn-success">Modificar pais</button>

<?php
echo form_close();
}
?>