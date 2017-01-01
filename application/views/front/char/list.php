<style>
.stat {cursor:pointer}
/*.form-control{ 
	max-width:100%

}*/
.modal-body {
	overflow-x: auto;
}
.modalStat .newModalStat{
	max-width:100px;
}
.bg-danger-dataTables-fix {
	background-color: #f2dede !important; 
}
.bg-success-dataTables-fix {
	background-color: #dff0d8 !important;
}
</style>
<table id="template" style="display:none">
	<tr class="stats">
		<td class="name"></td>
		<td class="health"></td>
		<td class="agility"></td>
		<td class="accuracy"></td>
		<td class="strength"></td>
		<td class="armour"></td>
		<td class="magicalSkill"></td>
		<td class="magicalDefence"></td>
	<tr>
	<tr class="abilities">
		<td class="name"></td>
		<td class="abilityName"></td>
		<td class="cooldown"></td>
		<td class="countDown"></td>
	</tr>
	
</table>
<div id="battleTemplate" style="display:none">
	<div class="battleRow">
		<div class="row">
			<div class="col-md-12">
				<div id="battleName">
					<h1 class="battleName"></h1>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<table class="table table-hover">
					<thead class="battleHead">
						<tr>
							<th>Name</th>
							<th>Health</th>
							<th>Order</th>
							<!--
								This will be added if the user is an GM
								<th>Actions <button type="button" class="btn btn-success pull-right">End turn</button></th>
							-->
						</tr>
					</thead>
					<tbody class="battleBody">
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<div class="col-md-12" id="rpContainer" style="height:100%; overflow:auto">
	<div class="row">
		<div class="col-md-12">
			<h1>Character Stats</h1>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<table class="table table-hover table-striped table-bordered" id="charStatsTable">
				<thead>
					<tr id="statTable">
						<td>Name</td>
					</tr>
				</thead>
				<tbody id="charStatsList">
				</tbody>
			</table>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<h1>Character Abilities</h1>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<table class="table table-hover table-striped table-bordered" id="charAbilityTable">
				<thead>
					<tr id="abilityUpperRow">
						<td>Character Name</td>
						<td>Ability Name</td>
						<td>Cooldown</td>
						<td>Countdown</td>
					</tr>
				</thead>
				<tbody id="charAbilityList">
				</tbody>
			</table>
		</div>
	</div>
</div>
<div id="modifierModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Modifier list</h4>
			</div>
			<div class="modal-body">
				<table id="" class="table table-sm" style="width:100%;"> 
					<thead>
						<tr id="modalFirstRow" style="width:100%">
							<th>Name</th>
							<th>Value</th>
							<th>Turns left</th>
						</tr>
					</thead>
					<tbody id="modalModifierBody">
					</tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<script>
/*$("#testTable").DataTable({
	"order":	[[2, "asc"]]

})*/
//these are used to store data that is needed everywhere. The CHAR_LIST is a list containing all the data from each character and all the modifiers on them. 
//The RP_CONFIG is a list with all the settings of this rp.
CHAR_LIST={}
RP_CONFIG={}

//this function is used to fill in the table containing the stats.
function fillInStatTable(){
	//first, grab all the modifiers that are present in this rp
	$.ajax({
		url		:	"<?php echo base_url("index.php/ajax/character/list/".$rpCode) ?>",
		method	:	"GET",
		dataType:	"json",
		success	:	function(data){
			//first, we need to fill in the CHAR_LIST.
			//loop over all the characters
			$.each(data.characters,function(key,value){
				//give them a place in the list
				CHAR_LIST[value.code]=value
				CHAR_LIST[value.code]["stats"]={}
			})
			//now loop over all the modifiers and insert them to the correct character
			$.each(data.modifiers,function(key2,value2){
				if(! CHAR_LIST[value2.code]['stats'][value2.statId]){
					CHAR_LIST[value2.code]["stats"][value2.statId]=[]
				}
				//we put the stat in the correct place
				CHAR_LIST[value2.code]["stats"][value2.statId].push(value2)
			})
			//now lets use this list to fill in the table
			var statBody=$("#charStatsList")
			$.each(CHAR_LIST, function(charKey,charValue){
				$(statBody).append('<tr id="'+charValue.code+'" class="character"><td>'+charValue.name+'</td></tr>')
				var row	=	$("#"+charValue.code)
				$.each(charValue.stats,function(statKey,statValue){
					var amount=0;
					$.each(statValue,function(modKey,modValue){
						amount=amount+Number(modValue.value)
					})
					$(row).append('<td class="stat" id="cell'+charKey+statKey+'" data-statid="'+statKey+'">'+amount+"</td>")
				})
			})
			$("#charStatsTable").DataTable()
		}
	})
}

