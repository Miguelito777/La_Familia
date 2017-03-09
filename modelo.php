<?php

/**
* Clase que realiza y finaliza una conexion
*/
class conexion
{
	public $host = "localhost";
	public $user = "root";
	public $password = "Jesus8";
	public $database = "colegio_la_familia";
	public $enlace;
	function __construct()
	{
		$this->enlace = mysql_connect($this->host,$this->user,$this->password);
		if (!$this->enlace) {
			die("Error en la conexion general: ".mysql_error());
		}
		if (!$database = mysql_select_db($this->database,$this->enlace)) {
			return false;
		}
		return true;
	}
	function closeconnect(){
		mysql_close($this->enlace);
	}
}

/**
* Clase para probar la conexion
*/
class log_in extends conexion
{
	public $usuario;
	public $passwordd;
	public $datosusuario;
	public $datosusuarioo;
	public $datosAdmin;
	function __construct($usuario, $passwordd)
	{
		$this->usuario = $usuario;
		$this->passwordd = $passwordd;
	}
	function is_docente(){
		$query = "SELECT idDocente, paswordDocente, nombresDocente from Docentes where paswordDocente = '$this->passwordd' and  nombresDocente = '$this->usuario'";	
		if (!parent:: __construct()) {
			die("Error el la conexion de localizacion de docente: "+mysql_error());
		}
		mysql_query("SET NAMES 'utf8'");
		if(!$this->datosusuario = mysql_query($query))
			die("Error el la consulta para localizacion del docente "+mysql_error());
		$this->closeconnect();
		if(mysql_num_rows($this->datosusuario) > 0){
			$docente = mysql_fetch_array($this->datosusuario,MYSQL_ASSOC);
			$_SESSION["id_docente"] = $docente["idDocente"];
			$_SESSION["Docente"] = new docente($_SESSION["id_docente"]);
			return true;
		}
		else
			return false;	
	}
	function is_estudiante(){
		$query = "SELECT idEstudiante, nombresEstudiante, Grados_idGrado from Estudiantes where paswordEstudiante = '$this->passwordd' and nombresEstudiante = '$this->usuario'";
		if (!parent:: __construct())
			die ("Error en la conexion para buscar a un estudiante"+mysql_error());
		mysql_query("SET NAMES 'utf8'");
		if(!$this->datosusuarioo = mysql_query($query))
			die("Error en la consulta para evaluar a un estudiante"+mysql_error());
		$this->closeconnect();
		$estenc = mysql_num_rows($this->datosusuarioo);
		if($estenc > 0){
			$estudiante = mysql_fetch_array($this->datosusuarioo,MYSQL_ASSOC);
            $_SESSION["Estudiante"] = new Estudiantes();
            $_SESSION["Estudiante"]->setIdEstudiante($estudiante["idEstudiante"]);
            $_SESSION["grado_estudiante"] = $estudiante["Grados_idGrado"];
            $_SESSION["Estudiante"]->gradoEstudiante = $estudiante["Grados_idGrado"];
            $_SESSION["id_estudiante"] = $estudiante["idEstudiante"];
			return true;
		}
		else
			return false;
	}	

	function is_Admin(){
		$query = "SELECT idSysAdmin, nombreAdmin, passwordAdmin from sisAdmin where passwordAdmin = '$this->passwordd' and nombreAdmin = '$this->usuario'";
		if (!parent:: __construct())
			die ("Error en la conexion para buscar a un estudiante"+mysql_error());
		mysql_query("SET NAMES 'utf8'");
		if(!$this->datosAdmin = mysql_query($query))
			die("Error en la consulta para evaluar a un estudiante"+mysql_error());
		$this->closeconnect();
		$estenc = mysql_num_rows($this->datosAdmin);
		if($estenc > 0){
			$sysAdmin = mysql_fetch_array($this->datosAdmin,MYSQL_ASSOC);
            $_SESSION["nombreAdmin"] = $sysAdmin["nombreAdmin"];
            $_SESSION["idSysAdmin"] = $sysAdmin["idSysAdmin"];
            $_SESSION["passwordAdmin"] = $sysAdmin["passwordAdmin"];
			return true;
		}
		else
			return false;
	}
}

/**
* Clase que administra el colegio
*/
class Colegio extends conexion
{
	public $grados;
	function __construct()
	{
	
	}
	function almacenarGrados($nombreGradoA,$seccionGrado,$jornadaGrado,$anioGrado){
		$anioGradoint = (int)$anioGrado;
		if (!parent:: __construct())
			die ("Error en la conexion para Almacenar los Grados".mysql_error());
		mysql_query("SET NAMES 'utf8'");
		$query = "INSERT into Grados(nombreGrado,grado_seccion,jornada,Anio_idanio) values('$nombreGradoA','$seccionGrado','$jornadaGrado',$anioGradoint)";
		if (!mysql_query($query)) {
			die("Error al almacenar grados: ".mysql_error());
		}
		$this->closeconnect();
	}

