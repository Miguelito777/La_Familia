/**
*Clase que administra los conceptos generales del colegio la familia
*/

function Colegio(){
	this.noticeGrades = [];
	this.filesNotice;
}
Colegio.prototype.addGrade = function(idGrade){
	this.noticeGrades.push(idGrade);
}
Colegio.prototype.newNotice = function(){
	var idsGrades = JSON.stringify(this.noticeGrades);
	$.ajax({
		url : "controlador.php",
		type : "POST",
		data : {"newNoticeGrades": idsGrades},
		success : function(data){
			if (parseInt(data) == 1)
				window.location = "newNoticeGrade.html";
			else
				window.location = "registros.html"
		}
	})
}
Colegio.prototype.addFileNotice = function(nameFile){
	this.filesNotice = nameFile;
}
Colegio.prototype.postNotice = function(notice){
	var noticeO = {};
	noticeO.textContent = notice;
	noticeO.fileContent = this.filesNotice;
	noticeO = JSON.stringify(noticeO);
	$.ajax({
		url : "controlador.php",
		type : "POST",
		data : {"postNotice" : noticeO},
		success : function(data){
			if(parseInt(data) == 1){
				alert("Noticia publicada exitosamente");
				window.location = "sysAdmin.html";
			}
			else
				alert("Error al aignar la notici");

		}
	})
}
Colegio.prototype.convertirObject = function(object){
	var cursos_array = [];
	for(var i in object){
		var curso = [];
		var k = 0;
		for(var j in object[i]){
			curso[k] = object[i][j];
			k++;
		}
		cursos_array.push(curso);
	}
	return cursos_array;
}
Colegio.prototype.obtenerGrados = function(){
	_this = this;
	$.ajax({
			data: {"obtenerGrados" : true},
			url: "controlador.php",
			type: "POST",
			Cache: false,
			success: function (data){
				var grados_objctJavascript = $.parseJSON(data);
				showGrades(grados_objctJavascript);
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




/**
*	Clase administrador
*/

function Administrador(){

}
Administrador.prototype.mostrarNombre = function(){
	$.ajax({
			data: {"administrador" : true},
			url: "controlador.php",
			type: "POST",
			Cache: false,
			success: function (data){
				if (data == "")
					window.location = "registros.html";
				else
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

/**
* Clase que administra los cursos 
* de los estudiantes
*/
function Cursos (){
	this.tareas_array = [];
	this.students = [];
	this.pluckingWorkStudent;
	this.resources;
}
Cursos.prototype.savePluckingWorkStudent = function(nota, observacion){
	var asignation = {};
	asignation.nota = nota;
	asignation.observacion = observacion;
	asignation = JSON.stringify(asignation);
        var _this = this;
        $.ajax({
                url : "controlador.php",
                data : {"savePluckingWorkStudent" : asignation},
                type : "POST",
                success : function (data){
                	if (data == "0")
                		alert("Error al calificar la tarea");
                }
        })	
}
Cursos.prototype.pluckingWorkStudents = function(idStudent){
        var _this = this;
        $.ajax({
                url : "controlador.php",
                data : {"pluckingWorkIdStudent" : idStudent},
                type : "GET",
                success : function (data){
                        _this.pluckingWorkStudent = $.parseJSON(data);
                		showPluckingWorkStudent();
                }
        })	
}
Cursos.prototype.getWorksPluckingsStudents = function(){
        var _this = this;
        $.ajax({
                url : "controlador.php",
                data : {"getWorksPluckingsStudents" : true},
                type : "GET",
                success : function (data){
                        _this.students = $.parseJSON(data);
                		showWorksPluckingsStudents();
                }
        })		
}
Cursos.prototype.getWorksPluckings = function(){
        var _this = this;
        $.ajax({
                url : "controlador.php",
                data : {"getWorksPluckings" : true},
                type : "GET",
                success : function (data){
                        var cursos_object = $.parseJSON(data);
                        _this.tareas_array = _this.convertirObject(cursos_object);
                		showWorksPluckings();
                }
        })	
}
Cursos.prototype.convertirObject = function(object){
	var cursos_array = [];
	for(var i in object){
		var curso = [];
		var k = 0;
		for(var j in object[i]){
			curso[k] = object[i][j];
			k++;
		}
		cursos_array.push(curso);
	}
	return cursos_array;
}

Cursos.prototype.solicitar = function (){
	var _this = this;
	$.ajax({
		url : "controlador.php",
		data : {cursosasignados : true},
		type : "POST",
		success : function (data){
			var cursos_object = $.parseJSON(data);
			_this.cursos_array = _this.convertirObject(cursos_object);
		}
	})
}

Cursos.prototype.solicitarTareas = function (){
        var _this = this;
        $.ajax({
                url : "controlador.php",
                data : {"tareasAsignadas" : true},
                type : "POST",
                success : function (data){
                        var cursos_object = $.parseJSON(data);
                        _this.tareas_array = _this.convertirObject(cursos_object);
                		showTareasAsignadas()
                }
        })
}
Cursos.prototype.solicitarNotas = function (){
        var _this = this;
        $.ajax({
                url : "controlador.php",
                data : {notasAsignadas : true},
                type : "POST",
                success : function (data){
                        var cursos_object = $.parseJSON(data);
                        _this.notas_array = _this.convertirObject(cursos_object);
                }
        })
}

Cursos.prototype.solicitarNotasParciales = function (){
        var _this = this;
        $.ajax({
                url : "controlador.php",
                data : {notasAsignadasParciales : true},
                type : "POST",
                success : function (data){
                        var cursos_object = $.parseJSON(data);
                        _this.notas_array = _this.convertirObject(cursos_object);
                }
        })
}

Cursos.prototype.solicitarNotasBimestrales = function (){
        var _this = this;
        $.ajax({
                url : "controlador.php",
                data : {notasAsignadasBimestrales: true},
                type : "POST",
                success : function (data){
                    var cursos_object = $.parseJSON(data);
                    _this.notas_array = _this.convertirObject(cursos_object);
                }
        })
}
Cursos.prototype.addResource = function(descripcion,name){
    var _this = this;
	var resource = {};
	resource.descriptionResource = descripcion;
	resource.fileResource = name;
	resource = JSON.stringify(resource);
	$.ajax({
		url : "controlador.php",
		type : "GET",
		data : {"addResource" : resource},
		success : function(data){
			_this.resources = $.parseJSON(data);
			showRerourcesCourse();

		}
	});	
}
Cursos.prototype.getResources = function(){
        var _this = this;
        $.ajax({
                url : "controlador.php",
                data : {"getResources": true},
                type : "GET",
                success : function (data){
					_this.resources = $.parseJSON(data);
					showRerourcesCourse();
                }
        })	
}

/**
* Clase que administra las notas
* de las tareas de los estudiantes
*/

function notasTareas(){

}
notasTareas.prototype.convertirObject = function(object){
	var cursos_array = [];
	for(var i in object){
		var curso = [];
		var k = 0;
		for(var j in object[i]){
			curso[k] = object[i][j];
			k++;
		}
		cursos_array.push(curso);
	}
	return cursos_array;
}
notasTareas.prototype.solicitar = function(){
	_this = this;
	$.ajax({
		data : {notastareas : true},
		url : "controlador.php",
		type : "POST",
		success : function (data){
			//console.log(data);
			var notas_object = $.parseJSON(data);
			_this.notas_array = _this.convertirObject(notas_object);	
		}
	})
}


/**
* Clase que administra a los estudiantes 
*/
function Estudiantesss(){


}
Estudiantesss.prototype.convertirObject = function(object){
	var cursos_array = [];
	for(var i in object){
		var curso = [];
		var k = 0;
		for(var j in object[i]){
			curso[k] = object[i][j];
			k++;
		}
		cursos_array.push(curso);
	}
	return cursos_array;
}

Estudiantesss.prototype.obtenerCursosParciales = function(){
	var _this = this;
	$.ajax({
		url : "controlador.php",
		data : {cursosAsignadosParciales : true},
		type : "POST",
		success : function (data){
			var cursos_object = $.parseJSON(data);
			_this.cursosNotasParciales = _this.convertirObject(cursos_object);
		}
	})
}

Estudiantesss.prototype.obtenerCursosBimestrales = function(){
	var _this = this;
	$.ajax({
		url : "controlador.php",
		data : {cursosAsignadosBimestrales : true},
		type : "POST",
		success : function (data){
			var cursos_object = $.parseJSON(data);
			_this.cursosNotasBimestrales = _this.convertirObject(cursos_object);
		}
	})
}

Estudiantesss.prototype.obtenerNotasBimestrales = function(){
	var _this = this;
	$.ajax({
		url : "controlador.php",
		data : {notasFinalesBimestrales : true},
		type : "POST",
		success : function (data){
			console.log(data);
			//alert("Regrese con los datos");
			//console.log("Ya tube que haber mostrado los datos");
			//var cursos_object = $.parseJSON(data);
			//_this.cursosNotasBimestrales = _this.convertirObject(cursos_object);
			
		}
	})
}

Estudiantesss.prototype.notasFinales = function (){
	var _this = this;
	$.ajax({
			data : {"estudianteabuscarfinales":true},
			url : "controlador.php",
			type : "POST",
			Cache : false,
			success : function(data){
				var salones = $.parseJSON(data);
				var cursos_array = [];
				for(var i in salones){
					var salon = [];
					var k = 0;
					for(var j in salones[i]){
						salon[k] = salones[i][j];
						k++;
					}
					cursos_array.push(salon);
				}
				//_this.estudiantesarray = cursos_array;
				console.log(data);
			}
			//console.log(_this.estudiantesarray);
		}).done(function(data,textStatus,jqXHR){
			if(console && console.log){
				console.log("Salones solicitados correctamente EPGD");
			}
		}).fail(function(jqXHR,textStatus,errorThrown){
			if(console && console.log)
				console.log("Error al soliticar los salones");
		});
}


/**
* Clase Docente
*/

function Docente(){
	this.notices = [];
}
Docente.prototype.getNotices = function(idCurso){
	var _this = this;
	$.ajax({
		url : "controlador.php",
		type : "GET",
		data : {"getNotices" : idCurso},
		success : function(data){
			_this.notices = [];
			var newNoticeS = $.parseJSON(data);
			for(var i in newNoticeS){
				_this.notices.unshift(newNoticeS[i]);				
			}
			showNewNotice();
		}
	})
}
Docente.prototype.setNewNotice = function(idCurso, notice){
	var _this = this;
	var newNotice = {};
	newNotice.idCurso = idCurso;
	newNotice.notice = notice;
	var newNoticeJson = JSON.stringify(newNotice);
	$.ajax({
		url : "controlador.php",
		type : "POST",
		data : {"newNotice" : newNoticeJson},
		success : function(data){
			var newNoticeS = $.parseJSON(data);
			_this.notices.unshift(newNoticeS);
			showNewNotice();
		}
	})
}