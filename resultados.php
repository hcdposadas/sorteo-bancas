<?php
/**
 * Resultados del Sorteo General PPC.
 */

require 'vendor/autoload.php';
require 'sorteo_ppc.php';

use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: index.php');
    exit();
}

if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
    die('Error: no se recibió el archivo');
}

$extension = strtolower(pathinfo($_FILES['archivo']['name'], PATHINFO_EXTENSION));

if ($extension === 'csv') {
    $reader = new Csv();
} elseif ($extension === 'xlsx') {
    $reader = new Xlsx();
} else {
    die('Formato no válido. Solo CSV o XLSX');
}

try {
    $spreadsheet = $reader->load($_FILES['archivo']['tmp_name']);
    $sheetData = $spreadsheet->getActiveSheet()->toArray();
    unset($sheetData[0]); // encabezado
} catch (\Throwable $e) {
    die('Error leyendo archivo: ' . $e->getMessage());
}

$parsed = ppc_parse_padron(array_values($sheetData));

try {
    $resultado = ppc_sortear($parsed['participantes']);
} catch (\Throwable $e) {
    die('Error en el sorteo: ' . $e->getMessage());
}

$fechaSorteo = date('d/m/Y H:i:s');

function e(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function nombre_completo(array $p): string
{
    return e($p['nombre'] . ' ' . $p['apellido']);
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Resultados del Sorteo General PPC - Honorable Concejo Deliberante de Posadas">
    <meta name="author" content="HCD Posadas">
    <link rel="icon" href="favicon.png">
    <title>Resultados del Sorteo General PPC - HCD Posadas</title>

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

        /* Stats tiles */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 1rem;
        }

        .stat-tile {
            background: var(--bg-color);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 1.25rem 1rem;
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

        .metodo {
            margin-top: 1.25rem;
            font-size: 0.8125rem;
            color: var(--text-secondary);
        }

        .metodo i {
            color: var(--success-color);
        }

        /* Alerts */
        .alert {
            border-radius: var(--radius-md);
            padding: 1rem;
            margin-bottom: 1.5rem;
            font-size: 0.8125rem;
        }

        .alert-warning {
            background: rgb(217 119 6 / 0.08);
            border: 1px solid rgb(217 119 6 / 0.3);
            color: var(--warning-color);
        }

        .alert ul {
            margin: 0.5rem 0 0 1.25rem;
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
            vertical-align: middle;
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        .concejal-cell {
            font-weight: 600;
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

        .tipo-nombre {
            display: block;
            margin-top: 0.25rem;
            font-size: 0.6875rem;
            color: var(--text-secondary);
        }

        .badge-titular { background: rgb(5 150 105 / 0.1); color: var(--success-color); }
        .badge-cotitular { background: rgb(30 64 175 / 0.1); color: var(--primary-color); }
        .badge-vacante { background: rgb(220 38 38 / 0.1); color: var(--danger-color); }

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

        .btn-secondary {
            background: var(--secondary-color);
            color: white;
            box-shadow: var(--shadow-sm);
        }

        .btn-secondary:hover {
            background: #475569;
            box-shadow: var(--shadow-md);
            transform: translateY(-1px);
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 2rem 0;
            color: var(--text-secondary);
            font-size: 0.75rem;
        }

        /* Actions */
        .actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 2rem;
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

            .actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }

        /* Print Styles */
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: white;
            }

            .card {
                box-shadow: none;
                border: 1px solid #ccc;
                margin-bottom: 1rem;
                break-inside: avoid;
            }

            .header {
                padding: 1rem 0;
            }

            .header-logo {
                width: 60px;
                height: 60px;
            }

            .header-title {
                font-size: 1.5rem;
            }

            .table {
                font-size: 0.75rem;
            }

            .table th,
            .table td {
                padding: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <img src="./escudo.webp" alt="HCD Posadas" class="header-logo">
            <h1 class="header-title">Resultados del Sorteo General PPC</h1>
            <p class="header-subtitle">Honorable Concejo Deliberante de Posadas</p>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container">
        <!-- Stats Card -->
        <div class="card">
            <div class="stats-grid">
                <div class="stat-tile">
                    <div class="stat-number"><?php echo $resultado['stats']['total']; ?></div>
                    <div class="stat-label">Personas habilitadas</div>
                </div>
                <div class="stat-tile">
                    <div class="stat-number"><?php echo count(PPC_ESCUELAS); ?></div>
                    <div class="stat-label">Bancas de escuelas</div>
                </div>
                <div class="stat-tile">
                    <div class="stat-number">11</div>
                    <div class="stat-label">Titulares</div>
                </div>
                <div class="stat-tile">
                    <div class="stat-number">11</div>
                    <div class="stat-label">Cotitulares</div>
                </div>
                <div class="stat-tile">
                    <div class="stat-number"><?php echo count($resultado['suplentes']); ?></div>
                    <div class="stat-label">Orden de suplencia</div>
                </div>
                <div class="stat-tile">
                    <div class="stat-number" style="font-size: 1rem; line-height: 2.4rem;"><?php echo $fechaSorteo; ?></div>
                    <div class="stat-label">Fecha del sorteo</div>
                </div>
            </div>
            <p class="metodo">
                <i class="fas fa-check-circle"></i>
                Sorteo realizado mediante orden general de prelación aleatorio, con cupos por tipo de banca
                y equilibrio de género entre mujeres y varones en titulares y cotitulares (el género O ocupa cupo de varones).
            </p>
        </div>

        <?php if (!empty($resultado['notas'])): ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-circle"></i>
            Cupos cubiertos por orden general de prelación:
            <ul>
                <?php foreach ($resultado['notas'] as $nota): ?>
                <li><?php echo e($nota); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <!-- Bancas del Parlamento (lista única) -->
        <div class="card">
            <h2 class="card-title">
                <i class="fas fa-chair"></i>
                Bancas del Parlamento
            </h2>

            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tipo de banca</th>
                            <th>Concejal que cede su banca</th>
                            <th>Rol</th>
                            <th>Nombre y Apellido / Institución</th>
                            <th>DNI / CUE</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (PPC_ESCUELAS as $esc): ?>
                        <tr>
                            <td><span class="badge badge-escuela">Escuelas</span></td>
                            <td class="concejal-cell"><?php echo e($esc['concejal']); ?></td>
                            <td><span class="badge badge-titular">Titular</span></td>
                            <td>
                                <?php echo e($esc['institucion']); ?>
                                <span class="tipo-nombre">Gestión <?php echo e($esc['gestion']); ?> · <?php echo e($esc['delegacion']); ?></span>
                            </td>
                            <td><?php echo e($esc['cue']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php foreach ($resultado['bancas'] as $tipo => $banca): ?>
                            <?php foreach ($banca['asignaciones'] as $a): ?>
                        <tr>
                            <td rowspan="2">
                                <span class="badge badge-tipo-<?php echo $tipo; ?>">Tipo <?php echo $tipo; ?></span>
                                <span class="tipo-nombre"><?php echo e($banca['nombre']); ?></span>
                            </td>
                            <td class="concejal-cell" rowspan="2"><?php echo e($a['concejal']); ?></td>
                            <td><span class="badge badge-titular">Titular</span></td>
                            <td>
                                <?php echo nombre_completo($a['titular']); ?>
                                <?php if ($a['titular']['cubre_vacante']): ?>
                                    <span class="badge badge-vacante">cubre vacante (tipo <?php echo $a['titular']['tipo']; ?>)</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($a['titular']['dni']); ?></td>
                        </tr>
                        <tr>
                            <td><span class="badge badge-cotitular">Cotitular</span></td>
                            <td>
                                <?php echo nombre_completo($a['cotitular']); ?>
                                <?php if ($a['cotitular']['cubre_vacante']): ?>
                                    <span class="badge badge-vacante">cubre vacante (tipo <?php echo $a['cotitular']['tipo']; ?>)</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($a['cotitular']['dni']); ?></td>
                        </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <p style="margin-top: 1rem; font-size: 0.8125rem; color: var(--text-secondary);">
                Las bancas de escuelas corresponden al sorteo de escuelas PPC realizado el 28/07/2026.
                Las restantes surgen del presente sorteo general.
            </p>
        </div>

        <!-- Orden de suplencia -->
        <div class="card">
            <h2 class="card-title">
                <i class="fas fa-list-ol"></i>
                Orden de Suplencia
            </h2>

            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>Nombre y Apellido</th>
                            <th>DNI</th>
                            <th>Tipo de banca</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($resultado['suplentes'] as $i => $s): ?>
                        <tr>
                            <td style="font-weight: 600;"><?php echo $i + 1; ?></td>
                            <td><?php echo nombre_completo($s); ?></td>
                            <td><?php echo e($s['dni']); ?></td>
                            <td><span class="badge badge-tipo-<?php echo $s['tipo']; ?>">Tipo <?php echo $s['tipo']; ?> — <?php echo e(PPC_TIPOS[$s['tipo']]['nombre']); ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <p style="margin-top: 1rem; font-size: 0.8125rem; color: var(--text-secondary);">
                Las suplencias cubren eventuales renuncias, vacancias o imposibilidades de participación,
                respetando el orden general de prelación resultante del sorteo.
            </p>
        </div>

        <!-- Actions -->
        <div class="actions no-print">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i>
                Imprimir
            </button>
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Volver
            </a>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer no-print">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Honorable Concejo Deliberante de Posadas | Todos los derechos reservados</p>
        </div>
    </footer>
</body>
</html>
