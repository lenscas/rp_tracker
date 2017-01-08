<div id="templates" style="display:none">
	<div class="statTemplate">
		<div class="form-group row">
			<label for="name" class="col-sm-2 col-form-label statName"></label>
			<div class="col-md-10">
				<input type="number" min="0" value="0" class="form-control statInput">
				<small id="nameHelp" class="form-text text-muted statDesc">This will be replaced by the description of the stat</small>
			</div>
		</div>
	</div>
	<div class="ability">
		<div class="panel panel-info">
			<div class="panel-heading abilityCount">Ability 1</div>
			<div class="panel-body">
				<div class="form-group row">
						<label for="name" class="col-sm-2 col-form-label">Name</label>
					<div class="col-md-10">
						<input type="text" class="form-control abilityName abilities" placeholder="ability name">
						<small id="nameHelp" class="form-text text-muted statDesc">This is the name of the ability</small>
					</div>
				</div>
				<div class="form-group row">
					<label for="name" class="col-sm-2 col-form-label">Cooldown</label>
					<div class="col-md-10">
						<input type="number" class="form-control abilityCooldown abilities" placeholder="ability cooldown" value="0" min="0">
						<small id="nameHelp" class="form-text text-muted">
							This is how many turns you need to wait after using the ability before you can use it again
						</small>
					</div>
				</div>
				<div class="form-group row">
					<label for="name" class="col-sm-2 col-form-label">Description</label>
					<div class="col-md-10">
						<textarea class="abilityDescription"></textarea>
						<small id="nameHelp" class="form-text text-muted statDesc">
							This is a description of what the ability does. It is recomended to both have a lore version and technical explanation
						</small>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="col-md-12" style="height:100%; overflow:auto">
	<div class="row" id="errors" style="display:none">
		<div class="alert alert-danger" id="errorMessage"></div>
	</div>
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
				<div class="form-group row">
					<label for="name" class="col-sm-2 col-form-label">Name</label>
					<div class="col-md-10">
						<input type="text" required class="form-control required" id="name" name="name" placeholder="character name">
						<small id="nameHelp" class="form-text text-muted">This is the name of your character</small>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-sm-2 col-form-label">Age</label>
					<div class="col-md-10">
						<input type="text" required class="form-control required" id="age" name="age" placeholder="character age">
						<small id="nameHelp" class="form-text text-muted">This is the age of your character</small>
					</div>
				</div>
			</div>
			<div id="screen2" style="display:none">
				<div class="form-group row">
					<label class="col-sm-2 col-form-label">Appearance</label>
					<div class="col-md-10">
						<textarea id="appearance" name="appearanceDescription" class="form-control"></textarea>
						<small id="nameHelp" class="form-text text-muted">
							This is the description of your character. Alternativly upload or link a picture below
						</small>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-sm-2">Picture</label>
					<div class="col-md-10">
						<input type="text" class="form-control" id="age" name="appearancePictureUrl" placeholder="appearance picture">
						<small id="nameHelp" class="form-text text-muted">An url linking to a picture for your character</small>
						<div class="row">
							<div class="col-md-2 col-md-offset-8">
								<label class="pull-right">Picture Upload</label>
							</div>
							<div class="col-md-2">
								<label class="btn btn-default btn-file pull-right">
									Browse <input type="file" name="appearancePicture" style="display: none;">
								</label>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="screen3" style="display:none">
				<div class="form-group row">
					<label class="col-sm-2 col-form-label">Backstory</label>
					<div class="col-md-10">
						<textarea id="backstory" name="backstory" class="form-control"></textarea>
						<small id="backstoryHelp" class="form-text text-muted">This is your characters backstory</small>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-sm-2 col-form-label">Personality</label>
					<div class="col-md-10">
						<textarea id="personality" name="personality" class="form-control"></textarea>
						<small id="personalityHelp" class="form-text text-muted">This is your characters personality</small>
					</div>
				</div>
				<!--<h3>Backstory</h3>
				<textarea id="backstory" name="backstory"></textarea>
				<h3>Personality</h3>
				<textarea id="personality" name="personality"></textarea> -->
			</div>
			<div id="screen4" style="display:none">
				<div id="statContainer" class="panel-group"></div>
			</div>
			<div id="screen5" style="display:none">
				<h3>Abilities</h3>
				<div id="abilitiesContainer"></div>
			</div>
			<div class="input-group" style="margin-top:5px; width:100%">
				<div class="col-md-6"></div>
				<div class="col-md-6">
					<button id="creatCharacter" class="btn btn-success pull-right">Create</button>
				</div>
			</div>
			<div class="input-group" style="width:100%">
				<h3>Aditional notes</h3>
				<textarea id="notes" name="notes"></textarea>
			</div>
		</form>
	</div>
</div>
<script>