	function obtenerGrados(){
		$query = "SELECT * from Grados where Anio_idanio = 2";
		if (!parent:: __construct())
			die ("Error en la conexion para buscar a un estudiante"+mysql_error());
		if(!$this->grados = mysql_query($query))
			die("Error en la consulta para evaluar a un estudiante"+mysql_error());
		$this->closeconnect();
	}
	function extraerGrado(){
		if ($grado = mysql_fetch_array($this->grados,MYSQL_ASSOC))
			return $grado;
		else
			return false;
	}
	public function postNotice($idGrados,$textContent, $filesContent, $unidad){
		for ($i = 0; $i < count($idGrados); $i++) { 
			$query = "INSERT into NoticiasGrados(contenidoNoticiaGrado, archivoNoticiaGrado, idGrado, unidad) values('$textContent', '$filesContent', $idGrados[$i], $unidad)";
			if (!parent:: __construct())
				die ("Error en la conexion para Almacenar los Grados".mysql_error());
			mysql_query("SET NAMES 'utf8'");
			if (!mysql_query($query)) {
				die("Error al almacenar grados: ".mysql_error());
			}
			$this->closeconnect();
		}
		return true;
	}
}

class PDF extends FPDF
{
	// Cabecera de página
	function Header()
	{
    	// Logo
    	//$this->Image('images/headindex.png',10,8,33);
    	// Arial bold 15
    	$this->SetFont('Arial','B',15);
    	// Movernos a la derecha
    	$this->Cell(50);
    	// Título
    	$this->Cell(100,10,'Colegio Cientifico y Tecnologico EL PEDREGAL',0,0,'C');
    	$this->Ln(5);
    	$this->SetFont('Arial','B',10);
    	$this->Cell(50);
    	$this->Cell(100,10,'Direccion',0,0,'C');
    	$this->Ln(5);
    	$this->SetFont('Arial','B',7);
    	$this->Cell(50);
    	$this->Cell(100,10,'Tel. 7766-7184 / 5699-0471',0,0,'C');
    	$this->Ln(10);
    	$this->SetFont('Arial','B',18);
    	$this->Cell(50);
    	$this->Cell(100,10,'Tarjeta de Calificaciones',0,0,'C');
    	// Salto de línea
    	$this->Ln(11);
	}
	function BasicTable($header, $data,$unidad)
	{
    	// Cabecera
    	$this->SetFont('Arial','B',8);
    	for ($i = 0; $i < count($header); $i++){
    		if ($i == 0)
    			$this->Cell(53,7,$header[$i],1,0,'C');
    		else
    			$this->Cell(23,7,$header[$i],1,0,'C');
    	} 
    	$this->Ln();
    	// Datos
    	$this->SetFont('Arial','',7);
    	$unidadesUsadas = count($data[0]);
    	for ($i = 0; $i < count($data); $i++) { 
    		for ($j = 0; $j < count($header); $j++){
    			if ($j == 0)
    				$this->Cell(53,7,$data[$i][$j],1,0);
    			if ($j < $unidadesUsadas && $j != 0) {
    				$this->Cell(23,7,$data[$i][$j],1,0,'C');
    			}
    			if($j >= $unidadesUsadas ){
    				$this->Cell(23,7,"",1,0,'C');
    			}
    		}
    		$this->Ln();
    	}
	}
}


/**
* Clase que maneja las asignaciones
*/
class asignaciones extends conexion
{
	public $idDocente;
	public $asignacionestodas;
	function __construct($idDocente)
	{
		$this->idDocente = $idDocente;
	}
	function todas(){
		 $query = "SELECT PD.id_periodos_docentes, G.idGrado, G.nombreGrado, G.grado_seccion, G.jornada, M.nombreMateria from Grados G inner join Periodos_docentes PD on G.Anio_idanio = 2 and G.idGrado = PD.Grados_idGrado inner join Materias M on PD.Materias_idMateria = M.idMateria and PD.Docentes_idDocente = $this->idDocente";
		 if (!parent:: __construct()) {
		 	die("Error en la conexion de busqueda de asignaciones: ".mysql_error());
		 }
		 if (!$this->asignacionestodas = mysql_query($query)) {
		 	die("Error en la busque de todas las asignaciones: ".mysql_error());
		 }
		 $this->closeconnect();
	}
	function todas_extraer(){
		if(!$asignacionestodas = mysql_fetch_array($this->asignacionestodas,MYSQL_ASSOC))
			return false;
		else
			return $asignacionestodas;
	}
}

