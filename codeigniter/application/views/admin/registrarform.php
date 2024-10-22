<div class="container">
  <div class="row">
    <div class="col-md-12">

      <h2>Agregar estudiante</h2>

      <?php echo form_open_multipart('usuarios/registrarbd'); ?>

      <select name="idCarrera" class="form-control form-select form-select-lg" required>
        <option value="" disabled selected>Seleccione una...</option>
        <?php
        foreach ($infocarreras->result() as $row) {
          ?>
        <option value="<?php echo $row->idCarrera?>"><?php echo $row->carrera?></option>
          <?php
        }
        ?>
      </select>

      <input type="text" name="nombre" placeholder="Ingrese su nombre">
      <input type="text" name="primerapellido" placeholder="Ingrese su primer apellido">
      <input type="text" name="segundoapellido" placeholder="Ingrese su segundo apellido">
      
      <button type="submit" class="btn btn-primary">INSCRIBIR ESTUDIANTE</button>

      <?php form_close(); ?>
      
    </div>
  </div>
</div>