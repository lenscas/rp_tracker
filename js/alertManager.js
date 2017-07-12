GLOBAL_ALERT_MAN = {
	elem : $("#basicAlert"),
	show : function(message,type="danger"){
		console.log("wtf")
		console.log(this.elem)
		this.elem.html(message).removeClass("alert-danger alert-warning alert-success alert-primary").addClass("alert-"+type).show()
	}
}
$( document ).ready(function(){
	GLOBAL_ALERT_MAN.elem = $("#basicAlert")
})