/**
* Clase que obtiene los datos de un docentes
*/
class docente extends conexion
{
	public $iddocente;
	function __construct($iddocente)
	{
		$this->iddocente = "$iddocente";
	}
	function docente_nombre(){
		$query = "SELECT nombresDocente from Docentes where idDocente = $this->iddocente";
		if(!parent::__construct()){
			die("Error en la conexion nombre docente: ".mysql_error());
		}
		mysql_query("SET NAMES 'utf8'");
		if(!$nombre = mysql_query($query)){
			die("Error al encontrar el nombre: ".mysql_error());
		}
		$this->closeconnect();
		$nombredocente = mysql_fetch_array($nombre,MYSQL_ASSOC);
		return $nombredocente["nombresDocente"];
	}
	public function setNotice($idAsignacion, $notice, $unidad){
		$query = "INSERT into Noticias(descripcionNoticia, idAsignacion, unidad) values('$notice',$idAsignacion,$unidad)";
		parent::__construct();
		mysql_query($query);
		$query = "SELECT * from Noticias where idNoticia = last_insert_id()";
		$newNotice = mysql_query($query);
		$this->closeconnect();
		$newNotice = mysql_fetch_array($newNotice,MYSQL_ASSOC);
		return json_encode($newNotice);
	}
	public function getNotices($idCurso, $unidad){
		$query = "SELECT * from Noticias where idAsignacion = $idCurso";
		parent::__construct();
		$newNotice = mysql_query($query);

		$this->closeconnect();
		$notices = array();
		while ($newNoticeA = mysql_fetch_array($newNotice,MYSQL_ASSOC)) {
			array_push($notices, $newNoticeA);
		}
		return json_encode($notices);
	}
}


/**
* Clase que manipula las tareas
*/
class tareas extends conexion
{
	public $idTarea;
	public $periodo_docentes;
	public $grado;
	public $periodo;
	public $tareas;
	public $estudiantes;
	public $notastareas;
	public $notatarea;
	public $detailWork = array();
	function __construct()
	{
		
	}

	public function setDigitalWork($idStudent, $pathWorck){
		$query = "UPDATE Nota_tareas set fileTarea = '$pathWorck' where Tareas_idTarea = $this->idTarea and Estudiantes_idEstudiante = $idStudent";
		if (!parent:: __construct()) 
			die("Error en la conexion para localizar las tareas "+mysql_error());
		else{
			mysql_query("SET NAMES 'utf8'");
			if (mysql_query($query))
				return true;
			else
				return false;
		}
	}

	public function getDetailsDigitalWork($idTarea, $idEstudiante){
		$query = "SELECT T.nombreTarea, T.fechaasignacionTarea, T.fechaentregaTarea, T.descripcionTarea, T.horaEntrega, T.valor, NT.Punteo_Tarea, NT.ObservacionTarea, NT.fileTarea from Tareas T inner join Nota_tareas NT on T.idTarea = NT.Tareas_idTarea and NT.Tareas_idTarea = $idTarea and Estudiantes_idEstudiante = $idEstudiante";
		if (!parent:: __construct()) 
			die("Error en la conexion para localizar las tareas "+mysql_error());
		if(!$detailWork = mysql_query($query))
			die("Error en la consutla de las notas "+mysql_error());
		else{
			if ($detailWork = mysql_fetch_array($detailWork,MYSQL_ASSOC)){
				array_push($this->detailWork, $detailWork);
			}
			else
				$this->detailWork = false;
		}
		$this->closeconnect();	
	}
	function notas($idcurso,$idestudiante,$unidad){
		$query = "SELECT T.idTarea, T.nombreTarea, T.fechaentregaTarea, NT.Punteo_Tarea, NT.ObservacionTarea from Tareas T inner join Nota_tareas NT on T.Unidad = $unidad and T.Periodos_docentes_id_periodos_docentes = $idcurso and T.idTarea = NT.Tareas_idTarea and NT.Estudiantes_idEstudiante = $idestudiante";
		if (!parent:: __construct()) 
			die("Error en la conexion para localizar las tareas "+mysql_error());
		if(!$this->notastareas = mysql_query($query))
			die("Error en la consutla de las notas "+mysql_error());
		$this->closeconnect();
	}

	function obtenernotas(){
		if ($notatarea = mysql_fetch_array($this->notastareas,MYSQL_ASSOC))
			return $notatarea;
		else
			return false;
	}
	// Funcion que obtiene la nota de una tarea
	function notaTarea($idTarea,$idEstudiante){
		$query = "SELECT Punteo_Tarea from Nota_tareas where Tareas_idTarea = $idTarea and Estudiantes_idEstudiante = $idEstudiante";
		if (!parent:: __construct()) 
			die("Error en la conexion para localizar las tareas "+mysql_error());
		if(!$this->notatarea = mysql_query($query))
			die("Error en la consutla de las notas "+mysql_error());
		$this->closeconnect();
	}

