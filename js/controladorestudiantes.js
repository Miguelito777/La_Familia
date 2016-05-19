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
						$.ajax({
							data : {guardarcurso : idcurso},
							url : "controlador.php",
							type : "POST",
							success : function(data){
								if (data == 1) {
									window.location = "../La_Familia/notasTareas.html";
								};		
							}
						})
						
					}
				};
			currentRow.onclick = createClickHandler(idcurso);	
		};


	},800);
	
}



function verFinales(){
        var estudiante = new Estudiantesss();
        //var salon = new Salon("Jesus","Menchu Xoyon","Silhyta");
        estudiante.notasFinales();
        //conferencia.obtenerTodasHorarios();
	document.getElementById("bimestrales").innerHTML = "<h1>Buscando Notas finales ...</h1>";
        setTimeout(function(){
 //       alert("Ya busque los estudianes");
	        document.getElementById("bimestrales").innerHTML = "";
                $("#bimestrales").append("<table class = 'table table-striped' id='cdisponibles'><tr class='success'><th>Curso</th><th>Primer Bimestre</th><th>Segundo Bimestre</th><th>Tercer Bimestre</th><th>Cuarto Bimestre</th><th>Promedio</th><th>Resultado</th></tr></table>");
                for (var i = 0; i < estudiante.estudiantesarray.length; i++) {
                        $("#cdisponibles").append("<tr><td>"+estudiante.estudiantesarray[i][0]+"</td><td>"+estudiante.estudiantesarray[i][1]+"</td><td>"+estudiante.estudiantesarray[i][2]+"</td><td>"+estudiante.estudiantesarray[i][3]+"</td><td>"+estudiante.estudiantesarray[i][4]+"</td><td>"+estudiante.estudiantesarray[i][5]+"</td><td>"+estudiante.estudiantesarray[i][6]+"</td></tr>");
                };

                /*document.getElementById("idsalon").innerHTML = "";
                $("#idsalon").append("<select class='form-control' id='selectsalones' onchange = 'horariosDisponibles(this.value)'><option value="+0+" selected = 'selected' '>Seleccionar salon</option></select>");
                for (var i = 0; i < salon.salonesarray.length; i++) {
                        $("#selectsalones").append("<option value="+salon.salonesarray[i][0]+">"+salon.salonesarray[i][1]+"</option>");
                };*/
        },500);

}

