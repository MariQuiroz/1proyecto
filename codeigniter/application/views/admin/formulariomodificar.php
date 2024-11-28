<div class="content-page">
    <div class="content">
<div class="container-fluid"> 
  <div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Modificar Usuario</h4>
        </div>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <?php if ($this->session->flashdata('error')): ?>
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  <?= htmlspecialchars($this->session->flashdata('error')); ?>
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                  </button>
              </div>
          <?php endif; ?>
      
          <?php if (isset($infoUsuario) && $infoUsuario): ?>
            <?= form_open_multipart('usuarios/modificarbd', array('class' => 'form-horizontal', 'id' => 'formUsuario')); ?>
            <input type="hidden" name="idUsuario" value="<?= htmlspecialchars($infoUsuario->idUsuario); ?>">

            <div class="form-group row mb-3">
              <label for="nombres" class="col-3 col-form-label">Nombres *</label>
              <div class="col-9">
                <input type="text" 
                    name="nombres" 
                    id="nombres" 
                    class="form-control <?php echo form_error('nombres') ? 'is-invalid' : ''; ?>"
                    placeholder="Ingrese sus nombres"
                    minlength="2" 
                    maxlength="20"
                    value="<?= htmlspecialchars($infoUsuario->nombres); ?>"
                    required
                    autocomplete="off"
                    title="Solo se permiten letras, espacios, tildes, apóstrofes y guiones">
                <?php echo form_error('nombres', '<div class="invalid-feedback">', '</div>'); ?>
              </div>
            </div>

            <div class="form-group row mb-3">
              <label for="apellidoPaterno" class="col-3 col-form-label">Apellido Paterno *</label>
              <div class="col-9">
                <input type="text" 
                    name="apellidoPaterno" 
                    id="apellidoPaterno" 
                    class="form-control <?php echo form_error('apellidoPaterno') ? 'is-invalid' : ''; ?>"
                    placeholder="Ingrese su apellido paterno"
                    minlength="2" 
                    maxlength="25"
                    value="<?= htmlspecialchars($infoUsuario->apellidoPaterno); ?>"
                    required
                    autocomplete="off"
                    title="Solo se permiten letras, espacios, tildes, apóstrofes y guiones">
                <?php echo form_error('apellidoPaterno', '<div class="invalid-feedback">', '</div>'); ?>
              </div>
            </div>

            <div class="form-group row mb-3">
              <label for="apellidoMaterno" class="col-3 col-form-label">Apellido Materno</label>
              <div class="col-9">
                <input type="text" 
                    name="apellidoMaterno" 
                    id="apellidoMaterno" 
                    class="form-control <?php echo form_error('apellidoMaterno') ? 'is-invalid' : ''; ?>"
                    placeholder="Ingrese su apellido materno"
                    minlength="2" 
                    maxlength="25"
                    value="<?= htmlspecialchars($infoUsuario->apellidoMaterno); ?>"
                    autocomplete="off"
                    title="Solo se permiten letras, espacios, tildes, apóstrofes y guiones">
                <?php echo form_error('apellidoMaterno', '<div class="invalid-feedback">', '</div>'); ?>
              </div>
            </div>

            <div class="form-group row mb-3">
              <label for="carnet" class="col-3 col-form-label">Carnet de Identidad *</label>
              <div class="col-9">
                <input type="text" 
                    name="carnet" 
                    id="carnet" 
                    class="form-control <?php echo form_error('carnet') ? 'is-invalid' : ''; ?>"
                    placeholder="Ej: 12345678 o EX-12345678"
                    value="<?= htmlspecialchars($infoUsuario->carnet); ?>"
                    required
                    maxlength="15">
                <?php echo form_error('carnet', '<div class="invalid-feedback">', '</div>'); ?>
                <small class="form-text text-muted">
                    Formatos válidos:
                    <ul class="mb-0">
                        <li>Ciudadanos bolivianos: 4 a 9 dígitos (Ej: 12345678)</li>
                        <li>Extranjeros: 1-2 letras + guión + números (Ej: EX-12345678)</li>
                    </ul>
                </small>
              </div>
            </div>

            <div class="form-group row mb-3">
              <label for="fechaNacimiento" class="col-3 col-form-label">Fecha de Nacimiento *</label>
              <div class="col-9">
                <input type="date" 
                    name="fechaNacimiento" 
                    id="fechaNacimiento" 
                    class="form-control <?php echo form_error('fechaNacimiento') ? 'is-invalid' : ''; ?>"
                    value="<?= htmlspecialchars($infoUsuario->fechaNacimiento); ?>"
                    required
                    max="<?php echo date('Y-m-d', strtotime('-15 years')); ?>">
                <?php echo form_error('fechaNacimiento', '<div class="invalid-feedback">', '</div>'); ?>
                <small class="form-text text-muted">Debe ser mayor de 15 años.</small>
              </div>
            </div>

            <div class="form-group row mb-3">
              <label for="sexo" class="col-3 col-form-label">Sexo *</label>
              <div class="col-9">
                <select name="sexo" 
                    id="sexo" 
                    class="form-control <?php echo form_error('sexo') ? 'is-invalid' : ''; ?>"
                    required>
                    <option value="">Seleccione su sexo</option>
                    <option value="M" <?= ($infoUsuario->sexo == 'M') ? 'selected' : ''; ?>>Masculino</option>
                    <option value="F" <?= ($infoUsuario->sexo == 'F') ? 'selected' : ''; ?>>Femenino</option>
                </select>
                <?php echo form_error('sexo', '<div class="invalid-feedback">', '</div>'); ?>
              </div>
            </div>

            <div class="form-group row mb-3">
              <label for="email" class="col-3 col-form-label">Email *</label>
              <div class="col-9">
                <input type="email" 
                    name="email" 
                    id="email" 
                    class="form-control <?php echo form_error('email') ? 'is-invalid' : ''; ?>"
                    placeholder="Ingrese su email"
                    maxlength="100"
                    value="<?= htmlspecialchars($infoUsuario->email); ?>"
                    required
                    autocomplete="off">
                <?php echo form_error('email', '<div class="invalid-feedback">', '</div>'); ?>
                <small class="form-text text-muted">El email debe ser único y válido.</small>
              </div>
            </div>

            <div class="form-group row mb-3">
              <label for="username" class="col-3 col-form-label">Nombre de Usuario *</label>
              <div class="col-9">
                <input type="text" 
                    name="username" 
                    id="username" 
                    class="form-control <?php echo form_error('username') ? 'is-invalid' : ''; ?>"
                    placeholder="Ingrese su nombre de usuario"
                    minlength="3"
                    maxlength="20"
                    value="<?= htmlspecialchars($infoUsuario->username); ?>"
                    required
                    autocomplete="off">
                <?php echo form_error('username', '<div class="invalid-feedback">', '</div>'); ?>
                <small class="form-text text-muted">Solo letras y números, entre 3 y 20 caracteres.</small>
              </div>
            </div>

            <div class="form-group row mb-3">
              <label for="rol" class="col-3 col-form-label">Rol *</label>
              <div class="col-9">
                <select id="rol" 
                    name="rol" 
                    class="form-control <?php echo form_error('rol') ? 'is-invalid' : ''; ?>"
                    required
                    onchange="toggleProfesionField()">
                    <option value="">Seleccione el rol</option>
                    <option value="administrador" <?= ($infoUsuario->rol == 'administrador') ? 'selected' : ''; ?>>Administrador</option>
                    <option value="encargado" <?= ($infoUsuario->rol == 'encargado') ? 'selected' : ''; ?>>Encargado</option>
                    <option value="lector" <?= ($infoUsuario->rol == 'lector') ? 'selected' : ''; ?>>Lector</option>
                </select>
                <?php echo form_error('rol', '<div class="invalid-feedback">', '</div>'); ?>
              </div>
            </div>

            <div class="form-group row mb-3" id="profesionContainer">
              <label for="profesion" class="col-3 col-form-label">Ocupación <?= ($infoUsuario->rol == 'lector') ? '*' : ''; ?></label>
              <div class="col-9">
                <?php if ($infoUsuario->rol == 'lector'): ?>
                    <select name="profesion" 
                        id="profesion" 
                        class="form-control <?php echo form_error('profesion') ? 'is-invalid' : ''; ?>"
                        required>
                        <option value="">Seleccione una profesión</option>
                        <option value="ESTUDIANTE" <?= ($infoUsuario->profesion == 'ESTUDIANTE') ? 'selected' : ''; ?>>Estudiante Umss</option>
                        <option value="DOCENTE" <?= ($infoUsuario->profesion == 'DOCENTE') ? 'selected' : ''; ?>>Docente Umss</option>
                        <option value="INVESTIGADOR" <?= ($infoUsuario->profesion == 'INVESTIGADOR') ? 'selected' : ''; ?>>Investigador</option>
                        <option value="OTRO" <?= ($infoUsuario->profesion == 'OTRO') ? 'selected' : ''; ?>>Otro</option>
                    </select>
                <?php else: ?>
                    <input type="text" 
                        class="form-control" 
                        id="profesion" 
                        name="profesion" 
                        value="<?= htmlspecialchars($infoUsuario->profesion); ?>"
                        readonly>
                <?php endif; ?>
                <?php echo form_error('profesion', '<div class="invalid-feedback">', '</div>'); ?>
              </div>
            </div>

            <div class="form-group row mb-3">
              <div class="col-9 offset-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
                <a href="<?php echo site_url('usuarios/mostrar'); ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
              </div>
            </div>

            <?= form_close(); ?>
          <?php else: ?>
            <div class="alert alert-danger">
              <p>No se encontró información del usuario.</p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