	function obtenerNotaTarea(){
		if ($notatarea = mysql_fetch_array($this->notatarea,MYSQL_ASSOC)){
			foreach ($notatarea as $key => $value) {
			//	echo "$value";
			}
			return $notatarea;
		}
			
		else
			return false;
	}
	// Funcion que obtiene la nota de una tarea para que el docente la administre
	function getPluckingWorkStudent($idTarea,$idEstudiante){
		$query = "SELECT T.nombreTarea, T.valor, NT.Punteo_Tarea, NT.ObservacionTarea, NT.fileTarea from Tareas T inner join Nota_tareas NT on T.idTarea = NT.Tareas_idTarea and NT.Tareas_idTarea = $idTarea and NT.Estudiantes_idEstudiante = $idEstudiante";
		if (!parent:: __construct()) 
			die("Error en la conexion para localizar las tareas "+mysql_error());
		if(!$notatarea = mysql_query($query))
			die("Error en la consutla de las notas "+mysql_error());
		else{
			$notatarea = mysql_fetch_assoc($notatarea);
			return $notatarea;
		}
		$this->closeconnect();
	}
	// Metodo que asigna una nota y observacion que un docente tiene para la tarea
	function setPluckingWorkStudent($idTarea,$idEstudiante, $nota, $observacion){
		$query = "UPDATE Nota_tareas SET Punteo_Tarea = $nota, ObservacionTarea = '$observacion' where Tareas_idTarea = $idTarea and Estudiantes_idEstudiante = $idEstudiante";
		if (!parent:: __construct()) 
			die("Error en la conexion para localizar las tareas "+mysql_error());
		if(!$notatarea = mysql_query($query)){
			die("Error en la consutla de las notas "+mysql_error());
			$this->closeconnect();
			return false;
		}
		else{
			$this->closeconnect();
			return true;
		}
		
	}
	function grado($grado,$unidad){
		$this->grado = $grado;
		$query = "SELECT T.idTarea, T.fechaasignacionTarea, T.fechaentregaTarea, T.nombreTarea, M.nombreMateria, T.descripcionTarea from Tareas T inner join Periodos_docentes PD on T.Unidad = $unidad and T.Periodos_docentes_id_periodos_docentes = PD.id_periodos_docentes and Grados_idGrado = '$this->grado' inner join Materias M on PD.Materias_idMateria = M.idMateria";
		if (!parent:: __construct()) 
			die("Error en la conexion para buscar las tareas de los estudiantes: "+mysql_error());
		if (!$this->tareas = mysql_query($query)) 
			die("Error al buscar las tareas del estudinate: "+mysql_error());
		$this->closeconnect();
	}
	
	function periodoDocente($periodo,$unidad){
		$this->periodo = (int)$periodo;
		$query = "SELECT T.idTarea, T.fechaasignacionTarea, T.fechaentregaTarea, T.nombreTarea, T.descripcionTarea from Tareas T inner join Periodos_docentes PD on T.Unidad = $unidad and T.Periodos_docentes_id_periodos_docentes = $this->periodo group by idTarea";
		if (!parent:: __construct())
			die("Error en la conexion para buscar las tareas de los estudiantes: "+mysql_error());
		if (!$this->tareas = mysql_query($query))
			die("Error al buscar las tareas del estudinate: "+mysql_error());
		$this->closeconnect();
	}
	
	function tarea(){
    	if ($tarea = mysql_fetch_array($this->tareas,MYSQL_ASSOC))
			return $tarea;
		else
			return false;
	}

	
	function docentes($periodo_docentes){
		$this->periodo_docentes = $periodo_docentes;
	}
	function asignar($nombre,$fecha_asignacion,$fecha_entrega,$descripcion){
		$query = "INSERT into Tareas (nombreTarea,fechaasignacionTarea,fechaentregaTarea,descripcionTarea,Periodos_docentes_id_periodos_docentes) values('$nombre','$fecha_asignacion','$fecha_entrega','$descripcion',$this->periodo_docentes)";
		if (!parent:: __construct())
			die("Error en la conexion para asignar tarea: "+mysql_error());
		mysql_query("SET NAMES 'utf8'");
		if (!mysql_query($query)){
			$this->closeconnect();
			return false;
		}
		$this->closeconnect();
		return true;
	}

	function buscar_estudiantes($grado){
		$query = "SELECT idEstudiante, nombresEstudiante, apellidosEstudiante from Estudiantes where Grados_idGrado = $grado";
		if (!parent:: __construct())
			die("Erro al conectar para buscar a los estudiantes "+mysql_error());
		if(!$this->estudiantes = mysql_query($query)){
			die("Error al buscar a los estudiantes "+mysql_error());
			return false;
		}
		$this->closeconnect();
		return true;
	}

