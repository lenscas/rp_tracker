<div class="col-md-8">
	<div class="row">
		<div style="text-align:center">	
			<h1 id="characterName"></h1>
		</div>
	</div>
	<div class="row">
		<table class="table removeNotExist">
			<tbody id="userData">
				<tr>
					<td>total Level</td>
					<td id="currentLevel"></td>
				<tr>
					<td>total earned xp</td>
					<td id="gainedXP"></td>
				</tr>
			</tbody>
		</table>
		<div class="col-md-3"></div>
		<div class="col-md-6">
			<img class="img-responsive" id="characterImage">
		</div>
	</div>
</div>
<script>
	$.ajax({
		url		:	"<?php echo base_url("index.php/ajax/character/".$charId)?>",
		method	:	"GET",
		dataType:	"json",
		success	:	function(data){
			$("#characterName").html(data.characterName)
			$("#currentLevel").html(data.currentLevel)
			$("#gainedXP").html(data.gainedXP)
			console.log(data)
			$("#characterImage").attr("src","<?php echo base_url()?>/"+data.basePicture)
			//$(template).clone().appendTo($("#row"+lastRow))
		}
	})
</script>
