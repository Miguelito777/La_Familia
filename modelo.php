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
	function __construct($usuario, $passwordd)
	{
		$this->usuario = $usuario;
		$this->passwordd = $passwordd;
	}
	function is_docente(){
		$query = "SELECT paswordDocente, nombresDocente from Docentes where paswordDocente = '$this->passwordd' and  nombresDocente = '$this->usuario'";	
		if (!parent:: __construct()) {
			die("Error el la conexion de localizacion de docente: "+mysql_error());
		}
		if(!$this->datosusuario = mysql_query($query))
			die("Error el la consulta para localizacion del docente "+mysql_error());
		$this->closeconnect();
		if(mysql_num_rows($this->datosusuario) > 0)
			return true;
		else
			return false;	
	}
	function is_estudiante(){
		$query = "SELECT idEstudiante, nombresEstudiante, Grados_idGrado from Estudiantes where paswordEstudiante = '$this->passwordd' and nombresEstudiante = '$this->usuario'";
		if (!parent:: __construct())
			die ("Error en la conexion para buscar a un estudiante"+mysql_error());
		if(!$this->datosusuario = mysql_query($query))
			die("Error en la consulta para evaluar a un estudiante"+mysql_error());
		$this->closeconnect();
		$estudiante = mysql_fetch_array($this->datosusuario,MYSQL_ASSOC);
		$_SESSION["grado_estudiante"] = $estudiante["Grados_idGrado"];
		if(mysql_num_rows($this->datosusuario)>0)
			return true;
		else
			return false;
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
		 $query = "SELECT PD.id_periodos_docentes, G.idGrado, G.nombreGrado, G.grado_seccion, G.jornada, M.nombreMateria from Grados G inner join Periodos_docentes PD on G.idGrado = PD.Grados_idGrado inner join Materias M on PD.Materias_idMateria = M.idMateria and PD.Docentes_idDocente = $this->idDocente";
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
		$this->iddocente = $iddocente;
	}
	function docente_nombre(){
		$query = "SELECT nombresDocente from Docentes where idDocente = $this->iddocente";
		if(!parent::__construct()){
			die("Error en la conexion nombre docente: ".mysql_error());
		}
		if(!$nombre = mysql_query($query)){
			die("Error al encontrar el nombre: ".mysql_error());
		}
		$this->closeconnect();
		$nombredocente = mysql_fetch_array($nombre,MYSQL_ASSOC);
		return $nombredocente["nombresDocente"];
	}

}

/**
* Clase que manipula las tareas
*/
class tareas extends conexion
{
	public $periodo_docentes;
	public $grado;
	public $tareas;
	public $estudiantes;
	function __construct()
	{
		
	}
	function grado($grado){
		$this->grado = $grado;
		$query = "SELECT T.idTarea, T.fechaasignacionTarea, T.fechaentregaTarea, T.nombreTarea, M.nombreMateria, T.descripcionTarea from Tareas T inner join Periodos_docentes PD on T.Periodos_docentes_id_periodos_docentes = PD.id_periodos_docentes and Grados_idGrado = '$this->grado' inner join Materias M on PD.Materias_idMateria = M.idMateria";
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
	function asignar_nota($idtarea,$idestudiante,$nota,$observacion){
		$query = "INSERT into Nota_tareas (Punteo_Tarea, ObservacionTarea, Tareas_idTarea, Estudiantes_idEstudiante) values ($nota,'$observacion',$idtarea,$idestudiante)";
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
	function __construct()
	{
		# code...
	}

	function consultar($idgrado){
		$query = "SELECT PD.id_periodos_docentes, M.nombreMateria from Materias M inner join Periodos_docentes  PD  on M.idMateria = PD.Materias_idMateria and PD.Grados_idGrado = $idgrado";
		if(!parent:: __construct())
			die("Error en la conexion para soolicitar los cursos "+mysql_error());
		if(!$this->cursos = mysql_query($query))
			die("Error en la solicitud de los cursos "+mysql_error());
		$this->closeconnect();
	}
	function obtener(){
		if ($cursos = mysql_fetch_array($this->cursos,MYSQL_ASSOC))
			return $cursos;
		else
			return false;
			
	}
}




?>