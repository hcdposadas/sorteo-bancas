<?php
/**
 * Created by PhpStorm.
 * User: matias
 * Date: 03/09/18
 * Time: 11:59
 *
 * Versión determinística (simula pero no sortea):
 * - Muestra spinner y retraso
 * - Lee el Excel "Hoja de cálculo sin título.xlsx"
 * - Renderiza RESULTADOS en dos columnas de listas como tu maqueta
 */

require __DIR__ . '/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

// Lista de autoridades (concejales)
$autoridades = [
    'Jair Dib',
    'Laura Traid',
    'Maria Eva Jiménez',
    'Luciana Scromeda',
    'Dardo Romero',
    'Hector Cardozo',
    'Mazal Malena',
    'Valeria GdO',
    'Salom Judith',
    'Samira Almiron',
    'Horacio Martinez',
    'Santiago Koch',
    'Argañaraz Pablo',
    'Pablo Velazquez',
    'Secretario',
    'Prosec. Leg',
    'Prosec Adm',
    'Defensoria del Pueblo'
];

// Lista de participantes con DNI
$participantes = [
    'ALMARAZ ISABEL DE JESUS|6247236', 
    'GONZÁLEZ TERESA RAQUEL|16486984',
    'LENGUAZA JUSTO GERMAN|11844121',
    'MARTINEZ CARMEN E.|14593817',
    'MONGES MARTHA ELVIRA|4414290',
    'DE LA VEGA MARÍA ELISA|10267932',
    'FLORENTÍN GONZALEZ MARÍA HILDA|16705381',
    'AMARILLA BLANCA ROSA|13004256',
    'ARANDA LUIS EDUARDO|11694645',
    'BARBOZA MARIA HORTENSIA|14946456',
    'BENITEZ ELIDA MARTA|14469454',
    'BERNAL MARÍA DEL CARMEN|13591737',
    'BOGADO STELLA MARYS|13732427',
    'BORDON CATALINO|7666690',
    'BORDON VIRGINIA|6048792',
    'BRITES CARMEN GRACIELA|6175683',
    'BUHLER BERTA|5163469',
    'CARDOZO MÓNICA ALEJANDRA|18696547',
    'CASAFUS HUGO FRANCISCO|12118339',
    'CHAVES MERCEDES|93974234',
    'DÁVALOS BRITEZ ELEUTERIA|18895434',
    'DIAZ GRACIELA CAROLINA|16829013',
    'DIAZ MARIA LUISA RUIZ|16650558',
    'DIRANI CESAR RICARDO|5219996',
    'DUARTE LEOPOLDINA MIRTA|17039506',
    'ESQUIVEL MARÍA ESTER|6170598',
    'FIGUEREDO NIDIA BEATRIZ|13884222',
    'FIGUEREDO SILVIA ELENA|13004680',
    'GARCETE RUBEN DARIO|12207617',
    'GERZELY MATILDE|10268811',
    'KRAUSE MARÍA CRISTINA|6519638',
    'LANDRISCINA ROXANA EDITH|17253018',
    'MACHADO TIARA|48475141',
    'MAIDANA GRACIELA ISABEL|11382541',
    'MARTÍNEZ MABEL|13197598',
    'MONTERO ANDRÉS CARLOS|14653121',
    'NOVOTKA MARCELA PABLA|24742716',
    'OVIEDO MARIO DOMINGO|10782447',
    'QUIROGA SILVIA MARIA|17525559',
    'QUIROZ MIRTA GLADIS|13905130',
    'RAMIREZ CLOTILDE ELISA|5942891',
    'RAVE MARTA ROSA|16412301',
    'RIBERO STELLA MARY|10848698',
    'RIESTRA FRANCISCO ALFREDO|17630484',
    'RIOS AURORA DE JESÚS|17299863',
    'RIVERO ALVARO RUBIO|19036687',
    'RODRÍGUEZ BLANCA|4539861',
    'RODRÍGUEZ DELIA LUISA|11313216',
    'ROGOVSKI ANA MARIA|14811934',
    'ROGOZINSKI LIDIA NELIDA|4536707',
    'RUIZ DIAZ DORA O.|92204718',
    'RUIZ DIAZ MARIA ROSARIO|13326664',
    'RUIZ MARÍA ITATÍ|12327583',
    'SEGOVIA MARÍA DEL CARMEN|14713278',
    'SORIA MIRTA CLARA|11915021',
    'SOSA VÍCTOR HUGO|12718503',
    'VALIENTE CRISTINA AIDÉ|16188853',
    'VIBERO HILDA ROSA|3921615',
    'VILLALBA PORFIRIA|12898273'
];

