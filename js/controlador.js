var docenteTareasAsignadas = [];
function colegioGrados(){
	document.getElementById("session").innerHTML = "<button type='button' class='btn btn-link' onclick= 'window.location.href = \"controlador.php?cerrarSession=true\"'>Cerrar sesión</button>";
	var colegioGradosAct = new Colegio();
	colegioGradosAct.obtenerGrados();
	setTimeout(function(){
		$("#asignaciones").append("<table class='table table-hover' id='tables'> <tr class='success'><th>Grado</th><th>Seccion</th><th>Jornada</th><th>Notas de Unidad</th><th>Solvencia</th></tr></table>");
		for (var i = 0; i < colegioGradosAct.grados_arrayJavascript.length ; i++) {
			$("#tables").append("<tr><td>"+colegioGradosAct.grados_arrayJavascript[i][1]+"</td><td>"+colegioGradosAct.grados_arrayJavascript[i][2]+"</td><td>"+colegioGradosAct.grados_arrayJavascript[i][3]+"</td><td><button type='button' class='btn btn-link' onclick= 'window.location.href = \"controlador.php?imprimirNotasUnidad="+colegioGradosAct.grados_arrayJavascript[i][0]+"&nombreGrado="+colegioGradosAct.grados_arrayJavascript[i][1]+"&seccionGrado="+colegioGradosAct.grados_arrayJavascript[i][2]+"&jornadaGrado="+colegioGradosAct.grados_arrayJavascript[i][3]+"\"'>Imprimir</button></td><td><button type='button' class='btn btn-link' onclick= 'window.location.href = \"controlador.php?solvenciaGrado="+colegioGradosAct.grados_arrayJavascript[i][0]+"\"'>Establecer</button></td></tr>");
		};
	},2000);
}

function colegioGradosRel(){
	document.getElementById("session").innerHTML = "<button type='button' class='btn btn-link' onclick= 'window.location.href = \"controlador.php?cerrarSession=true\"'>Cerrar sesión</button>";
	var colegioGradosAct = new Colegio();
	colegioGradosAct.obtenerGrados();
	setTimeout(function(){
		$("#asignaciones").append("<table class='table table-hover' id='tables'> <tr class='success'><th>Grado</th><th>Seccion</th><th>Jornada</th><th>Relacionar Curso</th></tr></table>");
		for (var i = 0; i < colegioGradosAct.grados_arrayJavascript.length ; i++) {
			$("#tables").append("<tr><td>"+colegioGradosAct.grados_arrayJavascript[i][1]+"</td><td>"+colegioGradosAct.grados_arrayJavascript[i][2]+"</td><td>"+colegioGradosAct.grados_arrayJavascript[i][3]+"</td><td><button type='button' class='btn btn-link' onclick= 'window.location.href = \"controlador.php?relGradoCurso="+colegioGradosAct.grados_arrayJavascript[i][0]+"\"'>Imprimir</button></td></tr>");
		};
	},2000);
}


function cargar(){
	docente.mostrar_nombre();
	eventbuttons.asignacionestodas();
	document.getElementById("session").innerHTML = "<button type='button' class='btn btn-link' onclick= 'window.location.href = \"controlador.php?cerrarSession=true\"'>Cerrar sesión</button>";
	document.getElementById("asignaciones").innerHTML = "<h1>Buscando sus asignaciones...</h1>";
} 

function nombreDocente(){
	docente.mostrar_nombre();
}

function datosAdmin(){
	var sysAdmin = new Administrador();
	sysAdmin.mostrarNombre();
}

function login(){
	
	usuario = $("#usuariocol").val();
	var password = $("#passwordcol").val();
	if (usuario != "" && password != "") {
		$.ajax({
			data : {"usuario":usuario, "password":password},
			url : "controlador.php",
			type : "POST",
			Cache : false,
			success : function(data){
				if (!data)
					alert("Usuario no registrado en el colegio");
				if (data == "docente") 
					window.location="../SIRPA/docentes.html";
				if (data == "estudiante") 
					window.location = "../SIRPA/estudiantes.html";
				if (data == "administrador")
					window.location = "../SIRPA/sysAdmin.html";
			}
		}).done(function(data,textStatus,jqXHR){
			if (console && console.log())
				console.log("Usuario y contrasenia encontrados correctamente");
		}).fail(function(jqXHR,textStatus,errorThrown){
			if (console && console.log)
				console.log("Error al buscar al usuario y contrasenia");
		})
	}
	else
		alert("Ingresar todos los campos requeridos");
}