	function estraer_estudiante(){
		if($estudiante = mysql_fetch_array($this->estudiantes,MYSQL_ASSOC))
			return $estudiante;
		else
			return false;
	}
	function asignar_nota($idtarea,$idestudiante,$notaInt,$observacion){
		$nota = (string)$notaInt;
		$query = "INSERT INTO Nota_tareas (Punteo_Tarea, ObservacionTarea, Tareas_idTarea, Estudiantes_idEstudiante) values ('$nota','$observacion', $idtarea, $idestudiante)";
		if (!parent:: __construct()){
			die("Error al conectarme para almacenar la nota de la tarea del estudiante "+mysql_error());
			return false;
		}
		mysql_query("SET NAMES 'utf8'");
		if (!mysql_query($query)) {
			die("Error al realizar la consulta para almacenal la nota de la tarea de un estudiante" +mysql_error());
			return false;
		}
		$this->closeconnect();
		return true;	
	}

	function actualizar_nota($idtarea,$idestudiante,$notaInt,$observacion){
		$nota = (int)$notaInt;
		$idEstudiante = (int)$idestudiante;
		$idTarea = (int)$idtarea;
		$query = "UPDATE Nota_tareas SET Punteo_Tarea = $nota, ObservacionTarea = '$observacion' where Tareas_idTarea = $idTarea and Estudiantes_idEstudiante = $idEstudiante";
		if (!parent:: __construct()){
			die("Error al conectarme para almacenar la nota de la tarea del estudiante "+mysql_error());
			return false;
		}
		mysql_query("SET NAMES 'utf8'");
		echo "Ahrota voy a realizar la transaccion";
		if (!mysql_query($query)) {
			//die("Error al realizar la consulta para almacenal la nota de la tarea de un estudiante" +mysql_error());
			echo "Error al actualizar la nota";
			return false;
		}
		$this->closeconnect();
		return true;	
	}

	function eliminar_nota($idtarea,$idestudiante){
		$idEstudiante = (int)$idestudiante;
		$idTarea = (int)$idtarea;
		$query = "DELETE from Nota_tareas where Tareas_idTarea = $idTarea and Estudiantes_idEstudiante = $idEstudiante";
		if (!parent:: __construct()){
			die("Error al conectarme para almacenar la nota de la tarea del estudiante "+mysql_error());
			return false;
		}
		if (!mysql_query($query)) {
			echo "Error al actualizar la nota";
			return false;
		}
		$this->closeconnect();
		return true;	
	}

	function asignar_nota_b($idtarea,$idestudiante,$nota){
                $query = "INSERT into NotasFinales(primerBimestre,Estudiantes_idEstudiante,Periodos_docentes_id_periodos_docentes) values ($nota,$idestudiante,$idtarea)";
                if (!parent:: __construct()){
                        die("Error al conectarme para almacenar la nota de la tarea del estudiante "+mysql_error());
                        return false;
                }
                if (!mysql_query($query)) {
                        die("Error al realizar la consulta para almacenal la nota de la tarea de un estudiante" +mysql_error());
                        return false;
                }
                $this->closeconnect();
                return true;
        }

}

/**	
* Clase que administra los cursos de un estudiante
*/
class Cursos extends conexion
{
	public $cursos;
	public $idCurso;
	public $tareas;
	public $notaParcial;
	public $notaBimestral;
	public $unidad;
	function __construct()
	{
		# code...
	}
	public function getResources(){
		$query = "SELECT * from Recursos where PDidAsigmacion = $this->idCurso and unidad = $this->unidad";
		parent:: __construct();
		mysql_query("SET NAMES 'utf8'");
		$resources = mysql_query($query);
		$this->closeconnect();
		$resourcesA = array();
		while($resource = mysql_fetch_array($resources, MYSQL_ASSOC)){
			array_push($resourcesA, $resource);
		}
		return $resourcesA;
	}
	public function setResources($descNewRsource,$nameNewRsource, $unidad){
		$query = "INSERT into Recursos (descripcionRecurso, archivoRecurso, PDidAsigmacion, unidad) values ('$descNewRsource','$nameNewRsource',$this->idCurso, $unidad)";
		parent:: __construct();
		mysql_query("SET NAMES 'utf8'");
		mysql_query($query);
		$query = "SELECT * from Recursos where PDidAsigmacion = $this->idCurso and unidad = $unidad";
		$resources = mysql_query($query);
		$this->closeconnect();
		$resourcesA = array();
		while($resource = mysql_fetch_array($resources, MYSQL_ASSOC)){
			array_push($resourcesA, $resource);
		}
		return $resourcesA;
	}

	function connect(){
		if(!parent:: __construct())
			die("Error en la conexion para almacenar cursos "+mysql_error());
	}
	function setIdCurso($idCurso){
		$this->idCurso = (int)$idCurso;
	}

	function setUnidad($unidad){
		$this->unidad = (int)$unidad;
	}

