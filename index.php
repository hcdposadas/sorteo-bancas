<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Sistema de Sorteo de Bancas - Honorable Concejo Deliberante de Posadas">
    <meta name="author" content="HCD Posadas">
    <link rel="icon" href="favicon.png">
    <title>Sorteo de Bancas - HCD Posadas</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="./dist/bundle.js"></script>
    
    <style>
        :root {
            --primary-color: #1e40af;
            --primary-hover: #1d4ed8;
            --secondary-color: #64748b;
            --success-color: #059669;
            --warning-color: #d97706;
            --danger-color: #dc2626;
            --bg-color: #f8fafc;
            --card-bg: #ffffff;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border-color: #e2e8f0;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --radius-sm: 0.375rem;
            --radius-md: 0.5rem;
            --radius-lg: 0.75rem;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            font-size: 0.875rem;
            line-height: 1.5;
            color: var(--text-primary);
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
            font-weight: 400;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        
        /* Header */
        .header {
            text-align: center;
            padding: 3rem 0 2rem;
        }
        
        .header-logo {
            width: 120px;
            height: 120px;
            margin-bottom: 1.5rem;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-md);
            object-fit: contain;
            background: white;
            padding: 0.5rem;
        }
        
        .header-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            letter-spacing: -0.025em;
        }
        
        .header-subtitle {
            font-size: 1.125rem;
            color: var(--text-secondary);
            font-weight: 400;
        }
        
        /* Cards */
        .card {
            background: var(--card-bg);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid var(--border-color);
            transition: box-shadow 0.2s ease;
        }
        
        .card:hover {
            box-shadow: var(--shadow-lg);
        }
        
        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .card-title i {
            color: var(--primary-color);
            font-size: 1.125rem;
        }
        
        /* Form Elements */
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }
        
        .form-label i {
            color: var(--primary-color);
            margin-right: 0.5rem;
            font-size: 0.875rem;
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            font-size: 0.875rem;
            font-family: inherit;
            transition: all 0.2s ease;
            background: var(--card-bg);
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgb(30 64 175 / 0.1);
        }
        
        .form-control:disabled {
            background: var(--bg-color);
            color: var(--text-secondary);
            cursor: not-allowed;
        }
        
        /* File Upload */
        .file-upload {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 2rem;
            border: 2px dashed var(--border-color);
            border-radius: var(--radius-lg);
            background: var(--bg-color);
            transition: all 0.2s ease;
            cursor: pointer;
        }
        
        .file-upload:hover {
            border-color: var(--primary-color);
            background: rgb(30 64 175 / 0.02);
        }
        
        .file-upload.dragover {
            border-color: var(--primary-color);
            background: rgb(30 64 175 / 0.05);
        }
        
        .file-upload input[type="file"] {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        
        .file-upload-icon {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .file-upload-text {
            font-size: 0.875rem;
            color: var(--text-primary);
            font-weight: 500;
            margin-bottom: 0.25rem;
        }
        
        .file-upload-subtext {
            font-size: 0.75rem;
            color: var(--text-secondary);
        }
        
        /* Grid */
        .grid {
            display: grid;
            gap: 1.5rem;
        }
        
        .grid-2 {
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        }
        
        /* Button */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: var(--radius-md);
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            font-family: inherit;
        }
        
        .btn-primary {
            background: var(--primary-color);
            color: white;
            box-shadow: var(--shadow-sm);
        }
        
        .btn-primary:hover {
            background: var(--primary-hover);
            box-shadow: var(--shadow-md);
            transform: translateY(-1px);
        }
        
        .btn-primary:active {
            transform: translateY(0);
        }
        
        .btn-lg {
            padding: 1rem 2rem;
            font-size: 1rem;
            font-weight: 600;
        }
        
        /* Table */
        .table-container {
            overflow-x: auto;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-color);
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }
        
        .table th {
            background: var(--bg-color);
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--text-primary);
            border-bottom: 1px solid var(--border-color);
            white-space: nowrap;
        }
        
        .table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-primary);
        }
        
        .table tbody tr:hover {
            background: var(--bg-color);
        }
        
        .table tbody tr:last-child td {
            border-bottom: none;
        }
        
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            background: var(--bg-color);
            color: var(--text-secondary);
        }
        
        /* Footer */
        .footer {
            text-align: center;
            padding: 2rem 0;
            color: var(--text-secondary);
            font-size: 0.75rem;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .header {
                padding: 2rem 0 1.5rem;
            }
            
            .header-title {
                font-size: 1.5rem;
            }
            
            .card {
                padding: 1.5rem;
            }
            
            .grid-2 {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <img src="./escudo.webp" alt="HCD Posadas" class="header-logo">
            <h1 class="header-title">Sorteo de Bancas</h1>
            <p class="header-subtitle">Honorable Concejo Deliberante de Posadas</p>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container">
        <!-- Configuration Card -->
        <div class="card">
            <h2 class="card-title">
                <i class="fas fa-cog"></i>
                Configuración del Sorteo
            </h2>
            
            <form action="resultados.php" method="POST" enctype="multipart/form-data">
                <!-- File Upload -->
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-file-upload"></i>
                        Archivo de Participantes
                    </label>
                    <div class="file-upload" id="fileUpload">
                        <input type="file" name="archivo" id="archivo" accept=".xlsx,.csv" required>
                        <div class="file-upload-icon">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <div class="file-upload-text">Seleccionar archivo</div>
                        <div class="file-upload-subtext">Formato Excel (.xlsx) o CSV</div>
                    </div>
                </div>
                
                <!-- Grid for inputs -->
                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-star"></i>
                            Cantidad de Titulares
                        </label>
                        <input type="number" name="titulares" class="form-control" value="18" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-users"></i>
                            Cantidad de Suplentes
                        </label>
                        <input type="number" name="suplentes" class="form-control" value="18" readonly>
                    </div>
                </div>
                
                <!-- Hidden total field -->
                <input type="hidden" name="total" id="total" value="0">
                
                <!-- Submit Button -->
                <div style="text-align: center; margin-top: 2rem;">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-random"></i>
                        Realizar Sorteo
                    </button>
                </div>
            </form>
        </div>

        <!-- Concejales Card -->
        <div class="card">
            <h2 class="card-title">
                <i class="fas fa-users"></i>
                Concejales de Referencia
            </h2>
            
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="60px">#</th>
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
                        foreach ($concejales as $n => $concejal) {
                            echo '<tr>';
                            echo '<td><span class="badge">' . $n . '</span></td>';
                            echo '<td>' . $concejal . '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Honorable Concejo Deliberante de Posadas | Todos los derechos reservados</p>
        </div>
    </footer>

    <script>
        // File upload functionality
        document.addEventListener('DOMContentLoaded', function() {
            const fileUpload = document.getElementById('fileUpload');
            const fileInput = document.getElementById('archivo');
            const uploadText = document.querySelector('.file-upload-text');
            const uploadSubtext = document.querySelector('.file-upload-subtext');
            
            // Handle file selection
            fileInput.addEventListener('change', function(e) {
                if (e.target.files.length > 0) {
                    const fileName = e.target.files[0].name;
                    uploadText.textContent = fileName;
                    uploadSubtext.textContent = 'Archivo seleccionado';
                    
                    // Send to counting script
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
                                
                            } catch (e) {
                                updateCounter('Error', 'text-danger', 'fa-times-circle', 0);
                            }
                        } else {
                            updateCounter('Error', 'text-danger', 'fa-times-circle', 0);
                        }
                    };
                    xhr.onerror = function() {
                        updateCounter('Error', 'text-danger', 'fa-times-circle', 0);
                    };
                    xhr.send(formData);
                }
            });
            
            // Drag and drop
            fileUpload.addEventListener('dragover', function(e) {
                e.preventDefault();
                fileUpload.classList.add('dragover');
            });
            
            fileUpload.addEventListener('dragleave', function(e) {
                e.preventDefault();
                fileUpload.classList.remove('dragover');
            });
            
            fileUpload.addEventListener('drop', function(e) {
                e.preventDefault();
                fileUpload.classList.remove('dragover');
                
                if (e.dataTransfer.files.length > 0) {
                    fileInput.files = e.dataTransfer.files;
                    const event = new Event('change', { bubbles: true });
                    fileInput.dispatchEvent(event);
                }
            });
            
            function updateCounter(text, textColor, iconClass, count) {
                document.getElementById('total').value = text;
            }
        });
    </script>
</body>
</html>