</div>
<script>
function toggleProfesionField() {
    var rolSelect = document.getElementById('rol');
    var profesionContainer = document.getElementById('profesionContainer');
    var currentProfesion = document.getElementById('profesion');
    
    if (rolSelect.value === 'lector') {
        var profesionSelect = document.createElement('select');
        profesionSelect.className = 'form-control';
        profesionSelect.id = 'profesion';
        profesionSelect.name = 'profesion';
        profesionSelect.required = true;
        
        var opciones = <?php echo json_encode($profesiones_lector); ?>;
        var optionDefault = document.createElement('option');
        optionDefault.value = '';
        optionDefault.text = 'Seleccione una profesión';
        profesionSelect.appendChild(optionDefault);
        
        for (var valor in opciones) {
            var option = document.createElement('option');
            option.value = valor;
            option.text = opciones[valor];
            profesionSelect.appendChild(option);
        }
        
        currentProfesion.parentNode.replaceChild(profesionSelect, currentProfesion);
    } else {
        var inputText = document.createElement('input');
        inputText.type = 'text';
        inputText.className = 'form-control';
        inputText.id = 'profesion';
        inputText.name = 'profesion';
        inputText.required = true;
        inputText.maxLength = 100;
        inputText.placeholder = 'Ingrese la ocupación';
        
        if (document.getElementById('profesion').tagName === 'SELECT') {
            document.getElementById('profesion').parentNode.replaceChild(inputText, document.getElementById('profesion'));
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('rol')) {
        toggleProfesionField();
    }
});