var tareas = {

	finales : function (grado,curso){
		
	 $.ajax({
                                data : {"grado":grado, "curso":curso},
                                type : "POST",
                                url : "controlador.php",
                                Cache: false,
                                success : function(data){
                                        var estudiantes_object = $.parseJSON(data);
                                	var estudiantes_array = [];
                                	for(var i in estudiantes_object){
                                        	var estudiante = [];
                                        	var k = 0;
                                        	for(var j in estudiantes_object[i]){
                                                	estudiante[k] = estudiantes_object[i][j];
                                                	k++;
                                        	}
                                        	estudiantes_array.push(estudiante);					
                                	}
					documento.estudiantes_mostrar(estudiantes_array, curso);
                                }
                        }).done(function(data,textStatus,jqXHR){
                                if(console && console.log){
                                        console.log("La tarea se a asignado correctamente");
                                }
                        }).fail(function(jqXHR,textStatus,errorThrown){
                                if(console && console.log)
                                        console.log("Error en la asignacion de tarea");
                        });
	},
	asignar : function(periodo_docente){
		var date = new Date();
		var nombretarea = prompt("Nombre de la Tarea");
		var fechaasignacion = date.getFullYear() + "/" + (date.getMonth() +1) + "/" + date.getDate();
		var fechaentrega = prompt("Fecha entrega");
		var descripcion = prompt("descripcion de la tarea");

		if(nombretarea != "" && fechaentrega != "" && descripcion != "" && nombretarea != null && fechaentrega != null && descripcion != null){
			$.ajax({
				data : {"periodo_docente" : periodo_docente, "nombretarea" : nombretarea, "fechaasignacion" : fechaasignacion, "fechaentrega" : fechaentrega, "descripcion" : descripcion},
				type : "POST",
				url : "controlador.php",
				Cache: false,
				success : function(data){
					alert(data);
				}
			}).done(function(data,textStatus,jqXHR){
				if(console && console.log){
					console.log("La tarea se a asignado correctamente");
				}
			}).fail(function(jqXHR,textStatus,errorThrown){
				if(console && console.log)
					console.log("Error en la asignacion de tarea");
			}); 
		}
		else
			alert("Ingresar todos los campos requeridos");
	},
	notas : function(grado,periododocente){
		document.getElementById("asignaciones").innerHTML = "<h2>Buscando tareas ...</h2>";
		var gradoo = parseInt(periododocente); 
		$.ajax({
			data : {"notasasignadas" : true, "grado" : gradoo},
			url : "controlador.php",
			type : "POST",
			success : function(data){
				var tareasobject = $.parseJSON(data);
				console.log(tareasobject);
				var tareasarray = [];
				var k = 0;
				for(var i in tareasobject){
					var tarea = [];
					k = 0;
					for (var j in tareasobject[i]){
						tarea[k] = tareasobject[i][j];
						k++; 
					};
					tareasarray.push(tarea);
				}
				documento.tareas_mostrar(tareasarray,grado);
			}
		}).done(function(data,textStatus,jqXHR){
			if(console && console.log){
				console.log("Tareas asignadas solicitadas correctamente");
			}
		}).fail(function(jqXHR,textStatus,errorThrown){
			if(console && console.log)
				console.log("Error al solicitar las tareas");
		}); 
	},
	notas_estudiantes : function (idtarea,grado){
		$.ajax({
			data : {"idtarea" : idtarea, "idgrado" : grado},
			type : "POST",
			url : "controlador.php",
			success : function (data){
				var estudiantes_object = $.parseJSON(data);
				var estudiantes_array = [];
				for(var i in estudiantes_object){
					var estudiante = [];
					var k = 0;
					for(var j in estudiantes_object[i]){
						estudiante[k] = estudiantes_object[i][j];
						k++;
					}
					estudiantes_array.push(estudiante);
				} 
				documento.estudiantes_mostrar_tarea(estudiantes_array,idtarea);
			}
		}).done(function(data,textStatus,jqXHR){
			if (console && console.log)
				console.log("La busqueda de los estudiantes ha sido realizada correcamente");
		}).fail(function (jqXHR,textStatus,errorThrown){
			if (console && console.log)
				console.log("Erro al solicitar a los estudiantes");
		});
	},
	notas_estudiantes_tarea : function (idtarea, idestudiante){
		var nota = prompt("Ingrese Nota: ");
		var observacion = prompt("Observacion: ");
		$.ajax({
			data : {"idtarea" : idtarea, "idestudiante" : idestudiante, "nota" : nota, "observacion" : observacion},
			url : "controlador.php",
			type : "POST",
			success : function (data){
				if(data)
					alert("Nota de tarea asignada correctamente");
				else
					alert("Erro al asignar la nota de la tarea");
			}
		}).done(function(data,textStatus,jqXHR){
			if(console && console.log)
				console.log("Se ha almacenado correctamente la tarea para este estudiante");
		}).fail(function(jqXHR,textStatus,errorThrown){
			if (console && console.log)
				console.log("Erro al almacenar la tarea del estudiante"); 
		});
	}, 
	notas_estudiantes_asignar : function (idcurso, idestudiante){
                var nota = prompt("Ingrese Nota de Examen Final: ");
                $.ajax({
                        data : {"idcurso" : idcurso, "idestudianteb" : idestudiante, "notaef" : nota},
                        url : "controlador.php",
                        type : "POST",
                        success : function (data){
                                if(data)
                                        alert("Nota de tarea asignada correctamente");
                                else
                                        alert("Erro al asignar la nota de la tarea");
                        }
                }).done(function(data,textStatus,jqXHR){
                        if(console && console.log)
                                console.log("Se ha almacenado correctamente la tarea para este estudiante");
                }).fail(function(jqXHR,textStatus,errorThrown){
                        if (console && console.log)
                                console.log("Erro al almacenar la tarea del estudiante");
                });
        }
}

var eventbuttons = {
	asignacionestodas : function(){
		docente.asignacionestodas();	
	} 
}

var documento = {
	asignaciones_mostrar: function(asignaciones){
		document.getElementById("asignaciones").innerHTML="";
		$("#asignaciones").append("<table class='table table-hover' id='tables'> <tr class='success'><th>Grado</th><th>Seccion</th><th>Jornada</th><th>Curso</th><th>Nueva Tarea</th><th>Nota de Tareas</th><th>Notas Examen de Unidad</th><th>Progreso Estudiantes</th></tr></table>");
		for (var i = 0; i < asignaciones.length ; i++) {
			$("#tables").append("<tr><td>"+asignaciones[i][2]+"</td><td>"+asignaciones[i][3]+"</td><td>"+asignaciones[i][4]+"</td><td>"+asignaciones[i][5]+"</td><td> <button type='button' class='btn btn-link' id="+asignaciones[i][0]+" onclick= 'window.location.href = \"controlador.php?asigTarGrado="+asignaciones[i][1]+"&asigTarCurso="+asignaciones[i][0]+"\"'>Asignar</button></td><td> <button type='button' class='btn btn-link' id="+asignaciones[i][0]+" onclick = 'window.location.href = \"controlador.php?asigNotTarGrado="+asignaciones[i][1]+"&asigNotTarCurso="+asignaciones[i][0]+"\"'>Asignar</button></td><td> <button type='button' class='btn btn-link' id="+asignaciones[i][0]+" onclick='window.location.href=\"controlador.php?gradoParciales="+asignaciones[i][1]+"&cursoParciales="+asignaciones[i][0]+"\"'>Asignar</button></td><td> <button type='button' class='btn btn-link' id="+asignaciones[i][0]+" onclick = 'window.location.href=\"controlador.php?gradoBimestrales="+asignaciones[i][1]+"&cursoBimestrales="+asignaciones[i][0]+"\"'>Visualizar</button></td></tr>");
		}; 	
	},
	tareas_mostrar : function (tareass,grado){
		document.getElementById("asignaciones").innerHTML = "";
		$("#asignaciones").append("<table class='table table-hover' id='tables'><tr class='success'><th>Fecha Asignacion</th><th>Fecha Entrega</th><th>Tarea</th><th>Materia</th><th>Descripcion</th></tr></table>");
		var i = 0;
		while  (i < tareass.length) {
			$("#tables").append("<tr><td>"+tareass[i][1]+"</td><td>"+tareass[i][2]+"</td><td>"+tareass[i][3]+"</td><td>"+tareass[i][4]+"</td><td>"+tareass[i][5]+"</td></tr>");
			i++;	
		};

		var table = document.getElementById("tables");
    	var rows = table.getElementsByTagName("tr");

 	   	for (var i = 0; i < rows.length; i++) {
 	   		var idtarea = tareass[i][0];
        	var currentRow = table.rows[i+1];
        	var createClickHandler = 
            	function(tareaid,grado) {
                	return function() {
                    	tareas.notas_estudiantes(tareaid,grado);
                	};
            	};
        	currentRow.onclick = createClickHandler(idtarea,grado);
    	}
	},
	estudiantes_mostrar : function (estudiantes, idtarea){
		
		document.getElementById("asignaciones").innerHTML = "";
		$("#asignaciones").append("<table class='table table-hover' id='tables'><tr class='success'><th>Nombres</th></tr></table>");
		for (var i = 0; i < estudiantes.length; i++) {
			$("#tables").append("<tr><td>"+estudiantes[i][1]+"</td></tr>");
		};

		var table = document.getElementById("tables");
		var rows = table.getElementsByTagName("tr");

		for (var i = 0; i < rows.length; i++) {
			var idestudiante = estudiantes[i][0];
			var currentRow = table.rows[i+1];
			var createClickHandler = 
				function (idtarea, idestudiante) {
					return function(){
					//	alert(idtarea);	
						tareas.notas_estudiantes_asignar(idtarea,idestudiante);	
					}
				};
			currentRow.onclick = createClickHandler(idtarea,idestudiante);	
		};			
	},

	  estudiantes_mostrar_tarea : function (estudiantes, idtarea){

                document.getElementById("asignaciones").innerHTML = "";
                $("#asignaciones").append("<table class='table table-hover' id='tables'><tr class='success'><th>Nombres</th></tr></table>");
                for (var i = 0; i < estudiantes.length; i++) {
                        $("#tables").append("<tr><td>"+estudiantes[i][1]+"</td></tr>");
                };

                var table = document.getElementById("tables");
                var rows = table.getElementsByTagName("tr");

                for (var i = 0; i < rows.length; i++) {
                        var idestudiante = estudiantes[i][0];
                        var currentRow = table.rows[i+1];
                        var createClickHandler =
                                function (idtarea, idestudiante) {
                                        return function(){
                                        //      alert(idtarea); 
                                                tareas.notas_estudiantes_tarea(idtarea,idestudiante);
                                        }
                                };
                        currentRow.onclick = createClickHandler(idtarea,idestudiante);
                };
        }

	
}