var ON_SCREEN=1
var CONFIG={}
function showError(error){
	$("#errors").show()
	$("#errorMessage").empty().html(error)
}
$.ajax({
	url		:	"<?php echo base_url("index.php/api/config/".$rpCode) ?>",
	method	:	"GET",
	dataType:	"json",
	success	:	function(data){
		CONFIG=data.data;
		var template=$("#templates").find(".ability")
		for(times=1;times<=CONFIG.max.startingAbilityAmount;times++){
			$(template).find(".abilityCount").empty().html("Ability "+times)
			$(template).find(".abilityName").attr("name","abilities[ability"+times+"][name]")
			$(template).find(".abilityCooldown").attr("name","abilities[ability"+times+"][cooldown]")
			$(template).find(".abilityDescription").attr("name","abilities[ability"+times+"][description]")
			$(template).clone().appendTo($("#abilitiesContainer"));
		}
		$("textarea").wysibb(EDITOR_DEFAULT_CONFIG);
		//make the area for users to fill in their stats for their characters
		template	=	$("#templates").find(".statTemplate")
		console.log(CONFIG.statSheet)
		$.each(CONFIG.statSheet,function(key,value){
			$(template).find(".statName").empty().html(value.name)
			var inputField=$(template).find(".statInput")
			
			$(inputField).attr("placeholder",value.name)
			$(inputField).attr("name","stats["+value.id+"]")
			let useDescription = value.fallbackDescription
			if(value.description!=""){
				useDescription = value.description
			}
			$(template).find(".statDesc").empty().html(useDescription)
			$(template).clone().appendTo($("#statContainer"))
		})
		$(template).remove()
		
	}
})
$(".pageSwap").on("click",function(event){
	//first, get which direction we need to go
	var id=$(this).attr("id")
	var direction=1
	if(id=="lastScreenButton"){
		direction=-1
	}
	if((ON_SCREEN<=1 && direction==-1) || (ON_SCREEN>=5 && direction==1)){
		//set it to disabled once again, as appently that didn't happen
		$(this).attr("disabled","disabled")
	} else {
		ON_SCREEN=ON_SCREEN+direction
		for(screen=1;screen<=5;screen++){
			$("#screen"+screen).css("display","none")
		}
		$("#screen"+ON_SCREEN).css("display","")
		if(ON_SCREEN==1){
			$("#lastScreenButton").prop("disabled","disabled")
			$("#nextScreenButton").prop("disabled","")
		} else if(ON_SCREEN==5){
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
	var error;
	//console.log($("#isMinion").prop("checked"))
	$('.required').each(function(index){
		if(! $(this).val()){
			if($(this).attr("id")=="name"){
				error="Your character needs to have a name";
			}else {
				error="Your character needs to have an age";
			}
			canPost=false;
			console.log('on required')
			console.log($(this))
			return false
		}
	})
	if(canPost){
		$('.statInput').each(function(index){
			let stat=Number($(this).val())
			console.log(stat)
			if(stat !=""){
				totalStatAmount=totalStatAmount+stat
			} else {
				error="one or more stats are not filled in or is not a number";
				console.log("on stat")
				console.log($(this))
				canPost=false
				return false
			}
		})
	}
	if(canPost){
		if(totalStatAmount!=CONFIG.max.startingStatAmount){
			error="The amount of stats you gave does not equal to the amount your character needs";
			console.log(totalStatAmount)
			canPost=false
		}
	}
	if(canPost){
		//console.log(tinyMCE.get("backstory").getContent())
		if( ( ! $("#backstory").bbcode() ) || ( ! $("#personality").bbcode() ) ) {
			console.log("on tinymce")
			error="Personality or Backstory are missing"
			canPost=false
		}
	}
	if(canPost){
		console.log("posted")
		//get the formData object. Not everything will be inside it however
		let data = new FormData(document.getElementById("mainPost"))
		//thus lets add those now
		data.set("appearanceDescription",$("#appearance").bbcode())
		data.set("backstory",$("#backstory").bbcode())
		data.set("personality",$("#personality").bbcode())
		$(".abilityDescription").each(function(){
			data.set($(this).attr("name"),$(this).bbcode())
		})
		$.ajax({
			url		:	"<?php echo base_url("index.php/api/characters/".$rpCode) ?>",
			method	:	"POST",
			data	:	data,
			dataType:	"json",
			cache: false,
			contentType: false,
			processData: false,
			success	:	function(data){
				if(data.success){
					window.location.href="<?php echo base_url("index.php/rp/character/view")?>/"+data.charCode
				}
			}
		})
	}else{
		showError(error);
	}
})
//simple solution against browser remembering disabled button status
//TODO better solution
$("#lastScreenButton").prop("disabled","disabled")
$("#nextScreenButton").prop("disabled","")
</script>
