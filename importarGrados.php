<!-- http://ProgramarEnPHP.wordpress.com -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>:: Importar Grados a la Base de Datos ::</title>
</head>

<body>
<!-- FORMULARIO PARA SOICITAR LA CARGA DEL EXCEL -->
Selecciona el archivo a importar:
<form name="importa" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data" >
<input type="file" name="excel" />
<input type='submit' name='enviar'  value="Importar"  />
<input type="hidden" value="upload" name="action" />
</form>
<!-- CARGA LA MISMA PAGINA MANDANDO LA VARIABLE upload -->

<?php 
header("Content-Type: text/html;charset=utf-8");
extract($_POST);
if ($action == "upload"){
//cargamos el archivo al servidor con el mismo nombre
//solo le agregue el sufijo bak_ 
	$archivo = $_FILES['excel']['name'];
	$tipo = $_FILES['excel']['type'];
	$destino = "bak_".$archivo;
	if (move_uploaded_file($_FILES['excel']['tmp_name'],$destino)) echo "Archivo Cargado Con Éxito";
	else echo "Error Al Cargar el Archivo";
////////////////////////////////////////////////////////
if (file_exists ("bak_".$archivo)){ 
/** Clases necesarias */
require_once('Classes/PHPExcel.php');
require_once('Classes/PHPExcel/Reader/Excel2007.php');
include 'modelo.php';
// Cargando la hoja de cálculo
$objReader = new PHPExcel_Reader_Excel2007();
$objPHPExcel = $objReader->load("bak_".$archivo);
$objFecha = new PHPExcel_Shared_Date();       

// Asignar hoja de excel activa
$objPHPExcel->setActiveSheetIndex(0);

//conectamos con la base de datos 
//$cn = mysql_connect ("localhost","root","pass") or die ("ERROR EN LA CONEXION");
//$db = mysql_select_db ("escuela",$cn) or die ("ERROR AL CONECTAR A LA BD");
	
        // Llenamos el arreglo con los datos  del archivo xlsx
for ($i=1;$i<=21;$i++){
	$_DATOS_EXCEL[$i]['nombreGrado'] = $objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
	$_DATOS_EXCEL[$i]['seccionGrado'] = $objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
	$_DATOS_EXCEL[$i]['jornadaGrado'] = $objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
	$_DATOS_EXCEL[$i]['anioGrado'] = $objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();
}

$gradosNuevos = new Colegio();
for ($i=1;$i<=21;$i++){
	echo "<br> <br>";
        echo $_DATOS_EXCEL[$i]['nombreGrado'];
	echo " ";
        echo $_DATOS_EXCEL[$i]['seccionGrado'];
	echo " ";
        echo $_DATOS_EXCEL[$i]['jornadaGrado'];
    echo " ";
    	echo $_DATOS_EXCEL[$i]['anioGrado'];
	$gradosNuevos->almacenarGrados($_DATOS_EXCEL[$i]['nombreGrado'],$_DATOS_EXCEL[$i]['seccionGrado'],$_DATOS_EXCEL[$i]['jornadaGrado'],$_DATOS_EXCEL[$i]['anioGrado']);
	echo "<br><br>";
}
}
//si por algo no cargo el archivo bak_ 
else{echo "Necesitas primero importar el archivo";}
$errores=0;
//recorremos el arreglo multidimensional 
//para ir recuperando los datos obtenidos
//del excel e ir insertandolos en la BD
/*foreach($_DATOS_EXCEL as $campo => $valor){
	//$sql = "INSERT INTO alumnos VALUES (NULL,'";
	foreach ($valor as $campo2 => $valor2){
		//$campo2 == "sexo" ? $sql.= $valor2."');" : $sql.= $valor2."','";
		echo "$valor2";	
	}
	//$result = mysql_query($sql);
	if (!$result){ echo "Error al insertar registro ".$campo;$errores+=1;}
}*/	
/////////////////////////////////////////////////////////////////////////

echo "<strong><center>ARCHIVO IMPORTADO CON EXITO, Importe los grados con exito</center></strong>";
//una vez terminado el proceso borramos el 
//archivo que esta en el servidor el bak_
unlink($destino);
}

?>
</body>
</html>
