var student = new Student();
var course = new Course();
var tarea;
var works;
function showCourses(){
	document.getElementById("courses").innerHTML = "<div style='text-align:center'><img src='img/ajax-loader.gif'></div>";
	student.getCourses();
}
function showCoursesVista(cursos){
	document.getElementById("courses").innerHTML = "";
	$("#courses").append("<table class='table table-hover table-striped' id='coursesData'><thead><tr class='info'><th><h1><span class='glyphicon glyphicon-book'></span></h1></th><th><p class='lead'>Mis Cursos</p></th></tr></thead></table>");
	for(var i in cursos){
		$("#coursesData").append("<tr><td><h3><span class='glyphicon glyphicon-book'></span></h3></td><td><p class='lead'>"+cursos[i]["nombreMateria"]+"</p></td></tr>");
	}
	var table = document.getElementById("coursesData");
	var rows = table.getElementsByTagName("tr");
    for (var i = 0; i < rows.length; i++) {
    	var idCourse = cursos[i]["id_periodos_docentes"];
		var currentRow = table.rows[i+1];
		var createClickHandler =
		function (idCourse) {
			return function(){
				searchWork(idCourse);
			}
		};
		currentRow.onclick = createClickHandler(idCourse);
	};
}
function searchWork(idCourse){
	window.location.href = "controlador.php?searchWork="+idCourse+"";
}

function getWorks(){
	document.getElementById("works").innerHTML = "<div style='text-align:center'><img src='img/ajax-loader.gif'></div>";
	course.getWorks();	
}
function showCoursesWorksAll(tareas){
	works = tareas;
	document.getElementById("works").innerHTML = "";
	$("#works").append("<table class='table table-hover table-striped' id='coursesData'><thead><tr class='info'><th><h3><span class='glyphicon glyphicon-pencil'></span></h3></th><th><p class='lead'>Titulo</p></th><th><p class='lead'>Fecha Entrega</p></th><th><p class='lead'>Hora Maxima</p></th><th><p class='lead'>Punteo</p></th><th><p class='lead'>Entrega</p></th></tr></thead></table>");
	for(var i in works){
		if (works[i]["digital"] == "0")
			$("#coursesData").append("<tr><td><h3><span class='glyphicon glyphicon-pencil'></span></h3></td><td><p class='lead'>"+works[i]["nombreTarea"]+"</p></td><td><p class='lead'>"+works[i]["fechaentregaTarea"]+"</p></td><td><p class='lead'>"+works[i]["horaEntrega"]+"</p></td><td><p class='lead'>"+works[i]["valor"]+"</p></td><td><p class='lead'>Manual</p></td></tr>");
		else if(works[i]["digital"] == "1")
			$("#coursesData").append("<tr><td><h3><span class='glyphicon glyphicon-pencil'></span></h3></td><td><p class='lead'>"+works[i]["nombreTarea"]+"</p></td><td><p class='lead'>"+works[i]["fechaentregaTarea"]+"</p></td><td><p class='lead'>"+works[i]["horaEntrega"]+"</p></td><td><p class='lead'>"+works[i]["valor"]+"</p></td><td><p class='lead'>Digital</p></td></tr>");
	}
	var table = document.getElementById("coursesData");
	var rows = table.getElementsByTagName("tr");
    for (var i = 0; i < rows.length; i++) {
		var currentRow = table.rows[i+1];
		var createClickHandler =
		function (idCourse) {
			return function(){
				showDetilWork(idCourse);
			}
		};
		currentRow.onclick = createClickHandler(i);
	};
}
function showDetilWork(positionWork){
	if(works[positionWork]["digital"] == "1")
		window.location.href = "controlador.php?searchWorkDigitalDetails="+works[positionWork]["idTarea"];
	else
		window.location.href = "controlador.php?searchWorkManualDetails="+works[positionWork]["idTarea"];
}
function getDetailWork(){
	tarea = new Tarea();
	tarea.getDetailDigitalWork();
}
function showDetailDigitalWork(detailDigitalWork){
	tarea.allPath = detailDigitalWork[0]["fileTarea"];
	document.getElementById("tituloTarea").innerHTML = detailDigitalWork[0]["nombreTarea"];
	document.getElementById("fechaAsignada").innerHTML = detailDigitalWork[0]["fechaasignacionTarea"];
	document.getElementById("fechaMaxima").innerHTML = detailDigitalWork[0]["fechaentregaTarea"];
	document.getElementById("horaMaxima").innerHTML = detailDigitalWork[0]["horaEntrega"];
	document.getElementById("descripcionTarea").innerHTML = detailDigitalWork[0]["descripcionTarea"];	
	document.getElementById("puntuacionTarea").innerHTML = detailDigitalWork[0]["valor"];
	if (detailDigitalWork[0]["Punteo_Tarea"] == null) {
		document.getElementById("puntuacionObtenida").innerHTML = "Pendiente de calificacion";		
	}else{
		document.getElementById("puntuacionObtenida").innerHTML = detailDigitalWork[0]["Punteo_Tarea"];
		document.getElementById("observacionTarea").innerHTML = detailDigitalWork[0]["ObservacionTarea"];
	}
	if (detailDigitalWork[0]["fileTarea"] == null)
		document.getElementById("file").innerHTML = "Pendiente de entrega";
	else
		document.getElementById("file").innerHTML = "<button type='button' class='btn btn-link' onclick= 'downlandFile()'>"+detailDigitalWork[0]["fileTarea"]+"</button>";
}