function validarInputCarnet(event) {
    const carnet = event.target.value;
    const key = event.key;

    // No permitir espacios o Enter
    if (key === ' ' || key === 'Enter') {
        event.preventDefault();
        return false;
    }

    // Si está vacío:
    // - Permitir números (para ciudadanos bolivianos)
    // - Permitir letras (para extranjeros)
    if (carnet.length === 0) {
        if (!/[\dA-Za-z]/.test(key)) {
            event.preventDefault();
            return false;
        }
        return true;
    }

    // FORMATO BOLIVIANO: Solo números (4-9 dígitos)
    if (/^\d+$/.test(carnet)) {
        // Si ya tiene 9 dígitos, no permitir más números
        if (carnet.length >= 9) {
            event.preventDefault();
            return false;
        }
        // Si tiene menos de 9 dígitos, solo permitir números
        if (!/\d/.test(key)) {
            event.preventDefault();
            return false;
        }
        return true;
    }

    // FORMATO EXTRANJERO: Letras + guión + números
    if (/^[A-Za-z]/.test(carnet)) {
        // Si solo tiene letras (máximo 2)
        if (/^[A-Za-z]{1,2}$/.test(carnet)) {
            // Si ya tiene 2 letras, solo permitir guión
            if (carnet.length === 2 && key !== '-') {
                event.preventDefault();
                return false;
            }
            // Si tiene 1 letra, permitir letra o guión
            if (!/[A-Za-z-]/.test(key)) {
                event.preventDefault();
                return false;
            }
        }
        // Si ya tiene el guión
        else if (carnet.includes('-')) {
            // Solo permitir números después del guión
            const [prefijo, numeros] = carnet.split('-');
            if (prefijo.length > 2) {
                event.preventDefault();
                return false;
            }
            if (numeros && numeros.length >= 9) {
                event.preventDefault();
                return false;
            }
            if (!/\d/.test(key)) {
                event.preventDefault();
                return false;
            }
        }
        // Si tiene letras y no tiene guión, solo permitir guión
        else if (!/[-]/.test(key)) {
            event.preventDefault();
            return false;
        }
    }

    // Verificar longitud total máxima
    if (carnet.length >= 15) {
        event.preventDefault();
        return false;
    }

    return true;
}

