<?php
/**
 * Created by PhpStorm.
 * User: matias
 * Date: 03/09/18
 * Time: 11:59
 */


?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Script para el sorteo de bancas">
    <meta name="author" content="HCD Posadas">
    <link rel="icon" href="favicon.png">

    <title>Sorteo de Bancas</title>

    <!-- Bootstrap core CSS -->
    <!--    <link href="http://localhost/sorteo-bancas/dist/bundle.js" rel="stylesheet">-->

    <script src="./dist/bundle.js"></script>


</head>

<body class="bg-light">

<div class="container">
    <div class="py-5 text-center">
        <img class="d-block mx-auto mb-4" src="./logo.png" alt="logo">
        <h2>Sorteo de Bancas</h2>
        <p class="lead">Para el Parlamento de la Mujer
        </p>
        <p>
            (si desea subir una planilla este es el formato válido )<br>
            <a href="example.xlsx" class="btn btn-primary">
                <i class="fa fa-file-excel-o"></i> Descargar Ejemplo</a>
        </p>
    </div>

    <div class="row">
        <div class="col-md-12">
            <form method="post" name="sorteo" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label for="archivo">Archivo</label>
                            <div class="custom-file">
                                <input type="file" name="archivo" class="custom-file-input" id="archivo" lang="es">
                                <label class="custom-file-label" for="archivo">Seleccionar Archivo</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="total">Total</label>
                            <input required type="number" name="total" class="form-control" id="total"
                                   aria-describedby="total"
                                   value="<?php ( isset( $_POST['total'] ) ) ? print $_POST['total'] : '' ?>">
                            <small id="totalHelpBlock" class="form-text text-muted">
                                Si se sube la planilla este valor será ignorado.
                            </small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="titulares">Titulares</label>
                            <input required type="number" name="titulares" class="form-control" id="titulares"
                                   value="<?php ( isset( $_POST['titulares'] ) ) ? print $_POST['titulares'] : '' ?>">

                        </div>
                        <div class="form-group">
                            <label for="suplentes">Suplentes</label>
                            <input required type="number" name="suplentes" class="form-control" id="suplentes"
                                   value="<?php ( isset( $_POST['suplentes'] ) ) ? print $_POST['suplentes'] : '' ?>">

                        </div>
                    </div>
                </div>

                <button class="btn btn-primary btn-lg btn-block" type="submit">Realizar Sorteo</button>
            </form>
        </div>
    </div>

    <div class="row mt-1">
		<?php

		require 'vendor/autoload.php';

		use PhpOffice\PhpSpreadsheet\Spreadsheet;
		use PhpOffice\PhpSpreadsheet\Reader\Csv;
		use PhpOffice\PhpSpreadsheet\Reader\Xlsx;


		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {


			$total     = $_POST['total'];
			$sheetData = null;
			$etiquetaColumna2 = 'Nº Orden';

			$file_mimes = array(
				'text/x-comma-separated-values',
				'text/comma-separated-values',
				'application/octet-stream',
				'application/vnd.ms-excel',
				'application/x-csv',
				'text/x-csv',
				'text/csv',
				'application/csv',
				'application/excel',
				'application/vnd.msexcel',
				'text/plain',
				'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
			);

			if ( isset( $_FILES['archivo']['name'] ) && in_array( $_FILES['archivo']['type'], $file_mimes ) ) {

				$arr_file  = explode( '.', $_FILES['archivo']['name'] );
				$extension = end( $arr_file );

				if ( 'csv' == $extension ) {
					$reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
				} else {
					$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
				}

				$spreadsheet = $reader->load( $_FILES['archivo']['tmp_name'] );

				$sheetData = $spreadsheet->getActiveSheet()->toArray();

				$total = count( $sheetData );
				$etiquetaColumna2 = 'Nombre, Apellido y DNI';
//				print_r( $sheetData );
			}


			$titulares = $_POST['titulares'];
			$suplentes = $_POST['suplentes'];

			$input = range( 1, $total );

			if ( ( $titulares + $suplentes ) <= $total ) {
				$rand_keys = array_rand( $input, $titulares + $suplentes );
				$i         = 1;


				print( '<div class="col-md-6 mb-6">' );
				print( '<h4 class="d-flex justify-content-between align-items-center mb-3">' );
				print( '<span class="text-muted">Titulares</span>' );
				print( '</h4>' );
				print( '<ul class="list-group mb-3">' );
				print( '
				<li class="list-group-item d-flex justify-content-between">
                    <span>Posición</span>
                    <strong>'.$etiquetaColumna2.'</strong>
                </li>
				' );

				foreach ( $rand_keys as $key => $rand_key ) {
					if ( $i > $titulares ) {
						break;
					}
					print( '<li class="list-group-item d-flex justify-content-between lh-condensed">' );
					print( '<div><h6 class="my-0">#' . $i . '</h6></div>' );
					if ( $sheetData ) {

						print( '<span class="text-muted">' . $sheetData[ $rand_key ][0] . ', ' . $sheetData[ $rand_key ][1] . ' - ' . $sheetData[ $rand_key ][2] . '</span>' );
					} else {

						print( '<span class="text-muted">' . $input[ $rand_key ] . '</span>' );
					}

					print( '</li>' );

					unset( $rand_keys[ $key ] );

					$i ++;

				}


				print( '</ul>' );
				print( '</div>' );

//				suplentes

				print( '<div class="col-md-6 mb-6">' );
				print( '<h4 class="d-flex justify-content-between align-items-center mb-3">' );
				print( '<span class="text-muted">Suplentes</span>' );
				print( '</h4>' );
				print( '<ul class="list-group mb-3">' );
				print( '
				<li class="list-group-item d-flex justify-content-between">
                    <span>Posición</span>
                    <strong>'.$etiquetaColumna2.'</strong>
                </li>
				' );

				foreach ( $rand_keys as $key => $rand_key ) {

					print( '<li class="list-group-item d-flex justify-content-between lh-condensed">' );
					print( '<div><h6 class="my-0">#' . $i . '</h6></div>' );
					if ( $sheetData ) {

						print( '<span class="text-muted">' . $sheetData[ $rand_key ][0] . ', ' . $sheetData[ $rand_key ][1] . ' - ' . $sheetData[ $rand_key ][2] . '</span>' );
					} else {

						print( '<span class="text-muted">' . $input[ $rand_key ] . '</span>' );
					}

					print( '</li>' );

					$i ++;
				}


				print( '</ul>' );
				print( '</div>' );

			}

		}
		?>

    </div>

    <footer class="my-5 pt-5 text-muted text-center text-small">
        <p class="mb-1">&copy; 2018 HCD Posadas</p>
    </footer>
</div>

</body>
</html>