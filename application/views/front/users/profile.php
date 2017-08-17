<style>
	.small {
		max-height:50px;
		overflow-y:hidden;
		cursor : pointer;
	}
</style>
	<div class="row" style="height:10%">
		<div style="text-align:center">	
			<h1 id="userName"></h1>
		</div>
	</div>
	<div class="row" style="height:90%">
		<div id="removeNotExist">
			<div class="col-md-12">
				<div class="row">
					<h2>Made roleplays</h2>
					<table class="table" id="madeRPs">
						<thead>
							<tr>
								<th>Name</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody id="madeRPsTbody">
						</tbody>
					</table>
				</div>
				<div class="row">
					<h2>Joined roleplays</h2>
					<table class="table" id="joinedRPs">
						<thead>
							<tr>
								<th>Name</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody id="joinedRPsTbody">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
<script>
	$( document ).ready(function(){
		<?php
			if(isset($userId)){
		?>
				doCall({
					url    : "users/<?php echo $userId ?>",
					method : "GET",
					statusCode: {
						200 : function(data){
							$("#userName").html(data.userData.username)
							let madeRpsBody = $("#madeRPsTbody")
							$.each(data.madeRPs,function(key,value){
								let row = $("<tr></tr>")
								row.append('<td><a href="<?php echo base_url("index.php/rp/details")?>/'+this.code+ '">'+this.name+"</a></td>")
								row.append('<td> <div class="desc small">'+this.description+"</td>")
								madeRpsBody.append(row)
							})
							$("#madeRPs").DataTable()
							let joinedRpsBody = $("#joinedRPsTbody")
							$.each(data.joinedRPs,function(key,value){
								let row = $("<tr></tr>")
								row.append('<td><a href="<?php echo base_url("index.php/rp/details")?>/'+this.code+ '">'+this.name+"</a></td>")
								row.append('<td><div class="desc small">'+this.description+"</div></td>")
								joinedRpsBody.append(row)
							})
							$("#joinedRPs").DataTable()
						},
						400 : function(){
							GLOBAL_ALERT_MAN.show("This accound does not exist")
						}
					}
				})
		<?php
		} else {
		?>
			GLOBAL_ALERT_MAN.show("This account does not exist")
			//$("#userName").html("This account does not exist")
			$("#removeNotExist").remove()
		<?php
		}
		?>
		$(".table").on("click",".desc",function(event){
			event.preventDefault()
			let jqElement= $(this)
			if(jqElement.hasClass("small")){
				jqElement.removeClass("small").addClass("big")
			} else {
				jqElement.removeClass("big").addClass("small")
			}
			//console.log("test")
		
		})
	})
</script>
