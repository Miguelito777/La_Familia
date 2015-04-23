$(document).on('ready',cargar);
function cargar(){
	tareas.asignadas();
}
var tareas = {
	asignadas : function(){
		$.ajax({
			data : {"notasasignadas" : true},
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
				documento.notas_mostrar(tareasarray);
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

var documento = {
	notas_mostrar : function(notas){
		document.getElementById("tareas").innerHTML = "";
		$("#tareas").append("<table class='table table-hover' id='tables'><tr class='success'><th>Fecha Asignacion</th><th>Fecha Entrega</th><th>Tarea</th><th>Materia</th><th>Descripcion</th></tr></table>");
		var i = 0;
		while  (i < notas.length) {
			$("#tables").append("<tr><td>"+notas[i][1]+"</td><td>"+notas[i][2]+"</td><td>"+notas[i][3]+"</td><td>"+notas[i][4]+"</td><td>"+notas[i][5]+"</td></tr>");
			i++;	
		};
	}
}

function verCursos(){
	var mis_cursos = new Cursos();
	mis_cursos.solicitar();
	setTimeout(function(){
		document.getElementById("idcursos").innerHTML = "";
		$("#idcursos").append("<table class='table table-hover' id='cursos'><tr><th>Mis Cursos</th></tr></table>");
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
						console.log("Usted selecciono el Curso: "+idcurso);
					}
				};
			currentRow.onclick = createClickHandler(idcurso);	
		};


	},50);
	
}

