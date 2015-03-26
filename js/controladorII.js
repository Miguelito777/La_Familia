$(document).on('ready',cargar);

function cargar(){
	docente.mostrar_nombre();
	eventbuttons.asignacionestodas();
}

function login(){
	var usuario = prompt("Usuario:");
	var password = prompt("Password: ");
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
					window.location="../La_Familia/docentes.html";
				if (data == "estudiante") 
					window.location = "../La_Familia/estudiantes.html";
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
	notas : function(grado){
		document.getElementById("asignaciones").innerHTML = "<h2>Buscando tareas ...</h2>";
		var gradoo = parseInt(grado); 
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
				documento.tareas_mostrar(tareasarray,gradoo);
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
				documento.estudiantes_mostrar(estudiantes_array,idtarea);
			}
		}).done(function(data,textStatus,jqXHR){
			if (console && console.log)
				console.log("La busqueda de los estudiantes ha sido realizada correcamente");
		}).fail(function (jqXHR,textStatus,errorThrown){
			if (console && console.log)
				console.log("Erro al solicitar a los estudiantes");
		});
	},
	notas_estudiantes_asignar : function (idtarea, idestudiante){
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
	}
}

var eventbuttons = {
	asignacionestodas : function(){
		docente.asignacionestodas();	
	} 
}

var documento = {
	asignaciones_mostrar: function(asignaciones){
		$("#asignaciones").append("<table class='table table-hover' id='tables'> <tr class='success'><th>Grado</th><th>Seccion</th><th>Jornada</th><th>Materia</th><th>Tareas</th><th>Notas</th><th>Finales</th></tr></table>");
		for (var i = 0; i < asignaciones.length ; i++) {
			$("#tables").append("<tr><td>"+asignaciones[i][2]+"</td><td>"+asignaciones[i][3]+"</td><td>"+asignaciones[i][4]+"</td><td>"+asignaciones[i][5]+"</td><td> <button type='button' class='btn btn-link' id="+asignaciones[i][0]+" onclick= 'tareas.asignar(this.id)'>Asignar</button></td><td> <button type='button' class='btn btn-link' id="+asignaciones[i][0]+" onclick = 'tareas.notas("+asignaciones[i][1]+")'>Asignar</button></td><td> <button type='button' class='btn btn-link' id="+asignaciones[i][0]+">Asignar</button></td></tr>");
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
		console.log(estudiantes);
		document.getElementById("asignaciones").innerHTML = "";
		$("#asignaciones").append("<table class='table table-hover' id='tables'><tr class='success'><th>Nombres</th><th>Apellidos</th></tr></table>");
		for (var i = 0; i < estudiantes.length; i++) {
			$("#tables").append("<tr><td>"+estudiantes[i][1]+"</td><td>"+estudiantes[i][2]+"</td></tr>");
		};

		var table = document.getElementById("tables");
		var rows = table.getElementsByTagName("tr");

		for (var i = 0; i < rows.length; i++) {
			var idestudiante = estudiantes[i][0];
			var currentRow = table.rows[i+1];
			var createClickHandler = 
				function (idtarea, idestudiante) {
					return function(){
						tareas.notas_estudiantes_asignar(idtarea,idestudiante);	
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
			success : function(data){
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