var docente = {
	mostrar_nombre: function(){
		$.ajax({
			data: {"datosdocente" : true},
			url: "controlador.php",
			type: "POST",
			Cache: false,
			success: function (data){
				document.getElementById("nombre").innerHTML = "<strong>"+data+"</strong>";
			}
		}).done(function( data, textStatus, jqXHR ) {
			if ( console && console.log ) {
				console.log( "La solicitud se ha completado correctamenteeee." );
			}
		}).fail(function( jqXHR, textStatus, errorThrown ){
			alert("La solicitud a fallado, no puede ser");
			if ( console && console.log ){
				console.log( "La solicitud a fallado Miguelito nuevo: " +  textStatus);
			}
		});		
	},
	asignacionestodas: function (){
		$.ajax({
			data : {"asignaciones" : true},
			url : "controlador.php",
			type : "POST",
		//	dataType: 'json',
			success : function(data){
				if (!data) {
					window.location="index.html";
				}
				else{
					var asignacionesobject = $.parseJSON(data);
					var asignacionarray = [];
					var k = 0;
					for (var i in asignacionesobject) {
						var mes = [];
						k = 0;
						for (var j in asignacionesobject[i]) {
							mes[k] = asignacionesobject[i][j];
							k++;
						};
						asignacionarray.push(mes);
					};
					documento.asignaciones_mostrar(asignacionarray);
				}
			}
		}).done(function( data, textStatus, jqXHR ) {
			if ( console && console.log ) {
				console.log( "La solicitud de asignaciones se realizo correctamente." );
			}
		}).fail(function( jqXHR, textStatus, errorThrown ){
			alert("La solicitud a fallado, no puede ser");
			if ( console && console.log ){
				console.log( "La solicitud de asignaciones fallo: " +  textStatus);
			}
		});
	} 
}

function notasdetareas(){
	document.getElementById("notasTareaCurso").innerHTML = "<h1>Buscando las notas de las tareas de este curso...</h1>";
	estudiante.mostrar_nombre();
	var notastareas = new notasTareas();
	notastareas.solicitar();
	document.getElementById("menuPrincipal").innerHTML = "<button type='button' class='btn btn-link' onclick= 'window.location.href = \"estudiantes.html\"'>Menú Principal</button>";
	document.getElementById("session").innerHTML = "<button type='button' class='btn btn-link' onclick= 'window.location.href = \"controlador.php?cerrarSession=true\"'>Cerrar sesión</button>";

	setTimeout(function(){
		document.getElementById("notasTareaCurso").innerHTML = " ";
		$("#notasTareaCurso").append("<table class='table table-striped' id='tablanotas'><tr class='success'><th>Nombre de la tarea</th><th>Fecha de Entrega</th><th>Punteo</th></tr></table>");
		console.log(notastareas.notas_array);
		for (var i = 0; i < notastareas.notas_array.length; i++) {
			$("#tablanotas").append("<tr><td>"+notastareas.notas_array[i][1]+"</td><td>"+notastareas.notas_array[i][2]+"</td><td>"+notastareas.notas_array[i][3]+"</td></tr>");
		};
	},3000); 
}


