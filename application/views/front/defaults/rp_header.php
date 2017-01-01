<div class="col-md-8" style="height:100%">
	<div class="row" style="height:50px">
		<div class="col-md-12">
			<div class="row">
				<div class="col-md-12">
					<a class="btn btn-default" href="<?php echo base_url("index.php/rp/details/".$rpCode) ?>">Details</a>
					<?php
						if($hasJoined) {
					
					?>
							<a class="btn btn-default" href="<?php echo base_url("index.php/rp/character/create/" . $rpCode) ?>">new character</a>
					<?php
						} else {
					?>
							<button class="btn btn-default" id="joinButton">Join</button>
					<?php
						}
					?>
					<a class="btn btn-default" href="<?php echo base_url("index.php/rp/character/list/".$rpCode) ?>">Characters</a>
					<a class="btn btn-default" href="<?php echo base_url("index.php/rp/battle/list/".$rpCode) ?>">Battles</a>
					<?php 
						if($isGM){
					?>
							<a class="btn btn-default" href="<?php echo base_url("index.php/rp/battle/create/".$rpCode)?>">New Battle</a>
					<?php
						}
					?>
				</div>
			</div>
		</div>
	</div>
	<div class="row" style="height:calc(100% - 50px)">
	<script>
	GLOBAL_IS_GM = <?php echo (($isGM) ? "true":"false"),"\n" ?>
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
					window.location="<?php echo base_url("index.php/rp/character/create/".$rpCode) ?>"
				}
				
			}
		})
	})
	</script>
