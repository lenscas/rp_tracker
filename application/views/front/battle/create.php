<div id="templates" style="display:none">
	<div class="form-group row characters">
		<label class="col-sm-2 col-form-label" for="BattleLink">Characters</label>
		<div class="col-md-8">
			<select type="text" class="form-control selectChar">
				<option value="">Emtpy</option>
			</select>
		</div>
		<div class="col-md-2">
			<button class="btn btn-danger removeChar" type="button">Remove</button>
		</div>
	</div>
</div>
<div class="col-md-8" style="height:100%; overflow:auto">
	<h1>Create Battle</h1>
	<form>
		<div class="form-group row">
			<label class="col-sm-2 col-form-label" for="BattleName">Name</label>
			<div class="col-md-10">
				<input type="text" class="form-control" id="battleName" aria-describedby="nameHelp" placeholder="Battle Name">
				<small id="nameHelp" class="form-text text-muted">This is the name for the battle.</small>
			</div>
		</div>
		<div class="form-group row">
			<label class="col-sm-2 col-form-label" for="BattleLink">Link</label>
			<div class="col-md-10">
				<input type="text" class="form-control" id="battleLink" aria-describedby="linkHelp" placeholder="Battle Link">
				<small id="linkHelp" class="form-text text-muted">This is the site that will be used to write the battle. You can leave this blank if you want.</small>
			</div>
		</div>
		<h2>Characters</h2>
		<div id="charList"></div>
		<div class="row">
			<div class="col-md-2 col-md-offset-10">
				<button type="button" class="btn btn-success" id="create">Create</button>
			</div>
		</div>
	</form>
</div>

<script>
AMOUNT_OF_SELECTS=0;
function addToCharList(){
	$("#templates").find(".characters").clone().appendTo("#charList")
	AMOUNT_OF_SELECTS=AMOUNT_OF_SELECTS+1
}
$.ajax({
	url		:	"<?php echo base_url("index.php/ajax/character/list/".$rpCode)?>",
	method	:	"GET",
	dataType:	"json",
	success	:	function(data){
		var template	=	$("#templates").find(".characters")
		$.each(data.characters,function(key,value){
			$(template).find(".selectChar").append('<option value="'+value.code+'">'+value.name+'</option>')
		})
		addToCharList()
	}
})
$("#charList").on("change", ".selectChar",function(event){
	var makeNew	=	true
	$("#charList").find(".selectChar").each(function(){
		if(!$(this).val()){
			makeNew=false;
			return false;
		}
	})
	if(makeNew){
		addToCharList()
	}
})
$("#charList").on("click",".removeChar",function(event){
	event.preventDefault()
	var row	=	$(this).parents(".characters")
	if($(row).find(".selectChar").val() && AMOUNT_OF_SELECTS>0){
		$(row).remove()
		AMOUNT_OF_SELECTS=AMOUNT_OF_SELECTS-1
	}
})
$("#create").on("click",function(event){
	event.preventDefault()
	//gather all the data
	var data={}
	data.name	=	$("#battleName").val()
	data.link	=	$("#battleLink").val()
	data.rpCode	=	"<?php echo $rpCode ?>"
	//add all the characters
	data.characters=[]
	$("#charList").find(".selectChar").each(function(){
		var charCode	=	$(this).val()
		if(charCode){
			data.characters.push(charCode)
		}
	})
	$.ajax({
		url		:	"<?php echo base_url("index.php/ajax/battle/create")?>",
		method	:	"post",
		dataType:	"json",
		data	:	data,
		success	:	function(data){console.log(data)}
	})
})
</script>
