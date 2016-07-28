<style>
	.character{
		cursor:pointer;
	}
</style>
<div class="col-md-8" id="rpContainer">
	<div class="col-md-8">
		<div class="row">
			<h2 id="name"></h2>
		</div>
	</div>
	<div class="col-md-4">
		<?php
			if($joined){
				$joinStyle="display:none";
				$charStyle="";
			} else {
				$joinStyle="";
				$charStyle="display:none";
			}
		?>
		<button class="btn btn-success pull-right" id="joinButton" style="<?php echo $joinStyle ?>">Join</button>
		<button class="btn btn-success pull-right" style="<?php echo $charStyle ?>" id="characterButton">Create Character</button>
	</div>
	<div class="col-md-8" ><pre id="description"></pre></div>
	<div class="col-md-4">
		<div class="row" id="info">
			<table>
				<tr>
					<td>Creator:</td>
					<td id="creator"></td>
				</tr>
				<tr>
					<td>Abilities: </td>
					<td id="abilities"></td>
				</tr>
				<tr>
					<td>Stats: </td>
					<td id="stats"></td>
				</tr>
				<tr>
					<td>Sheet</td>
					<td id="sheet"></td>
				</tr>
			</table>
		</div>
		<div class="row">
			<h3><a href="<?php echo base_url("index.php/rp/character/list/".$rpCode )?>">All characters</a></h3>
			<div id="characters"></div>
		</div>
		
	</div>
</div>
<script>
	$.ajax({
	url		:	"<?php echo base_url("index.php/ajax/rp/details/".$rpCode) ?>",
	method	:	"GET",
	dataType:	"json",
	success	:	function(data){
		$("#name").html(data.name+":"+data.code);
		$("#description").html(data.description)
		$("#creator").html(data.username)
		$("#abilities").html(data.startingAbilityAmount)
		$("#stats").html(data.startingStatAmount)
		$("#sheet").html(data.statSheetName)
		var charRows=$("#characters")
		if(data.characters){
			$.each(data.characters,function(key,value){
				$(charRows).append('<h4 id="code'+value.code+'" class="character">'+value.name+'</h4>')
			})
		}
	}
	})
	$("#joinButton").on("click",function(event){
		var button=this
		event.preventDefault()
		$.ajax({
			url		:	"<?php echo base_url("index.php/ajax/rp/join/".$rpCode) ?>",
			method	:	"GET",
			dataType:	"json",
			success	:	function(data){
				console.log(data)
				if(data.success||data.error=="Already Joined"){
					$(button).hide()
					$("#characterButton").show()
				}
				
			}
		})
	})
	$("#characterButton").on("click",function(event){
		event.preventDefault()
		window.location="<?php echo base_url("index.php/rp/character/create/".$rpCode) ?>"
	})
	$("#characters").on("click",".character",function(event){
		event.preventDefault()
		var id=$(this).attr("id")
		var cleanId=id.replace("code","")
		window.location="<?php echo base_url("index.php/rp/character/view")?>/"+cleanId
	})
</script>
