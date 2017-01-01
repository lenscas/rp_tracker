<div id="templates" style="display:none">
	<div class="abilityTemplate">
		<h3 class="abilityName"></h3>
		<div class="abilityDescription"></div>
	</div>
</div>
<div class="col-md-12" id="rpContainer" style="height:100%; overflow:auto">
	<div class="col-md-12" id="textContainer">
		<div class="row">
			
			<div class="col-md-6">
				<h1 id="name" class="fillIn"></h1>
				<table class="table table-striped table-hover table-condensed pull-left" id="statTable">
					<tr>
						<td>Age:</td>
						<td id="age" class='fillIn'></td>
					</tr>
				</table>
				<div id="abilityContainer"></div>
			</div>
			<div class="col-md-6" id="appearance">
				<img id="picture" class="img-responsive thumbnail">	
			</div>
		</div>
		<div class="row">
			<h3>Backstory</h3>
			<div id="backstory" class="fillIn"></div>
			<h3>Personality</h3>
			<div id="personality" class="fillIn"></div>
			<h3 class="notes">Extra notes</h3>
			<div id="notes" class="notes fillIn" style="margin-bottom:15px;"></div> 
		</div>
	</div>
</div>
<script>
function fillIn(data){
	var elements=$(".fillIn")
	$.each(elements,function(key,value){
		var id=$(value).attr("id")
		if(typeof data[id] !=="undefined"){
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
			var statTable=$("#statTable")
			$.each(data.character.stats,function(key,value){
				$(statTable).append("<tr><td>"+value.name+"</td><td>"+value.value+"</td></tr>")
			})
			var template = $("#templates").find(".abilityTemplate")
			console.log(template)
			var abilityContainer = $("#abilityContainer")
			$.each(data.abilities,function(key,value){
				$(template).find(".abilityName").empty().append(value.name+" ("+value.cooldown+")")
				$(template).find(".abilityDescription").empty().html(value.description)
				$(template).clone().appendTo(abilityContainer)
			})
		}
		if(data.character.appearancePicture){
			$("#picture").attr("src","<?php echo base_url() ?>"+data.character.appearancePicture)
		} else {
			console.log(data.character)
			$("#appearance").empty().html("<h3>Appearance</h3><div>"+data.character.appearanceDescription+"</div>")
		}

	}

})
</script>
