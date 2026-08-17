<?php
/**
 * Verificación por CLI de la lógica del Sorteo General PPC.
 * Uso: php tests/verificar_sorteo.php [iteraciones]
 */

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../sorteo_ppc.php';

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

$fallas = 0;
function check(bool $cond, string $msg): void
{
    global $fallas;
    if (!$cond) {
        $fallas++;
        echo "FALLA: $msg\n";
    }
}

// --- Carga del padrón de ejemplo -------------------------------------------
$reader = new Xlsx();
$spreadsheet = $reader->load(__DIR__ . '/../example-ppc.xlsx');
$sheetData = $spreadsheet->getActiveSheet()->toArray();
unset($sheetData[0]);
$parsed = ppc_parse_padron(array_values($sheetData));
$participantes = $parsed['participantes'];

check(count($participantes) === 60, 'padrón de ejemplo: se esperaban 60 participantes, hay ' . count($participantes));
check($parsed['advertencias'] === [], 'padrón de ejemplo: advertencias inesperadas: ' . implode(' | ', $parsed['advertencias']));
foreach ($participantes as $p) {
    check(preg_match('/^\d+$/', $p['dni']) === 1, "DNI no numérico: {$p['dni']}");
}

$stats = ppc_estadisticas($participantes);
check($stats['por_tipo'] === [2 => 11, 3 => 8, 4 => 41], 'distribución por tipo inesperada: ' . json_encode($stats['por_tipo']));

// --- Invariantes sobre N sorteos -------------------------------------------
$N = (int)($argv[1] ?? 300);
$ordenesPrimeros = [];
$concejalesTodos = [];
foreach (PPC_TIPOS as $cfg) {
    foreach ($cfg['concejales'] as $c) $concejalesTodos[] = $c;
}

for ($run = 0; $run < $N; $run++) {
    $r = ppc_sortear($participantes);

    // Tamaños globales
    check(count($r['seleccionados']) === 22, "run $run: seleccionados != 22");
    check(count($r['suplentes']) === 38, "run $run: suplentes != 38");
    $roles = array_count_values(array_column($r['seleccionados'], 'rol'));
    check(($roles['Titular'] ?? 0) === 11 && ($roles['Cotitular'] ?? 0) === 11, "run $run: roles 11/11 incumplido");

    // Sin duplicados y sin solapamiento con suplencia
    $dnisSel = array_column($r['seleccionados'], 'dni');
    check(count(array_unique($dnisSel)) === 22, "run $run: DNI duplicado en seleccionados");
    $dnisSup = array_column($r['suplentes'], 'dni');
    check(array_intersect($dnisSel, $dnisSup) === [], "run $run: persona seleccionada y suplente a la vez");

    // Orden de prelación 1..60 único; suplencia estrictamente creciente
    $ordenes = array_column($r['orden_general'], 'orden');
    sort($ordenes);
    check($ordenes === range(1, 60), "run $run: orden general no es 1..60");
    $ordSup = array_column($r['suplentes'], 'orden');
    $ordSorted = $ordSup;
    sort($ordSorted);
    check($ordSup === $ordSorted, "run $run: suplencia no respeta orden de prelación");

    // Cupos por tipo y rol
    foreach ([2 => [4, 4], 3 => [4, 4], 4 => [3, 3]] as $tipo => [$cupoT, $cupoC]) {
        $t = $c = 0;
        foreach ($r['seleccionados'] as $p) {
            if ($p['banca_tipo'] === $tipo) {
                $p['rol'] === 'Titular' ? $t++ : $c++;
            }
        }
        check($t === $cupoT && $c === $cupoC, "run $run: cupos tipo $tipo = $t/$c, esperado $cupoT/$cupoC");
    }

    // Concejales: los 11, cada uno con 1 titular y 1 cotitular de su tipo
    $porConcejal = [];
    foreach ($r['seleccionados'] as $p) {
        $porConcejal[$p['concejal']][$p['rol']] = $p['banca_tipo'];
    }
    check(count($porConcejal) === 11, "run $run: cantidad de concejales != 11");
    check(array_diff($concejalesTodos, array_keys($porConcejal)) === [], "run $run: falta algún concejal");
    foreach (PPC_TIPOS as $tipo => $cfg) {
        foreach ($cfg['concejales'] as $con) {
            check(($porConcejal[$con]['Titular'] ?? 0) === $tipo && ($porConcejal[$con]['Cotitular'] ?? 0) === $tipo,
                "run $run: $con no tiene 1T+1C de tipo $tipo");
        }
    }

    // Equilibrio de género con este padrón (clase: O cuenta como V)
    $clases = []; // [tipo][rol][clase] => n
    foreach ($r['seleccionados'] as $p) {
        $clases[$p['banca_tipo']][$p['rol']][ppc_clase($p)] = ($clases[$p['banca_tipo']][$p['rol']][ppc_clase($p)] ?? 0) + 1;
    }
    // Tipo 2 (pool 7M/4V): 2M+2V exactos en cada grupo
    foreach (['Titular', 'Cotitular'] as $rol) {
        check(($clases[2][$rol]['M'] ?? 0) === 2 && ($clases[2][$rol]['V'] ?? 0) === 2, "run $run: tipo 2 $rol sin 2M+2V");
    }
    // Tipo 3 (pool exacto de 8: clases 2M/6V): entran todos, titulares con las 2 M como máximo posible
    $t3 = ($clases[3]['Titular']['M'] ?? 0) + ($clases[3]['Cotitular']['M'] ?? 0);
    check($t3 === 2, "run $run: tipo 3 no tiene las 2 mujeres del pool");
    // Tipo 4 (pool amplio): 3M+3V en total, 2/1 compensado entre grupos
    $m4t = $clases[4]['Titular']['M'] ?? 0;
    $m4c = $clases[4]['Cotitular']['M'] ?? 0;
    check($m4t + $m4c === 3, "run $run: tipo 4 total M != 3 ($m4t+$m4c)");
    check(abs($m4t - (3 - $m4t)) === 1 && abs($m4c - (3 - $m4c)) === 1, "run $run: tipo 4 grupos sin diferencia 1");

    // Con este padrón no hay vacantes de cupo
    check($r['notas'] === [], "run $run: notas de vacantes inesperadas");
    foreach ($r['seleccionados'] as $p) {
        check(!$p['cubre_vacante'], "run $run: cubre_vacante inesperado");
    }

    $ordenesPrimeros[] = $r['orden_general'][0]['dni'];
}

