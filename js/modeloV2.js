/**
* Class Student
*/

function Student(){
	this.notices = [];
	this.noticesGrade;
	this.noticesCourse;
	this.resourcesCourse;
}
Student.prototype.getCourses = function(){
	var _this = this;
	$.ajax({
		url : "controlador.php",
		data : {"getCourses" : true},
		type : "GET",
		success : function (data){
			var cursos_object = $.parseJSON(data);
			showCoursesVista(cursos_object);
		}
	})
}
Student.prototype.getNoticesGrade = function(){
	var _this = this;
	$.ajax({
		url : "controlador.php",
		data : {"getNoticesStudentGrade" : true},
		type : "GET",
		success : function (data){
			_this.noticesGrade = $.parseJSON(data);
			showNoticesGrade();
		}
	})
}
Student.prototype.getNoticesCourse = function(){
	var _this = this;
	$.ajax({
		url : "controlador.php",
		data : {"getNoticesStudentCourse" : true},
		type : "GET",
		success : function (data){
			_this.noticesCourse = $.parseJSON(data);
			showNoticesCourse();
		}
	})
}
Student.prototype.getResources = function(){
	var _this = this;
	$.ajax({
		url : "controlador.php",
		data : {"getResourcesStudents" : true},
		type : "GET",
		success : function (data){
			_this.resourcesCourse = $.parseJSON(data);
			showResourceCourse();
		}
	})
}

/**
* Class Course
*/

function Course(){

}
Course.prototype.getWorks = function(){
	var _this = this;
	$.ajax({
		url : "controlador.php",
		data : {"getWorks" : true},
		type : "GET",
		success : function (data){
			var tareas = $.parseJSON(data);
			showCoursesWorksAll(tareas);
		}
	})
}

/**
* Clase Tarea
*/

function Tarea(){
	this.allPath;
}
Tarea.prototype.getDetailDigitalWork = function(){
	$.ajax({
		url : "controlador.php",
		type : "GET",
		data : {"getDetailDigitalWork":true},
		success : function(data){
			var detailDigitalWork = $.parseJSON(data);
			showDetailDigitalWork(detailDigitalWork);
		} 
	})
}
Tarea.prototype.setDigitalWork = function(allPath){
	this.allPath = allPath;
	$.ajax({
		url : "controlador.php",
		type : "GET",
		data : {"setDigitalWork":allPath},
		success : function(data){
			if(data == "0")
				alert("Error al subir la tarea");
			else
				saveWork();
		} 
	})	
}