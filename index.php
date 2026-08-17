<?php require 'sorteo_ppc.php'; ?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Sorteo General PPC - Honorable Concejo Deliberante de Posadas">
    <meta name="author" content="HCD Posadas">
    <link rel="icon" href="favicon.png">
    <title>Sorteo General PPC - HCD Posadas</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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

        /* Stats tiles */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .stat-tile {
            background: var(--bg-color);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 1rem;
            text-align: center;
        }

        .stat-number {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .stat-label {
            font-size: 0.75rem;
            color: var(--text-secondary);
        }

        /* Alerts */
        .alert {
            border-radius: var(--radius-md);
            padding: 1rem;
            margin-top: 1rem;
            font-size: 0.8125rem;
        }

        .alert-warning {
            background: rgb(217 119 6 / 0.08);
            border: 1px solid rgb(217 119 6 / 0.3);
            color: var(--warning-color);
        }

        .alert-danger {
            background: rgb(220 38 38 / 0.08);
            border: 1px solid rgb(220 38 38 / 0.3);
            color: var(--danger-color);
        }

        .alert ul {
            margin: 0.5rem 0 0 1.25rem;
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

        .btn-primary:hover:not(:disabled) {
            background: var(--primary-hover);
            box-shadow: var(--shadow-md);
            transform: translateY(-1px);
        }

        .btn-primary:disabled {
            background: var(--secondary-color);
            cursor: not-allowed;
            opacity: 0.6;
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
            vertical-align: top;
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
            white-space: nowrap;
        }

        .badge-tipo-2 { background: rgb(30 64 175 / 0.1); color: var(--primary-color); }
        .badge-tipo-3 { background: rgb(5 150 105 / 0.1); color: var(--success-color); }
        .badge-tipo-4 { background: rgb(217 119 6 / 0.1); color: var(--warning-color); }
        .badge-escuela { background: rgb(147 51 234 / 0.1); color: #9333ea; }

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
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <img src="./escudo.webp" alt="HCD Posadas" class="header-logo">
            <h1 class="header-title">Sorteo General PPC</h1>
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
                        Padrón de inscripciones individuales
                    </label>
                    <div class="file-upload" id="fileUpload">
                        <input type="file" name="archivo" id="archivo" accept=".xlsx,.csv" required>
                        <div class="file-upload-icon">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <div class="file-upload-text">Seleccionar archivo</div>
                        <div class="file-upload-subtext">Excel (.xlsx) o CSV — columnas: Nombre, Apellido, DNI, Género (V/M/O), Tipo de banca (2/3/4)</div>
                    </div>
                </div>

                <!-- Resumen del padrón -->
                <div id="resumen" style="display: none;">
                    <div class="stats-grid">
                        <div class="stat-tile">
                            <div class="stat-number" id="statTotal">0</div>
                            <div class="stat-label">Personas habilitadas</div>
                        </div>
                        <div class="stat-tile">
                            <div class="stat-number" id="statTipo2">0</div>
                            <div class="stat-label">Personas con discapacidad</div>
                        </div>
                        <div class="stat-tile">
                            <div class="stat-number" id="statTipo3">0</div>
                            <div class="stat-label">Personas mayores</div>
                        </div>
                        <div class="stat-tile">
                            <div class="stat-number" id="statTipo4">0</div>
                            <div class="stat-label">Participación general</div>
                        </div>
                        <div class="stat-tile">
                            <div class="stat-number" id="statGenero" style="font-size: 1.125rem; line-height: 2.4rem;">-</div>
                            <div class="stat-label">Mujeres / Varones / Otro</div>
                        </div>
                    </div>
                    <div id="mensajePadron"></div>
                </div>

                <!-- Submit Button -->
                <div style="text-align: center; margin-top: 2rem;">
                    <button type="submit" id="btnSortear" class="btn btn-primary btn-lg" disabled>
                        <i class="fas fa-random"></i>
                        Realizar Sorteo
                    </button>
                </div>
            </form>
        </div>

        <!-- Bancas y concejales Card -->
        <div class="card">
            <h2 class="card-title">
                <i class="fas fa-users"></i>
                Bancas y Concejales
            </h2>

            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tipo de banca</th>
                            <th>Cupo</th>
                            <th>Concejales que ceden su banca</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="badge badge-escuela">Escuelas</span> Instituciones educativas</td>
                            <td><?php echo count(PPC_ESCUELAS); ?> bancas (ya sorteadas el 28/07/2026)</td>
                            <td><?php echo htmlspecialchars(implode(', ', array_column(PPC_ESCUELAS, 'concejal'))); ?></td>
                        </tr>
                        <?php foreach (PPC_TIPOS as $tipo => $cfg): ?>
                        <tr>
                            <td><span class="badge badge-tipo-<?php echo $tipo; ?>">Tipo <?php echo $tipo; ?></span> <?php echo htmlspecialchars($cfg['nombre']); ?></td>
                            <td><?php echo $cfg['titulares']; ?> titulares y <?php echo $cfg['cotitulares']; ?> cotitulares</td>
                            <td><?php echo htmlspecialchars(implode(', ', $cfg['concejales'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <p style="margin-top: 1rem; font-size: 0.8125rem; color: var(--text-secondary);">
                <i class="fas fa-info-circle" style="color: var(--primary-color);"></i>
                Se sortean 22 personas (11 titulares y 11 cotitulares) mediante un orden general de prelación aleatorio,
                con equilibrio de género entre mujeres y varones en cada grupo (el género O ocupa cupo de varones).
                Las personas no seleccionadas integran el orden de suplencia.
            </p>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Honorable Concejo Deliberante de Posadas | Todos los derechos reservados</p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileUpload = document.getElementById('fileUpload');
            const fileInput = document.getElementById('archivo');
            const uploadText = document.querySelector('.file-upload-text');
            const uploadSubtext = document.querySelector('.file-upload-subtext');
            const resumen = document.getElementById('resumen');
            const btnSortear = document.getElementById('btnSortear');
            const mensajePadron = document.getElementById('mensajePadron');

            fileInput.addEventListener('change', function(e) {
                if (e.target.files.length === 0) return;

                uploadText.textContent = e.target.files[0].name;
                uploadSubtext.textContent = 'Procesando archivo...';
                btnSortear.disabled = true;

                var formData = new FormData();
                formData.append('archivo', e.target.files[0]);

                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'contar.php', true);
                xhr.onload = function() {
                    var response;
                    try {
                        response = JSON.parse(xhr.responseText);
                    } catch (err) {
                        response = { success: false, message: 'Error procesando el archivo' };
                    }

                    resumen.style.display = 'block';
                    document.getElementById('statTotal').textContent = response.total || 0;
                    if (response.por_tipo) {
                        document.getElementById('statTipo2').textContent = response.por_tipo[2] || 0;
                        document.getElementById('statTipo3').textContent = response.por_tipo[3] || 0;
                        document.getElementById('statTipo4').textContent = response.por_tipo[4] || 0;
                    }
                    if (response.por_genero) {
                        document.getElementById('statGenero').textContent =
                            (response.por_genero.M || 0) + ' / ' + (response.por_genero.V || 0) + ' / ' + (response.por_genero.O || 0);
                    }

                    var html = '';
                    if (!response.success) {
                        html += '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> ' + response.message + '</div>';
                    }
                    if (response.advertencias && response.advertencias.length > 0) {
                        html += '<div class="alert alert-warning"><i class="fas fa-exclamation-circle"></i> Advertencias del padrón:<ul>';
                        response.advertencias.forEach(function(a) { html += '<li>' + a + '</li>'; });
                        html += '</ul></div>';
                    }
                    mensajePadron.innerHTML = html;

                    uploadSubtext.textContent = response.success ? 'Archivo válido' : 'Archivo con problemas';
                    btnSortear.disabled = !response.success;
                };
                xhr.onerror = function() {
                    uploadSubtext.textContent = 'Error de conexión';
                    btnSortear.disabled = true;
                };
                xhr.send(formData);
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
                    fileInput.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });
        });
    </script>
</body>
</html>