//this function is used to update the total sum of modifiers that gets displayed in the stat table.
function updateStatCell(stats,cellId){
	var total	=	0;
	$.each(stats,function(key,value){
		total=total+Number(value.value)
	})
	$("#"+cellId).empty().html(total)
}

//this function is used to update the information that is displayed in the modal.
function updateModal(data,statId,character){
	//make it easier to get the table body as it will be needed a lot
	var table=$("#modalModifierBody")
	//empty it and get set the stat ID that is currenty looked at
	$(table).empty()
	$(table).data("stat-id",statId)
	//loop over all the stats and give them their rows
	$.each(data,function(key,value){
		$(table).append('<tr class="modalRow" data-char-code="'+character+'" id="modalRow'+key+'"><td><input value="'+value.name+'" class="modalStat form-control" name="name"></td><td><input value="'+value.value+'" class="modalStat form-control" name=value></td><td><input value="'+value.countDown+'" class="modalStat form-control" name="count-down"></td></tr>"')
		//if the current user is a GM then put the update and delete buttons in the table as well
		if(RP_CONFIG.isGM){
			$("#modalRow"+key).append('<td><button class="updateModifiers btn btn-success" data-mod-id="'+value.modifiersId+'">Update</button></td><td><button class="deleteModifier btn btn-danger" data-mod-id="'+value.modifiersId+'">Delete</button></td>')
		}
	})
	//if the user is not a gm disable the input fields
	if(! RP_CONFIG.isGM){
		$(table).find(".modalStat").prop("disabled",true)
	}else {
		//if the user is a GM add another row that can be used to create new modifiers
		$(table).append('<tr id="modalCreateNewMod"><td><input value="" name="name" class="newModalStat form-control"></td><td><input value="" name="value" class="newModalStat form-control"></td><td><input value="" name="count-down" class="newModalStat form-control"></td><td><button class="btn btn-success" data-stat-id="'+statId+'" data-char-code="'+character+'" id="createModfiers">Create</button></td></tr>"')
	}
}
//first, get the configuration for this rp
$.ajax({
	url		:	"<?php echo base_url("index.php/ajax/rp/getConfig/".$rpCode) ?>",
	method	:	"GET",
	dataType:	"json",
	success	:	function(data){
		RP_CONFIG=data.data
		if( data.data.isGM){
			//the user is a GM, make the table that will display all the modifiers of a stat able to show the edit and delete buttons
			$("#modalFirstRow").append('<th col-span="2">Actions</th>')
		}
		//fill the names of the stat in.
		var row=$("#statTable")
		$.each(data.data.statSheet,function(key,value){
			$(row).append("<td>"+value.name+"</td>")
		})
		//run the logic to fill in the stat table
		fillInStatTable()
		
	}
})
//this will get all the abilities and fills in the ability table
$.ajax({
	url		:	"<?php echo base_url("index.php/ajax/rp/abilityList/".$rpCode)?>",
	method	:	"GET",
	dataType:	"json",
	success	:	function(data){
		var list	=	$("#charAbilityList")
		var template=	$("#template").find(".abilities")
		$.each(data,function(key,value){
			$.each(value,function(key2,value2){
				$(template).find("."+key2).empty().html(value2)
			})
			$(template).clone().appendTo(list)
		})
		$("#charAbilityTable").DataTable()
	}
})
//This will make the modal appear when someone clicks on a stat. The modal will display all the modifiers that effect this stat.
	$("#charStatsList").on("click",".stat",function(event){
		event.preventDefault()
		var stat		=	$(this).data("statid")
		var character	=	$(this).parent(".character").attr("id")
	
		updateModal(CHAR_LIST[character]["stats"][stat],stat,character)
		$("#modifierModal").modal()
	})
