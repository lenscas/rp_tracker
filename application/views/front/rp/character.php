<div class="col-md-8">
	<div class="row">
		<div class="col-md-4">
			<button id="lastScreenButton" class="pageSwap btn btn-warning" disabled>
				Back
			</button>
		</div>
		<div class="col-md-4">
			<h3>Create your character</h3>
		</div>
		<div class="col-md-4">
			<button id="nextScreenButton" class="pageSwap btn btn-success pull-right">
				Next
			</button>
		</div>
	</div>
	<div class="row">
		<form method="POST" id="mainPost" action="<?php echo base_url("index.php/rp/character/create/".$rpCode) ?>" enctype="multipart/form-data">
			<div id="screen1">
				<div class="input-group">
					<span class="input-group-addon"> Name</span>
					<input type="text" name="name" id="name" class="form-control required" placeholder="Name">
				</div>
				<div class="input-group">
					<span class="input-group-addon">Age</span>
					<input type="text" name="age" id="age" class="form-control required" placeholder="Age">
				</div>
			</div>
			<div id="screen2" style="display:none">
				<h3>Appearance</h3>
				<textarea id="appearance" name="appearanceDescription"></textarea>
				<!--<input type="file", name="appearancePicture">-->
				<h3>Upload a picture</h3>
				<label class="btn btn-default btn-file">
					Browse <input type="file" name="appearancePicture" style="display: none;">
				</label>
			</div>
			<div id="screen3" style="display:none">
				<h3>Backstory</h3>
				<textarea id="backstory" name="backstory"></textarea>
				<h3>Personality</h3>
				<textarea id="personality" name="personality"></textarea>
			</div>
			<div id="screen4" style="display:none">
				<div class="input-group">
					<span class="input-group-addon">Health</span>
					<input type="text" id="health" name="health" class="stat form-control" placeholder="health">
				</div>
				<div class="input-group">
					<span class="input-group-addon">Armour</span>
					<input type="text" id="armour" name="armour" class="stat form-control" placeholder="armour">
				</div>
				<div class="input-group">
					<span class="input-group-addon">Agility</span>
					<input type="text" id=:agility name="agility" class="stat form-control" placeholder="agility">
				</div>
				<div class="input-group">
					<span class="input-group-addon">Accuracy</span>
					<input type="text" id="accuracy" name="accuracy" class="stat form-control" placeholder="Accuracy">
				</div>
				<div class="input-group">
					<span class="input-group-addon">Strength</span>
					<input type="text" id="strenght" name="strength" class="stat form-control" placeholder="Strength">
				</div>
				<div class="input-group">
					<span class="input-group-addon">Magical Skill</span>
					<input type="text" id="magicalSkill" name="magicalSkill" class="stat form-control" placeholder="Magical skill">
				</div>
				<div class="input-group">
					<span class="input-group-addon">Magical Defence</span>
					<input type="text" id="magicalDefence" name="magicalDefence" class="stat form-control" placeholder="Magical Defence">
				</div>
			</div>
			<button id="creatCharacter" class="btn btn-success pull-right">Create</button>
		</form>
	</div>
</div>
<script>
tinymce.init({
	selector: 'textarea'  // change this value according to your HTML
});
var ON_SCREEN=1
$(".pageSwap").on("click",function(event){
	//first, get which direction we need to go
	var id=$(this).attr("id")
	var direction=1
	if(id=="lastScreenButton"){
		direction=-1
	}
	if((ON_SCREEN<=1 && direction==-1) || (ON_SCREEN>=4 && direction==1)){
		//set it to disabled once again, as appently that didn't happen
		$(this).attr("disabled","disabled")
	} else {
		ON_SCREEN=ON_SCREEN+direction
		for(screen=1;screen<=4;screen++){
			$("#screen"+screen).css("display","none")
		}
		$("#screen"+ON_SCREEN).css("display","")
		if(ON_SCREEN==1){
			$("#lastScreenButton").prop("disabled","disabled")
			$("#nextScreenButton").prop("disabled","")
		} else if(ON_SCREEN==4){
			$("#lastScreenButton").prop("disabled","")
			$("#nextScreenButton").prop("disabled","disabled")
		} else {
			$("#lastScreenButton").prop("disabled","")
			$("#nextScreenButton").prop("disabled","")
		}
	}
})
$("#creatCharacter").on("click",function(event){
	event.preventDefault()
	var canPost=true
	var totalStatAmount=0;
	$('.required').each(function(index){
		if(! $(this).val()){
			canPost=false;
			console.log('on required')
			console.log($(this))
			return false
		}
	})
	
	if(canPost){
		$('.stat').each(function(index){
			var stat=Number($(this).val())
			if(stat){
				totalStatAmount=totalStatAmount+stat
			} else {
				console.log("on stat")
				console.log($(this))
				canPost=false
				return false
			}
		})
	}
	if(canPost){
		if(totalStatAmount!=25){
			console.log("too many points given")
			canPost=false
		}
	}
	if(canPost){
		console.log(tinyMCE.get("backstory").getContent())
		if( ( ! tinymce.get("backstory").getContent() ) || ( ! tinymce.get("personality").getContent() ) ) {
			console.log("on tinymce")
			canPost=false
		}
	}
	if(canPost){
		$("#mainPost").submit();
	}
})
//simple solution against browser remembering disabled button status
//TODO better solution
$("#lastScreenButton").prop("disabled","disabled")
$("#nextScreenButton").prop("disabled","")
</script>
