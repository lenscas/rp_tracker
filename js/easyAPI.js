function doCall(basicObject,seperatetor ="api/"){
	basicObject.url      = GLOBAL_BASE_URL+"index.php/" +seperatetor+basicObject.url
	basicObject.dataType = "json"
	if(! basicObject.statusCode[422]){
		basicObject.statusCode[422] = function(xhr){
			GLOBAL_ALERT_MAN.show(xhr.responseJSON.error)
		}
	}
	console.log(basicObject)
	$.ajax(basicObject)
}
