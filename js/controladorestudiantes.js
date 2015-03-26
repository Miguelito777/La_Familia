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