function asignarTareas(){
	document.getElementById("menuPrincipal").innerHTML = "<button type='button' class='btn btn-link' style='margin:auto' onclick= 'window.location.href = \"docentes.html\"'>Menú Principal</button>";
	document.getElementById("session").innerHTML = "<button type='button' class='btn btn-link' onclick= 'window.location.href = \"controlador.php?cerrarSession=true\"'>Cerrar sesión</button>";
	var curso = new Cursos();
	curso.solicitarTareas();
	document.getElementById("tareasAsignadas").innerHTML = "<h1>Buscando tareas asignadas...</h1>";
	setTimeout(function(){
		if (curso.tareas_array === undefined) {
			setTimeout(function(){
				if (curso.tareas_array === undefined) {
					setTimeout(function(){
						for (var i = 0; i < curso.tareas_array.length; i++) {
							curso.tareas_array[i].shift();
						};
						var obj = { width: 1095, height: 447, title: "Asignación de Tareas",resizable:false,draggable:false };
						obj.colModel = 
						[
							{ title: "Titulo", width: 273, dataType: "string", align: "center", editable: true},
							{ title: "Fecha de Asignacion", width: 135, dataType: "integer", align: "center", editable: false},
							{ title: "Fecha de Entrega", width: 125, dataType: "integer", align: "center", editable: true },
							{ title: "Descripción", width: 350, dataType: "integer", align: "center", editable: true }
						];
						obj.dataModel = { data: curso.tareas_array};
						var $grid = $("#tareasAsignadas").pqGrid(obj);
						$("#tareasAsignadas").pqGrid({editModel:{clicksToEdit: 1,saveKey:13 }});
				                    
						$grid.on( "pqgridcellsave", function(){
							obj.dataModel = { data: curso.tareas_array};
							var $grid = $("#tareasAsignadas").pqGrid(obj);
							$("#tareasAsignadas").pqGrid({editModel:{clicksToEdit: 1,saveKey:13 }});
						});

						$("#otraTar").on('click',function saludo(){
							var date = new Date();
							var listaMes = document.getElementById("mesEntrega");
							var valorSeleccionadovalue = listaMes.options[listaMes.selectedIndex].value;
							var listaDia = document.getElementById("diaEntrega");
							var horaEntrega = document.getElementById("horaEntrega").value;
							var valorSelectDia = listaDia.options[listaDia.selectedIndex].value;
							var mesInt = parseInt(valorSeleccionadovalue);
							var diaInt = parseInt(valorSelectDia);
							var nombretarea = document.getElementById("recipient-name").value;
							var fechaasignacion = date.getFullYear() + "-" + (date.getMonth() +1) + "-" + date.getDate();
							var fechaentrega = date.getFullYear()+"-"+mesInt+"-"+diaInt;
							var descripcion = document.getElementById("message-text").value;

							if(diaInt != 0 && nombretarea != "" && fechaentrega != "" && descripcion != "" && nombretarea != null && fechaentrega != null && descripcion != null)
								curso.tareas_array.push([nombretarea,fechaasignacion, fechaentrega,descripcion]);
							else
								alert("Ingresar todos los campos requeridos");
				        				
							obj.dataModel = { data: curso.tareas_array};
							var $grid = $("#tareasAsignadas").pqGrid(obj);
							$("#tareasAsignadas").pqGrid({editModel:{clicksToEdit: 1,saveKey:13 }});      			
						});
					},1000);
				}
				else{
					for (var i = 0; i < curso.tareas_array.length; i++) {
						curso.tareas_array[i].shift();
					};
					var obj = { width: 1095, height: 447, title: "Asignación de Tareas",resizable:false,draggable:false };
					obj.colModel = 
					[
						{ title: "Titulo", width: 273, dataType: "string", align: "center", editable: true},
						{ title: "Fecha de Asignacion", width: 135, dataType: "integer", align: "center", editable: false},
						{ title: "Fecha de Entrega", width: 125, dataType: "integer", align: "center", editable: true },
						{ title: "Descripción", width: 350, dataType: "integer", align: "center", editable: true }
					];
					obj.dataModel = { data: curso.tareas_array};
					var $grid = $("#tareasAsignadas").pqGrid(obj);
					$("#tareasAsignadas").pqGrid({editModel:{clicksToEdit: 1,saveKey:13 }});
			                    
					$grid.on( "pqgridcellsave", function(){
						obj.dataModel = { data: curso.tareas_array};
						var $grid = $("#tareasAsignadas").pqGrid(obj);
						$("#tareasAsignadas").pqGrid({editModel:{clicksToEdit: 1,saveKey:13 }});
					});

					$("#otraTar").on('click',function saludo(){
						var date = new Date();
						var listaMes = document.getElementById("mesEntrega");
						var valorSeleccionadovalue = listaMes.options[listaMes.selectedIndex].value;
						var listaDia = document.getElementById("diaEntrega");
						var valorSelectDia = listaDia.options[listaDia.selectedIndex].value;
						var mesInt = parseInt(valorSeleccionadovalue);
						var diaInt = parseInt(valorSelectDia);
						var nombretarea = document.getElementById("recipient-name").value;
						var fechaasignacion = date.getFullYear() + "-" + (date.getMonth() +1) + "-" + date.getDate();
						var fechaentrega = date.getFullYear()+"-"+mesInt+"-"+diaInt;
						var descripcion = document.getElementById("message-text").value;

						if(diaInt != 0 && nombretarea != "" && fechaentrega != "" && descripcion != "" && nombretarea != null && fechaentrega != null && descripcion != null)
							curso.tareas_array.push([nombretarea,fechaasignacion, fechaentrega,descripcion]);
						else
							alert("Ingresar todos los campos requeridos");
			        				
						obj.dataModel = { data: curso.tareas_array};
						var $grid = $("#tareasAsignadas").pqGrid(obj);
						$("#tareasAsignadas").pqGrid({editModel:{clicksToEdit: 1,saveKey:13 }});      			
					});
				}
			},1000);
		}
		else{
			// Le asigno el botton para eliminar la tarea
			docenteTareasAsignadas = curso.tareas_array;
			for (var i = 0; i < curso.tareas_array.length; i++) {
				curso.tareas_array[i].push("<button type='button' class='btn btn-link' id="+i+" onclick = 'deleteTarea(this.id)'>eliminar</button>");
			};
			var obj = { width: 1095, height: 447, title: "Asignación de Tareas",resizable:false,draggable:false };
			obj.colModel = 
			[
				{ title: "codigo", width: 25, dataType: "string", align: "center", editable: false},
				{ title: "Titulo", width: 273, dataType: "string", align: "center", editable: true},
				{ title: "Fecha Asignacion", width: 135, dataType: "integer", align: "center", editable: false},
				{ title: "Fecha Entrega", width: 125, dataType: "integer", align: "center", editable: true },
				{ title: "Hora entrega", width: 125, dataType: "integer", align: "center", editable: true },
				{ title: "Descripción", width: 200, dataType: "integer", align: "center", editable: true},
				{ title: "Opciones", width: 125, dataType: "integer", align: "center", editable: false}
			];
			obj.dataModel = { data: curso.tareas_array};
			var $grid = $("#tareasAsignadas").pqGrid(obj);
			$("#tareasAsignadas").pqGrid({editModel:{clicksToEdit: 1,saveKey:13 }});
	                    
			$grid.on( "pqgridcellsave", function(){
				obj.dataModel = { data: curso.tareas_array};
				var $grid = $("#tareasAsignadas").pqGrid(obj);
				$("#tareasAsignadas").pqGrid({editModel:{clicksToEdit: 1,saveKey:13 }});
			});

			$("#otraTar").on('click',function saludo(){
				var date = new Date();
				var listaMes = document.getElementById("mesEntrega");
				var valorSeleccionadovalue = listaMes.options[listaMes.selectedIndex].value;
				var listaDia = document.getElementById("diaEntrega");
				var valorSelectDia = listaDia.options[listaDia.selectedIndex].value;
				var mesInt = parseInt(valorSeleccionadovalue);
				var diaInt = parseInt(valorSelectDia);
				var nombretarea = document.getElementById("recipient-name").value;
				var fechaasignacion = date.getFullYear() + "-" + (date.getMonth() +1) + "-" + date.getDate();
				var fechaentrega = date.getFullYear()+"-"+mesInt+"-"+diaInt;
				var descripcion = document.getElementById("message-text").value;

				if(diaInt != 0 && nombretarea != "" && fechaentrega != "" && descripcion != "" && nombretarea != null && fechaentrega != null && descripcion != null)
					curso.tareas_array.push([nombretarea,fechaasignacion, fechaentrega,descripcion]);
				else
					alert("Ingresar todos los campos requeridos");
	        				
				obj.dataModel = { data: curso.tareas_array};
				var $grid = $("#tareasAsignadas").pqGrid(obj);
				$("#tareasAsignadas").pqGrid({editModel:{clicksToEdit: 1,saveKey:13 }});      			
			});
		}
	},1000);	
					

                    $("#saveTareas").on('click',function saludo()
                    {
                    document.getElementById("tareasAsignadas").innerHTML = "<h1>Almacenando tareas asignadas...</h1>";
                    var keys = ["Titulo","fAsignacion","fEntrega","descripcionTarea"];
                    obj = null;
                    output = [];

                    for (i = 0; i < curso.tareas_array.length; i++) {
                    	obj = {};
                    	for (k = 0; k < keys.length; k++) {
                    		obj[keys[k]] = curso.tareas_array[i][k];
                    	}

                    	output.push(obj);
                    }

                        var json_stringg = JSON.stringify(output);
                                              
                        $.ajax(
                        {
                            data: {"tareasAsignadasUpdate":json_stringg},
                            type: "POST",
                            url: "controlador.php",
                            cache: false,
                            success: function(data) 
                            {
                                window.location="docentes.html"
                            }
                        })
                        .done(function( data, textStatus, jqXHR ) 
                        {
                           if ( console && console.log ) 
                            {
                                console.log( "La solicitud se ha completado correctamenteeee." );
                            }
                        })
                        .fail(function( jqXHR, textStatus, errorThrown ) 
                        {
                            alert("La solicitud a fallado, no puede ser");
                            if ( console && console.log ) 
                            {
                                console.log( "La solicitud a falladoooooo: " +  textStatus);
                            }
                        });
        });	
}

