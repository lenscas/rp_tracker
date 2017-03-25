<style>
	.canEdit {
		cursor : pointer; 
	}
</style>
<div id="templates" style="display:none">
	<div class="abilityTemplate canEdit">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="abilityName"></h4> 
			</div>
			<div class="panel-body">
				<div class="abilityDescription"></div>
			</div>
		</div>
	</div>
	<div class="abilityEditTemplate">
		<div class="form-group row">
			<label for="name" class="col-sm-2 col-form-label">Name</label>
			<div class="col-md-10">
				<input type="text" name="ability" data-what="name" class="form-control abilityName">
				<small id="nameHelp" class="form-text text-muted statDesc">This is the name of the ability</small>
			</div>
		</div>
		<div class="form-group row">
			<label for="name" class="col-sm-2 col-form-label">Cooldown</label>
			<div class="col-md-10">
				<input type="number" name="ability" data-what="cooldown" class="form-control abilityCooldown" placeholder="ability cooldown" value="0" min="0">
				<small id="nameHelp" class="form-text text-muted">
					This is how many turns you need to wait after using the ability before you can use it again
				</small>
			</div>
		</div>
		<div class="form-group row">
			<label for="name" class="col-sm-2 col-form-label">Description</label>
			<div class="col-md-10">
				<textarea class="abilityDescription" data-what="description" name="ability"></textarea>
				<small id="nameHelp" class="form-text text-muted statDesc">
					This is a description of what the ability does. It is recomended to both have a lore version and technical explanation
				</small>
			</div>
		</div>
	</div>
</div>
<div class="col-md-12" id="rpContainer" style="height:100%; overflow:auto">
	<div class="col-md-12" id="textContainer">
		<div class="row">
			<div class="col-md-6">
				<div class="row">
					<div class="col-md-12">
						<h1 id="name" class="fillIn canEdit"></h1>
						<table data-what="stats" class="canEdit table table-striped table-hover table-condensed pull-left" id="statTable">
							<tr>
								<td>Age:</td>
								<td id="age" class='fillIn'></td>
							</tr>
						</table>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div id="abilityContainer" data-what="abilities" class="canEdit panel-group"></div>
					</div>
				</div>
			</div>
			<div class="col-md-6" id="appearance">
				<img id="picture" data-what="appearance" class="canEdit img-responsive thumbnail">	
			</div>
		</div>
		<div class="row">
			<h3 class="canEdit"   data-what="backstory"   >Backstory</h3>
			<div id="backstory"   data-what="backstory"   class="canEdit fillIn"></div>
			<h3 class="canEdit"   data-what="personality" >Personality</h3>
			<div id="personality" data-what="personality" class="canEdit fillIn"></div>
			<h3 class="canEdit notes"     data-what="extraNotes"  >Extra notes</h3>
			<div id="notes"       data-what="extraNotes"  class="canEdit notes fillIn" style="margin-bottom:15px;"></div> 
		</div>
	</div>
	<div id="editModal" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Modal Header</h4>
				</div>
				<form id="patchChar">
					<div class="modal-body" id="modalBody">
						<div id="statsEdit" class="editCharDiv" style="display:none">
							<table id="editStatTable" class="table table-striped table-hover table-condensed">
								<tr>
									<th>Name</th>
									<th>New</th>
									<th>Old</th>
								<tr>
									<td>Age</td>
									<td><input type="number" name="age" min="0"></td>
									<td><input type="number" disabled min="0"></td>
								</tr>
							</table>
						</div>
						<div id="abilitiesEdit" class="editCharDiv" style="display:none">
						</div>
						<div id="backstoryEdit" class="editCharDiv" style="display:none">
							<h2>Backstory</h2>
							<textarea name="backstory" data-fillin="backstory" class="fillIn"></textarea>
						</div>
						<div id="personalityEdit" class="editCharDiv" style="display:none">
							<h2>Personality</h2>
							<textarea name="personality" data-fillin="personality" class="fillIn"></textarea>
						</div>
						<div id="extraNotesEdit" class="editCharDiv" style="display:none">
							<h2>Extra notes</h2>
							<textarea name="extraNotes" data-fillin="extraNotes" class="fillIn"></textarea>
						</div>
						<div id="appearanceEdit" class="editCharDiv" style="display:none">
							<input type="url" id="imageEditURL" name="appearancePicture">
							<textarea  name="appearanceDescription" data-fillin="appearanceDescription" class="fillIn">
							</textarea>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
						<button class="btn btn-success" >Edit</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<script>
