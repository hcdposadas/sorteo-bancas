<?php
/**
 * Script para contar participantes disponibles para el sistema híbrido
 */

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

// Asignaciones fijas del sistema híbrido
$asignaciones_fijas = [
    'ORTEGA ANTONIA BEATRIZ', 'MEDINA JUANA', 'OCAMPO CAMILA', 'AVALOS YAMILA',
    'MANDAGARAN MARIEL', 'LOVERA RAQUEL ISABELA', 'GONZALEZ CARLA', 'FERNÁNDEZ DEBORA',
    'MELGAREJO DANIELA', 'CASCO BRENDA', 'ZIPILIBAN PAULINA', 'LATTES CAMILA', 'BLANCO ROCIO',
    'CABRERA VANESSA', 'BENITEZ LAURA', 'BUCKMAYER LARA MAGALI', 'VECCHIETTI FRANCESCA'
];

$response = ['success' => false, 'count' => 0, 'disponibles' => 0, 'message' => ''];

try {
    if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("No se subió ningún archivo válido");
    }

    $extension = strtolower(pathinfo($_FILES['archivo']['name'], PATHINFO_EXTENSION));

    if ($extension === 'csv') {
        $reader = new Csv();
    } elseif ($extension === 'xlsx') {
        $reader = new Xlsx();
    } else {
        throw new Exception("Formato no válido. Solo CSV o XLSX");
    }

    // Leer archivo REAL
    $spreadsheet = $reader->load($_FILES['archivo']['tmp_name']);
    $sheetData = $spreadsheet->getActiveSheet()->toArray();

    // Eliminar encabezado
    unset($sheetData[0]);

    // Limpiar filas vacías
    $sheetData = array_filter($sheetData, function ($row) {
        return !empty($row[0]) || !empty($row[1]);
    });

    // Reindexar
    $sheetData = array_values($sheetData);

    // Contar participantes reales
    $participantes = [];
    foreach ($sheetData as $row) {
        if (!empty($row[0]) && !empty($row[1])) {
            $participantes[] = trim($row[0] . ' ' . $row[1]);
        }
    }

    $total = count($participantes);

    // Eliminar los ya asignados (sistema híbrido)
    $disponibles = array_filter($participantes, function($p) use ($asignaciones_fijas) {
        return !in_array(strtoupper($p), $asignaciones_fijas);
    });

    $disponibles_count = count($disponibles);

    if ($disponibles_count >= 34) {
        $response = [
            'success' => true,
            'count' => $total,
            'disponibles' => $disponibles_count,
            'message' => "Archivo válido con $disponibles_count participantes disponibles"
        ];
    } else {
        $response = [
            'success' => false,
            'count' => $total,
            'disponibles' => $disponibles_count,
            'message' => "Se necesitan mínimo 34 participantes disponibles. Solo hay $disponibles_count."
        ];
    }

} catch (Exception $e) {
    $response = [
        'success' => false,
        'count' => 0,
        'disponibles' => 0,
        'message' => "Error: " . $e->getMessage()
    ];
}

header('Content-Type: application/json');
echo json_encode($response);
?>