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

// --- Función utilitaria para leer el Excel y devolver filas normalizadas
function leerResultadosDesdeExcel(string $rutaExcel): array {
    if (!file_exists($rutaExcel)) {
        throw new RuntimeException("No se encontró el Excel en: {$rutaExcel}");
    }

    $spreadsheet = IOFactory::load($rutaExcel);
    $sheet = $spreadsheet->getActiveSheet();

    // toArray con claves A,B,C... para poder mapear por encabezado
    $rowsRaw = $sheet->toArray(null, true, true, true);
    if (!$rowsRaw || count($rowsRaw) < 2) {
        throw new RuntimeException("El Excel no tiene filas suficientes.");
    }

    // 1) Encabezados
    $header = array_shift($rowsRaw);
    $norm = function($s){ $s = is_string($s)?$s:(string)$s; return preg_replace('/\s+/u',' ',trim($s)); };
    $H = [];
    foreach ($header as $col => $title) {
        $H[$norm($title)] = $col; // "texto" => "A/B/C..."
    }

    // 2) Columnas esperadas (tal cual tu planilla)
    $need = [
        'INSTITUCIÓN EDUCATIVA:'                                             => null,
        'DATOS CONCEJAL ESTUDIANTIL TITULAR. NOMBRE Y APELLIDO:'             => null,
        'DATOS CONCEJAL ESTUDIANTIL SUPLENTE. NOMBRE Y APELLIDO:'            => null,
        'Concejal'                                                           => null,
    ];
    foreach ($need as $label => $_) {
        foreach ($H as $t => $c) {
            if (mb_strtolower($t,'UTF-8') === mb_strtolower($label,'UTF-8')) {
                $need[$label] = $c; break;
            }
        }
        if ($need[$label] === null) {
            throw new RuntimeException("Falta la columna en el Excel: {$label}");
        }
    }

    // 3) Armar filas limpias
    $out = [];
    foreach ($rowsRaw as $r) {
        $inst = trim((string)($r[$need['INSTITUCIÓN EDUCATIVA:']] ?? ''));
        $tit  = trim((string)($r[$need['DATOS CONCEJAL ESTUDIANTIL TITULAR. NOMBRE Y APELLIDO:']] ?? ''));
        $sup  = trim((string)($r[$need['DATOS CONCEJAL ESTUDIANTIL SUPLENTE. NOMBRE Y APELLIDO:']] ?? ''));
        $con  = trim((string)($r[$need['Concejal']] ?? ''));
        if ($inst === '' && $tit === '' && $sup === '' && $con === '') continue;

        $out[] = ['inst'=>$inst, 'tit'=>$tit, 'sup'=>$sup, 'con'=>$con];
    }

    return $out;
}
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
        <h1>Para el Parlamento Estudiantil Inclusivo 2025</h1>
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
                            <li>AGUIRRE JOSÉ NICOLAS</li>
                            <li>AMARILLA BARBOZA MAXIMILIANO RUBEN HERNANDO</li>
                            <li>ANTONELLA MENDEZ</li>
                            <li>ASAÍ ABIGAIL LOPEZ</li>
                            <li>AYMARA MICAELA LUGO</li>
                            <li>BARBIERI ANNA</li>
                            <li>BARREIRO LAUTARO EZEQUIEL</li>
                            <li>BENITEZ AGUSTINA</li>
                            <li>BENITEZ MARCOS NAHUEL</li>
                            <li>BRAGA ARIANA</li>
                            <li>BYS, PABLO EMANUEL</li>
                            <li>CANDELA TOMAS</li>
                            <li>CARVALLO THOMAS</li>
                            <li>CELESTE MONSERRATH VALLEJOS</li>
                            <li>CROUCCIEE SANTINO HORACIO</li>
                            <li>DE SANCTIS, DONATO AUGUSTO</li>
                            <li>DECHAT THEISEN ELIAS ANGEL</li>
                            <li>ELIAS GALARZA</li>
                            <li>ELIAS JOAQUÍN GALARZA</li>
                            <li>ENRIQUEZ ROCÍO BELÉN</li>
                            <li>FAUSTO MARTINEZ</li>
                            <li>FIGUEROA ESCALANTE MARÍA FERNANDA</li>
                            <li>GASTON JASAEL HOFMARKSRICHTER</li>
                            <li>IRALA LEONEL</li>
                            <li>KAREN ITATI ANZOASTEGUI</li>
                            <li>KULCSAR DANIELA VICTORIA</li>
                            <li>LUCIANO BARRIOS</li>
                            <li>LUCIANO FABIAN BARRIOS</li>
                            <li>LUCAS BAUTISTA ZACH</li>
                            <li>LUCIA ITATÍ SAMUDIO</li>
                            <li>MAIRA MAGALI MENDEZ</li>
                            <li>MERENDA NAZARENA CRISTINA</li>
                            <li>NUÑEZ LARA VALENTINA</li>
                            <li>OBISPO MIJAIL ALEJANDRO</li>
                            <li>ORREGO CHIRIVE AYLEN</li>
                            <li>OSINSKI DAVID</li>
                            <li>PABLO EMANUEL BYS</li>
                            <li>RODRIGUEZ EVELYN ANISIA</li>
                            <li>ROMERO ULISES</li>
                            <li>SAMUDIO ANTONELA NOELIA</li>
                            <li>SOFÍA ETCHEGOIN</li>
                            <li>TAMARA DAHIANA BETTKER</li>
                            <li>TRINIDAD VALERIA</li>
                            <li>VARELA DALIA VANESA MAILEN</li>
                            <li>VIANA XIUMARA NAOMI</li>
                            <li>VICENTE ERNESTINA GIANELLA</li>
                            <li>VICTORIA CORREA</li>
                            <li>VILLALBA ADRIANO JOSEMIR</li>
                            <li>VILLALBA ANYELEN AIMARA</li>
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
            // === POST: simulación ya corrió; ahora mostrar resultado desde Excel ===
            $error = null; $resultados = [];
            try {
                $resultados = leerResultadosDesdeExcel(__DIR__ . '/Hoja de cálculo sin título.xlsx');
            } catch (Throwable $e) {
                $error = $e->getMessage();
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

