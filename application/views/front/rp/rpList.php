<style>
	div.rpTemplate:hover{cursor:pointer}
</style>
<div id="template" style="display:none">
	<div class="row rpTemplate" style>
		<input class="code" type="hidden">
		<div class="col-md-12">
			<div class="col-md-2">
				 <div class="thumbnail">
					<img class="creatorAvatar img-responsive" style="width:100%">
					<div class="caption">
						<p class="creatorName"></p>
					</div>
				</div>
			</div>
			<div class="col-md-10">
				<div class="row">
					<p class="rpName"></p>
				</div>
				<div class="row description" style="max-height:50px; overflow:hidden"></div>
			</div>
		</div>
	</div>
</div>
<div class="col-md-8" id="rpContainer">
	
</div>
<script>
var ALL_RPS={}
function renderRPs(){
	var template=$("#template")
	console.log(template)
	$("#rpContainer").empty()
	console.log(ALL_RPS)
	$.each(ALL_RPS,function(key,value){
		console.log($(template).find(".creatorName"))
		$(template).find(".creatorAvatar").attr("src",value.avatar+"&s=200")
		$(template).find(".creatorName").html(value.username)
		$(template).find(".rpName").html(value.name+"#"+value.code)
		$(template).find(".code").val(value.code)
		$(template).find(".description").html(value.description)
		$(template).find(".rpTemplate").clone().appendTo($("#rpContainer"))
	})
}
$.ajax({
	url		:	"<?php echo base_url("index.php/api/rp") ?>",
	method	:	"GET",
	dataType:	"json",
	success	:	function(data){
		ALL_RPS=data
		renderRPs()
	}
})
$("#rpContainer").on("click",".rpTemplate",function(){
	window.location="<?php echo base_url("index.php/rp/details")?>/"+$(this).find(".code").val()
})
</script>
