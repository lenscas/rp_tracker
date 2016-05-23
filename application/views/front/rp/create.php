<div class="col-md-6">
	<div class="row">
		<div class="col-md-12"><h2>Create the rp</h2></div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<form>
				<div class="input-group">
					<span class="input-group-addon" id="nameAddon">Name</span>
					<input type="text" id="name" class="form-control" placeholder="RP name" aria-describedby="nameAddon">
				</div>
				<div class="input-group">
					<span class="input-group-addon">Starting max stats</span>
					<input type="text" id="startingStatAmount" class="form-control" placeholder="startingStatAmount">
				</div>
				<div class="input-group">
					<span class="input-group-addon">Starting max amount abilities</span>
					<input type="text" id="startingAbilityAmount" class="form-control" placeholder="Starting max amount abilities">
				</div>
				<h3>Description</h3>
				<textarea id="description"></textarea>
				<div class="input-group" style="width:100%">
					<div class="col-md-12">
						<div class="col-md-6">
							<div class="checkbox">
								<label>
									<input type="checkbox" id="isPrivate"> Set private
								</label>
							</div>
						</div>
						<div class="col-md-6" style="margin-top:5px">
							<button class="btn btn-success pull-right" id="create">Create</button>
						</div>
					</div>
				</div>
				
			</form>
		</div>
	</div>
</div>
<script>
	$("#create").on("click",function(event){
		event.preventDefault()
		var name		=	$("#name").val()
		var isPrivate	=	$("#isPrivate").is(':checked')
		var maxStat		=	$("#startingStatAmount").val()
		var maxAbilty	=	$("#startingAbilityAmount").val()
		var description	=	tinyMCE.get("description").getContent()
		$.ajax({
		url		:	"<?php echo base_url("/index.php/ajax/rp/create") ?>",
		dataType:	"json",
		method	:	"POST",
		data	:	{name			:	name,
				isPrivate				:	isPrivate,
				startingStatAmount		:	maxStat,
				startingAbilityAmount	:	maxAbilty,
				description				:	description
			},
		success	:	function(data){
				if(data.success){
					window.location="<?php echo base_url("/index.php/rp/success") ?>/"+data.playerId
				}
			}
		})
	})
tinymce.init({
	selector: 'textarea'  // change this value according to your HTML
});

</script>
