<?php
/**
 * Valida el padrón PPC subido y devuelve el resumen (total, por tipo, por género).
 */

require 'vendor/autoload.php';
require 'sorteo_ppc.php';

use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

$response = ['success' => false, 'total' => 0, 'message' => ''];

try {
    if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No se subió ningún archivo válido');
    }

    $extension = strtolower(pathinfo($_FILES['archivo']['name'], PATHINFO_EXTENSION));

    if ($extension === 'csv') {
        $reader = new Csv();
    } elseif ($extension === 'xlsx') {
        $reader = new Xlsx();
    } else {
        throw new Exception('Formato no válido. Solo CSV o XLSX');
    }

    $spreadsheet = $reader->load($_FILES['archivo']['tmp_name']);
    $sheetData = $spreadsheet->getActiveSheet()->toArray();
    unset($sheetData[0]); // encabezado

    $parsed = ppc_parse_padron(array_values($sheetData));
    $stats = ppc_estadisticas($parsed['participantes']);

    $ok = $stats['total'] >= PPC_TOTAL_BANCAS;
    $response = [
        'success'      => $ok,
        'total'        => $stats['total'],
        'por_tipo'     => $stats['por_tipo'],
        'por_genero'   => $stats['por_genero'],
        'por_prioridad' => $stats['por_prioridad'],
        'advertencias' => $parsed['advertencias'],
        'message'      => $ok
            ? "Padrón válido: {$stats['total']} personas habilitadas"
            : 'Se necesitan al menos ' . PPC_TOTAL_BANCAS . " personas habilitadas. Solo hay {$stats['total']}.",
    ];
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
