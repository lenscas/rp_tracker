<div class="col-md-6" id="rpContainer">
	<div class="col-md-8">
		<div class="row">
			<h2 id="name"></h2>
		</div>
	</div>
	<div class="col-md-4">
		<button class="btn btn-success pull-right" id="joinButton">Join</button>
	</div>
	<div class="col-md-8" id="description"></div>
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
			</table>
		</div>
		<div class="row" id="characters"></div>
		
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
		var charRows=$("#characters")
		if(data.characters){
			$.each(data.characters,function(key,value){
				$(charRows).append("<h4>"+value.name+"</h4>")
			})
		}
	}
	})

</script>
