<br><br>
<h1>Agregar Paises</h1>
<br>

<?php
echo form_open_multipart("pais/agregarbd");
?>

<input type="text" class="form-control" name="pais" placeholder="Escribe pais" required>
<input type="number" class="form-control" name="superficie" placeholder="Escribe superficie" required>
<input type="number" class="form-control" name="poblacion" placeholder="Escribe poblacion" required>
<button type="submit" class="btn btn-success">Agregar pais</button>

<?php
echo form_close();
?>