/*
*Aqui obtengo las notas de las tareas 
*de los estudiantes
*/


function asigNotasTareas(){
	document.getElementById("menuPrincipal").innerHTML = "<button type='button' class='btn btn-link' onclick= 'window.location.href = \"docentes.html\"'>Menú Principal</button>";
	document.getElementById("session").innerHTML = "<button type='button' class='btn btn-link' onclick= 'window.location.href = \"controlador.php?cerrarSession=true\"'>Cerrar sesión</button>";
	var curso = new Cursos();
	curso.solicitarTareas();
	curso.solicitarNotas();

	document.getElementById("notasAsignadas").innerHTML = "<h1>Buscando Notas de tareas asignadas...</h1>";
	var columnas = [];
	setTimeout(function(){
        if (curso.notas_array === undefined) {
			setTimeout(function(){
				if (curso.notas_array === undefined){
					setTimeout(function(){
						var obj = { width: 1095, height: 447, title: "Asignación de Tareas",resizable:false,draggable:false };
					 	var cantidadEstudiantes = curso.notas_array.length;
					 	var cantidadTarEstudiantes = curso.notas_array[0].length;
					 	columnas.push({title: "Estudiantes", width: 300, dataType: "string", align: "center", editable: false});
					 	for (var i = 0; i < curso.tareas_array.length; i++) {
					 		columnas.push({title: curso.tareas_array[i][1], width: 100, dataType: "string", align: "center"});
					 	};
						columnas.push({title: "Zona", width: 50, dataType: "integer", align: "center", editable: false});
						for (var i = 0; i < curso.notas_array.length; i++) {
							var zona = 0;
							for (var j = 1; j < curso.notas_array[i].length; j++) {
								if (curso.notas_array[i][j] != "")
									zona += parseInt(curso.notas_array[i][j]);
								else
									zona += 0;
							};
							curso.notas_array[i].push(zona);
						};
						obj.colModel = columnas;
						obj.dataModel = { data: curso.notas_array};
			            var $grid = $("#notasAsignadas").pqGrid(obj);
			            $("#notasAsignadas").pqGrid({editModel:{clicksToEdit: 1,saveKey:13 }});
						
						$grid.on( "pqgridcellsave", function() {
			            	for (var i = 0; i < curso.notas_array.length; i++) {
			            		var zona = 0;
								var actNot = curso.notas_array[i].length;
								for (var j = 1; j < actNot-1; j++) {
									if (curso.notas_array[i][j] != "" && curso.notas_array[i][j] != "<br>")
										zona += parseInt(curso.notas_array[i][j]);
									else
										zona += 0;
								};
								curso.notas_array[i][actNot-1] = zona;
							};
								
							obj.dataModel = { data: curso.notas_array};
							var $grid = $("#tareasAsignadas").pqGrid(obj);
							$("#tareasAsignadas").pqGrid({editModel:{clicksToEdit: 1,saveKey:13 }});
						});						
					},1000);
				}
				else{
					var obj = { width: 1095, height: 447, title: "Asignación de Tareas",resizable:false,draggable:false };
				 	var cantidadEstudiantes = curso.notas_array.length;
				 	var cantidadTarEstudiantes = curso.notas_array[0].length;
				 	columnas.push({title: "Estudiantes", width: 300, dataType: "string", align: "center", editable: false});
				 	for (var i = 0; i < curso.tareas_array.length; i++) {
				 		columnas.push({title: curso.tareas_array[i][1], width: 100, dataType: "string", align: "center"});
				 	};
					columnas.push({title: "Zona", width: 50, dataType: "integer", align: "center", editable: false});
					for (var i = 0; i < curso.notas_array.length; i++) {
						var zona = 0;
						for (var j = 1; j < curso.notas_array[i].length; j++) {
							if (curso.notas_array[i][j] != "")
								zona += parseInt(curso.notas_array[i][j]);
							else
								zona += 0;
						};
						curso.notas_array[i].push(zona);
					};
					obj.colModel = columnas;
					obj.dataModel = { data: curso.notas_array};
		            var $grid = $("#notasAsignadas").pqGrid(obj);
		            $("#notasAsignadas").pqGrid({editModel:{clicksToEdit: 1,saveKey:13 }});
					
					$grid.on( "pqgridcellsave", function() {
		            	for (var i = 0; i < curso.notas_array.length; i++) {
		            		var zona = 0;
							var actNot = curso.notas_array[i].length;
							for (var j = 1; j < actNot-1; j++) {
								if (curso.notas_array[i][j] != "" && curso.notas_array[i][j] != "<br>")
									zona += parseInt(curso.notas_array[i][j]);
								else
									zona += 0;
							};
							curso.notas_array[i][actNot-1] = zona;
						};
							
						obj.dataModel = { data: curso.notas_array};
						var $grid = $("#tareasAsignadas").pqGrid(obj);
						$("#tareasAsignadas").pqGrid({editModel:{clicksToEdit: 1,saveKey:13 }});
					});					
				}
			},1000);	
		}
		else{
			var obj = { width: 1095, height: 447, title: "Asignación de Tareas",resizable:false,draggable:false };
		 	var cantidadEstudiantes = curso.notas_array.length;
		 	var cantidadTarEstudiantes = curso.notas_array[0].length;
		 	columnas.push({title: "Estudiantes", width: 300, dataType: "string", align: "center", editable: false});
		 	for (var i = 0; i < curso.tareas_array.length; i++) {
		 		columnas.push({title: curso.tareas_array[i][1], width: 100, dataType: "string", align: "center"});
		 	};
			columnas.push({title: "Zona", width: 50, dataType: "integer", align: "center", editable: false});
			for (var i = 0; i < curso.notas_array.length; i++) {
				var zona = 0;
				for (var j = 1; j < curso.notas_array[i].length; j++) {
					if (curso.notas_array[i][j] != "")
						zona += parseInt(curso.notas_array[i][j]);
					else
						zona += 0;
				};
				curso.notas_array[i].push(zona);
			};
			obj.colModel = columnas;
			obj.dataModel = { data: curso.notas_array};
            var $grid = $("#notasAsignadas").pqGrid(obj);
            $("#notasAsignadas").pqGrid({editModel:{clicksToEdit: 1,saveKey:13 }});
			
			$grid.on( "pqgridcellsave", function() {
            	for (var i = 0; i < curso.notas_array.length; i++) {
            		var zona = 0;
					var actNot = curso.notas_array[i].length;
					for (var j = 1; j < actNot-1; j++) {
						if (curso.notas_array[i][j] != "" && curso.notas_array[i][j] != "<br>")
							zona += parseInt(curso.notas_array[i][j]);
						else
							zona += 0;
					};
					curso.notas_array[i][actNot-1] = zona;
				};
					
				obj.dataModel = { data: curso.notas_array};
				var $grid = $("#tareasAsignadas").pqGrid(obj);
				$("#tareasAsignadas").pqGrid({editModel:{clicksToEdit: 1,saveKey:13 }});
			});
		} 
	},1000);
	
					$("#saveNotasTareas").on('click',function saludo()
                    {	
                    	document.getElementById("notasAsignadas").innerHTML = "<h1>Almacenando las notas de las tareas...</h1>";
                    	var keys = [];
                    	for (var i = 1; i < columnas.length-1; i++) {
                    		keys.push(columnas[i].title);
                    	};
                    
                    	obj = null;
                    	output = [];
                    	for (i = 0; i < curso.notas_array.length; i++) {
                    		obj = {};
                    		for (k = 0; k < keys.length; k++) {
                    			obj[keys[k]] = curso.notas_array[i][k+1];
                    		}
                    		output.push(obj);
                    	}

                        var json_stringg = JSON.stringify(output);

                       $.ajax(
                        {
                            data: {"notasAsignadasUpdate":json_stringg},
                            type: "POST",
                            url: "controlador.php",
                            cache: false,
                            success: function(data) 
                            {
                                window.location="docentes.html"
                                //console.log(data);
                            }
                        })
                        .done(function( data, textStatus, jqXHR ) 
                        {
                           if ( console && console.log ) 
                            {
                                console.log( "La solicitud se ha completado correctamenteeee." );
                            }
                        })
                        .fail(function( jqXHR, textStatus, errorThrown ) 
                        {
                            alert("La solicitud a fallado, no puede ser");
                            if ( console && console.log ) 
                            {
                                console.log( "La solicitud a falladoooooo: " +  textStatus);
                            }
                        });

        			});	
}


