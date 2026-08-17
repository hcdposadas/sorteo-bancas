<?php
/**
 * Lógica del Sorteo General PPC - HCD Posadas
 *
 * Pautas (PPC_pautas sorteo general.docx):
 *  - 22 personas: 11 titulares y 11 cotitulares.
 *  - Banca tipo 2 (personas con discapacidad): 4 titulares y 4 cotitulares.
 *  - Banca tipo 3 (personas mayores):          4 titulares y 4 cotitulares.
 *  - Banca tipo 4 (participación general):     3 titulares y 3 cotitulares.
 *  - Orden general de prelación aleatorio único para todo el padrón.
 *  - Equilibrio de género M/V en titulares y en cotitulares de cada tipo,
 *    siempre que el padrón lo permita. El género O ocupa cupo de varones.
 *  - Cupos que no se puedan completar se cubren por orden general de prelación,
 *    priorizando el género necesario para preservar el equilibrio.
 *  - Las personas no seleccionadas integran el orden de suplencia según el
 *    orden general de prelación.
 *  - Cada concejal recibe al azar 1 titular y 1 cotitular de su tipo de banca.
 */

const PPC_TIPOS = [
    2 => [
        'nombre'      => 'Personas con discapacidad',
        'titulares'   => 4,
        'cotitulares' => 4,
        'concejales'  => ['SALOM JUDITH', 'SCROMEDA LUCIANA', 'ZARZA FERNANDO', 'FERNANDEZ MARIA ELENA'],
    ],
    3 => [
        'nombre'      => 'Personas mayores',
        'titulares'   => 4,
        'cotitulares' => 4,
        'concejales'  => ['TRAID LAURA', 'VIGO DANIEL', 'PAONESSA MATIAS (Defensor del Pueblo)', 'MARTINEZ ANGEL'],
    ],
    4 => [
        'nombre'      => 'Participación general',
        'titulares'   => 3,
        'cotitulares' => 3,
        'concejales'  => ['DIB JAIR', 'HORIANSKI SANTIAGO', 'ARGAÑARAZ PABLO'],
    ],
];

const PPC_TOTAL_BANCAS = 22;

/** Bancas de escuelas ya asignadas en el sorteo de escuelas PPC (28/07/2026). */
const PPC_ESCUELAS = [
    ['institucion' => 'BOP102', 'cue' => '540150900', 'gestion' => 'Pública', 'delegacion' => 'Itaembé Guazú', 'concejal' => 'MAZAL MALENA'],
    ['institucion' => 'Instituto Proyección 2000', 'cue' => '540046600', 'gestion' => 'Privada', 'delegacion' => 'Itaembé Miné Este', 'concejal' => 'GOMEZ DE OLIVEIRA VALERIA'],
    ['institucion' => 'Instituto Posadas 0403', 'cue' => '540073800', 'gestion' => 'Privada', 'delegacion' => 'Villa Urquiza', 'concejal' => 'SAMIRA ALMIRÓN'],
    ['institucion' => 'BOP 9', 'cue' => '540104500', 'gestion' => 'Pública', 'delegacion' => 'Sede central', 'concejal' => 'CARDOZO HÉCTOR'],
];

/**
 * Convierte las filas crudas de la planilla (sin encabezado) en participantes.
 * Columnas esperadas: Nombre | Apellido | DNI | Género (V/M/O) | Tipo de banca (2/3/4).
 *
 * @return array{participantes: array, advertencias: string[]}
 */
function ppc_parse_padron(array $sheetData): array
{
    $participantes = [];
    $advertencias = [];

    foreach ($sheetData as $i => $row) {
        $fila = $i + 2; // número de fila en la planilla (1 = encabezado)
        $nombre = trim((string)($row[0] ?? ''));
        $apellido = trim((string)($row[1] ?? ''));

        if ($nombre === '' && $apellido === '') {
            continue; // fila vacía
        }
        if ($nombre === '' || $apellido === '') {
            $advertencias[] = "Fila $fila: falta nombre o apellido, se omitió.";
            continue;
        }

        $dni = preg_replace('/\D/', '', (string)($row[2] ?? ''));
        if ($dni === '') {
            $advertencias[] = "Fila $fila ($nombre $apellido): sin DNI.";
        }

        $genero = strtoupper(substr(trim((string)($row[3] ?? '')), 0, 1));
        if (!in_array($genero, ['V', 'M', 'O'], true)) {
            $advertencias[] = "Fila $fila ($nombre $apellido): género inválido «" . trim((string)($row[3] ?? '')) . "», se omitió.";
            continue;
        }

        $tipo = (int)round((float)($row[4] ?? 0));
        if (!isset(PPC_TIPOS[$tipo])) {
            $advertencias[] = "Fila $fila ($nombre $apellido): tipo de banca inválido «" . trim((string)($row[4] ?? '')) . "», se omitió.";
            continue;
        }

        $participantes[] = [
            'nombre'   => $nombre,
            'apellido' => $apellido,
            'dni'      => $dni,
            'genero'   => $genero,
            'tipo'     => $tipo,
        ];
    }

    return ['participantes' => $participantes, 'advertencias' => $advertencias];
}