function fillIn(data){
	var elements=$(".fillIn")
	$.each(elements,function(key,value){
		let id=$(value).attr("id")
		if(!id){
			id = $(value).data("fillin")
		}
		if(typeof data[id] !=="undefined"){
			$(value).empty().html(data[id])
		}
		
	})
}
$.ajax({
	url		:	"<?php echo base_url("index.php/api/characters/".$rpCode."/".$charCode) ?>",
	method	:	"GET",
	dataType:	"json",
	success	:	function(data){
		if(data.success){
			fillIn(data.character)
			let statTable=$("#statTable")
			let editStatTable = $("#editStatTable")
			editStatTable.find("input").val(data.character.age)
			$.each(data.character.stats,function(key,value){
				$(statTable).append("<tr><td>"+value.name+"</td><td>"+value.value+"</td></tr>")
				let nameTD = $("<td>"+value.name+"</td>")
				let newVal = $('<td><input name="stats" data-modid="'+value.id+'" value="'+value.value+'" type="number" min="0"></td>')
				let oldVal = $('<td><input value="'+value.value+'" type="number" min="0" disabled></td>')
				let newRow = $("<tr></tr>")
				newRow.append(nameTD).append(newVal).append(oldVal)
				editStatTable.append(newRow)
			})
			var template = $("#templates").find(".abilityTemplate")
			var abilityContainer = $("#abilityContainer")
			let editAbilityContainer = $("#abilitiesEdit")
			let AbilityEditTemplate  = $("#templates").find(".abilityEditTemplate")
			$.each(data.abilities,function(key,value){
				$(template).find(".abilityName").empty().append(value.name+" ("+value.cooldown+")")
				$(template).find(".abilityDescription").empty().html(value.description)
				$(template).clone().appendTo(abilityContainer)
				AbilityEditTemplate.find(".abilityName").val(value.name).data("id",value.id)
				AbilityEditTemplate.find(".abilityCooldown").val(value.cooldown).data("id",value.id)
				AbilityEditTemplate.find(".abilityDescription").val(value.description).data("id",value.id)
				//by default clone() does not copy data fields, unless you give true as a parameter
				AbilityEditTemplate.clone(true).appendTo(editAbilityContainer)
			})
			$("#imageEditURL").val(data.character.appearancePicture)
			
		}
		if(data.character.appearancePicture){
			let imgSRC =""
			if(data.character.isLocalImage==1){
				imgSRC = "<?php echo base_url() ?>"+data.character.appearancePicture
			} else {
				imgSRC=data.character.appearancePicture
			}
			$("#picture").attr("src",imgSRC)
		} else {
			$("#appearance").empty().html("<h3>Appearance</h3><div>"+data.character.appearanceDescription+"</div>")
		}
		$("textarea").wysibb(EDITOR_DEFAULT_CONFIG);
	}
})
$("body").on("click",".canEdit",function(){
	let jqEl = $(this)
	let what = jqEl.data("what")
	$("#editModal").find(".editCharDiv").hide()
	$("#"+what+"Edit").show()
	$("#editModal").modal()
})
$("#patchChar").on("submit",function(event){
	event.preventDefault()
	let modalBody = $("#modalBody")
	let whatToUpdate = modalBody.find(".editCharDiv:visible").attr("id").replace("edit","")
	let updateData   = {}
	modalBody.find("input:visible").each(function(){
		let jqEl  = $(this)
		if(jqEl.is(':disabled')){
			return;
		}
		let name  = jqEl.attr("name")
		let value = jqEl.val()
		if(name=="stats"){
			if(! updateData["stats"]){
				updateData["stats"]={}
			} 
			updateData["stats"][jqEl.data("modid")]=value
		}else if (name=="ability"){
			if(! updateData["abilities"]){
				updateData["abilities"]={}
			}
			let id   = jqEl.data('id')
			let what = jqEl.data('what')
			if(! updateData["abilities"][id]){
				updateData["abilities"][id]={}
			}
			updateData["abilities"][id][what]=value
		}else {
			updateData[name] = value
		}
		
	})
	modalBody.find(".wysibb:visible").find("textarea").each(function(){
		let jqEl  = $(this)
		if(jqEl.is(':disabled')){
			return;
		}
		let name  = jqEl.attr("name")
		let value = jqEl.bbcode()
		if (name=="ability"){
			if(! updateData["abilities"]){
				updateData["abilities"]={}
			}
			let id   = jqEl.data('id')
			let what = jqEl.data('what')
			if(! updateData["abilities"][id]){
				updateData["abilities"][id]={}
			}
			updateData["abilities"][id][what]=value
		}else {
			updateData[name] = value
		}
	})
	$.ajax({
		url		:	"<?php echo base_url("index.php/api/rp/".$rpCode."/characters/".$charCode)?>",
		dataType:	"json",
		method	:	"PATCH",
		data	:	updateData,
		success	:	function(data){
			if(data.success){
				window.location.reload();
			}
		}
	})
	console.log(updateData)
})
</script>