	function asignarNotaParcial($noParciall,$notaParciall,$idEstudiantee){
		$noParcial = (int)$noParciall;
		$notaParcial = (int)$notaParciall;
		$idEstudiante = (int)$idEstudiantee;
		$query = "INSERT INTO Parciales (noParcial,notaParcial,idEstudiante,idCurso) values($noParcial,$notaParcial,$idEstudiante,$this->idCurso)";
		if (!parent:: __construct())
			die("Error en la conexion para asignar tarea: "+mysql_error());
		mysql_query("SET NAMES 'utf8'");
		if (!mysql_query($query)){
			$this->closeconnect();
			return false;
		}
		$this->closeconnect();
		return true;
	}
	function actualizarNotaParcial($noParciall,$notaParciall,$idEstudiantee){
		$noParcial = (int)$noParciall;
		$notaParcial = (int)$notaParciall;
		$idEstudiante = (int)$idEstudiantee;
		$query = "UPDATE Parciales SET notaParcial = $notaParcial where idEstudiante = $idEstudiante and noParcial = $noParcial and idCurso = $this->idCurso";
		if (!parent:: __construct())
			die("Error en la conexion para asignar tarea: "+mysql_error());
		mysql_query("SET NAMES 'utf8'");
		if (!mysql_query($query)){
			$this->closeconnect();
			return false;
		}
		$this->closeconnect();
		return true;
	}

	function asignarTarea($nombre,$fecha_asignacion,$fecha_entrega,$descripcion, $hora, $tipo, $valor, $idGrado){
		$valorTarea = (int)$valor;
		if ($tipo == "0")
			$query = "INSERT into Tareas (nombreTarea,fechaasignacionTarea,fechaentregaTarea,descripcionTarea,Periodos_docentes_id_periodos_docentes, Unidad, horaEntrega, digital, valor) values('$nombre','$fecha_asignacion','$fecha_entrega','$descripcion',$this->idCurso,$this->unidad, '$hora', false, $valorTarea)";
		else if($tipo == "1")
			$query = "INSERT into Tareas (nombreTarea,fechaasignacionTarea,fechaentregaTarea,descripcionTarea,Periodos_docentes_id_periodos_docentes, Unidad, horaEntrega, digital, valor) values('$nombre','$fecha_asignacion','$fecha_entrega','$descripcion',$this->idCurso,$this->unidad, '$hora', true, $valorTarea)";
		if (!parent:: __construct())
			die("Error en la conexion para asignar tarea: "+mysql_error());
		mysql_query("SET NAMES 'utf8'");
		if (!mysql_query($query)){
			$this->closeconnect();
			return false;
		}
		$query = "SELECT idTarea from Tareas where idTarea = last_insert_id()";
		$idNewTarea = mysql_query($query);
		$idNewTarea = mysql_fetch_array($idNewTarea, MYSQL_ASSOC);
		$idNewTarea = $idNewTarea["idTarea"];
		$query = "SELECT idEstudiante from Estudiantes where Grados_idGrado = $idGrado";
		$estudiantes = mysql_query($query);
		while ($estudiante = mysql_fetch_array($estudiantes, MYSQL_ASSOC)) {
			$idEstudiante = $estudiante["idEstudiante"]; 
			$query = "INSERT into Nota_tareas(Tareas_idTarea, Estudiantes_idEstudiante) values($idNewTarea, $idEstudiante)";
			mysql_query($query);
		}
		$this->closeconnect();
		return true;
	}

	function actualizarTarea($nombre,$fecha_asignacion,$fecha_entrega,$descripcion,$idTarea, $horaEntrega, $tipoEntrega, $valor){
		$valorTarea = (int)$valor;
		if ($tipoEntrega == '0')
			$query = "UPDATE Tareas SET nombreTarea = '$nombre', fechaasignacionTarea = '$fecha_asignacion', fechaentregaTarea = '$fecha_entrega', descripcionTarea = '$descripcion', valor = $valorTarea, horaEntrega = '$horaEntrega', digital = false where idTarea = $idTarea";
		else if($tipoEntrega == '1')
			$query = "UPDATE Tareas SET nombreTarea = '$nombre', fechaasignacionTarea = '$fecha_asignacion', fechaentregaTarea = '$fecha_entrega', descripcionTarea = '$descripcion', valor = $valorTarea, horaEntrega = '$horaEntrega', digital = true where idTarea = $idTarea";
		else
			$query = "SET NAMES 'utf8'";	
		if (!parent:: __construct())
			die("Error en la conexion para asignar tarea: "+mysql_error());
		mysql_query("SET NAMES 'utf8'");
		if (!mysql_query($query)){
			echo "Error al actualizar la tarea";
			$this->closeconnect();
			return false;
		}
		$this->closeconnect();
		return true;
	}

	function almacenarCurso($idcurso,$curso,$idotro){
		$idgrado = (int)$idcurso;
		$iddocente = (int)$curso;
		$idcurso = (int)$idotro;
		$query = "INSERT INTO Periodos_docentes (Grados_idGrado,Docentes_idDocente,Materias_idMateria) values ($idgrado,$iddocente,$idcurso)";
		if(!$isertarcurso = mysql_query($query))
			die("Error al almacenar un curso "+mysql_error());	
	}