function ppc_estadisticas(array $participantes): array
{
    $porTipo = [2 => 0, 3 => 0, 4 => 0];
    $porGenero = ['M' => 0, 'V' => 0, 'O' => 0];
    foreach ($participantes as $p) {
        $porTipo[$p['tipo']]++;
        $porGenero[$p['genero']]++;
    }
    return ['total' => count($participantes), 'por_tipo' => $porTipo, 'por_genero' => $porGenero];
}

/** Clase de género para los cupos: O ocupa el cupo de varones. */
function ppc_clase(array $p): string
{
    return $p['genero'] === 'M' ? 'M' : 'V';
}

/** Fisher-Yates con random_int (CSPRNG). */
function ppc_shuffle(array $items): array
{
    $items = array_values($items);
    for ($i = count($items) - 1; $i > 0; $i--) {
        $j = random_int(0, $i);
        [$items[$i], $items[$j]] = [$items[$j], $items[$i]];
    }
    return $items;
}

/**
 * Toma hasta $cupo personas del pool (índices sobre $orden, ya en prelación),
 * respetando topes por clase de género; si el pool no alcanza para cumplir
 * los topes, completa igual por prelación (equilibrio "siempre que se pueda").
 *
 * @return array{0: int[], 1: array{M: int, V: int}} índices tomados y conteo por clase
 */
function ppc_tomar(array $orden, array $poolIdx, int $cupo, array $caps, array &$seleccionado): array
{
    $tomados = [];
    $cuenta = ['M' => 0, 'V' => 0];

    foreach ($poolIdx as $idx) {
        if (count($tomados) >= $cupo) break;
        if ($seleccionado[$idx]) continue;
        $clase = ppc_clase($orden[$idx]);
        if ($cuenta[$clase] < $caps[$clase]) {
            $tomados[] = $idx;
            $cuenta[$clase]++;
            $seleccionado[$idx] = true;
        }
    }

    // Segunda pasada: relajar topes si el padrón no permite el equilibrio.
    foreach ($poolIdx as $idx) {
        if (count($tomados) >= $cupo) break;
        if ($seleccionado[$idx]) continue;
        $tomados[] = $idx;
        $cuenta[ppc_clase($orden[$idx])]++;
        $seleccionado[$idx] = true;
    }

    return [$tomados, $cuenta];
}

/**
 * Ejecuta el sorteo completo.
 *
 * @return array{
 *   orden_general: array,
 *   bancas: array,
 *   seleccionados: array,
 *   suplentes: array,
 *   stats: array,
 *   notas: string[]
 * }
 */
