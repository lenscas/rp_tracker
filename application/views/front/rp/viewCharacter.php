<div class="col-md-8" id="rpContainer" style="height:100%; overflow:auto">
	<div class="col-md-12" id="textContainer">
		<div class="row">
			<img id="picture" class="img-responsive col-md-6 thumbnail pull-right">	
			<h1 id="name" class="fillIn"></h1>
			<span>Age:&nbsp;<span class="fillIn" id="age"></span></span>
			<h4>Backstory</h4>
			<div id="backstory" class="fillIn"></div>
			<h4>Personality</h4>
			<div id="personality" class="fillIn"></div>
			<h4>Backstory</h4>
			<div id="backstory" class="fillIn"></div>
			<table class="table table-striped table-hover table-condensed">
				<tr>
					<td>Health:</td>
					<td id="health" class="fillIn"></td>
				</tr>
				<tr>
					<td>Armour</td>
					<td id="armour" class="fillIn"></td>
				</tr>
				<tr>
					<td>Agility</td>
					<td id="agility" class="fillIn"></td>
				</tr>
				<tr>
					<td>Strength</td>
					<td id="strength" class="fillIn"></td>
				</tr>
				<tr>
					<td>Accuracy</td>
					<td id="accuracy" class="fillIn"></td>
				</tr>
				<tr>
					<td>Magical Skill</td>
					<td id="magicalSkill" class="fillIn"></td>
				</tr>
				<tr>
					<td>Magical Defence</td>
					<td id="magicalDefence" class="fillIn"></td>
				</tr>
			</table>
		</div>
	</div>
</div>
<script>
function fillIn(data){
	var elements=$(".fillIn")
	$.each(elements,function(key,value){
		console.log(value)
		var id=$(value).attr("id")
		console.log(id)
		if(typeof data[id] !=="undefined"){
			console.log("wtf?")
			$(value).empty().html(data[id])
		}
		
	})
}
$.ajax({
	url		:	"<?php echo base_url("index.php/ajax/rp/getCharacter/".$charCode) ?>",
	method	:	"GET",
	dataType:	"json",
	success	:	function(data){
		if(data.success){
			fillIn(data.character)
		}
		if(data.character.appearancePicture){
			$("#picture").attr("src","<?php echo base_url() ?>"+data.character.appearancePicture)
		} else {
			$("#picture").hide()
		}

	}

})
</script>
