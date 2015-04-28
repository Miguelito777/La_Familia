/**
* Clase que administra los cursos 
* de los estudiantes
*/
function Cursos (){

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
			var notas_object = $.parseJSON(data);
			_this.notas_array = _this.convertirObject(notas_object);	
		}
	})
}