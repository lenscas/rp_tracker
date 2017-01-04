<div id="templates" style="display:none">
	<div class="grave col-md-4">
		<a href="" class="graveLocation">
			<h3 class="name"></h3>
			<img class="img-responsive corpse">
		</a>
	</div>
</div>
<div class="col-md-8" style="height:100%">
	<div class="row" style="height:10%">
		<div style="text-align:center">	
			<h1 id="userName"></h1>
		</div>
	</div>
	<div class="row" style="height:20%">
		<table class="table removeNotExist">
			<tbody id="userData">
				<tr>
					<td>total Level</td>
					<td id="totalLevel"></td>
				<tr>
					<td>total earned xp</td>
					<td id="totalXP"></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="row col-md-12" id="graveyard" style="height:70%;overflow:auto">
		<div class="row" id="row0"></div>
	</div>
</div>
<script>
	<?php 
		if(isset($userId)){
	?>
		$.ajax({
			url		:	"<?php echo base_url("index.php/api/users/".$userId)?>",
			method	:	"GET",
			dataType:	"json",
			success	:	function(data){
				$("#userName").html(data.profile.username)
				$("#totalLevel").html(data.profile.totalLevel)
				$("#totalXP").html(data.profile.totalXP)
				//get the template that we need
				var template=$("#templates").find(".grave")
				//needed so we can insert in the correct row
				var lastRow=0
				//needed so we can create rows when needed
				var times=0
				//the place where we show all the characters
				var graveyard = $("#graveyard")
				$.each(data.graveyard,function(key,value){
					console.log(value)
					if(times>=3){
						lastRow=lastRow+1
						times=0
						$(graveyard).append('<div class="row" id="row'+lastRow+'"></div>')
					}
					$(template).find(".name").empty().append(value.name)
					$(template).find(".graveLocation").attr("href","<?php echo base_url("index.php/character")?>/"+value.id)
					$(template).find(".corpse").attr("src","<?php echo base_url("")?>/"+value.basePicture)
					console.log($(template))
					$(template).clone().appendTo($("#row"+lastRow))
				})
			}
		})
	<?php
	} else {
	?>
		$("#userName").html("This account does not exist")
		$(".removeNotExist").remove()
	<?php
	}
	?>
</script>
