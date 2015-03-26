<?php
	session_start();
	include 'modelo.php';

	if (isset($_POST["usuario"]) && isset($_POST["password"])) {
		$usuario = new log_in($_POST["usuario"],$_POST["password"]);
		if ($usuario->is_docente())
			echo "docente";
		if ($usuario->is_estudiante())
			echo "estudiante";
		else
			echo false;
	}
	if (isset($_POST["idtarea"]) && isset($_POST["idestudiante"]) && isset($_POST["nota"]) && isset($_POST["observacion"])){
		$idtarea = (int)$_POST["idtarea"];
		$idestudiante = (int)$_POST["idestudiante"];
		$nota = (int)$_POST["nota"];
		$observacion = $_POST["observacion"];
		$tareass = new tareas();
		if (!$tareass->asignar_nota($idtarea,$idestudiante,$nota,$observacion)){
			echo false;
		}
		else{
			echo true;
		} 
			
	}

	if (isset($_POST["idgrado"])) {
		$tareas = new tareas();
		if(!$tareas->buscar_estudiantes($_POST["idgrado"]))
			die("Error al buscar a los estudiantes "+mysql_error());
		$i = 0;
		$estudiantes = array(); 
		while ($estudiante = $tareas->estraer_estudiante()){
			$j = 0;
			foreach ($estudiante as $key => $value) {
				$estudiantes[$i][$j] = $value;
				$j++;
			}
			$i++;
		}
		$estudiantes_object = (object)$estudiantes;
		echo json_encode($estudiantes_object);
	}

	if (isset($_POST["notasasignadas"])) {
		$tareas = new tareas();
		if (isset($_POST["grado"])){
			$tareas->grado(isset($_POST["grado"]));
		}
		else
			$tareas->grado(1);
		$tareasa = array();
		$i = 0;
		while ($tarea = $tareas->tarea()) {
		 	$j = 0;
		 	foreach ($tarea as $key => $value) {
		 		$tareasa[$i][$j] = $value;
		 		$j++;
		 	}
		 	$i++;
		 } 
		 $tareas_object = (object)$tareasa; 
		 echo json_encode($tareas_object);
	}


	if (isset($_POST["datosdocente"])) {
		$docente = new docente(1);
		echo $docente->docente_nombre();
	}
	if (isset($_POST["asignaciones"])) {
		$docente = new asignaciones(1);
		$docente->todas();
		$asignaciones = array();
		$i = 0;
		while ($asignacion = $docente->todas_extraer()) {
			$j = 0;
			foreach ($asignacion as $key => $value) {
				$asignaciones[$i][$j] = $value;
				$j++;
			}
			$i++;
		}
		$asignaciones_object = (object)$asignaciones;
		echo json_encode($asignaciones_object);
	}
	if (isset($_POST["periodo_docente"])) {
		$tarea = new tareas(); 
		$tarea->docentes($_POST["periodo_docente"]);

		if (isset($_POST["nombretarea"])) {
			if(!$tarea->asignar($_POST["nombretarea"],$_POST["fechaasignacion"],$_POST["fechaentrega"],$_POST["descripcion"]))
			{
				echo "Error al asignar la terea";
			}
			else
				echo "La tarea se ha asignado correctamente";
		}
		
		
	}
?>