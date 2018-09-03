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
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.png">

    <title>Checkout example for Bootstrap</title>

    <!-- Bootstrap core CSS -->
    <!--    <link href="http://localhost/sorteo-bancas/dist/bundle.js" rel="stylesheet">-->

    <script src="./dist/bundle.js"></script>


</head>

<body class="bg-light">

<div class="container">
    <div class="py-5 text-center">
        <img class="d-block mx-auto mb-4" src="./logo.png" alt="logo">
        <h2>Sorteo de Bancas</h2>
        <p class="lead">Para el Parlamento de la Mujer</p>
    </div>

    <div class="row">
        <div class="col-md-12">
            <form method="post" name="sorteo">
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label for="total">Total</label>
                            <input required type="number" name="total" class="form-control" id="total"
                                   aria-describedby="total"
                                   value="<?php ( isset( $_POST['total'] ) ) ? print $_POST['total'] : '' ?>">
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
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
			$total     = $_POST['total'];
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
				print('
				<li class="list-group-item d-flex justify-content-between">
                    <span>Posición</span>
                    <strong>Nº Orden</strong>
                </li>
				');

				foreach ( $rand_keys as $key => $rand_key ) {
					if ($i > $titulares){
						break;
					}

					print( '<li class="list-group-item d-flex justify-content-between lh-condensed">' );
					print( '<div><h6 class="my-0">#' . $i . '</h6></div>' );
					print( '<span class="text-muted">' . $input[ $rand_key ] . '</span>' );
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
				print('
				<li class="list-group-item d-flex justify-content-between">
                    <span>Posición</span>
                    <strong>Nº Orden</strong>
                </li>
				');

				foreach ( $rand_keys as $key => $rand_key ) {

					print( '<li class="list-group-item d-flex justify-content-between lh-condensed">' );
					print( '<div><h6 class="my-0">#' . $i . '</h6></div>' );
					print( '<span class="text-muted">' . $input[ $rand_key ] . '</span>' );
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