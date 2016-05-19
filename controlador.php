<?php
	session_start();
	require 'fpdf.php';
	include 'modelo.php';
	$_SESSION["unidad"] = 2;
	if (isset($_GET["relGradoCurso"])) {
		echo "Tengo el grado a relacionar";
	}
	if (isset($_GET["solvenciaGrado"])) {
		
	}

	if (isset($_GET["imprimirNotasUnidad"])) {
		$gradoEstudiantes = (int)$_GET["imprimirNotasUnidad"];
		$nombreGrado = $_GET["nombreGrado"];
		$seccionGrado = $_GET["seccionGrado"];
		$jornadaGrado = $_GET["jornadaGrado"];
		
		//A continuacion obtengo los estudiantes para este grado
		$notasGrado = new Grado();
		$notasGrado->setIdGrado($gradoEstudiantes);
		$notasGrado->obtenerEstudiantes();
		$i = 0;
        $estudiantes = array();
        while ($estudiante = $notasGrado->obtenerEstudiantesExtraer()){
        	$j = 0;
            foreach($estudiante as $key => $value){
            	$estudiantes[$i][$j] = $value;
                $j++;
            }
			$i++;
        }
        $pdf = new PDF();
        // A continuacion obtengo las notas para cada estudiante
        for ($e = 0; $e < count($estudiantes); $e++) {
        	$pdf->AddPage();
        	$pdf->SetFont('Arial','B',10);
        	$pdf->Cell(0,10,'Alumno:   '.$estudiantes[$e][1]);
        	$pdf->Ln(5);
        	$pdf->Cell(125,10,"Grado:      ".$nombreGrado);
        	$pdf->Ln(5);
        	$pdf->Cell(50,10,"Seccion:  ".$seccionGrado);
        	$pdf->Ln(5);
        	$pdf->Cell(50,10,"Jornada:    ".$jornadaGrado);
        	$pdf->Ln(15); 
        	$cursosParciales = new Cursos();
			$notasClase = new tareas();


			// Obtengo los cursos del estudiante
			$cursosParciales->consultar($gradoEstudiantes);
			$cursosParcialesA = array();
			$i = 0;
			while ($curso = $cursosParciales->obtener()) {
		 		$j = 0;
		 		foreach ($curso as $key => $value) {
		 			$cursosParcialesA[$i][$j] = $value;
		 			$j++;
		 		}
		 		$i++;
		 	} 
		 	
		 	// Ahora busco la nota de examen de unidad por cada curso	
		 	$notasParcialesCursos = array();
		 	for ($i = 0; $i < count($cursosParcialesA); $i++) { 
	 			$cursosParciales->setIdCurso($cursosParcialesA[$i][0]);
	 			for ($j=0; $j < $_SESSION["unidad"];$j++) { 
	 			 	$cursosParciales->consultarParcial($j+1,$estudiantes[$e][0]);
		 			$notasParcialesCursos[$i][$j] = $cursosParciales->extraerNotaParcial();
	 			} 
		 	}
		 	
		 	for ($i = 0; $i < count($notasParcialesCursos); $i++) {
		 		for ($j=0; $j < $_SESSION["unidad"]; $j++) { 
		 			if (!$notasParcialesCursos[$i][$j]) {
            			$notasParcialesCursos[$i][$j] = 0;
            		}
            		else{
            			foreach ($notasParcialesCursos[$i][$j] as $value) {
            				$notasParcialesCursos[$i][$j] = (int)$value;
            			}
            		}	
		 		}
            }

       		
            // Obtengo las tareas de cada curso de cada bimestre
            $notaUnidadCursos = array();
            for ($bim = 0; $bim < $_SESSION["unidad"]; $bim++) { 
            	$tareasCursos = array();
            	for ($i = 0; $i < count($cursosParcialesA); $i++) { 
            		$cursosParciales->setIdCurso($cursosParcialesA[$i][0]);
            		$cursosParciales->setUnidad($bim+1);
            		$cursosParciales->consultarTareas();
            		$idTareas = array();
            		$j = 0;
            		while ($tareaCurso = $cursosParciales->extraerTarea()){
                		$k = 0;
                		foreach($tareaCurso as $value){
                			if($k==0)
                				$idTareas[$j] = $value;
                			$k++;
                		}
                		$j++;
					}
					array_push($tareasCursos, $idTareas);
            	}

            	// Obtengo las notas de las tareas de cada curso y las sumo como zona para cada curso
            	$notasTareas = array();
            	for ($i = 0; $i < count($cursosParcialesA); $i++) { 
           			$tamanioTareas = count($tareasCursos[$i]);
					if ($tamanioTareas != 0) {
						$zona = 0;
            			for ($j = 0; $j < count($tareasCursos[$i]); $j++) { 
              				$notasClase->notaTarea($tareasCursos[$i][$j],$estudiantes[$e][0]);
               				$notaTarea = $notasClase->obtenerNotaTarea();	
               				if (!$notaTarea) {
               					$notaTarea = 0;
               				}
               				else{
               					foreach ($notaTarea as $value) {
               						$notaTarea = (int)$value;
               					}
               				}
               				$zona = $zona + $notaTarea;
               			}
               			$notasTareas[$i] = $zona;
            		}
					else{
						$notasTareas[$i] = 0;
					}
            	}
            
		 		// Sumo las notas de cada examen de unidad con la zona de cada curso
            	for ($i = 0; $i < count($cursosParcialesA); $i++) {
            		if ($bim == 0) {
            			$notaUnidadCursos[$i] = array();
            		} 
            		$notaUnidadCursos[$i][$bim] = $notasTareas[$i]+$notasParcialesCursos[$i][$bim];
            	}
            }

            // A las notas les asigno el nombre de sus cursos
            for ($i = 0; $i < count($notaUnidadCursos); $i++) {
            	array_unshift($notaUnidadCursos[$i],$cursosParcialesA[$i][1]);
            }
            $headerArray = array('Curso','Primera Unidad','Segunda Unidad', 'Tercera Unidad', 'Cuarta Unidad','Quinta Unidad','Promedio');
            $pdf->BasicTable($headerArray,$notaUnidadCursos,$_SESSION["unidad"]);
            $pdf->Ln(15);
            $pdf->SetFont('Arial','B',10);
        	$pdf->Cell(0,10,'Observaciones______________________________________');
        	$pdf->Ln(5);
        	$pdf->Cell(0,10,'___________________________________________________');
			$pdf->Ln(20); 
			$pdf->SetFont('Arial','i',10);
			$pdf->Cell(0,10,'DIOS, Ciencia y arte',0,0,'C');
        	$pdf->Ln(5); 
        }
		$pdf->Output();
        //echo "<h1>Procesos realizados exitosamente</h1>";
	}


	if (isset($_POST["obtenerGrados"])) {
		$colegioGrados = new Colegio();
		$colegioGrados->obtenerGrados();
		$grados = array();
		$i = 0;
		while ($grado = $colegioGrados->extraerGrado()) {
			$j = 0;
			foreach ($grado as $value) {
				$grados[$i][$j] = $value;
				$j++;
			}
			$i++;
		}
		$cursos_object = utf8_encode_recursive($grados);
        echo json_encode($cursos_object);
	}
	
	if(isset($_POST["estudianteabuscarfinales"])){
		$salones = new Estudiantes();
		$salones->almacenadosfinales($_SESSION["id_estudiante"]);
		
		$j = 0;
		$salonesarray = array();
		while ($salon = $salones->obtenerEstudiantesfinales()){
			$k = 0;
			foreach ($salon as $key => $value) {
				$salonesarray[$j][$k] = $value;
				$k++;
			}
			$j++;
		}

		$salones_object = utf8_encode_recursive($salonesarray);
		echo json_encode($salones_object);
	}

	if (isset($_POST["grado"]) && isset($_POST["curso"])){
		$estudiantes = new Estudiantes();
		$estudiantes->verGrado($_POST["grado"]);
		$estudiantesarray = array();
                $i = 0;
                while ($tarea = $estudiantes->obtenerEstudiantes()) {
                        $j = 0;
                        foreach ($tarea as $key => $value) {
                                $estudiantesarray[$i][$j] = $value;
                                $j++;
                        }
                        $i++;
                 }
		$estudiantes_object = utf8_encode_recursive($estudiantesarray);
         echo json_encode($estudiantes_object);
	}

	if (isset($_POST["usuario"]) && isset($_POST["password"])) {
		$usuario = new log_in($_POST["usuario"],$_POST["password"]);
		if ($usuario->is_docente())
			echo "docente";
		if ($usuario->is_estudiante())
			echo "estudiante";
		if ($usuario->is_Admin()) {
			echo "administrador";
		}
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

	if (isset($_POST["idcurso"]) && isset($_POST["idestudianteb"]) && isset($_POST["notaef"])){
                $idtarea = (int)$_POST["idcurso"];
                $idestudiante = (int)$_POST["idestudianteb"];
                $nota = (int)$_POST["notaef"];
                $tareass = new tareas();
                if (!$tareass->asignar_nota_b($idtarea,$idestudiante,$nota)){
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
		$estudiantes_object = utf8_encode_recursive($estudiantes);
		echo json_encode($estudiantes_object);
	}




	// Script Encargado de buscar las tareas signadas de los estudiantes
	if (isset($_POST["notasasignadasT"])) {
		$tareas = new tareas();
		if (isset($_POST["grado"]))
			$tareas->periodoDocente($_POST["grado"],$_SESSION["unidad"]);
		else
			$tareas->grado($_SESSION["grado_estudiante"],$_SESSION["unidad"]);
		$tareasa = array();
		$i = 0;
		while ($tarea = $tareas->tarea()) {
		 	$j = 0;
		 	foreach ($tarea as $value) {
		 		$tareasa[$i][$j] = $value;
		 		$j++;
		 	}
		 	$i++;
		 } 
		 $tareas_object = utf8_encode_recursive($tareasa); 
		 echo json_encode($tareas_object);
	}

	if (isset($_POST["cursosasignados"])) {
		$tareas = new Cursos();
		$tareas->consultar($_SESSION["grado_estudiante"]);
		$tareasa = array();
		$i = 0;
		while ($tarea = $tareas->obtener()) {
		 	$j = 0;
		 	foreach ($tarea as $key => $value) {
		 		$tareasa[$i][$j] = $value;
		 		$j++;
		 	}
		 	$i++;
		 } 
		 $tareas_object = utf8_encode_recursive($tareasa); 
		echo json_encode($tareas_object);
		
	}
	// Script que busca las notas parciales de los 5 bimestres por curso
	if (isset($_POST["cursosAsignadosParciales"])) {
		if (isset($_SESSION["grado_estudiante"])) {
			$cursosParciales = new Cursos();
			$cursosParciales->consultar($_SESSION["grado_estudiante"]);
			$cursosParcialesA = array();
			$i = 0;
			while ($curso = $cursosParciales->obtener()) {
		 		$j = 0;
		 		foreach ($curso as $key => $value) {
		 			$cursosParcialesA[$i][$j] = $value;
		 			$j++;
		 		}
		 		$i++;
		 	} 

		 	// Ahora busco la nota parcial por cada curso	
		 	$notasParcialesCursos = array();
		 	for ($i = 0; $i < count($cursosParcialesA); $i++) { 
	 			$cursosParciales->setIdCurso($cursosParcialesA[$i][0]); 
		 		for ($j = 0; $j < 5; $j++) {
		 			$cursosParciales->consultarParcial($j+1,$_SESSION["id_estudiante"]);
		 			$notasParcialesCursos[$i][$j] = $cursosParciales->extraerNotaParcial();
		 		}
		 	}
		 	
		 	 for ($i = 0; $i < count($notasParcialesCursos); $i++) {
                	for ($j = 0; $j < count($notasParcialesCursos[$i]); $j++) { 
            			if (!$notasParcialesCursos[$i][$j]) {
            				$notasParcialesCursos[$i][$j] = 0;
            			}
            			else{
            				foreach ($notasParcialesCursos[$i][$j] as $key => $value) {
            					$notasParcialesCursos[$i][$j] = (int)$value;
            				}
            			}	
                	}
                }

                for ($i = 0; $i < count($notasParcialesCursos); $i++) {
                	array_unshift($notasParcialesCursos[$i],$cursosParcialesA[$i][1]);
                }  

		 $notasParcialesCursos = utf8_encode_recursive($notasParcialesCursos); 
		echo json_encode($notasParcialesCursos);
		}
		else
			header("Location:registros.html");
		
	}



	// Script que busca las notas bimestrales de los 5 bimestres por curso
	if (isset($_POST["cursosAsignadosBimestrales"])) {
		if (isset($_SESSION["grado_estudiante"])) {
			$cursosParciales = new Cursos();
			$notasClase = new tareas();


			// Obtengo los curos del estudiante
			$cursosParciales->consultar($_SESSION["grado_estudiante"]);
			$cursosParcialesA = array();
			$i = 0;
			while ($curso = $cursosParciales->obtener()) {
		 		$j = 0;
		 		foreach ($curso as $key => $value) {
		 			$cursosParcialesA[$i][$j] = $value;
		 			$j++;
		 		}
		 		$i++;
		 	} 
		 	
		 	// Ahora busco la nota de examen de unidad por cada curso	
		 	$notasParcialesCursos = array();

		 	for ($i = 0; $i < count($cursosParcialesA); $i++) {
		 		$cursosParciales->setIdCurso($cursosParcialesA[$i][0]); 
		 		for ($parUni = 0; $parUni < $_SESSION["unidad"]; $parUni++) { 
		 			$cursosParciales->consultarParcial($parUni+1,$_SESSION["id_estudiante"]);
		 			$notasParcialesCursos[$i][$parUni] = $cursosParciales->extraerNotaParcial();
		 		}
	 			
		 	}
		 	
		 	for ($i = 0; $i < count($notasParcialesCursos); $i++) {
		 		for ($j = 0; $j < count($notasParcialesCursos[$i]); $j++) { 
		 			if (!$notasParcialesCursos[$i][$j]) {
            			$notasParcialesCursos[$i][$j] = 0;
            		}
            		else{
            			foreach ($notasParcialesCursos[$i][$j] as $value) {
            				$notasParcialesCursos[$i][$j] = (int)$value;
            			}
            		}	
		 		}
            }

       		//obtengo las zonas por cada curso en cada unidad
            $notasTareas = array();
            
       		for ($unidadAct = 1; $unidadAct <= $_SESSION["unidad"]; $unidadAct++) { 

       			// Obtengo las tareas de cada curso
            	$tareasCursos = array();
            	for ($i = 0; $i < count($cursosParcialesA); $i++) { 
            		$cursosParciales->setIdCurso($cursosParcialesA[$i][0]);
            		$cursosParciales->setUnidad($unidadAct);
            		$cursosParciales->consultarTareas();
            		$idTareas = array();
            		$j = 0;
            		while ($tareaCurso = $cursosParciales->extraerTarea()){
                		$k = 0;
                		foreach($tareaCurso as $value){
                			if($k==0)
                				$idTareas[$j] = $value;
                			$k++;
                		}
                		$j++;
					}
					array_push($tareasCursos, $idTareas);
            	}

        	
            	// Obtengo las notas de las tareas de cada curso y las sumo como zona para cada curso
            	
            	for ($i = 0; $i < count($cursosParcialesA); $i++) {
           			$tamanioTareas = count($tareasCursos[$i]);
					if ($tamanioTareas != 0) {
						$zona = 0;
            			for ($j = 0; $j < count($tareasCursos[$i]); $j++) { 
              				$notasClase->notaTarea($tareasCursos[$i][$j],$_SESSION["id_estudiante"]);
               				$notaTarea = $notasClase->obtenerNotaTarea();	
               				if (!$notaTarea) {
               					$notaTarea = 0;
               				}
               				else{
               					foreach ($notaTarea as $value) {
               						$notaTarea = (int)$value;
               					}
               				}
               				$zona = $zona + $notaTarea;
               			}
               			$notasTareas[$i][$unidadAct-1] = $zona;
            		}
					else{
						$notasTareas[$i][$unidadAct-1] = 0;
					}
            	}
       		}
       		
          	// Sumo las notas de cada examen de unidad con la zona de cada curso
            $notaUnidadCursos = array();
            for ($i = 0; $i < count($cursosParcialesA); $i++) {
            	$notaUnidadCursos[$i] = array();
            	for ($uniAct = 0; $uniAct < $_SESSION["unidad"]; $uniAct++) { 
            		$notaUnidadCursos[$i][$uniAct] = $notasTareas[$i][$uniAct]+$notasParcialesCursos[$i][$uniAct];
            	}
            }
   
            // A las notas les asigno el nombre de sus cursos
            for ($i = 0; $i < count($notaUnidadCursos); $i++) {
            	array_unshift($notaUnidadCursos[$i],$cursosParcialesA[$i][1]);
            } 

        $notasBimestralesCursos = utf8_encode_recursive($notaUnidadCursos); 
		echo json_encode($notasBimestralesCursos);
		}
		else
			header("Location:registros.html");
	}

	// Script que busca las notas finales de los 5 bimestres por curso
	if (isset($_POST["notasFinalesBimestrales"])) {
		if (isset($_SESSION["grado_estudiante"])) {
			// Script para saber las notas finales

// Fragmento que encuentra los cursos del estudiante		
			$cursosBimestrales = new Cursos();
			$notastareas = new tareas();
			$cursosBimestrales->consultar($_SESSION["grado_estudiante"]);
			$cursosBimestralesA = array();
			$i = 0;
			while ($curso = $cursosBimestrales->obtener()) {
		 		$j = 0;
		 		foreach ($curso as $key => $value) {
		 			$cursosBimestralesA[$i][$j] = $value;
		 			$j++;
		 		}
		 		$i++;
		 	} 
             $cantCursos = count($cursosBimestralesA);

// Fragmento que encuentra las notas de examenes bimestrales

		 	$notasBimestralesAlumno = array();
            for ($i = 0; $i < count($cursosBimestralesA); $i++) {
            	$cursosBimestrales->setIdCurso($cursosBimestralesA[$i][0]);
               	$cursosBimestrales->consultarBimestrales($_SESSION["id_estudiante"]);
               	$notasBimestralesAlumno[$i] = $cursosBimestrales->extraerNotaBimestral(); 
            }

            for ($i = 0; $i < count($cursosBimestralesA); $i++) { 
            	if (!$notasBimestralesAlumno[$i]) {
            		$arrayBlanco = array();
            		$notasBimestralesAlumno[$i] = $arrayBlanco;
            		for ($j = 0; $j < 5; $j++) { 
            			$notasBimestralesAlumno[$i][$j] = 0;	
            		}
            	}
            	else{
            		$j = 0;
            		foreach ($notasBimestralesAlumno[$i] as $value) {
            			$bimestralInt = (int)$value;
            			$notasBimestralesAlumno[$i][$j] = $bimestralInt;
            			$j++;
            		}
            	}
            }
            $cantExBim = count($notasBimestralesAlumno);

 //Fragmento que encuentra las notas de examenes parciales

            $notasParcialesCursos = array();
		 	for ($i = 0; $i < count($cursosBimestralesA); $i++) { 
	 			$cursosBimestrales->setIdCurso($cursosBimestralesA[$i][0]); 
		 		for ($j = 0; $j < 5; $j++) {
		 			$cursosBimestrales->consultarParcial($j+1,$_SESSION["id_estudiante"]);
		 			$notasParcialesCursos[$i][$j] = $cursosBimestrales->extraerNotaParcial();
		 		}
		 	}
		 	
		 	 for ($i = 0; $i < count($cursosBimestralesA); $i++) {
                	for ($j = 0; $j < count($cursosBimestralesA[$i]); $j++) { 
            			if (!$notasParcialesCursos[$i][$j]) {
            				$notasParcialesCursos[$i][$j] = 0;
            			}
            			else{
            				foreach ($notasParcialesCursos[$i][$j] as $key => $value) {
            					$notasParcialesCursos[$i][$j] = (int)$value;
            				}
            			}	
                	}
             }
             $cantExPar = count($notasParcialesCursos);

//A continuacion obtendre las notas de las tareas

             $notaPrimerBimestre = array();   
             for ($i = 0; $i < count($cursosBimestralesA); $i++) { 
             	$notastareas->notas($cursosBimestralesA[$i][0],$_SESSION["id_estudiante"]);
			 	$notaPrimerBimestre[$i] = 0;
		 		while ($notaarray = $notastareas->obtenernotas()) {
					$j = 0;
					foreach ($notaarray as $key => $value) {
						if($j == 3)
							$notaPrimerBimestre[$i] = $notaPrimerBimestre[$i]+(int)$value;	
						$j++;
					}
				}   
             }

             $cantNotTar = count($notaPrimerBimestre);

//A Continuacion voy a fucionar los 3 tipos de notas
             
             $notasFinalesUnidad = array(); 
             for ($i = 0; $i < count($cursosBimestralesA); $i++) { 
             	for ($j = 0; $j < 5; $j++) { 
             		$tip1 = gettype($notasParcialesCursos[$i][$j]);
             		$tip2 = gettype($notasBimestralesAlumno[$i][$j]);
             		echo "Tipo 1 es $tip1";
             		echo "Tipo 2 es $tip2";
             		$notasFinalesUnidad[$i][$j] = (int)$notasParcialesCursos[$i][$j]+(int)$notasBimestralesAlumno[$i][$j];
             	}
             }

             for ($i = 0; $i < count($cursosBimestralesA); $i++) { 
             	$notasFinalesUnidad[$i][0] = $notasFinalesUnidad[$i][0] + $notaPrimerBimestre[$i]; 
             }

             $cantNotaFinBim = count($notasFinalesUnidad);
            
             echo "Encontre $cantCursos cursos <br>";
             echo "Encontre $cantExBim notas de examenes bimestrales";
             echo "Encontre $cantExPar notas de examenes parciales";
             echo "Encontre $cantNotTar totales de notas por curso";
             echo "El total de array de notas Finales es $cantNotaFinBim GRACIAS A DIOS";

		 	/*$notasBimestralesCursos = utf8_encode_recursive($notasBimestralesAlumno); 
			echo json_encode($notasBimestralesCursos);*/
		}
		else
			header("Location:registros.html");
	}

	if (isset($_POST["guardarcurso"])) {
		$_SESSION["cursoguardaro"] = $_POST["guardarcurso"];
		echo true;		
	}


	if (isset($_POST["datosdocente"])) {
		if (isset($_SESSION["id_docente"])) {
			$docente = new docente($_SESSION["id_docente"]);
			echo $docente->docente_nombre();
		}
		else{
			echo "Favor de iniciar session";
		}

	}

	if (isset($_POST["administrador"])) {
		if (isset($_SESSION["nombreAdmin"])) {
			$nombreSysAdmin = $_SESSION["nombreAdmin"];
			echo "$nombreSysAdmin";
		}
		else{
			echo "Favor de iniciar session";
		}

	}

	
	
	if (isset($_POST["datosEstudiante"])) {
		if (isset($_SESSION["id_estudiante"])) {
			$estudiante = new Estudiantes();
			$estudiante->setIdEstudiante($_SESSION["id_estudiante"]);
			echo $estudiante->estudiante_nombre();
		}
		else{
			echo "Favor de iniciar session";
		}

	}
	
function utf8_encode_recursive ($array)
{
    $result = array();
    foreach ($array as $key => $value)
    {
        if (is_array($value))
        {
            $result[$key] = utf8_encode_recursive($value);
        }
        else if (is_string($value))
        {
            $result[$key] = utf8_encode($value);
        }
        else
        {
            $result[$key] = $value;
        }
    }
    return $result;
}		
	if (isset($_POST["asignaciones"])) {
		if (isset($_SESSION["id_docente"])) {
			$docente = new asignaciones($_SESSION["id_docente"]);
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
			$tamarray = count($asignaciones);
			//$encodedArray = array_map(utf8_encode, $asignaciones);
			$array_utf8 = utf8_encode_recursive($asignaciones);	
			//$asignaciones_object = (object)$encodedArray;
			//////////////////	header('Content-type: application/json; charset=utf-8');
			echo json_encode($array_utf8);
			}
		else{
			echo false;
		}
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

	if (isset($_POST["notastareas"])) {
		$notastareas = new tareas();
		$notastareas->notas($_SESSION["cursoguardaro"],$_SESSION["id_estudiante"],$_SESSION["unidad"]);
		$notasaray = array();
		$i = 0;
		while ( $notaarray = $notastareas->obtenernotas()) {
			$j = 0;
			foreach ($notaarray as $key => $value) {
				$notasaray[$i][$j] = $value;
				$j++;
			}
			$i++;
		}
		
		$notasObject = utf8_encode_recursive($notasaray);
		echo json_encode($notasObject);
	}



	/*if(isset($_GET["asigTarGrado"]) && isset($_GET["asigTarCurso"])){
		$_SESSION["gradoGeneral"] = $_GET["asigTarGrado"];
		$_SESSION["cursoGeneral"] = $_GET["asigTarCurso"];
		$asigTarCurso = new Cursos();
		$asigTarCurso->setIdCurso($_SESSION["cursoGeneral"]);

		$asigTarCurso->consultarTareas();
		$i = 0;
		$tareasCurso = array();
		while ($tareaCurso = $asigTarCurso->extraerTarea()){
			$j = 0;
			foreach ($tareaCurso as $key => $value){
				$tareasCurso[$i][$j] = $value;
				$j++;
			}
			$i++;
		}
		$_SESSION["curTareasAsig"] = $tareasCurso;
		header('Location: asignacionTareas.html');
	}



*/


	if(isset($_GET["asigNotTarGrado"]) && isset($_GET["asigNotTarCurso"])){
		if (isset($_SESSION["id_docente"])) {
			$_SESSION["gradoGeneral"] = $_GET["asigNotTarGrado"];
                $_SESSION["cursoGeneral"] = $_GET["asigNotTarCurso"];
                $asigTarCurso = new Cursos();
				$asigNotTarGrado = new Grado();
				$asigNotTarGrado->setIdGrado($_SESSION["gradoGeneral"]);
                $asigNotTarGrado->obtenerEstudiantes();
				$asigTarCurso->setIdCurso($_SESSION["cursoGeneral"]);
				$asigTarCurso->setUnidad($_SESSION["unidad"]);
                $asigTarCurso->consultarTareas();
                $i = 0;
                $tareasCurso = array();
                while ($tareaCurso = $asigTarCurso->extraerTarea()){
                        $j = 0;
                        foreach($tareaCurso as $key => $value){
                                $tareasCurso[$i][$j] = $value;
                                $j++;
                        }
                        $i++;
				}
				
				$i = 0;
                $estudiantesCurso = array();
                while ($estudianteCurso = $asigNotTarGrado->obtenerEstudiantesExtraer()){
                        $j = 0;
                        foreach($estudianteCurso as $key => $value){
                                $estudiantesCurso[$i][$j] = $value;
                                $j++;
                        }
                        $i++;
                }
				                
                $notasClase = new tareas();
                $notasTareasAlumnos = array();
                for ($i = 0; $i < count($estudiantesCurso); $i++) { 
                	for ($j = 0; $j < count($tareasCurso); $j++) { 
                		$notasClase->notaTarea($tareasCurso[$j][0],$estudiantesCurso[$i][0]);
                		$notasTareasAlumnos[$i][$j] = $notasClase->obtenerNotaTarea();	
                	}
                }
                $_SESSION["estNotasAsig"] = $estudiantesCurso;
                $_SESSION["tarNotasAsig"] = $tareasCurso;
                $_SESSION["notNotasAsig"] = $notasTareasAlumnos;

                // Funcion que coloca 0 si el estudiante no tiene una nota
                for ($i = 0; $i < count($notasTareasAlumnos); $i++) {
                	for ($j = 0; $j < count($notasTareasAlumnos[$i]); $j++) { 
            			if (!$notasTareasAlumnos[$i][$j]) {
            				$notasTareasAlumnos[$i][$j] = "";
            			}
            			else{
            				foreach ($notasTareasAlumnos[$i][$j] as $key => $value) {
            					$notasTareasAlumnos[$i][$j] = (int)$value;
            				}
            			}	
                	}
                }
                
               	$tamArrayNotas = count($notasTareasAlumnos);
               if ($tamArrayNotas == 0) {
               	$arrayBlanco = array();
               		for ($i = 0; $i < count($estudiantesCurso); $i++) { 
               			array_push($notasTareasAlumnos,$arrayBlanco);
               		}
               		for ($i = 0; $i < count($estudiantesCurso); $i++) {
               			array_push($notasTareasAlumnos[$i],$estudiantesCurso[$i][1]);
               		}
                }
                if ($tamArrayNotas > 0){
                	for ($i = 0; $i < count($estudiantesCurso); $i++) {
                		array_unshift($notasTareasAlumnos[$i],$estudiantesCurso[$i][1]);
                	}               	
                }
                $_SESSION["notasCompletasC"] = $notasTareasAlumnos;
                header('Location: asigNotasTareas.html');
		}
		else
			header('Location: registros.html');
                
    }

    	// Fragmento que busca los estudiantes y sus notas parciales
    if(isset($_GET["gradoParciales"]) && isset($_GET["cursoParciales"])){
		if (isset($_SESSION["id_docente"])) {
			$_SESSION["gradoGeneral"] = $_GET["gradoParciales"];
            $_SESSION["cursoGeneral"] = $_GET["cursoParciales"];
            $asigTarCurso = new Cursos();
            $asigTarCurso->setIdCurso($_SESSION["cursoGeneral"]);
            $asigTarCurso->setUnidad($_SESSION["unidad"]);
            $asigTarCurso->consultarTareas();
            $notasClase = new tareas();
			$asigNotTarGrado = new Grado();
			$asigNotTarGrado->setIdGrado($_SESSION["gradoGeneral"]);
            $asigNotTarGrado->obtenerEstudiantes();
			
            $i = 0;
            $tareasCurso = array();
            while ($tareaCurso = $asigTarCurso->extraerTarea()){
                $j = 0;
                foreach($tareaCurso as $key => $value){
                    $tareasCurso[$i][$j] = $value;
                    $j++;
                }
                $i++;
			}
			$i = 0;
            $estudiantesCurso = array();
            while ($estudianteCurso = $asigNotTarGrado->obtenerEstudiantesExtraer()){
            	$j = 0;
                foreach($estudianteCurso as $key => $value){
                	$estudiantesCurso[$i][$j] = $value;
                    $j++;
                 }
                 $i++;
            }
            $_SESSION["estNotasAsig"] = $estudiantesCurso;


			$tamanioTareas = count($tareasCurso);
			if ($tamanioTareas != 0) {
				$notasTareasAlumnos = array();
            	for ($i = 0; $i < count($estudiantesCurso); $i++) { 
            		for ($j = 0; $j < count($tareasCurso); $j++) { 
              			$notasClase->notaTarea($tareasCurso[$j][0],$estudiantesCurso[$i][0]);
               			$notasTareasAlumnos[$i][$j] = $notasClase->obtenerNotaTarea();	
               		}
            	}

            	for ($i = 0; $i < count($notasTareasAlumnos); $i++) {
                	for ($j = 0; $j < count($notasTareasAlumnos[$i]); $j++) { 
            			if (!$notasTareasAlumnos[$i][$j]) {
            				$notasTareasAlumnos[$i][$j] = 0;
            			}
            			else{
            				foreach ($notasTareasAlumnos[$i][$j] as $key => $value) {
            					$notasTareasAlumnos[$i][$j] = (int)$value;
            				}
            			}	
                	}
             	}
            	$zonas = array();
            	$zona = array();
            	for ($i = 0; $i < count($estudiantesCurso); $i++) {
            		$zonaTareas = 0; 
            		for ($j = 0; $j < count($notasTareasAlumnos[$i]); $j++) { 
            			$zonaTareas = $zonaTareas + $notasTareasAlumnos[$i][$j];
            		}
            		array_push($zonas, $zona);
            		$zonas[$i][0] = $zonaTareas;
            	}
			}
			else{
				$zona = array();
            	$zonas = array();

            	for ($i = 0; $i < count($estudiantesCurso); $i++) { 
            		array_push($zonas, $zona);
            		$zonas[$i][0] = 0;
            	}
			}

            $notasParcialesAlumnos = array();
            for ($i = 0; $i < count($estudiantesCurso); $i++) { 
              	for ($j = 0; $j < 1; $j++) { 
               		$asigTarCurso->consultarParcial($_SESSION["unidad"],$estudiantesCurso[$i][0]);
               		$notasParcialesAlumnos[$i][$j] = $asigTarCurso->extraerNotaParcial(); 
               	}
            }

            $_SESSION["notNotasAsig"] = $notasParcialesAlumnos;

            for ($i = 0; $i < count($estudiantesCurso); $i++) { 
            	if(!$notasParcialesAlumnos[$i][0])
            		$notasParcialesAlumnos[$i][0] = "";
            	else{
            		foreach ($notasParcialesAlumnos[$i][0] as $value) {
            			$notasParcialesAlumnos[$i][0] = (int)$value;
            		}
            	}

            }

             for ($i = 0; $i < count($estudiantesCurso); $i++) {
             	array_unshift($zonas[$i],$estudiantesCurso[$i][1]);
             	array_push($zonas[$i],$notasParcialesAlumnos[$i][0]);
             }

             $_SESSION["parcialesCompletosC"] = $zonas;
             header('Location: asignacionNotaParcial.html');
		}
		else
			header('Location: registros.html');
                
    }


    	// Fragmento que busca los estudiantes y sus notas bimestrales
    if(isset($_GET["gradoBimestrales"]) && isset($_GET["cursoBimestrales"])){
		if (isset($_SESSION["id_docente"])) {
			$_SESSION["gradoGeneral"] = $_GET["gradoBimestrales"];
            $_SESSION["cursoGeneral"] = $_GET["cursoBimestrales"];
            $asigTarCurso = new Cursos();
            $asigTarCurso->setIdCurso($_SESSION["cursoGeneral"]);
            $asigNotTarGrado = new Grado();
            $notasClase = new tareas();
			$asigNotTarGrado->setIdGrado($_SESSION["gradoGeneral"]);
            $asigNotTarGrado->obtenerEstudiantes();
            $i = 0;
            $estudiantesCurso = array();
            while ($estudianteCurso = $asigNotTarGrado->obtenerEstudiantesExtraer()){
            	$j = 0;
                foreach($estudianteCurso as $key => $value){
                	$estudiantesCurso[$i][$j] = $value;
                    $j++;
                 }
                 $i++;
            }
            $_SESSION["estNotasAsig"] = $estudiantesCurso;

            // Variable identificadora de cada Bimestre
            $zonabim = 0;
            $zonas = array();
 // Bloque que realiza las operaciones por cada bimestre
            for ($unida = 1; $unida <= $_SESSION["unidad"]; $unida++) {
            	// preparo una nueva unidad 
            	$asigTarCurso->setUnidad($unida);

            	// Consulto las tareas de esa unidad
            	$asigTarCurso->consultarTareas();

            	// Almaceno las tareas de esa unidad en el array tareasCurso
            	$i = 0;
            	$tareasCurso = array();
            	while ($tareaCurso = $asigTarCurso->extraerTarea()){
                	$j = 0;
                	foreach($tareaCurso as $key => $value){
                    	$tareasCurso[$i][$j] = $value;
                    	$j++;
                	}
                	$i++;
				}


				$tamanioTareas = count($tareasCurso);
	 //Si el curso tiene como minimo unatarea 
				if ($tamanioTareas != 0) {
					// Array que almacena las notas de las tareas por cada alumno, el formato de la tabla resultante es la de asignar nota a las tareas
					$notasTareasAlumnos = array();
            		for ($i = 0; $i < count($estudiantesCurso); $i++) { 
            			for ($j = 0; $j < count($tareasCurso); $j++) { 
              				$notasClase->notaTarea($tareasCurso[$j][0],$estudiantesCurso[$i][0]);
               				$notasTareasAlumnos[$i][$j] = $notasClase->obtenerNotaTarea();	
               			}
            		}

            		//solamente convierto la tabla en valores de nuemros
            		for ($i = 0; $i < count($notasTareasAlumnos); $i++) {
                		for ($j = 0; $j < count($notasTareasAlumnos[$i]); $j++) { 
            				if (!$notasTareasAlumnos[$i][$j]) {
            					$notasTareasAlumnos[$i][$j] = 0;
            				}
            				else{
            					foreach ($notasTareasAlumnos[$i][$j] as $value) {
            						$notasTareasAlumnos[$i][$j] = (int)$value;
            					}
            				}	
                		}
             		}
             		// Almaceno la suma de todas las notas de tareas por cada estudiante en el bimestre correspondiente 
            		
            		for ($i = 0; $i < count($estudiantesCurso); $i++) {
            			$zona = array();
            			$zonaTareas = 0; 
            			for ($j = 0; $j < count($notasTareasAlumnos[$i]); $j++) { 
            				$zonaTareas = $zonaTareas + $notasTareasAlumnos[$i][$j];
            			}
            			array_push($zonas, $zona);
            			$zonas[$i][$zonabim] = $zonaTareas;
            		}
				}
				// De lo contrario le asigno a cada estudiante zona de 0 en el bimestre correspondiente
				else{
            		for ($i = 0; $i < count($estudiantesCurso); $i++) {
            			$zona = array(); 
            			array_push($zonas, $zona);
            			$zonas[$i][$zonabim] = 0;
            		}
				}
            	$zonabim++;
            }
            // Busco la nota de los parciales por cada estudiante y lo almaceno en el bimestre correspondiente
            $notasParcialesAlumnos = array();
            for ($i = 0; $i < count($estudiantesCurso); $i++) { 
              	for ($j = 0; $j < $_SESSION["unidad"]; $j++) { 
               		$asigTarCurso->consultarParcial($j+1,$estudiantesCurso[$i][0]);
               		$notasParcialesAlumnos[$i][$j] = $asigTarCurso->extraerNotaParcial(); 
               	}
            }

            $_SESSION["notNotasAsig"] = $notasParcialesAlumnos;

            //Simplemente convierto la nota de los parciales a un numero  
            for ($i = 0; $i < count($estudiantesCurso); $i++) {
            	for ($j = 0; $j < count($notasParcialesAlumnos[$i]); $j++) { 
            	 	if(!$notasParcialesAlumnos[$i][$j])
            			$notasParcialesAlumnos[$i][$j] = 0;
            		else{
            			foreach ($notasParcialesAlumnos[$i][$j] as $value) {
            				$notasParcialesAlumnos[$i][$j] = (int)$value;
            			}
            		}
            	} 
            	
            }
            // Sumo las zona con los examenes Bimestrales.
            $notasBimestralesAlumno = array();
            for ($i = 0; $i < count($estudiantesCurso); $i++) { 
            	$notasBimestralesAlumno[$i] = array();
            	for ($zonabimf = 0; $zonabimf < $_SESSION["unidad"]; $zonabimf++) { 
            		$bimestralAlumno = $zonas[$i][$zonabimf]+$notasParcialesAlumnos[$i][$zonabimf];
            		$notasBimestralesAlumno[$i][$zonabimf] = $bimestralAlumno;
            	}
            }
            

             for ($i = 0; $i < count($estudiantesCurso); $i++) {
             	array_unshift($notasBimestralesAlumno[$i],$estudiantesCurso[$i][1]);
             }

             $_SESSION["bimestralesCompletosC"] = $notasBimestralesAlumno;
             header('Location: asignacionNotasFinales.html');
		}
		else
			header('Location: registros.html');
                
    }

        // Script que se ejecuta cuando el usuario ingresa a la opcion Asignar Tareas
        if(isset($_GET["asigTarGrado"]) && isset($_GET["asigTarCurso"])){
        	if (isset($_SESSION["id_docente"])) {
        		$asigTarCurso = new Cursos();
                $_SESSION["cursoGeneral"] = $_GET["asigTarCurso"];
                $asigTarCurso->setIdCurso($_SESSION["cursoGeneral"]);
                $asigTarCurso->setUnidad($_SESSION["unidad"]);
                $asigTarCurso->consultarTareas();
                $i = 0;
                $tareasCurso = array();
                while ($tareaCurso = $asigTarCurso->extraerTarea()){
                        $j = 0;
                        foreach($tareaCurso as $key => $value){
                                $tareasCurso[$i][$j] = $value;
                                $j++;
                        }
                        $i++;
				}
                $_SESSION["tarNotasAsig"] = $tareasCurso;
        		header('Location: asignacionTareas.html');
        	}
        	else
        		header('Location:registros.html'); 
        }

        if (isset($_POST["tareasAsignadas"])){
                $tareasObject = utf8_encode_recursive($_SESSION["tarNotasAsig"]);
                echo json_encode($tareasObject);
        }

        if (isset($_POST["notasAsignadas"])){
                $tareasObject = utf8_encode_recursive($_SESSION["notasCompletasC"]);         
                echo json_encode($tareasObject);
        };

        if (isset($_POST["notasAsignadasParciales"])){
                $tareasObject = utf8_encode_recursive($_SESSION["parcialesCompletosC"]);         
                echo json_encode($tareasObject);
        };




        if (isset($_POST["notasAsignadasBimestrales"])){
                $tareasObject = utf8_encode_recursive($_SESSION["bimestralesCompletosC"]);         
                echo json_encode($tareasObject);
        };

        



        if (isset($_POST["tareasAsignadasUpdate"])){
        		$tareasAsignadasUpdate = json_decode($_POST["tareasAsignadasUpdate"],true);
                $curso = new Cursos(); 
                $curso->setIdCurso($_SESSION["cursoGeneral"]);
                $curso->setUnidad($_SESSION["unidad"]);
                $tareasAntiguas = $_SESSION["tarNotasAsig"];
                
                for ($i = 0; $i < count($tareasAntiguas); $i++) { 
                	$curso->actualizarTarea($tareasAsignadasUpdate[$i]["Titulo"],$tareasAsignadasUpdate[$i]["fAsignacion"],$tareasAsignadasUpdate[$i]["fEntrega"],$tareasAsignadasUpdate[$i]["descripcionTarea"],$tareasAntiguas[$i][0]);
                }

                if (count($tareasAsignadasUpdate) > count($tareasAntiguas)) {
                	for ($i = count($tareasAntiguas); $i < count($tareasAsignadasUpdate); $i++) { 
                		$curso->asignarTarea($tareasAsignadasUpdate[$i]["Titulo"],$tareasAsignadasUpdate[$i]["fAsignacion"],$tareasAsignadasUpdate[$i]["fEntrega"],$tareasAsignadasUpdate[$i]["descripcionTarea"]);
                	}
                }
        }
        

        if (isset($_POST["notasAsignadasUpdate"])){
        		$notasAsignadasUpdate = json_decode($_POST["notasAsignadasUpdate"],true);
                $tareaNotas = new tareas(); 
                $tareasAntiguas = $_SESSION["tarNotasAsig"];
                $estudiantesNotas = $_SESSION["estNotasAsig"];
                $notasAntiguas = $_SESSION["notNotasAsig"];
                
                for ($i = 0; $i < count($estudiantesNotas); $i++) {
                	$j=0;
                	foreach ($notasAsignadasUpdate[$i] as $value) {
                		if (is_string($value)) {
                			if ($value != "" && $value != "<br>") {
                				$valueInt = (int)$value;
                				if (!$notasAntiguas[$i][$j]) {
                					$tareaNotas->asignar_nota($tareasAntiguas[$j][0],$estudiantesNotas[$i][0],$value,"");
                				}
                				else{
                					foreach ($notasAntiguas[$i][$j] as $valuee) {
            							$notasAntiguas[$i][$j] = (int)$valuee;
            						}
                					if ($valueInt != $notasAntiguas[$i][$j]) {
                						if ($valueInt > $notasAntiguas[$i][$j] || $valueInt < $notasAntiguas[$i][$j]) {
                							$tareaNotas->actualizar_nota($tareasAntiguas[$j][0],$estudiantesNotas[$i][0],$value,"");
                						}
                					}
                				}
                			}
                		}
                		$j++;
                	}
                }
        }

        if (isset($_POST["notasParcialesUpdate"])){
        		$notasAsignadasUpdate = json_decode($_POST["notasParcialesUpdate"],true);
                $curso = new Cursos(); 
                $curso->setIdCurso($_SESSION["cursoGeneral"]);

                $estudiantesNotas = $_SESSION["estNotasAsig"];
                $notasAntiguas = $_SESSION["notNotasAsig"];

                for ($i = 0; $i < count($estudiantesNotas); $i++) {
                	$j=0;
                	foreach ($notasAsignadasUpdate[$i] as $value) {
                		if (is_string($value)) {
                			if ($value != "" && $value != "<br>") {
                				$valueInt = (int)$value;
                				if (!$notasAntiguas[$i][$j]) {
                					$curso->asignarNotaParcial($_SESSION["unidad"],$valueInt,$estudiantesNotas[$i][0]);
                				}
                				else{
                					foreach ($notasAntiguas[$i][$j] as $valuee) {
            							$notasAntiguas[$i][$j] = (int)$valuee;
            						}
                					if ($valueInt != $notasAntiguas[$i][$j]) {
                						if ($valueInt > $notasAntiguas[$i][$j] || $valueInt < $notasAntiguas[$i][$j]) {
                							$curso->actualizarNotaParcial($_SESSION["unidad"],$valueInt,$estudiantesNotas[$i][0]);
                						}
                					}
                				}
                			}                				
                		}
                		$j++;
                	}
                }
        }


        if (isset($_GET["imprimirNotasFinales"])){
        	$notasBimEstudiantes = $_SESSION["bimestralesCompletosC"];
        	$pdf = new PDF();
        	$pdf->AddPage();

        	$headerArray = array('Curso','Primera Unidad','Segunda Unidad', 'Tercera Unidad', 'Cuarta Unidad','Quinta Unidad','Promedio');
            $pdf->BasicTable($headerArray,$notasBimEstudiantes,$_SESSION["unidad"]);
            $pdf->Ln(15);
            $pdf->SetFont('Arial','B',10);
        	$pdf->Cell(0,10,'Observaciones______________________________________');
        	$pdf->Ln(5);
        	$pdf->Cell(0,10,'___________________________________________________');
			$pdf->Ln(20); 
			$pdf->SetFont('Arial','i',10);
			$pdf->Cell(0,10,'DIOS, Ciencia y arte',0,0,'C');
        	$pdf->Ln(5); 		
			$pdf->Output();
        }
        if (isset($_GET["cerrarSession"])) {
        	session_destroy();
        	header("Location:registros.html");
        }
?>