/*
*Aqui obtengo las notas de los Parciales 
*de los estudiantes
*/


function asigNotasParciales(){
	document.getElementById("menuPrincipal").innerHTML = "<button type='button' class='btn btn-link' onclick= 'window.location.href = \"docentes.html\"'>Menú Principal</button>";
	document.getElementById("session").innerHTML = "<button type='button' class='btn btn-link' onclick= 'window.location.href = \"controlador.php?cerrarSession=true\"'>Cerrar sesión</button>";
	var curso = new Cursos();
	curso.solicitarNotasParciales();

	document.getElementById("asignaciones").innerHTML = "<h1>Buscando notas de tareas y examenes de unidad...</h1>";
	var columnas = [];
	setTimeout(function(){
        if (curso.notas_array === undefined) {
        	setTimeout(function(){
        		if (curso.notas_array === undefined) {
        			setTimeout(function(){
						var obj = { width: 1095, height: 447, title: "Asignación de Notas Parciales",resizable:false,draggable:false };
						columnas.push({title: "Estudiantes", width: 300, dataType: "string", align: "center", editable: false});
						columnas.push({title: "Zona", width: 100, dataType: "integer", align: "center", editable: false});
						columnas.push({title: "Examen de unidad", width: 100, dataType: "integer", align: "center", editable: true});
						obj.colModel = columnas;
						obj.dataModel = { data: curso.notas_array};
						var $grid = $("#asignaciones").pqGrid(obj);
						$("#asignaciones").pqGrid({editModel:{clicksToEdit: 1,saveKey:13 }});
								
						$grid.on( "pqgridcellsave", function(){
							obj.dataModel = { data: curso.notas_array};
							var $grid = $("#asignaciones").pqGrid(obj);
							$("#asignaciones").pqGrid({editModel:{clicksToEdit: 1,saveKey:13 }});
						});
        			},1000);
        		}
        		else{
					var obj = { width: 1095, height: 447, title: "Asignación de Notas Parciales",resizable:false,draggable:false };
					columnas.push({title: "Estudiantes", width: 300, dataType: "string", align: "center", editable: false});
					columnas.push({title: "Zona", width: 100, dataType: "integer", align: "center", editable: false});
					columnas.push({title: "Examen de unidad", width: 100, dataType: "integer", align: "center", editable: true});
					obj.colModel = columnas;
					obj.dataModel = { data: curso.notas_array};
					var $grid = $("#asignaciones").pqGrid(obj);
					$("#asignaciones").pqGrid({editModel:{clicksToEdit: 1,saveKey:13 }});
							
					$grid.on( "pqgridcellsave", function(){
						obj.dataModel = { data: curso.notas_array};
						var $grid = $("#asignaciones").pqGrid(obj);
						$("#asignaciones").pqGrid({editModel:{clicksToEdit: 1,saveKey:13 }});
					});
        		}
        	},1000);
		}
		else{
			var obj = { width: 1095, height: 447, title: "Asignación de Notas Parciales",resizable:false,draggable:false };
			columnas.push({title: "Estudiantes", width: 300, dataType: "string", align: "center", editable: false});
			columnas.push({title: "Zona", width: 100, dataType: "integer", align: "center", editable: false});
			columnas.push({title: "Examen de unidad", width: 100, dataType: "integer", align: "center", editable: true});
			obj.colModel = columnas;
			obj.dataModel = { data: curso.notas_array};
			var $grid = $("#asignaciones").pqGrid(obj);
			$("#asignaciones").pqGrid({editModel:{clicksToEdit: 1,saveKey:13 }});
					
			$grid.on( "pqgridcellsave", function(){
				obj.dataModel = { data: curso.notas_array};
				var $grid = $("#asignaciones").pqGrid(obj);
				$("#asignaciones").pqGrid({editModel:{clicksToEdit: 1,saveKey:13 }});
			});
		}
	},1000);
	
					$("#saveNotasParciales").on('click',function saludo()
                    {	
                    	document.getElementById('asignaciones').innerHTML="";
                    	document.getElementById("asignaciones").innerHTML = "<h1>Almacenando las notas de examenes de unidad...</h1>";
                    	
                    	var keys = ["examenUnidad"];
                    
                    	obj = null;
                    	output = [];
                    	for (i = 0; i < curso.notas_array.length; i++) {
                    		obj = {};
                    		for (k = 0; k < keys.length; k++) {
                    			obj[keys[k]] = curso.notas_array[i][k+2];
                    		}
                    		output.push(obj);
                    	}

                        var json_stringg = JSON.stringify(output);
                       $.ajax(
                        {
                            data: {"notasParcialesUpdate":json_stringg},
                            type: "POST",
                            url: "controlador.php",
                            cache: false,
                            success: function(data) 
                            {
                                window.location="docentes.html";
                            }
                        })
                        .done(function( data, textStatus, jqXHR ) 
                        {
                           if ( console && console.log ) 
                            {
                                console.log( "La solicitud se ha completado correctamenteeee." );
                            }
                        })
                        .fail(function( jqXHR, textStatus, errorThrown ) 
                        {
                            alert("La solicitud a fallado, no puede ser");
                            if ( console && console.log ) 
                            {
                                console.log( "La solicitud a falladoooooo: " +  textStatus);
                            }
                        });
        			});	
}



