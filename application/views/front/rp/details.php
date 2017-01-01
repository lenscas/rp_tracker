<style>
	.character{
		cursor:pointer;
	}
</style>
<div class="col-md-12" id="rpContainer">
	<div class="col-md-12">
		<div class="row">
			<h2 id="name"></h2>
		</div>
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
				$(charRows).append('<h4 id="code'+value.code+'" class="character"><a>'+value.name+'</a></h4>')
			})
		}
	}
	})
	$("#characters").on("click",".character",function(event){
		event.preventDefault()
		var id=$(this).attr("id")
		var cleanId=id.replace("code","")
		window.location="<?php echo base_url("index.php/rp/character/view")?>/"+cleanId
	})
</script>