function ppc_sortear(array $participantes): array
{
    if (count($participantes) < PPC_TOTAL_BANCAS) {
        throw new RuntimeException(
            'El padrón tiene ' . count($participantes) . ' personas habilitadas y se necesitan al menos ' . PPC_TOTAL_BANCAS . '.'
        );
    }

    // 1. Orden general de prelación.
    $orden = ppc_shuffle($participantes);
    foreach ($orden as $i => &$p) {
        $p['orden'] = $i + 1;
    }
    unset($p);

    $seleccionado = array_fill(0, count($orden), false);
    $notas = [];
    $grupos = []; // por tipo: ['T' => idx[], 'C' => idx[], caps restantes por grupo]

    // 2. Cupos por tipo con equilibrio de género.
    foreach (PPC_TIPOS as $tipo => $cfg) {
        $poolIdx = [];
        foreach ($orden as $idx => $p) {
            if ($p['tipo'] === $tipo) $poolIdx[] = $idx;
        }

        $cupoT = $cfg['titulares'];
        $cupoC = $cfg['cotitulares'];
        // Tope por clase para titulares: mitad del cupo (con margen de 1 si es impar).
        $tope = intdiv($cupoT, 2) + ($cupoT % 2);
        $capsT = ['M' => $tope, 'V' => $tope];
        [$tit, $cuentaT] = ppc_tomar($orden, $poolIdx, $cupoT, $capsT, $seleccionado);

        // Cotitulares: compensa lo tomado en titulares para que el total del
        // tipo quede lo más cercano posible a mitad y mitad.
        $targetClase = intdiv($cupoT + $cupoC, 2);
        $capsC = [
            'M' => max(0, $targetClase - $cuentaT['M']),
            'V' => max(0, $targetClase - $cuentaT['V']),
        ];
        [$cot, $cuentaC] = ppc_tomar($orden, $poolIdx, $cupoC, $capsC, $seleccionado);

        $grupos[$tipo] = [
            'T' => ['idx' => $tit, 'cupo' => $cupoT, 'caps' => $capsT, 'cuenta' => $cuentaT],
            'C' => ['idx' => $cot, 'cupo' => $cupoC, 'caps' => $capsC, 'cuenta' => $cuentaC],
        ];
    }

    // 3. Vacantes de cupo: se cubren por orden general de prelación, buscando
    //    el género necesario para preservar el equilibrio cuando sea posible.
    foreach ($grupos as $tipo => &$g) {
        foreach (['T', 'C'] as $rol) {
            while (count($g[$rol]['idx']) < $g[$rol]['cupo']) {
                $capsRest = [
                    'M' => max(0, $g[$rol]['caps']['M'] - $g[$rol]['cuenta']['M']),
                    'V' => max(0, $g[$rol]['caps']['V'] - $g[$rol]['cuenta']['V']),
                ];
                $elegido = null;
                foreach ($orden as $idx => $p) {
                    if (!$seleccionado[$idx] && $capsRest[ppc_clase($p)] > 0) {
                        $elegido = $idx;
                        break;
                    }
                }
                if ($elegido === null) {
                    foreach ($orden as $idx => $p) {
                        if (!$seleccionado[$idx]) {
                            $elegido = $idx;
                            break;
                        }
                    }
                }
                $g[$rol]['idx'][] = $elegido;
                $g[$rol]['cuenta'][ppc_clase($orden[$elegido])]++;
                $seleccionado[$elegido] = true;
                $pe = $orden[$elegido];
                $notas[] = 'Banca ' . ($rol === 'T' ? 'titular' : 'cotitular') . ' tipo ' . $tipo
                    . ' cubierta por orden general de prelación: ' . $pe['nombre'] . ' ' . $pe['apellido']
                    . ' (inscripción tipo ' . $pe['tipo'] . ').';
            }
        }
    }
    unset($g);

    // 4. Asignación aleatoria de concejales: 1 titular + 1 cotitular cada uno.
    $bancas = [];
    $seleccionados = [];
    foreach (PPC_TIPOS as $tipo => $cfg) {
        $concejales = ppc_shuffle($cfg['concejales']);
        $asignaciones = [];
        foreach ($concejales as $i => $concejal) {
            $par = ['concejal' => $concejal];
            foreach (['titular' => 'T', 'cotitular' => 'C'] as $clave => $rol) {
                $p = $orden[$grupos[$tipo][$rol]['idx'][$i]];
                $p['rol'] = $clave === 'titular' ? 'Titular' : 'Cotitular';
                $p['banca_tipo'] = $tipo;
                $p['concejal'] = $concejal;
                $p['cubre_vacante'] = ($p['tipo'] !== $tipo);
                $seleccionados[] = $p;
                $par[$clave] = $p;
            }
            $asignaciones[] = $par;
        }
        $bancas[$tipo] = ['nombre' => $cfg['nombre'], 'asignaciones' => $asignaciones];
    }

    // 5. Orden de suplencia: no seleccionados según orden general de prelación.
    $suplentes = [];
    foreach ($orden as $idx => $p) {
        if (!$seleccionado[$idx]) $suplentes[] = $p;
    }

    return [
        'orden_general' => $orden,
        'bancas'        => $bancas,
        'seleccionados' => $seleccionados,
        'suplentes'     => $suplentes,
        'stats'         => ppc_estadisticas($participantes),
        'notas'         => $notas,
    ];
}