// Convertir letras a mayúsculas
document.getElementById('carnet').addEventListener('input', function(e) {
    const start = e.target.selectionStart;
    const end = e.target.selectionEnd;
    e.target.value = e.target.value.toUpperCase();
    e.target.setSelectionRange(start, end);
});

// Validar pegado
function validarPegadoCarnet(event) {
    event.preventDefault();
    const texto = (event.clipboardData || window.clipboardData).getData('text').toUpperCase();
    // Patrón para ambos formatos
    const patronBoliviano = /^\d{4,9}$/;
    const patronExtranjero = /^[A-Z]{1,2}-\d{1,9}$/;
    if (patronBoliviano.test(texto) || patronExtranjero.test(texto)) {
        event.target.value = texto;
    }
}

// Convertir letras a mayúsculas
document.getElementById('carnet').addEventListener('input', function(e) {
    const start = e.target.selectionStart;
    const end = e.target.selectionEnd;
    e.target.value = e.target.value.toUpperCase();
    e.target.setSelectionRange(start, end);
});

// Validar pegado
function validarPegadoCarnet(event) {
    event.preventDefault();
    const texto = (event.clipboardData || window.clipboardData).getData('text').toUpperCase();
    // Patrón que acepta ambos formatos
    const patron = /^(\d{4,9}(-\d{1,4})?[A-Z]{0,2}|[A-Z]{1,2}(-?\d{1,4}))$/;
    if (patron.test(texto)) {
        event.target.value = texto;
    }
}

// Agregar los eventos al campo
document.addEventListener('DOMContentLoaded', function() {
    const carnetInput = document.getElementById('carnet');
    if (carnetInput) {
        carnetInput.addEventListener('keypress', validarInputCarnet);
        carnetInput.addEventListener('paste', validarPegadoCarnet);
        carnetInput.maxLength = 15;
    }
});