function showNoticiesGrade(){
	student.getNoticesGrade();
}
function showNoticiesCourse(){
	student.getNoticesCourse();
}
function showNoticesGrade(){
	document.getElementById("notices").innerHTML = "";
	document.getElementById("notices").innerHTML = "<table class='table table-hover' id='tableNoticesGrade'><thead><tr><th>Noticia</th><th>Archivos</th></tr></thead></table>";
	for(var i in student.noticesGrade){
		$("#tableNoticesGrade").append("<tr><td><p class='lead'>"+student.noticesGrade[i]["contenidoNoticiaGrado"]+"</p></td><td><button type='button' class='btn btn-link' id="+i+" onclick= 'downlandFileNoticeGrade(this.id)'>"+student.noticesGrade[i]["archivoNoticiaGrado"]+"</button></td></tr>");
	}
}
function showNoticesCourse(){
	document.getElementById("notices").innerHTML = "";
	document.getElementById("notices").innerHTML = "<table class='table table-hover' id='tableNoticesGrade'><thead><tr><th>Noticia</th></tr></thead></table>";
	for(var i in student.noticesCourse){
		$("#tableNoticesGrade").append("<tr><td><p class='lead'>"+student.noticesCourse[i]["descripcionNoticia"]+"</p></td></tr>");
	}
}
function downlandFileNoticeGrade(idFileNoticeGRade){
	window.location = "server/php/files/"+student.noticesGrade[idFileNoticeGRade]["archivoNoticiaGrado"];
}
function showNoticesAll(){
	document.getElementById("notices").innerHTML="";
	$("#notices").append("<table class='table table-hover table-striped' id='tableNotices'><thead><tr><th>Noticias</th></tr></thead></table>");
	for(var i in student.notices){
		$("#tableNotices").append("<tr><td>"+student.notices[i]["descripcionNoticia"]+"</td></tr>");
	}	
}

function setDigitalWork(path){
	var allPath = "server/php/files/"+path;
	tarea.setDigitalWork(allPath);
}
function saveWork(){
	document.getElementById("file").innerHTML = "<button type='button' class='btn btn-link' onclick= 'downlandFile()'>"+tarea.allPath+"</button>";
}

function downlandFile(){
	document.location = tarea.allPath;
}

function getResources(){
	student.getResources();
}
function showResourceCourse(){
	document.getElementById("recursos").innerHTML = '';
	document.getElementById("recursos").innerHTML = "<table class='table table-hover' id='resourcesShareds'><thead><tr><th>Descripción</th><th>Recurso</th></tr></thead></table>";
	for(var i in student.resourcesCourse){
		$("#resourcesShareds").append("<tr><td>"+student.resourcesCourse[i]["descripcionRecurso"]+"</td><td><button type='button' class='btn btn-link' onclick='window.location = \"server/php/files/"+student.resourcesCourse[i]["archivoRecurso"]+"\"'>"+student.resourcesCourse[i]["archivoRecurso"]+"</button></td></tr>");
	}}