function asigNotasBimestrales(){
	document.getElementById("asignaciones").innerHTML = "<h1>Buscando notas Finales asignadas...</h1>";
	document.getElementById("menuPrincipal").innerHTML = "<button type='button' class='btn btn-link' onclick= 'window.location.href = \"docentes.html\"'>Menú Principal</button>";
	document.getElementById("session").innerHTML = "<button type='button' class='btn btn-link' onclick= 'window.location.href = \"controlador.php?cerrarSession=true\"'>Cerrar sesión</button>";
	var curso = new Cursos();
	curso.solicitarNotasBimestrales();

	var columnas = [];
	setTimeout(function(){
        if (curso.notas_array === undefined) {
        	setTimeout(function(){
        		if (curso.notas_array === undefined) {
        			setTimeout(function(){
						var obj = { width: 1095, height: 447, title: "Asignación de Notas Bimestrales",resizable:false,draggable:false };
						columnas.push({title: "Estudiantes", width: 300, dataType: "string", align: "center", editable: false});
						columnas.push({title: "Primera Unidad", width: 150, dataType: "integer", align: "center", editable: false});
						columnas.push({title: "Segunda Unidad", width: 150, dataType: "integer", align: "center", editable: false});
						columnas.push({title: "Tercera Unidad", width: 150, dataType: "integer", align: "center", editable: false});
						columnas.push({title: "Cuarta Unidad", width: 150, dataType: "integer", align: "center", editable: false});
						columnas.push({title: "Quinta Unidad", width: 150, dataType: "integer", align: "center", editable: false});


						obj.colModel = columnas;
						obj.dataModel = { data: curso.notas_array};
						var $grid = $("#asignaciones").pqGrid(obj);
						$("#asignaciones").pqGrid({editModel:{clicksToEdit: 1,saveKey:13 }});
									
						$grid.on( "pqgridcellsave", function()   {
							obj.dataModel = { data: curso.notas_array};
							var $grid = $("#asignaciones").pqGrid(obj);
							$("#asignaciones").pqGrid({editModel:{clicksToEdit: 1,saveKey:13 }});
						});
        			});
        		}
        		else{
					var obj = { width: 1095, height: 447, title: "Asignación de Notas Bimestrales",resizable:false,draggable:false };
					columnas.push({title: "Estudiantes", width: 300, dataType: "string", align: "center", editable: false});
					columnas.push({title: "Primera Unidad", width: 150, dataType: "integer", align: "center", editable: false});
					columnas.push({title: "Segunda Unidad", width: 150, dataType: "integer", align: "center", editable: false});
					columnas.push({title: "Tercera Unidad", width: 150, dataType: "integer", align: "center", editable: false});
					columnas.push({title: "Cuarta Unidad", width: 150, dataType: "integer", align: "center", editable: false});
					columnas.push({title: "Quinta Unidad", width: 150, dataType: "integer", align: "center", editable: false});


					obj.colModel = columnas;
					obj.dataModel = { data: curso.notas_array};
					var $grid = $("#asignaciones").pqGrid(obj);
					$("#asignaciones").pqGrid({editModel:{clicksToEdit: 1,saveKey:13 }});
								
					$grid.on( "pqgridcellsave", function()   {
						obj.dataModel = { data: curso.notas_array};
						var $grid = $("#asignaciones").pqGrid(obj);
						$("#asignaciones").pqGrid({editModel:{clicksToEdit: 1,saveKey:13 }});
					});
        		}
        	},100);
		}
		else{
			var obj = { width: 1095, height: 447, title: "Asignación de Notas Bimestrales",resizable:false,draggable:false };
			columnas.push({title: "Estudiantes", width: 300, dataType: "string", align: "center", editable: false});
			columnas.push({title: "Primera Unidad", width: 150, dataType: "integer", align: "center", editable: false});
			columnas.push({title: "Segunda Unidad", width: 150, dataType: "integer", align: "center", editable: false});
			columnas.push({title: "Tercera Unidad", width: 150, dataType: "integer", align: "center", editable: false});
			columnas.push({title: "Cuarta Unidad", width: 150, dataType: "integer", align: "center", editable: false});
			columnas.push({title: "Quinta Unidad", width: 150, dataType: "integer", align: "center", editable: false});


			obj.colModel = columnas;
			obj.dataModel = { data: curso.notas_array};
			var $grid = $("#asignaciones").pqGrid(obj);
			$("#asignaciones").pqGrid({editModel:{clicksToEdit: 1,saveKey:13 }});
						
			$grid.on( "pqgridcellsave", function()   {
				obj.dataModel = { data: curso.notas_array};
				var $grid = $("#asignaciones").pqGrid(obj);
				$("#asignaciones").pqGrid({editModel:{clicksToEdit: 1,saveKey:13 }});
			});
		}
	},1000);
}

/*
*Funciones que se encarga de buscar las tareas de un estudiante
*Que manipula todo sobre los estudiantes
*/

function cargarEstudiante(){
	document.getElementById("menuPrincipal").innerHTML = "<button type='button' class='btn btn-link' onclick= 'window.location.href = \"estudiantes.html\"'>Menú Principal</button>";
	document.getElementById("session").innerHTML = "<button type='button' class='btn btn-link' onclick= 'window.location.href = \"controlador.php?cerrarSession=true\"'>Cerrar sesión</button>";
	estudiante.mostrar_nombre();
}
function mostrarTareas(){
	tareasEst.asignadas();
}
var estudiante = {
	mostrar_nombre: function(){
		$.ajax({
			data: {"datosEstudiante" : true},
			url: "controlador.php",
			type: "POST",
			Cache: false,
			success: function (data){
				document.getElementById("nombre").innerHTML = "<strong>"+data+"</strong>";
			}
		}).done(function( data, textStatus, jqXHR ) {
			if ( console && console.log ) {
				console.log( "La solicitud se ha completado correctamenteeee." );
			}
		}).fail(function( jqXHR, textStatus, errorThrown ){
			alert("La solicitud a fallado, no puede ser");
			if ( console && console.log ){
				console.log( "La solicitud a fallado Miguelito nuevo: " +  textStatus);
			}
		});		
	}
}

var tareasEst = {
	asignadas : function(){
		$.ajax({
			data : {"notasasignadasT" : true},
			url : "controlador.php",
			type : "POST",
			success : function(data){
				var tareasobject = $.parseJSON(data);
				console.log(tareasobject);
				var tareasarray = [];
				var k = 0;
				for(var i in tareasobject){
					var tarea = [];
					k = 0;
					for (var j in tareasobject[i]){
						tarea[k] = tareasobject[i][j];
						k++; 
					};
					tareasarray.push(tarea);
				}
				documentoEst.notas_mostrar(tareasarray);
			}
		}).done(function(data,textStatus,jqXHR){
			if(console && console.log){
				console.log("Tareas asignadas solicitadas correctamente");
			}
		}).fail(function(jqXHR,textStatus,errorThrown){
			if(console && console.log)
				console.log("Error al solicitar las tareas");
		}); 
	}
}