//This will update a modifier.
$("#modalModifierBody").on("click",".updateModifiers",function(event){
	event.preventDefault()
	//get all the data that we need
	var	tr		=	$(this).parents("tr.modalRow")
	var	modId	=	$(this).data("mod-id")
	var	name	=	$(tr).find("[name='name']").val()
	var	value	=	$(tr).find("[name='value']").val()
	var timer	=	$(tr).find("[name='count-down']").val()
	var test	=	{name	:	name,value	:	value,countDown	:	timer}
	$.ajax({
		url		:	"<?php echo base_url("index.php/ajax/modifiers/update")?>/"+modId,
		method	:	"POST",
		data	:	test,
		dataType:	"json",
		success	:	function(data){
			//database is updated, now lets update our local object as well.
			if(data.success){
				var charCode	=	$(tr).data("char-code")
				var statId		=	$(tr).parents("tbody").data("stat-id")
				var statKey		=	$(tr).attr("id").replace("modalRow","")
				CHAR_LIST[charCode]["stats"][statId][statKey]['name']		=name
				CHAR_LIST[charCode]["stats"][statId][statKey]['value']		=value
				CHAR_LIST[charCode]["stats"][statId][statKey]['countDown']	=timer
				//update the correct cell. We don't update the modal as it should be the same already
				updateStatCell(CHAR_LIST[charCode]["stats"][statId],"cell"+charCode+statId)
			}
			
		}
	})
})
//this creates a new modifier
$("#modalModifierBody").on("click","#createModfiers",function(event){
	event.preventDefault()
	//grab the stuff that we need
	var	tr		=$(this).parents("tr#modalCreateNewMod")
	var	charCode=$(this).data("char-code")
	data={name		:	$(tr).find("[name='name']").val(),
		value		:	$(tr).find("[name='value']").val(),
		countDown	:	$(tr).find("[name='count-down']").val(),
		statId		:	$(this).data("stat-id")
	}
	$.ajax({
		url		:	"<?php echo base_url("index.php/ajax/modifiers/create")?>/"+charCode,
		method	:	"POST",
		data	:	data,
		dataType:	"json",
		success	:	function(returnData){
			//database is updated, lets update our local object
			if(returnData.success){
				data.code	=	charCode
				data.modifiersId		=	returnData.id
				CHAR_LIST[charCode]["stats"][data["statId"]].push(data)
				//update both the modal and the correct cell. We update the modal as a new input for the creation of a modifier needs to appear and this modifier needs to get an update button
				updateStatCell(CHAR_LIST[charCode]['stats'][data.statId],"cell"+charCode+data.statId)
				updateModal(CHAR_LIST[charCode]['stats'][data.statId],data.statId,charCode)
			}
		}
	})
})
$("#modalModifierBody").on("click",".deleteModifier",function(event){
	event.preventDefault()
	//some stuff we need later to update the totals
	var	charCode	=	$(this).parents("tr.modalRow").data("char-code");
	var	statId		=	$(this).parents("tbody").data("stat-id")
	var	modId		=	$(this).data("mod-id")
	var	modKey		=	$(this).parents("tr.modalRow").attr("id").replace("modalRow","")
	
	$.ajax({
		url		:	"<?php echo base_url("index.php/ajax/modifiers/delete")?>/"+modId,
		method	:	"GET",
		dataType:	"json",
		success	:	function(data){
			if(data.success){
				//it is updated on the database, lets update it locally
				CHAR_LIST[charCode]['stats'][statId].splice(modKey,1)
				updateStatCell(CHAR_LIST[charCode]['stats'][statId],"cell"+charCode+statId)
				updateModal(CHAR_LIST[charCode]['stats'][statId],statId,charCode)
			}
		}
	})
})

</script>