// También podemos agregar una función para convertir letras a mayúsculas automáticamente
document.getElementById('carnet').addEventListener('input', function(e) {
    if (/[a-z]/.test(e.target.value)) {
        const pos = e.target.selectionStart;
        e.target.value = e.target.value.toUpperCase();
        e.target.setSelectionRange(pos, pos);
    }
});
// Validación para nombres y apellidos
function validarNombresApellidos(event) {
    const patron = /^[a-zA-ZáéíóúüÁÉÍÓÚÜñÑ\s'\-]$/;
    if (!patron.test(event.key) && event.key !== 'Backspace' && event.key !== 'Tab') {
        event.preventDefault();
        return false;
    }
    return true;
}

// Función para convertir texto a mayúsculas manteniendo la posición del cursor
function convertirMayusculas(elemento) {
    const start = elemento.selectionStart;
    const end = elemento.selectionEnd;
    elemento.value = elemento.value.toUpperCase();
    elemento.setSelectionRange(start, end);
}

// Validación para email
function validarEmail(event) {
    const email = event.target.value;
    // Permitir solo caracteres válidos para email
    const patron = /^[a-zA-Z0-9@._\-]$/;
    if (!patron.test(event.key) && event.key !== 'Backspace' && event.key !== 'Tab') {
        event.preventDefault();
        return false;
    }
    return true;
}

// Validación para la profesión cuando es texto
function validarProfesion(event) {
    const patron = /^[a-zA-ZáéíóúüÁÉÍÓÚÜñÑ\s'\-]$/;
    if (!patron.test(event.key) && event.key !== 'Backspace' && event.key !== 'Tab') {
        event.preventDefault();
        return false;
    }
    return true;
}

// Inicialización de las validaciones
document.addEventListener('DOMContentLoaded', function() {
    // Nombres y apellidos
    const camposNombre = ['nombres', 'apellidoPaterno', 'apellidoMaterno'];
    camposNombre.forEach(campo => {
        const elemento = document.getElementById(campo);
        if (elemento) {
            elemento.addEventListener('keypress', validarNombresApellidos);
            elemento.addEventListener('input', function() {
                convertirMayusculas(this);
            });
            elemento.addEventListener('paste', function(e) {
                e.preventDefault();
                const texto = (e.clipboardData || window.clipboardData).getData('text');
                if (/^[a-zA-ZáéíóúüÁÉÍÓÚÜñÑ\s'\-]*$/.test(texto)) {
                    this.value = texto.toUpperCase();
                }
            });
        }
    });

    // Email
    const emailInput = document.getElementById('email');
    if (emailInput) {
        emailInput.addEventListener('keypress', validarEmail);
        emailInput.addEventListener('input', function() {
            this.value = this.value.toLowerCase();
        });
        emailInput.addEventListener('paste', function(e) {
            e.preventDefault();
            const texto = (e.clipboardData || window.clipboardData).getData('text');
            this.value = texto.toLowerCase();
        });
    }

    // Fecha de nacimiento
    const fechaNacimiento = document.getElementById('fechaNacimiento');
    if (fechaNacimiento) {
        const maxDate = new Date();
        maxDate.setFullYear(maxDate.getFullYear() - 15);
        fechaNacimiento.max = maxDate.toISOString().split('T')[0];
    }

    // Profesión (cuando es input text)
    const profesionInput = document.getElementById('profesion');
    if (profesionInput && profesionInput.tagName === 'INPUT') {
        profesionInput.addEventListener('keypress', validarProfesion);
        profesionInput.addEventListener('input', function() {
            convertirMayusculas(this);
        });
        profesionInput.addEventListener('paste', function(e) {
            e.preventDefault();
            const texto = (e.clipboardData || window.clipboardData).getData('text');
            if (/^[a-zA-ZáéíóúüÁÉÍÓÚÜñÑ\s'\-]*$/.test(texto)) {
                this.value = texto.toUpperCase();
            }
        });
    }
    const usernameInput = document.getElementById('username');
    if (usernameInput) {
        usernameInput.addEventListener('keypress', function(event) {
            const patron = /^[a-zA-Z0-9]$/;
            if (!patron.test(event.key)) {
                event.preventDefault();
                return false;
            }
            return true;
        });

        usernameInput.addEventListener('paste', function(e) {
            e.preventDefault();
            const texto = (e.clipboardData || window.clipboardData).getData('text');
            if (/^[a-zA-Z0-9]*$/.test(texto)) {
                this.value = texto.toLowerCase();
            }
        });
    }
});
</script>