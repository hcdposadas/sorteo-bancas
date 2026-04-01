<?php
/**
 * Created by PhpStorm.
 * User: matias
 * Date: 03/09/18
 * Time: 11:59
 */

?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Script para el sorteo de bancas">
    <meta name="author" content="HCD Posadas">
    <link rel="icon" href="favicon.png">

    <title>Sorteo de Bancas</title>

    <!-- Bootstrap core CSS -->
    <!--    <link href="http://localhost/sorteo-bancas/dist/bundle.js" rel="stylesheet">-->

    <script src="./dist/bundle.js"></script>

<script>
// Custom file input - Solución directa
document.addEventListener('DOMContentLoaded', function() {
    var fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(function(input) {
        input.addEventListener('change', function(e) {
            var fileName = e.target.files[0].name;
            var label = input.nextElementSibling;
            if (label && label.classList.contains('custom-file-label')) {
                label.textContent = fileName;
            }
            
            // Contar participantes del archivo usando AJAX
            if (e.target.files.length > 0) {
                updateCounter('Procesando...', 'text-warning', 'fa-spinner fa-spin', 0);
                
                var formData = new FormData();
                formData.append('archivo', e.target.files[0]);
                
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'contar.php', true);
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        try {
                            var response = JSON.parse(xhr.responseText);
                            
                            if (response.success) {
                                updateCounter(
                                    response.disponibles + ' disponibles', 
                                    'text-success', 
                                    'fa-check-circle', 
                                    response.disponibles
                                );
                            } else {
                                updateCounter(
                                    response.disponibles + ' disponibles', 
                                    'text-danger', 
                                    'fa-exclamation-triangle', 
                                    response.disponibles
                                );
                            }
                            
                            // Actualizar mensaje de ayuda
                            var totalHelp = document.getElementById('totalHelpBlock');
                            var statusDiv = document.getElementById('total-status');
                            statusDiv.textContent = response.message;
                            
                            if (response.success) {
                                statusDiv.className = 'font-weight-bold text-success';
                            } else {
                                statusDiv.className = 'font-weight-bold text-danger';
                            }
                            
                        } catch (e) {
                            updateCounter('Error', 'text-danger', 'fa-times-circle', 0);
                            document.getElementById('total-status').textContent = 'No se pudo procesar el archivo';
                            document.getElementById('total-status').className = 'font-weight-bold text-danger';
                        }
                    } else {
                        updateCounter('Error', 'text-danger', 'fa-times-circle', 0);
                        document.getElementById('total-status').textContent = 'Error de conexión';
                        document.getElementById('total-status').className = 'font-weight-bold text-danger';
                    }
                };
                xhr.onerror = function() {
                    updateCounter('Error', 'text-danger', 'fa-times-circle', 0);
                    document.getElementById('total-status').textContent = 'Error de conexión';
                    document.getElementById('total-status').className = 'font-weight-bold text-danger';
                };
                xhr.send(formData);
            }
        });
    });
    
    function updateCounter(text, textColor, iconClass, count) {
        document.getElementById('total').value = text;
        
        var icon = document.getElementById('total-icon');
        icon.innerHTML = '<i class="fa ' + iconClass + ' ' + textColor + '"></i>';
        
        // Actualizar barra de progreso
        var progressBar = document.getElementById('total-progress');
        var percentage = Math.min((count / 36) * 100, 100);
        progressBar.style.width = percentage + '%';
        progressBar.setAttribute('aria-valuenow', percentage);
        
        // Cambiar color de la barra según el progreso
        progressBar.className = 'progress-bar';
        if (percentage >= 100) {
            progressBar.classList.add('bg-success');
        } else if (percentage >= 50) {
            progressBar.classList.add('bg-info');
        } else if (percentage >= 25) {
            progressBar.classList.add('bg-warning');
        } else {
            progressBar.classList.add('bg-danger');
        }
    }
});
</script>

    <style>
        body{
            font-size: 1rem;
            font-weight: 400;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .main-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 2rem;
            margin: 2rem auto;
        }
        .text-muted{
            color: #6c757d !important;
        }
        .container {
            max-width: 900px;
        }
        .d-block.mx-auto.mb-4 {
            max-width: 120px;
            height: auto;
        }
        .header-section {
            text-align: center;
            padding-bottom: 2rem;
            border-bottom: 2px solid #f8f9fa;
            margin-bottom: 2rem;
        }
        .form-section {
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }
        .reference-section {
            background: #fff;
            border: 1px solid #dee2e6;
            padding: 1.5rem;
            border-radius: 10px;
        }
        .btn-primary {
            background: linear-gradient(45deg, #007bff, #0056b3);
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .btn-primary:hover {
            background: linear-gradient(45deg, #0056b3, #004085);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,123,255,0.3);
        }
        .form-control {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            padding: 12px;
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
        }
        .custom-file {
            border-radius: 8px;
        }
        .custom-file-input:focus ~ .custom-file-label {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
        }
        h2 {
            color: #2c3e50;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        .lead {
            color: #6c757d;
            font-size: 1.1rem;
        }
        .section-title {
            color: #495057;
            font-weight: 600;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
        }
        .section-title::before {
            content: '';
            width: 4px;
            height: 20px;
            background: #007bff;
            margin-right: 10px;
        }
    </style>

</head>

<body>

<div class="container">
    <div class="main-container">
        <!-- Header Section -->
        <div class="header-section">
            <img class="d-block mx-auto mb-4" src="./escudo.webp" alt="logo">
            <h2>Sorteo de Bancas</h2>
            <p class="lead">Para el Parlamento de la Mujer</p>
            <p>
                <small class="text-muted">Formato válido para la planilla de participantes</small><br>
                <a href="example.xlsx" class="btn btn-outline-primary btn-sm mt-2">
                    <i class="fa fa-file-excel-o"></i> Descargar Ejemplo
                </a>
            </p>
        </div>

        <!-- Form Section -->
        <div class="form-section">
            <h3 class="section-title">Configuración del Sorteo</h3>
            <form method="post" name="sorteo" enctype="multipart/form-data" action="resultados.php">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="archivo" class="font-weight-bold">
                                <i class="fa fa-upload"></i> Archivo de Participantes
                            </label>
                            <div class="custom-file">
                                <input type="file" name="archivo" class="custom-file-input" id="archivo" lang="es">
                                <label class="custom-file-label" for="archivo">Seleccionar Archivo</label>
                            </div>
                            <small class="form-text text-muted">
                                Formato Excel (.xlsx) o CSV. Mínimo 34 participantes.
                            </small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <!-- Contador oculto -->
                        <input type="hidden" name="total" id="total" value="0">
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="titulares" class="font-weight-bold">
                                <i class="fa fa-star"></i> Cantidad de Titulares
                            </label>
                            <input type="number" name="titulares" class="form-control" id="titulares"
                                   value="18" readonly>
                            <small class="form-text text-muted">Se sortearán 18 titulares</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="suplentes" class="font-weight-bold">
                                <i class="fa fa-users"></i> Cantidad de Suplentes
                            </label>
                            <input type="number" name="suplentes" class="form-control" id="suplentes"
                                   value="18" readonly>
                            <small class="form-text text-muted">Se sortearán 18 suplentes</small>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button class="btn btn-primary btn-lg" type="submit">
                        <i class="fa fa-random"></i> Realizar Sorteo
                    </button>
                </div>
            </form>
        </div>

        <!-- Reference Section -->
        <div class="reference-section">
            <h3 class="section-title">Concejales de Referencia</h3>
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th width="10%">#</th>
                                    <th>Concejal</th>
                                </tr>
                            </thead>
                            <tbody>
								<?php
								$concejales = [
									1  => 'VIGO DANIEL',
									2  => 'TRAID LAURA',
									3  => 'SCROMEDA LUCIANA',
									4  => 'ZARZA FERNANDO',
									5  => 'CARDOZO HÉCTOR',
									6  => 'MAZAL MALENA',
									7  => 'MARTINEZ ANGEL',
									8  => 'GOMEZ DE OLIVEIRA VALERIA',
									9  => 'SALOM JUDITH',
									10 => 'SAMIRA ALMIRÓN',
									11 => 'DIB JAIR',
									12 => 'FERNANDEZ MARIA ELENA',
									13 => 'ARGAÑARAZ PABLO',
									14 => 'HORIANSKI SANTIAGO',
									15 => 'PAONESA MATIAS (Defensor del Pueblo)',
									16 => 'PRENDONE MARIELA (Prosecretaria Legislativo)',
									17 => 'MOHR CAMILO (Prosecretario Administrativo)',
									18 => 'TURKIENICZ GUSTAVO (Secretario)',
								];
								foreach ( $concejales as $n => $concejal ) {
									echo '<tr>';
									echo '<td><span class="badge badge-primary">' . $n . '</span></td>';
									echo '<td>' . $concejal . '</td>';
									echo '</tr>';
								}
								?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <footer class="text-center mt-4 pt-4 border-top">
            <p class="text-muted small mb-0">
                &copy; <?php echo date('Y');?> HCD Posadas | Honorable Concejo Deliberante de Posadas
            </p>
        </footer>
    </div>
</div>

</body>
</html>