// Mezclar aleatoriamente los participantes
shuffle($participantes);

// Crear las 18 bancas
$resultados = [];
for ($i = 0; $i < 18; $i++) {
    $titularData = explode('|', $participantes[$i * 2] ?? 'Sin asignar|');
    $suplenteData = explode('|', $participantes[($i * 2) + 1] ?? 'Sin asignar|');
    $concejal = $autoridades[$i] ?? 'No asignado';

    $resultados[] = [
        'tit' => $titularData[0] . (isset($titularData[1]) ? ' (DNI: ' . $titularData[1] . ')' : ''),
        'sup' => $suplenteData[0] . (isset($suplenteData[1]) ? ' (DNI: ' . $suplenteData[1] . ')' : ''),
        'con' => $concejal
    ];
}

// Partir en dos listas como tu maqueta: la primera con 9 bancas, el resto en la segunda
$primerBloque = array_slice($resultados, 0, 9);
$segundoBloque = array_slice($resultados, 9);

// Render helper para un bloque de filas
$renderBloque = function(array $filas, int $offsetBanca = 0) {
    // Título
    echo '<ul class="list-group mb-3 shadow">';
    echo '<li class="list-group-item" style="display:grid;grid-template-columns:80px 1fr 1fr 1.5fr;">';
    echo '<span>Banca</span><strong>Concejal</strong><strong>Titular</strong><strong>Institución</strong>';
    echo '</li>';

    foreach ($filas as $idx => $r) {
        $banca = $offsetBanca + $idx + 1;
        $concejal = strtoupper($r['con']);
        $tit = $r['tit'];
        $institucion = $r['inst'];
        echo '<li class="list-group-item lh-condensed" style="display:grid;grid-template-columns:80px 1fr 1fr 1.5fr;border-top:1px solid #ccc;">';
        echo '<div><h3 class="my-0">#' . $banca . '</h3></div>';
        echo '<span class="text-muted">' . htmlspecialchars($concejal) . '</span>';
        echo '<span class="text-muted">' . htmlspecialchars($tit) . '</span>';
        echo '<span class="text-muted">' . htmlspecialchars($institucion) . '</span>';
        echo '</li>';
    }

    echo '</ul>';
};
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Script para el sorteo de bancas">
    <meta name="author" content="HCD Posadas">
    <link rel="icon" href="favicon.png">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <title>Sorteo de Bancas</title>

    <script src="./dist/bundle.js"></script>

    <style>
        body { font-size: 1rem; font-weight: 900; }
        .text-muted { color: #000 !important; }
        span { text-transform: uppercase; display: inline-flex; align-items: center; }
        .list-group-item:nth-child(odd) { background-color: #f9f9f9; }
    </style>
</head>
<body class="bg-light">
<div class="container">
    <div class="py-2 text-center">
        <img class="d-block mx-auto mb-0" style="width: 200px;"
             src="logo.png" alt="logo">
        <h1>Sorteo de Bancas</h1>
        <h1>Para el Parlamento Adulto Mayor 2025</h1>
    </div>

    <div class="row">
        <div class="col-md-12">
        <?php if ($_SERVER['REQUEST_METHOD'] !== 'POST'): ?>
            <!-- FORM: solo para disparar la simulación -->
            <form id="sorteoForm" method="post" name="sorteo" enctype="multipart/form-data">
                <div class="row">
                    <div id="listaInscriptos" style="padding-left:40px"
                         class="col-md-12 card p-8 mt-4 mb-4 bg-light shadow border border-muted text-center text-muted rounded">
                        <h3 class="text-center m-4">LISTA DE INSCRIPTOS</h3>
                        <ol style="column-count: 2; padding-left: 20px;">
                        <li>ALMARAZ ISABEL DE JESUS</li>
<li>AMARILLA BLANCA ROSA</li>
<li>ARANDA LUIS EDUARDO</li>
<li>BARBOZA MARIA HORTENSIA</li>
<li>BENITEZ ELIDA MARTA</li>
<li>BERNAL MARÍA DEL CARMEN</li>
<li>BOGADO STELLA MARYS</li>
<li>BORDON CATALINO</li>
<li>BORDON VIRGINIA</li>
<li>BRITES CARMEN GRACIELA</li>
<li>BUHLER BERTA</li>
<li>CARDOZO MÓNICA ALEJANDRA</li>
<li>CASAFUS HUGO FRANCISCO</li>
<li>CHAVES MERCEDES</li>
<li>DÁVALOS BRITEZ ELEUTERIA</li>
<li>DE LA VEGA MARÍA ELISA</li>
<li>DIAZ GRACIELA CAROLINA</li>
<li>DIAZ MARIA LUISA RUIZ</li>
<li>DIRANI CESAR RICARDO</li>
<li>DUARTE LEOPOLDINA MIRTA</li>
<li>ESQUIVEL MARÍA ESTER</li>
<li>FIGUEREDO NIDIA BEATRIZ</li>
<li>FIGUEREDO SILVIA ELENA</li>
<li>FLORENTÍN GONZALEZ MARÍA HILDA</li>
<li>GARCETE RUBEN DARIO</li>
<li>GERZELY MATILDE</li>
<li>GONZÁLEZ TERESA RAQUEL</li>
<li>KRAUSE MARÍA CRISTINA</li>
<li>LANDRISCINA ROXANA EDITH</li>
<li>LENGUAZA JUSTO GERMAN</li>
<li>MACHADO TIARA</li>
<li>MAIDANA GRACIELA ISABEL</li>
<li>MARTÍNEZ MABEL</li>
<li>MARTINEZ CARMEN E.</li>
<li>MONGES MARTHA ELVIRA</li>
<li>MONTERO ANDRÉS CARLOS</li>
<li>NOVOTKA MARCELA PABLA</li>
<li>OVIEDO MARIO DOMINGO</li>
<li>QUIROGA SILVIA MARIA</li>
<li>QUIROZ MIRTA GLADIS</li>
<li>RAMIREZ CLOTILDE ELISA</li>
<li>RAVE MARTA ROSA</li>
<li>RIBERO STELLA MARY</li>
<li>RIESTRA FRANCISCO ALFREDO</li>
<li>RIOS AURORA DE JESÚS</li>
<li>RIVERO ALVARO RUBIO</li>
<li>RODRÍGUEZ BLANCA</li>
<li>RODRÍGUEZ DELIA LUISA</li>
<li>ROGOVSKI ANA MARIA</li>
<li>ROGOZINSKI LIDIA NELIDA</li>
<li>RUIZ DIAZ DORA O.</li>
<li>RUIZ DIAZ MARIA ROSARIO</li>
<li>RUIZ MARÍA ITATÍ</li>
<li>SEGOVIA MARÍA DEL CARMEN</li>
<li>SORIA MIRTA CLARA</li>
<li>SOSA VÍCTOR HUGO</li>
<li>VALIENTE CRISTINA AIDÉ</li>
<li>VIBERO HILDA ROSA</li>
<li>VILLALBA PORFIRIA</li>
                        </ol>
                    </div>
                </div>

                <button id="submitBtn" class="btn btn-primary btn-lg btn-block" type="submit">Realizar Sorteo</button>
                <div id="spinner" class="text-center mt-4" style="display:none;">
                    <div class="spinner-border" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                </div>
            </form>
        <?php else: ?>
            <?php
            // Procesar el sorteo
            $error = null;
            $resultados = [];
            
            // Lista de participantes con DNI
            $participantesConDni = [
                'ALMARAZ ISABEL DE JESUS|6247236', 'AMARILLA BLANCA ROSA|13004256', 'ARANDA LUIS EDUARDO|11694645',
                'BARBOZA MARIA HORTENSIA|14946456', 'BENITEZ ELIDA MARTA|14469454', 'BERNAL MARÍA DEL CARMEN|13591737',
                'BOGADO STELLA MARYS|13732427', 'BORDON CATALINO|7666690', 'BORDON VIRGINIA|6048792',
                'BRITES CARMEN GRACIELA|6175683', 'BUHLER BERTA|5163469', 'CARDOZO MÓNICA ALEJANDRA|18696547',
                'CASAFUS HUGO FRANCISCO|12118339', 'CHAVES MERCEDES|93974234', 'DÁVALOS BRITEZ ELEUTERIA|18895434',
                'DE LA VEGA MARÍA ELISA|10267932', 'DIAZ GRACIELA CAROLINA|16829013', 'DIAZ MARIA LUISA RUIZ|16650558',
                'DIRANI CESAR RICARDO|5219996', 'DUARTE LEOPOLDINA MIRTA|17039506', 'ESQUIVEL MARÍA ESTER|6170598',
                'FIGUEREDO NIDIA BEATRIZ|13884222', 'FIGUEREDO SILVIA ELENA|13004680', 'FLORENTÍN GONZALEZ MARÍA HILDA|16705381',
                'GARCETE RUBEN DARIO|12207617', 'GERZELY MATILDE|10268811', 'GONZÁLEZ TERESA RAQUEL|16486984',
                'KRAUSE MARÍA CRISTINA|6519638', 'LANDRISCINA ROXANA EDITH|17253018', 'LENGUAZA JUSTO GERMAN|11844121',
                'MACHADO TIARA|48475141', 'MAIDANA GRACIELA ISABEL|11382541', 'MARTÍNEZ MABEL|13197598',
                'MARTINEZ CARMEN E.|14593817', 'MONGES MARTHA ELVIRA|4414290', 'MONTERO ANDRÉS CARLOS|14653121',
                'NOVOTKA MARCELA PABLA|24742716', 'OVIEDO MARIO DOMINGO|10782447', 'QUIROGA SILVIA MARIA|17525559',
                'QUIROZ MIRTA GLADIS|13905130', 'RAMIREZ CLOTILDE ELISA|5942891', 'RAVE MARTA ROSA|16412301',
                'RIBERO STELLA MARY|10848698', 'RIESTRA FRANCISCO ALFREDO|17630484', 'RIOS AURORA DE JESÚS|17299863',
                'RIVERO ALVARO RUBIO|19036687', 'RODRÍGUEZ BLANCA|4539861', 'RODRÍGUEZ DELIA LUISA|11313216',
                'ROGOVSKI ANA MARIA|14811934', 'ROGOZINSKI LIDIA NELIDA|4536707', 'RUIZ DIAZ DORA O.|92204718',
                'RUIZ DIAZ MARIA ROSARIO|13326664', 'RUIZ MARÍA ITATÍ|12327583', 'SEGOVIA MARÍA DEL CARMEN|14713278',
                'SORIA MIRTA CLARA|11915021', 'SOSA VÍCTOR HUGO|12718503', 'VALIENTE CRISTINA AIDÉ|16188853',
                'VIBERO HILDA ROSA|3921615', 'VILLALBA PORFIRIA|12898273'
            ];
            
            // Lista de participantes para mostrar (sin DNI)
            $participantes = array_map(function($item) {
                return explode('|', $item)[0];
            }, $participantesConDni);
            
            // Personas que no pueden ser sorteadas
            $excluidos = [
                'ALMARAZ ISABEL DE JESUS',
                'GONZÁLEZ TERESA RAQUEL',
                'LENGUAZA JUSTO GERMAN',
                'MARTINEZ CARMEN E.',
                'MONGES MARTHA ELVIRA',
                'DE LA VEGA MARÍA ELISA',
                'FLORENTÍN GONZALEZ MARÍA HILDA'
            ];
            
            // Personas con posiciones fijas
            $fijos = [
                'DE LA VEGA MARÍA ELISA' => 'titular',
                'FLORENTÍN GONZALEZ MARÍA HILDA' => 'suplente',
                'BARBOZA MARIA HORTENSIA' => 'titular_hector'
            ];
            
            // Buscar a las personas fijas en la lista completa de participantes
            $titularFijo = null;
            $suplenteFijo = null;
            $barbozaHortensia = null;
            
            foreach ($participantesConDni as $participante) {
                if (strpos($participante, 'DE LA VEGA MARÍA ELISA|') === 0) {
                    $titularFijo = $participante;
                }
                if (strpos($participante, 'FLORENTÍN GONZALEZ MARÍA HILDA|') === 0) {
                    $suplenteFijo = $participante;
                }
                if (strpos($participante, 'BARBOZA MARIA HORTENSIA|') === 0) {
                    $barbozaHortensia = $participante;
                }
            }
            
            // Filtrar participantes para el sorteo (excluyendo a los especificados y los de posiciones fijas)
            $participantesSorteo = array_filter($participantesConDni, function($item) use ($excluidos, $titularFijo, $suplenteFijo, $barbozaHortensia) {
                $nombre = explode('|', $item)[0];
                return !in_array($nombre, $excluidos) && 
                       $item !== $titularFijo && 
                       $item !== $suplenteFijo &&
                       $item !== $barbozaHortensia;
            });
            
            // Reindexar el array después de las eliminaciones
            $participantesSorteo = array_values($participantesSorteo);
            
            // Mezclar aleatoriamente los participantes restantes
            shuffle($participantesSorteo);
            
            // Crear las 18 bancas
            for ($i = 0; $i < 18; $i++) {
                $concejal = $autoridades[$i] ?? 'No asignado';
                
                // Verificar si es Mazal Malena o Hector Cardozo para asignar posiciones fijas
                if ($concejal == 'Mazal Malena') {
                    // Asignar las personas fijas para Mazal Malena
                    $titularData = $titularFijo ? explode('|', $titularFijo) : ['Sin asignar'];
                    $suplenteData = $suplenteFijo ? explode('|', $suplenteFijo) : ['Sin asignar'];
                } elseif ($concejal == 'Hector Cardozo' && $barbozaHortensia) {
                    // Asignar a BARBOZA MARIA HORTENSIA como titular de Hector Cardozo
                    $titularData = explode('|', $barbozaHortensia);
                    
                    // Buscar un suplente de los participantes restantes
                    $suplente = array_shift($participantesSorteo);
                    $suplenteData = $suplente ? explode('|', $suplente) : ['Sin asignar'];
                } else {
                    // Para las demás bancas, usar el sorteo normal
                    // Reindexar el array después de posibles eliminaciones
                    $participantesSorteo = array_values($participantesSorteo);
                    $titularData = isset($participantesSorteo[$i * 2]) ? 
                        explode('|', $participantesSorteo[$i * 2]) : 
                        ['Sin asignar'];
                    $suplenteData = isset($participantesSorteo[($i * 2) + 1]) ? 
                        explode('|', $participantesSorteo[($i * 2) + 1]) : 
                        ['Sin asignar'];
                }
                
                $resultados[] = [
                    'tit' => $titularData[0] . (isset($titularData[1]) ? ' (DNI: ' . $titularData[1] . ')' : ''),
                    'sup' => $suplenteData[0] . (isset($suplenteData[1]) ? ' (DNI: ' . $suplenteData[1] . ')' : ''),
                    'con' => $concejal
                ];
            }

            // Partir en dos listas como tu maqueta: la primera con 9 bancas, el resto en la segunda
            $primerBloque = array_slice($resultados, 0, 9);
            $segundoBloque = array_slice($resultados, 9);

            // Render helper para un bloque de filas
            $renderBloque = function(array $filas, int $offsetBanca = 0) {
                // Título
            echo '<ul class="list-group mb-3 shadow">';
            echo '<li class="list-group-item" style="display:grid;grid-template-columns:80px 1fr 1fr 1fr;">';
            echo '<span>BANCA</span><strong>CONCEJAL</strong><strong>TITULAR</strong><strong>SUPLENTE</strong>';
                echo '</li>';

                foreach ($filas as $idx => $r) {
                $banca = $offsetBanca + $idx + 1;
                $concejal = strtoupper($r['con']);
                
                // Procesar titular
                $titData = explode(' (DNI:', $r['tit']);
                $titName = trim($titData[0]);
                $titDni = isset($titData[1]) ? 'DNI: ' . rtrim($titData[1], ')') : '';
                
                // Procesar suplente
                $supData = explode(' (DNI:', $r['sup']);
                $supName = trim($supData[0]);
                $supDni = isset($supData[1]) ? 'DNI: ' . rtrim($supData[1], ')') : '';
                
                echo '<li class="list-group-item lh-condensed" style="display:grid;grid-template-columns:80px 1fr 1fr 1fr;border-top:1px solid #ccc;">';
                echo '<div><h3 class="my-0">#' . $banca . '</h3></div>';
                echo '<span class="text-muted">' . htmlspecialchars($concejal) . '</span>';
                echo '<div class="d-flex flex-column">';
                echo '<span class="text-muted">' . htmlspecialchars($titName) . '</span>';
                if (!empty($titDni)) {
                    echo '<small class="text-muted" style="font-size: 0.8em;">' . htmlspecialchars($titDni) . '</small>';
                }
                echo '</div>';
                echo '<div class="d-flex flex-column">';
                echo '<span class="text-muted">' . htmlspecialchars($supName) . '</span>';
                if (!empty($supDni)) {
                    echo '<small class="text-muted" style="font-size: 0.8em;">' . htmlspecialchars($supDni) . '</small>';
                }
                echo '</div>';
                    echo '</li>';
                }

                echo '</ul>';
            };
            ?>

            <div class="row mt-1">
                <div class="col-md-12 mb-6">
                    <h4 class="d-flex justify-content-center align-items-center mb-3">
                        <span class="text-muted">RESULTADOS</span>
                    </h4>

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php else: ?>
                        <?php
                        // Primer bloque (1..9)
                        $renderBloque($primerBloque, 0);
                        // Segundo bloque (10..N)
                        if (!empty($segundoBloque)) {
                            echo '<div style="margin-top:40px;">';
                            $renderBloque($segundoBloque, 9);
                            echo '</div>';
                        }
                        ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
        </div>
    </div>

    <footer class="my-5 pt-5 text-muted text-center text-small">
        <p class="mb-1">&copy; <?php print date('Y'); ?> HCD Posadas</p>
    </footer>
</div>

<?php if ($_SERVER['REQUEST_METHOD'] !== 'POST'): ?>
<script>
  // Simulación del "sorteo": spinner + retardo, luego POST real
  const form = document.getElementById('sorteoForm');
  form.addEventListener('submit', function (event) {
    event.preventDefault(); // evitar envío inmediato

    const lista = document.getElementById('listaInscriptos');
    if (lista) lista.style.display = 'none';

    document.getElementById('submitBtn').disabled = true;
    document.getElementById('spinner').style.display = 'block';

    // Retardo artificial para el show (2s)
    setTimeout(() => { event.target.submit(); }, 2000);
  });
</script>
<?php endif; ?>
</body>
</html>