var documentoEst = {
	notas_mostrar : function(notas){
		document.getElementById("tareas").innerHTML = "";
		$("#tareas").append("<table class='table table-striped' id='tables'><tr class='success'><th>Fecha Asignacion</th><th>Fecha Entrega</th><th>Tarea</th><th>Materia</th><th>Descripcion</th></tr></table>");
		var i = 0;
		while  (i < notas.length) {
			$("#tables").append("<tr><td>"+notas[i][1]+"</td><td>"+notas[i][2]+"</td><td>"+notas[i][3]+"</td><td>"+notas[i][4]+"</td><td>"+notas[i][5]+"</td></tr>");
			i++;	
		};
	}
}

function verCursos(){
	document.getElementById("idcursos").innerHTML = "<h1>Buscando sus cursos...</h1>"
	var mis_cursos = new Cursos();
	mis_cursos.solicitar();
	setTimeout(function(){
		document.getElementById("idcursos").innerHTML = "";
		$("#idcursos").append("<table class='table table-hover' id='cursos'><tr class='success'><th><p class='text-center'>Mis Cursos</p></th></tr></table>");
		for (var i = 0; i < mis_cursos.cursos_array.length; i++) {
			$("#cursos").append("<tr><td>"+mis_cursos.cursos_array[i][1]+"</td></tr>")
		};

		var table = document.getElementById("cursos");
		var rows = table.getElementsByTagName("tr");

		for (var i = 0; i < rows.length; i++) {
			var idcurso = mis_cursos.cursos_array[i][0];
			var currentRow = table.rows[i+1];
			var createClickHandler = 
				function (idcurso) {
					return function(){
						$.ajax({
							data : {guardarcurso : idcurso},
							url : "controlador.php",
							type : "POST",
							success : function(data){
								if (data == 1) {
									window.location = "../SIRPA/notasTareas.html";
								};		
							}
						})
						
					}
				};
			currentRow.onclick = createClickHandler(idcurso);	
		};


	},3000);
	
}

function notasParciales(){
	document.getElementById("parciales").innerHTML = "<h1>Buscando sus notas parciales...</h1>";
	var cursosParciales = new Estudiantesss();
	cursosParciales.obtenerCursosParciales();
	setTimeout(function(){
		if (cursosParciales.cursosNotasParciales === undefined) {
			document.getElementById("parciales").innerHTML = "";
			document.getElementById("parciales").innerHTML = "<h1>Se ha producido un error. Vuelva a internatarlo...</h1>";	
		}
		else{
			document.getElementById("parciales").innerHTML = "";
			$("#parciales").append("<table class='table table-striped' id='tablesParciales'><tr class='success'><th>Cursos</th><th>Primera Unidad</th><th>Segunda Unidad</th><th>Tercera Unidad</th><th>Cuarta Unidad</th><th>Quinta Unidad</th></tr></table>");
			for (var i = 0; i < cursosParciales.cursosNotasParciales.length; i++) {
				$("#tablesParciales").append("<tr><td>"+cursosParciales.cursosNotasParciales[i][0]+"</td><td>"+cursosParciales.cursosNotasParciales[i][1]+"</td><td>"+cursosParciales.cursosNotasParciales[i][2]+"</td><td>"+cursosParciales.cursosNotasParciales[i][3]+"</td><td>"+cursosParciales.cursosNotasParciales[i][4]+"</td><td>"+cursosParciales.cursosNotasParciales[i][5]+"</td></tr>");	
			};
		}
		
	},3000);
}

function notasBimestrales(){
	document.getElementById("examenesBimestrales").innerHTML = "<h1>Buscando sus notas totales de unidad...</h1>";
	var cursosBimestrales = new Estudiantesss();
	cursosBimestrales.obtenerCursosBimestrales();
	setTimeout(function(){
		if (cursosBimestrales.cursosNotasBimestrales === undefined) {
			document.getElementById("examenesBimestrales").innerHTML = "";
			document.getElementById("examenesBimestrales").innerHTML = "<h1>Se ha producido un error. Vuelva a internatarlo...</h1>";	
		}
		else{
			document.getElementById("examenesBimestrales").innerHTML = "";
			$("#examenesBimestrales").append("<table class='table table-striped' id='tablesBimestrales'><tr class='success'><th>Cursos</th><th>Primer Bimestre</th><th>Segundo Bimestre</th><tr><table>");
			for (var i = 0; i < cursosBimestrales.cursosNotasBimestrales.length; i++) {
				$("#tablesBimestrales").append("<tr><td>"+cursosBimestrales.cursosNotasBimestrales[i][0]+"</td><td>"+cursosBimestrales.cursosNotasBimestrales[i][1]+"</td><td>"+cursosBimestrales.cursosNotasBimestrales[i][2]+"</td></tr>");	
			};
		}
	},3000);
}

function obtenerDiasMes(){
	var date = new Date();
	var dia = date.getDate();
	var mes = (date.getMonth()+1);

	var meses = ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
	document.getElementById('anio').innerHTML = "del "+anio;
   	for (var i = mes; i <= 12; i++) {
   		if (i == mes) 
		   	$("#mesEntrega").append("<option value="+i+" selected>"+meses[i-1]+"</option>");
   		else
   			$("#mesEntrega").append("<option value="+i+">"+meses[i-1]+"</option>");
   	};

	for (var i = dia; i <= 31; i++) {
   		$("#diaEntrega").append("<option value="+i+">"+i+"</option>");
   	};
   	$("#diaEntrega").append("<option value="+0+" selected>Seleccione un dia</option>");
	
   	
   	$("#mesEntrega").change(function(){
   		document.getElementById("diaEntrega").options.length = 0;
   		var lista = document.getElementById("mesEntrega");
 		// Obtener el valor de la opción seleccionada
		var valorSeleccionadovalue = lista.options[lista.selectedIndex].value;		
		//var valorSeleccionado = lista.options[lista.selectedIndex].text;
		var idmesInt = parseInt(valorSeleccionadovalue);

		if (idmesInt == mes) {
			for (var i = dia; i <= 31; i++) {
   				$("#diaEntrega").append("<option value="+i+">"+i+"</option>");
   			};
   			$("#diaEntrega").append("<option value="+0+" selected>Seleccione un dia</option>");
		}
		else{
			for (var i = 1; i <= 31; i++) {
   				$("#diaEntrega").append("<option value="+i+">"+i+"</option>");
   			};
   			$("#diaEntrega").append("<option value="+0+" selected>Seleccione un dia</option>");
		}
   	})
}


function deleteTarea(posicionEliminar){
	console.log("Voy a eliminar la tarea "+docenteTareasAsignadas[posicionEliminar]);

}
