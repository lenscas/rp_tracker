<style>
.bg-danger-dataTables-fix {
	background-color: #f2dede !important; 
}
.bg-success-dataTables-fix {
	background-color: #dff0d8 !important;
}
</style>
<div id="templates" style="display:none">
	<table id="charRowTemplate">
		<tr class="charRow" data-charCode="">
			<td class="name"></td>
			<td class="turn"></td>
			<td class="actions" >
				<button class="btn btn-danger remove">Remove</button>
				<a class="lookAt btn btn-primary">Character</a>
			</td>
		</tr>
	</table>
	<div id="linkTemplates">
		<a class="btn btn-primary locTemplate">Location</a>
		<a class="btn btn-success manTemplate">Details</a>
	</div>
	<div class="battleTemplate">
		<div class="row">
			<div class="col-md-12">
				<h2>
					<div class="battleName pull-left"></div>
					<div class="pull-right buttonContainers"></div>
				</h2>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12 row">
				<table class="table table-hover battleTable">
					<thead>
						<tr>
							<th class="col-md-7">Name</th>
							<th class="col-md-1">Order</th>
							<th class="col-md-4">Actions</th>
						</tr>
					</thead>
					<tbody class="battleBody"></tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<div class="col-md-12" style="height:100%; overflow:auto" id="battleContainer">
</div>
<script>
	//let GLOBAL_IS_ADMIN=true
	
	$.ajax({
		url		:	"<?php echo base_url("index.php/api/battle/".$rpCode) ?>",
		dataType:	"json",
		method	:	"GET",
		success	:	function(data){
			let battleContainer	=	$("#battleContainer")
			let battleTemplate	=	$("#templates").find(".battleTemplate")
			let linkTemplates	=	$("#linkTemplates")
			let man				=	$(linkTemplates).find(".manTemplate")
			let loc				=	$(linkTemplates).find(".locTemplate")
			let battleTable		=	$(battleTemplate).find(".battleBody")
			let battleName		=	$(battleTemplate).find(".battleName")
			let charRowTemplate	=	$("#charRowTemplate").find(".charRow")
			if(! GLOBAL_IS_GM){
				$(charRowTemplate).find(".remove").remove()
			}
			$.each(data,function(key,value){
				$(battleName).html(value.name)
				$(man).attr("href","<?php echo base_url("index.php/rp/battle/manage")?>/"+value.id)
				$(battleTemplate).find(".buttonContainers").empty()
				$(man).clone().appendTo($(battleTemplate).find(".buttonContainers"))
				$(loc).attr("href",value.link)
				$(loc).clone().appendTo($(battleTemplate).find(".buttonContainers"))
				$(battleTable).empty()
				$.each(value.characters,function(charKey,charValue){
					$(charRowTemplate).find(".name").empty().html(charValue.name)
					$(charRowTemplate).find(".turn").empty().html(charValue.turnOrder)
					$(charRowTemplate).find(".lookAt").attr("href","<?php echo base_url("index.php/rp/character/view")?>/"+charValue.code)
					if(GLOBAL_IS_GM){
						console.log("test")
						$(charRowTemplate).find(".lookAt").data("charcode",charValue.code)
					}
					if(charValue.isTurn==1){
						$(charRowTemplate).addClass("bg-success-dataTables-fix")
					} else {
						$(charRowTemplate).removeClass("bg-success-dataTables-fix")
					}
					$(charRowTemplate).clone().appendTo(battleTable)
				})
				$(battleTemplate).clone().appendTo(battleContainer)
				
			})
			$(".battleTable").DataTable({})
			console.log(data)
		}
	})
	$("#battleContainer").on("click",".remove",function(){
		console.log($(this).data("charcode"))
	})
	
</script>