// Aleatoriedad: el primer lugar de prelación debe variar entre corridas
check(count(array_unique($ordenesPrimeros)) > 5, 'aleatoriedad: el primer puesto casi no varía entre corridas');

// --- Casos borde sintéticos -------------------------------------------------
function persona(string $n, int $i, string $g, int $t): array
{
    return ['nombre' => $n, 'apellido' => "Ap$i", 'dni' => (string)(90000000 + $i), 'genero' => $g, 'tipo' => $t];
}

// a) Insuficiencia en tipo 2: solo 5 inscriptos tipo 2 → 3 vacantes por orden general
$sint = [];
$i = 0;
for ($k = 0; $k < 5; $k++) $sint[] = persona('T2', $i++, $k % 2 ? 'M' : 'V', 2);
for ($k = 0; $k < 8; $k++) $sint[] = persona('T3', $i++, $k % 2 ? 'M' : 'V', 3);
for ($k = 0; $k < 20; $k++) $sint[] = persona('T4', $i++, $k % 2 ? 'M' : 'V', 4);
$r = ppc_sortear($sint);
$vac = array_filter($r['seleccionados'], fn($p) => $p['cubre_vacante']);
check(count($vac) === 3, 'sintético a: se esperaban 3 vacantes cubiertas, hubo ' . count($vac));
foreach ($vac as $p) {
    check($p['banca_tipo'] === 2 && $p['tipo'] !== 2, 'sintético a: vacante mal asignada');
}
check(count($r['notas']) === 3, 'sintético a: notas != 3');
$t2 = array_filter($r['seleccionados'], fn($p) => $p['banca_tipo'] === 2);
check(count($t2) === 8, 'sintético a: bancas tipo 2 != 8');

// b) Padrón de un solo género: el sorteo igualmente completa los cupos
$sint = [];
$i = 0;
for ($k = 0; $k < 11; $k++) $sint[] = persona('M2', $i++, 'M', 2);
for ($k = 0; $k < 11; $k++) $sint[] = persona('M3', $i++, 'M', 3);
for ($k = 0; $k < 11; $k++) $sint[] = persona('M4', $i++, 'M', 4);
$r = ppc_sortear($sint);
check(count($r['seleccionados']) === 22, 'sintético b: no completó los cupos con un solo género');

// c) Menos de 22 personas → excepción
try {
    ppc_sortear(array_slice($sint, 0, 21));
    check(false, 'sintético c: no lanzó excepción con padrón insuficiente');
} catch (RuntimeException $e) {
    // esperado
}

echo $fallas === 0
    ? "OK: $N sorteos verificados sin fallas.\n"
    : "$fallas fallas detectadas.\n";
exit($fallas === 0 ? 0 : 1);