	function consultar ($idgrado){
		$query = "SELECT PD.id_periodos_docentes, M.nombreMateria from Materias M inner join Periodos_docentes  PD  on M.idMateria = PD.Materias_idMateria and PD.Grados_idGrado = $idgrado";
		if(!parent:: __construct())
			die("Error en la conexion para soolicitar los cursos "+mysql_error());
		if(!$this->cursos = mysql_query($query))
			die("Error en la solicitud de los cursos "+mysql_error());
		$this->closeconnect();
	}
	function obtener (){
		if ($cursos = mysql_fetch_array($this->cursos,MYSQL_ASSOC))
			return $cursos;
		else
			return false;
	}
	function consultarTareas(){
		$query = "SELECT T.idTarea, T.nombreTarea, T.fechaasignacionTarea, T.fechaentregaTarea, T.descripcionTarea, T.horaEntrega, T.digital, T.valor from Tareas T inner join Periodos_docentes PD on T.Periodos_docentes_id_periodos_docentes = PD.id_periodos_docentes and id_periodos_docentes = $this->idCurso and T.Unidad = $this->unidad";
		if(!parent:: __construct())
			die("Error al buscar a las tareas"+mysql_error());
		if(!$this->tareas = mysql_query($query))
			die("Error al buscar las tareas"+mysql_error());
		$this->closeconnect();
	}
	function extraerTarea (){
		if($tareaCurso = mysql_fetch_array($this->tareas,MYSQL_ASSOC))
			return $tareaCurso;
		else
			return false;
	}
	/*function getWorks(){
		$query = "SELECT T.idTarea, T.nombreTarea, T.fechaasignacionTarea, T.fechaentregaTarea, T.horaEntrega, T.descripcionTarea, NT.Punteo_Tarea, NT.ObservacionTarea, NT.fileTarea from Tareas T inner join Nota_tareas NT on T.idTarea = NT.Tareas_idTarea inner join Periodos_docentes PD on T.Periodos_docentes_id_periodos_docentes = PD.id_periodos_docentes and id_periodos_docentes = $this->idCurso and T.Unidad = $this->unidad";
		if(!parent:: __construct())
			die("Error al buscar a las tareas"+mysql_error());
		if(!$this->tareas = mysql_query($query))
			die("Error al buscar las tareas"+mysql_error());
		$this->closeconnect();
		$tareasA = array();
		while ($tareaCurso = mysql_fetch_array($this->tareas,MYSQL_ASSOC)) {
			array_push($tareasA, $tareaCurso);
		}
		return json_encode($tareasA);
	}*/
	function consultarParcial($noParciall,$idEstudiantee){
		$noParcial = (int)$noParciall;
		$idEstudiante = (int)$idEstudiantee;
		$query = "SELECT notaParcial from Parciales where noParcial = $noParcial and idEstudiante = $idEstudiante and idCurso = $this->idCurso";
		if(!parent:: __construct())
			die("Error al buscar a las tareas"+mysql_error());
		if(!$this->notaParcial = mysql_query($query))
			die("Error al buscar las tareas"+mysql_error());
		$this->closeconnect();
	}
	function extraerNotaParcial (){
		if($notaParcial = mysql_fetch_array($this->notaParcial,MYSQL_ASSOC))
			return $notaParcial;
		else
			return false;
	}
	function consultarBimestrales($idEstudiantee){
		$idEstudiante = (int)$idEstudiantee;
		$query = "SELECT primerBimestre, segundoBimestre, tercerBimestres, cuartoBimestre, quintoBimestre from NotasFinales where Estudiantes_idEstudiante = $idEstudiante and Periodos_docentes_id_periodos_docentes = $this->idCurso";
		if(!parent:: __construct())
			die("Error al buscar a las tareas"+mysql_error());
		if(!$this->notaBimestral = mysql_query($query))
			die("Error al buscar las tareas"+mysql_error());
		$this->closeconnect();
	}
	function extraerNotaBimestral(){
		if($notaBimestral = mysql_fetch_array($this->notaBimestral,MYSQL_ASSOC))
			return $notaBimestral;
		else
			return false;
	}
	function cerrar(){
		$this->closeconnect();
	}
}


/**
* Clase que administra a los estudiantes
*/
class Estudiantes extends conexion
{
	public $estudiantes;
	public $estudiantefinales;
	public $idEstudiante;
	public $gradoEstudiante;
	function __construct()
	{
		if (!parent:: __construct()) {
                        die("Error en la conexion para almacenar a un estudiante "+mysql_error());
                }
	}
	public function getNoticesGrade($unidad){
		parent:: __construct();
		$query = "SELECT * from NoticiasGrados where idGrado = $this->gradoEstudiante and unidad = $unidad";
		if($noticesGrade = mysql_query($query)){
			$this->closeconnect();
			$noticesGradeA = array();
			while ($noticeGrade = mysql_fetch_array($noticesGrade,MYSQL_ASSOC)) {
				array_unshift($noticesGradeA, $noticeGrade);
			}
			return $noticesGradeA;
		}
		else
			echo "Error en todo";
	}
	function setIdEstudiante($idEstudiante){
		$this->idEstudiante = $idEstudiante;
	}

