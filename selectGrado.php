<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Perfil Docentes</title>
	<link rel="stylesheet" href="css/bootstrap.css">
</head>
<body>
		<div class="container">
			<div class="row bg-success">
				<div class="col-xs-4">
					<table class="table table-condensed ">
						<thead>
						</thead>
						<tbody>
							<tr>
								<th>Administrador:</th>
								<td id = "nombre">
									<h3>INIT computer</h3>
								</td>	
							</tr>
						</tbody>
					</table>
				</div>
				<div class="col-xs-4"></div> 
				<div class="col-xs-4" id="session">
					
				</div>
			</div>
			<div class="row">
				<div class="col-xs-4">
					<img src="images/headindex.png" class="img-responsive" alt="Responsive image">	
				</div>
				<div class="col-xs-8">
						<h1>BIENVENIDOS</h1><br>
					<p class="lead">Fecha: <div id="dia"></div><div id="mes"></div><div id="anio"></div></p>
					<script>
						var date = new Date();
						var anio = date.getFullYear();
						var mes = (date.getMonth() +1);
						var dia = date.getDate();
						document.getElementById('dia').innerHTML = dia;
						document.getElementById('anio').innerHTML = "del "+anio;
						switch(mes) {
   							case 1:
                                                        document.getElementById('mes').innerHTML = 'enero';
                                                        break;
							case 2:
                                                        document.getElementById('mes').innerHTML = 'febrero';
                                                        break;
							case 3:
                                                        document.getElementById('mes').innerHTML = 'marzo';
                                                        break;
							case 4:
                                                        document.getElementById('mes').innerHTML = 'abril';
                                                        break;
							case 5:
                                                        document.write('mayo');
                                                        break;
							case 6:
        						document.getElementById('mes').innerHTML = 'Junio';
        						break;
    							case 7:
                                                        document.getElementById('mes').innerHTML = 'julio';
                                                        break;
        						case 8:
                                                        document.getElementById('mes').innerHTML = 'agosto';
                                                        break;
        						case 9:
                                                        document.getElementById('mes').innerHTML = 'septiembre';
                                                        break;
							case 10:
                                                        document.getElementById('mes').innerHTML = 'octubre';
                                                        break;
						}
						
					</script>
				</div>
			</div>
			<div class="row">
				<div class="container">
					<div class="col-xs-12" id="asignaciones"></div>
				</div>
			</div>
			<br>
			<br>
			<div class="row">
				<div class="col-xs-4"></div>
				<div class="col-xs-4">Derechos reservados Colegio la familia</div>
				<div class="col-xs-4"></div>
			</div>
		</div>
	<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js'></script>
	<script type='text/javascript' src="js/jquery-2.1.1.js"></script>
	<script type="text/javascript" src="js/modelo.js"></script>
	<script type="text/javascript" src="js/controlador.js"></script>
	<script>
	colegioGradosRel();
	</script>
</body>
</html>
