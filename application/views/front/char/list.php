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
<div class="col-md-8" id="rpContainer" style="height:100%; overflow:auto">
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
						<!--<td>Health</td>
						<td>Agility</td>
						<td>Accuracy</td>
						<td>Strength</td>
						<td>Armour</td>
						<td>Magical Skill</td>
						<td>Magical Defence</td>-->
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
<script>
//first, get the configuration for this rp
CHAR_LIST={}
function fillInStatTable(){
$.ajax({
	url		:	"<?php echo base_url("index.php/ajax/character/list/".$rpCode) ?>",
	method	:	"GET",
	dataType:	"json",
	success	:	function(data){
		//first, fill in the CHAR_LIST.
		//loop over all the characters
		$.each(data.characters,function(key,value){
			//give them a place in the list
			CHAR_LIST[value.code]=value
			CHAR_LIST[value.code]["stats"]={}
			//loop over all the modifiers and check if they belong to this characters
			$.each(data.modifiers,function(key2,value2){
				if(value2.code==value.code){
					//it belongs to this character but it is a new stat it modifies thus we make a new entry for it.
					if(! CHAR_LIST[value.code][value2.statId]){
						CHAR_LIST[value.code]["stats"][value2.statId]=[]
					}
					//we put the stat in the correct place
					CHAR_LIST[value.code]["stats"][value2.statId].push(value2)
				}
				
			})
		})
		var statBody=$("#charStatsList")
		$.each(CHAR_LIST, function(charKey,charValue){
			$(statBody).append('<tr id="'+charValue.code+'"><td>'+charValue.name+'</td></tr>')
			var row	=	$("#"+charValue.code)
			$.each(charValue.stats,function(statKey,statValue){
				var amount=0;
				$.each(statValue,function(modKey,modValue){
					amount=amount+Number(modValue.value)
				})
				$(row).append("<td>"+amount+"</td>")
			})
		})
		$("#charStatsTable").DataTable()
	}
})
}
$.ajax({
	url		:	"<?php echo base_url("index.php/ajax/rp/getConfig/".$rpCode) ?>",
	method	:	"GET",
	dataType:	"json",
	success	:	function(data){
		var row=$("#statTable")
		$.each(data.data.statSheet,function(key,value){
			$(row).append("<td>"+value.name+"</td>")
		})
		fillInStatTable()
	}
})


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

</script>
