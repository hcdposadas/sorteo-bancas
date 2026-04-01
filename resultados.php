<?php
/**
 * Página de resultados del sorteo - Sistema Híbrido
 */

require 'vendor/autoload.php';

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
    unset($sheetData[0]);
    $sheetData = array_values($sheetData);
} catch (\Throwable $e) {
    die('Error leyendo archivo: ' . $e->getMessage());
}

// ✅ PASO 1 — DEFINIR LA ESTRUCTURA FIJA
$asignaciones = [
    ['concejal' => 'CARDOZO HÉCTOR', 'titular' => 'ORTEGA ANTONIA - 22665897', 'suplente' => 'MEDINA JUANA'],
    ['concejal' => 'MAZAL MALENA', 'titular' => 'OCAMPO CAMILA', 'suplente' => 'AVALOS YAMILA'],
    ['concejal' => 'TRAID LAURA', 'titular' => 'MANDAGARAN MARIEL - 22582834', 'suplente' => 'LOVERA RAQUEL ISABELA - 20731813'],
    ['concejal' => 'ARGAÑARAZ PABLO', 'titular' => 'GONZALEZ CARLA', 'suplente' => 'SORIA MIRTA CLARA - 11915021'],
    ['concejal' => 'GOMEZ DE OLIVEIRA VALERIA', 'titular' => 'FERNÁNDEZ DEBORA - 34366452', 'suplente' => 'GENESINI SANDRA - 34824003'],
    ['concejal' => 'HORIANSKI SANTIAGO', 'titular' => 'MELGAREJO DANIELA', 'suplente' => 'YZA CINTIA - 31911666'],
    ['concejal' => 'MOHR CAMILO', 'titular' => 'CASCO BRENDA', 'suplente' => 'GALARZA ROSANA MAGALÍ - 37158949'],
    ['concejal' => 'PRENDONE MARIELA', 'titular' => 'JMILOVKI ANA MARÍA - 17064059', 'suplente' => 'ROJAS VERONICA - 30145967'],
    ['concejal' => 'MARTINEZ ANGEL', 'titular' => 'MACEIRA JORGELINA NOEMI - 24210020', 'suplente' => 'CORDOBE SOFIA - 42715950'],
    ['concejal' => 'SCROMEDA LUCIANA', 'titular' => 'ZIPILIBAN PAULINA', 'suplente' => 'QUINTANA MARIA ESTHER - 13897779'],
    ['concejal' => 'DIB JAIR', 'titular' => 'BROUSSE ANTONELLA - 37325005', 'suplente' => 'POR SORTEO'],
    ['concejal' => 'PAONESA MATIAS', 'titular' => 'PIPAN ANGELA ALEJANDRA - 18295485', 'suplente' => 'PINTOS SOFIA - 26610394'],
    ['concejal' => 'SALOM JUDITH', 'titular' => 'LATTES CAMILA - 36058081', 'suplente' => 'BLANCO ROCIO - 37083497'],
    ['concejal' => 'VIGO DANIEL', 'titular' => 'DAVALOS SOL CAMILA - 40534763', 'suplente' => 'CHIODIN RAYEN BEATRIZ - 34897111'],
    ['concejal' => 'FERNANDEZ MARIA ELENA', 'titular' => 'CABRERA VANESSA', 'suplente' => 'BENITEZ LAURA'],
    ['concejal' => 'ZARZA FERNANDO', 'titular' => 'NUÑEZ MARISA ELIZABETH - 28094619', 'suplente' => 'MENDEZ PATRICIA - 32417957'],
    ['concejal' => 'SAMIRA ALMIRÓN', 'titular' => 'BUCKMAYER LARA - 43944957', 'suplente' => 'VECCHIETTI FRANCESCA'],
    ['concejal' => 'TURKIENICZ GUSTAVO', 'titular' => 'PERIE CAROLINA SUSAN - 17039831', 'suplente' => 'CENTENO IVANA ANDREA - 33012017'],
];

// ✅ PASO 2 — LEER EL EXCEL BIEN
$participantes = [];

foreach ($sheetData as $row) {
    if (!empty($row[0]) && !empty($row[1])) {
        $participantes[] = [
            'nombre' => trim($row[0] . ' ' . $row[1]),
            'dni' => $row[2] ?? ''
        ];
    }
}

// ✅ PASO 3 — ELIMINAR LOS YA USADOS
$usados = [];

