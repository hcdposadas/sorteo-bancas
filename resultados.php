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
    ['concejal' => 'CARDOZO HÉCTOR', 'titular' => 'ORTEGA ANTONIA BEATRIZ - 22665897', 'suplente' => 'MEDINA JUANA'],
    ['concejal' => 'MAZAL MALENA', 'titular' => 'OCAMPO CAMILA', 'suplente' => 'AVALOS YAMILA'],
    ['concejal' => 'TRAID LAURA', 'titular' => 'MANDAGARAN MARIEL - 22582834', 'suplente' => 'LOVERA RAQUEL ISABELA - 20731813'],
    ['concejal' => 'ARGAÑARAZ PABLO', 'titular' => 'GONZALEZ CARLA', 'suplente' => 'POR SORTEO'],
    ['concejal' => 'GOMEZ DE OLIVEIRA VALERIA', 'titular' => 'FERNÁNDEZ DEBORA - 34366452', 'suplente' => 'POR SORTEO'],
    ['concejal' => 'HORIANSKI SANTIAGO', 'titular' => 'MELGAREJO DANIELA', 'suplente' => 'POR SORTEO'],
    ['concejal' => 'MOHR CAMILO', 'titular' => 'CASCO BRENDA', 'suplente' => 'POR SORTEO'],
    ['concejal' => 'PRENDONE MARIELA', 'titular' => 'POR SORTEO', 'suplente' => 'POR SORTEO'],
    ['concejal' => 'MARTINEZ ANGEL', 'titular' => 'POR SORTEO', 'suplente' => 'POR SORTEO'],
    ['concejal' => 'SCROMEDA LUCIANA', 'titular' => 'ZIPILIBAN PAULINA', 'suplente' => 'POR SORTEO'],
    ['concejal' => 'DIB JAIR', 'titular' => 'POR SORTEO', 'suplente' => 'POR SORTEO'],
    ['concejal' => 'PAONESA MATIAS', 'titular' => 'POR SORTEO', 'suplente' => 'POR SORTEO'],
    ['concejal' => 'SALOM JUDITH', 'titular' => 'LATTES CAMILA - 36058081', 'suplente' => 'BLANCO ROCIO - 37083497'],
    ['concejal' => 'VIGO DANIEL', 'titular' => 'POR SORTEO', 'suplente' => 'POR SORTEO'],
    ['concejal' => 'FERNANDEZ MARIA ELENA', 'titular' => 'CABRERA VANESSA', 'suplente' => 'BENITEZ LAURA'],
    ['concejal' => 'ZARZA FERNANDO', 'titular' => 'POR SORTEO', 'suplente' => 'POR SORTEO'],
    ['concejal' => 'SAMIRA ALMIRÓN', 'titular' => 'BUCKMAYER LARA MAGALI - 43944957', 'suplente' => 'VECCHIETTI FRANCESCA'],
    ['concejal' => 'TURKIENICZ GUSTAVO', 'titular' => 'POR SORTEO', 'suplente' => 'POR SORTEO'],
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
    <meta name="description" content="Resultados del Sorteo de Bancas">
    <meta name="author" content="HCD Posadas">
    <link rel="icon" href="favicon.png">
    <title>Resultados del Sorteo de Bancas</title>
    <script src="./dist/bundle.js"></script>
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
        .container {
            max-width: 1200px;
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
        .table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .table th {
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
            border: none;
        }
        .table td {
            vertical-align: middle;
            font-weight: 500;
        }
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        .stats-section {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }
        .badge-success {
            background: #28a745;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
        }
        .badge-info {
            background: #17a2b8;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
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
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white;
            }
            .main-container {
                box-shadow: none;
                margin: 0;
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="main-container">
        <!-- Header Section -->
        <div class="header-section">
            <img class="d-block mx-auto mb-4" src="./escudo.webp" alt="logo">
            <h2>Resultados del Sorteo de Bancas</h2>
            <p class="lead">Parlamento de la Mujer - <?php echo date('d/m/Y'); ?></p>
        </div>

        <!-- Results Table -->
        <div class="row mb-4">
            <div class="col-md-12">
                <table class="table table-bordered table-hover">
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
                            <td><strong><?php echo $asignacion['concejal']; ?></strong></td>
                            <td><?php echo $asignacion['titular']; ?></td>
                            <td><?php echo $asignacion['suplente']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="text-center no-print">
            <button onclick="window.print()" class="btn btn-primary mr-2">
                <i class="fa fa-print"></i> Imprimir
            </button>
            <a href="index.php" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Volver
            </a>
        </div>

        <footer class="text-center mt-4 pt-4 border-top no-print">
            <p class="text-muted small mb-0">
                &copy; <?php echo date('Y');?> HCD Posadas | Honorable Concejo Deliberante de Posadas
            </p>
        </footer>
    </div>
</div>
</body>
</html>