	function estudiante_nombre(){
		$query = "SELECT nombresEstudiante from Estudiantes where idEstudiante = $this->idEstudiante";
		if(!parent::__construct()){
			die("Error en la conexion nombre docente: ".mysql_error());
		}
		mysql_query("SET NAMES 'utf8'");
		if(!$nombre = mysql_query($query)){
			die("Error al encontrar el nombre: ".mysql_error());
		}
		$this->closeconnect();
		$nombreEstudiante = mysql_fetch_array($nombre,MYSQL_ASSOC);
		return $nombreEstudiante["nombresEstudiante"];
	}

	function almacenadosfinales ($idestudiante){
		$query = "SELECT NM.nombreMateria, NF.primerBimestre, NF.segundoBimestre, NF.tercerBimestres, NF.cuartoBimestre, NF.promedio, NF.resultado from Materias NM inner join Periodos_docentes PD on NM.idMateria = PD.Materias_idMateria inner join NotasFinales NF on PD.id_periodos_docentes = NF.Periodos_docentes_id_periodos_docentes and NF.Estudiantes_idEstudiante = $idestudiante";
		if (!parent:: __construct())
			die ("Error en la conexion para solicitar los salones "+mysql_error());
		if (!$this->estudiantefinales = mysql_query($query)) 
			die("Error al solicitar los salones "+mysql_error());	
	}
	
	function obtenerEstudiantesfinales(){
                if ($estudiante = mysql_fetch_array($this->estudiantefinales,MYSQL_ASSOC))
                        return $estudiante;
                else
                        return false;
        }


	function verGrado($grado){
		$gradoint = (int)$grado;
		$query = "SELECT idEstudiante, nombresEstudiante from Estudiantes where Grados_idGrado = $gradoint";
		if(!$this->estudiantes = mysql_query($query))
			die("Error al buscar los estudiates "+mysql_error());
	}
	function obtenerEstudiantes(){
		if ($estudiante = mysql_fetch_array($this->estudiantes,MYSQL_ASSOC))
			return $estudiante;
		else
			return false;
	}

	function almacenarEstudiante($password, $nombre,$grado){
		$gradoInt = (int)$grado;
		$query = "INSERT into Estudiantes (paswordEstudiante, nombresEstudiante, Grados_idGrado) values ('$password','$nombre',$gradoInt)";
		if (!mysql_query($query)) {
			die("Error al almacenar un estudiante "+mysql_error());
		}
	}
	function cerrar (){
		$this->closeconnect();
	}

	public function getNotices($idCurso, $unidad){
		$query = "SELECT * from Noticias where idAsignacion = $idCurso and unidad = $unidad";
		parent::__construct();
		$newNotice = mysql_query("SET NAMES 'utf8'");
		$newNotice = mysql_query($query);
		$this->closeconnect();
		$notices = array();
		while ($newNoticeA = mysql_fetch_array($newNotice,MYSQL_ASSOC)) {
			array_push($notices, $newNoticeA);
		}
		return json_encode($notices);
	}
	public function getResources($idCurso, $unidad){
		$query = "SELECT * from Recursos where PDidAsigmacion = $idCurso and unidad = $unidad";
		parent::__construct();
		$newNotice = mysql_query("SET NAMES 'utf8'");
		$newNotice = mysql_query($query);
		$this->closeconnect();
		$notices = array();
		while ($newNoticeA = mysql_fetch_array($newNotice,MYSQL_ASSOC)) {
			array_push($notices, $newNoticeA);
		}
		return json_encode($notices);
	}
}

/**
* Clase Grado
*/
class Grado extends conexion
{
	public $idGrado;
	public $nombreGrado;
	public $seccionGrado;
	public $anioGrado;
	public $cursosGrado;
	public $estudiantesGrado;

	function __construc(){
		if(!parent:: __construct()){
			die("Error en la conexion de Grado"+mysql_error());
		}
	}
	function setIdGrado($idGrado){
		$this->idGrado = $idGrado;
	}
	function obtenerEstudiantes(){
		$query = "SELECT E.idEstudiante, E.nombresEstudiante from Estudiantes E inner join Grados G on E.Grados_idGrado = G.idGrado and G.idGrado = $this->idGrado";
		if(!$this->estudiantesGrado = mysql_query($query))
			die("Error al buscar a los estudiantes"+mysql_error());
	}
	function obtenerEstudiantesExtraer(){
		if($estudianteGrado = mysql_fetch_array($this->estudiantesGrado,MYSQL_ASSOC))
			return $estudianteGrado;
		else
			return false;
	}
}
// Que onda Miguelito, el anterior saber que le pasó
?>