foreach ($asignaciones as $a) {
    if ($a['titular'] !== 'POR SORTEO') {
        $usados[] = strtoupper(trim($a['titular']));
    }
    if ($a['suplente'] !== 'POR SORTEO') {
        $usados[] = strtoupper(trim($a['suplente']));
    }
}

$disponibles = array_filter($participantes, function($p) use ($usados) {
    return !in_array(strtoupper($p['nombre']), $usados);
});

$disponibles = array_values($disponibles);

// ✅ PASO 3.5 — CREAR MAPA DE PARTICIPANTES CON DNI
$participantes_con_dni = [];
foreach ($participantes as $p) {
    $participantes_con_dni[strtoupper(trim($p['nombre']))] = $p['dni'];
}

// ✅ PASO 4 — CONTAR CUÁNTOS "POR SORTEO" NECESITAMOS
$por_sorteo_necesarios = 0;
foreach ($asignaciones as $a) {
    if ($a['titular'] === 'POR SORTEO') $por_sorteo_necesarios++;
    if ($a['suplente'] === 'POR SORTEO') $por_sorteo_necesarios++;
}

// ✅ PASO 5 — VALIDAR CANTIDAD SUFICIENTE
if (count($disponibles) < $por_sorteo_necesarios) {
    die("Error: No hay suficientes participantes disponibles. Se necesitan $por_sorteo_necesarios pero solo hay " . count($disponibles) . ".");
}

// ✅ PASO 6 — HACER EL SORTEO Y ASIGNAR DNI A TODOS
shuffle($disponibles);

$index = 0;
foreach ($asignaciones as &$a) {
    // Procesar TITULAR
    if ($a['titular'] === 'POR SORTEO') {
        if ($index < count($disponibles)) {
            $participante = $disponibles[$index];
            $nombre_completo = $participante['nombre'];
            if (!empty($participante['dni'])) {
                $nombre_completo .= ' - ' . $participante['dni'];
            }
            $a['titular'] = $nombre_completo;
            $index++;
        }
    } else {
        // Es un nombre fijo, buscar su DNI
        $nombre_normalizado = strtoupper(trim($a['titular']));
        if (isset($participantes_con_dni[$nombre_normalizado]) && !empty($participantes_con_dni[$nombre_normalizado])) {
            $a['titular'] .= ' - ' . $participantes_con_dni[$nombre_normalizado];
        }
    }

    // ProcesAR SUPLENTE
    if ($a['suplente'] === 'POR SORTEO') {
        if ($index < count($disponibles)) {
            $participante = $disponibles[$index];
            $nombre_completo = $participante['nombre'];
            if (!empty($participante['dni'])) {
                $nombre_completo .= ' - ' . $participante['dni'];
            }
            $a['suplente'] = $nombre_completo;
            $index++;
        }
    } else {
        // Es un nombre fijo, buscar su DNI
        $nombre_normalizado = strtoupper(trim($a['suplente']));
        if (isset($participantes_con_dni[$nombre_normalizado]) && !empty($participantes_con_dni[$nombre_normalizado])) {
            $a['suplente'] .= ' - ' . $participantes_con_dni[$nombre_normalizado];
        }
    }
}

// ✅ PASO 7 — MOSTRAR EXACTAMENTE COMO LA IMAGEN
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Resultados del Sorteo de Bancas - Honorable Concejo Deliberante de Posadas">
    <meta name="author" content="HCD Posadas">
    <link rel="icon" href="favicon.png">
    <title>Resultados del Sorteo de Bancas - HCD Posadas</title>
    
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
        
        .table tbody tr:hover {
            background: var(--bg-color);
        }
        
        .table tbody tr:last-child td {
            border-bottom: none;
        }
        
        .table td:first-child {
            font-weight: 600;
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
            <h1 class="header-title">Resultados del Sorteo de Bancas</h1>
            <p class="header-subtitle">Parlamento de la Mujer - <?php echo date('d/m/Y'); ?></p>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container">
        <!-- Results Card -->
        <div class="card">
            <h2 class="card-title">
                <i class="fas fa-trophy"></i>
                Resultados del Sorteo
            </h2>
            
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="25%">CONCEJAL</th>
                            <th width="37.5%">TITULAR</th>
                            <th width="37.5%">SUPLENTE</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($asignaciones as $asignacion): ?>
                        <tr>
                            <td><?php echo $asignacion['concejal']; ?></td>
                            <td><?php echo $asignacion['titular']; ?></td>
                            <td><?php echo $asignacion['suplente']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
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
