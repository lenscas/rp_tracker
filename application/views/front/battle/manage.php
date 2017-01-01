<style>
	.stat {cursor:pointer}
	/*.form-control{ 
		max-width:100%

	}*/
	.modal-body {
		overflow-x: auto;
	}
	.modalStat .newModalStat{
		max-width:100px;
	}
	.bg-danger-dataTables-fix {
		background-color: #f2dede !important; 
	}
	.bg-success-dataTables-fix {
		background-color: #dff0d8 !important;
	}
</style>
<div class="col-md-12" style="overflow:auto; height:100%">
	<div class="row">
		<div class="col-md-12">
			<h1>
				<div class="pull-left" id="battleName"></div>
				<div class="pull-right">
					<button class="btn btn-success">Next turn</button>
				</div>
			</h1>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<h2>Attacking</h2>
			<select id="attackingCharacter" class="form-control charSelect"></select>
		</div>
		<div class="col-md-6">
			<h2>Defending</h2>
			<select id="defendingCharacter" class="form-control charSelect"></select>
		</div>
	</div>
	<div class="row"><p></p></div>
	<div class="row">
		<div class="col-md-6">
			<select id="attackingCharacterStat" class="form-control">
				<option>Statname (stat amount)</option>
			</select>
		</div>
		<div class="col-md-6">
			<select id="defendingCharacterStat" class="form-control">
				<option>Statname (stat amount)</option>
			</select>
		</div>
	</div>
	<div class="row"><p></p></div>
	<div class="row">
		<div class="col-md-4" style="text-align:center">
			<button class="btn btn-warning" id="basicAttack">Normal Attack</button>
		</div>
		<div class="col-md-4" style="text-align:center">
			<button class="btn btn-primary" id="abilityAttack">Ability Attack</button>
		</div>
		<div class="col-md-4" style="text-align:center">
			<button class="btn btn-danger" id="customRoll">Custom Rolls</button>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12" > <!--style="text-align:center" -->
			<h3 class="text-center">Outcome</h3>
			<p class="text-center" id="outcomeText"></p>
			<button class="pull-right btn btn-success" id="safeDamage" disabled>Safe Damage</button>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12" style="text-align:center">
			<h3>Characters</h3>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<table id="battleTable">
				<thead>
					<tr id="battleTableHead">
						<th>Name</th>
						<th>Turn order</th>
					</tr>
				</thead>
				<tbody id="battleTableBody">
				</tbody>
			</table>
		</div>
	</div>
</div>
<script>
	var ALL_CHARACTERS
	var RP_CONFIG
	var STAT_ID_TO_ROLE={}
	var ROLE_TO_STAT_ID={}
	var ALL_MODDIFIERS
	
	function updateCharSelect(charSelect,data,careAboutSelected,selectOverWrite=false){
		$(charSelect).empty()
		$.each(data,function(key,value){
			let selected = ""
			if(careAboutSelected){
				if(selectOverWrite!=false){
					if(selectOverWrite = value.code){
						selected = "selected"
					}
				} else{
					if(value.isTurn==1 && careAboutSelected){
						selected="selected"
					}
				}
			}
			$(charSelect).append('<option value="'+value.code+'" '+selected+'>'+value.name+'</option>')
			
		})
	}
	//this depends on createBetterModList being run first
	function updateBattleTable(){
		let battleHeader = $("#battleTableHead")
		$(battleHeader).find(".isStat").remove()
		$.each(RP_CONFIG.statSheet,function(key,value){
			$(battleHeader).append('<th class="isStat">'+value.name+"</th>")
		})
		let battleBody=$("#battleTableBody")
		battleBody.empty()
		$.each(ALL_CHARACTERS,function(key,value){
			let charRow=$("<tr></tr>") 
			battleBody.append(charRow)
			$(charRow).append("<td>"+ value.name+"</td>")
			$(charRow).append("<td>"+value.turnOrder+"</td>")
			$.each(RP_CONFIG.statSheet,function(statKey,statValue){
				$(charRow).append("<td>"+value.stats[statValue.id].total+"</td>")
			})
			if(value.isTurn==1){
				charRow.addClass("bg-success-dataTables-fix")
			}
		})
		$("#battleTable").dataTable()
	}
	//this function combines the data available in ALL_MODDIFIERS and makes the modifiers nicer available per character
	function createBetterModList(){
		$.each(ALL_CHARACTERS,function(key,value){
			let sortedModifiers={}
			$.each(ALL_MODDIFIERS,function(modKey,modValue){
				if(modValue.code == value.code){
					if(! sortedModifiers[modValue.statId]){
						sortedModifiers[modValue.statId]={}
						sortedModifiers[modValue.statId]["total"]=0
						sortedModifiers[modValue.statId]["name"]=getStatNameById(modValue.statId)
						sortedModifiers[modValue.statId]["modifiers"]=[]
					}
					sortedModifiers[modValue.statId]["modifiers"].push(modValue)
					sortedModifiers[modValue.statId]["total"]+=Number(modValue.value)
				}
			})
			ALL_CHARACTERS[key]["stats"]=sortedModifiers
		})
	}
	function getStatNameById(statId){
		let statName =""
		$.each(RP_CONFIG.statSheet,function(key,value){
			if(value.id==statId){
				statName=value.name
			}
		})
		return statName
	}
	//look in the character array to find the character by its code
	function getCharByCode(code){
		let char = false
		let key  = false
		$.each(ALL_CHARACTERS,function(key,value){
			if(value.code==code){
				char = value;
				return false //breaks the loop
			}
		})
		if(char){
			return {character : char,key :key}
		}
		throw new Error('"'+code + '" Code not found')
	}
	//this function updates the stat select. It depends on createBetterModList
	function updateStatSelectByChar(selector,characterCode=false){
		if(characterCode===false){
			characterCode=$("#"+selector).val()
		}
		let characterEntry = getCharByCode(characterCode).character
		//now that we have the character we also have all his stats thanks to createBetterModList()
		//this also added a nice total field for each stat
		//thus now its time to update the stat selector but as allways, first its time to clear it
		let statSelect = $("#"+selector+"Stat")
		statSelect.empty()
		$.each(characterEntry.stats,function(key,value){
			statSelect.append('<option value = "'+key+'">'+ value.name +'('+value.total+')</option>')
		})
		
		//find the character in the character array
		
	}
	function updatePage(attackingCharSelect=false,defendingCharSelect=false){
		$.ajax({
			url		:	"<?php echo base_url("index.php/ajax/battle/getBattle/".$battleId )?>",
			method	:	"GET",
			dataType:	"json",
			success	:	function(data){
				$("#battleName").html(data.battle.name)
				ALL_CHARACTERS = data.characters
				ALL_MODDIFIERS = data.modifiers
				console.log(ALL_MODDIFIERS)
				updateCharSelect($("#attackingCharacter"),ALL_CHARACTERS,true,attackingCharSelect)
				updateCharSelect($("#defendingCharacter"),ALL_CHARACTERS,true,defendingCharSelect)
				$.ajax({
					url		:	"<?php echo base_url("index.php/ajax/rp/getConfig") ?>/"+data.battle.code,
					dataType:	"json",
					method	:	"GET",
					success	:	function(data){
						if(data.success){
							RP_CONFIG=data.data
							console.log(RP_CONFIG)
							$.each(RP_CONFIG.statSheet,function(key,value){
								STAT_ID_TO_ROLE[value.id]=value.role
								ROLE_TO_STAT_ID[value.role]=value.id
							
							})
							//we now have a list of all the modifiers and their meaning
							//lets use this knowledge to update the better sorted modifiers
							createBetterModList()
							console.log(ALL_CHARACTERS)
							updateStatSelectByChar("attackingCharacter")
							updateStatSelectByChar("defendingCharacter")
							updateBattleTable()
						}
					}
				})
			}
		})
	}
	updatePage()
	$(".charSelect").on("change",function(){
		updateStatSelectByChar($(this).attr("id"),$(this).val())
	})
	//here we do the part where we do all the rolls
	function rollDice(){
		return Math.floor(Math.random()*10)+1 //a dice is always 1 to 10
	}
	function rollMultipleDice(amount){
		let total=0
		for(i=0;i<amount;i++){
			total += rollDice()
		}
		return total
	}
	//this function is used to see if an attack landed.
	function checkEvaded(accuracy,agility){
		let accRoll=rollMultipleDice(accuracy)
		let agiRoll=rollMultipleDice(agility)
		return accRoll>agiRoll
	}
	//this function checks how much (if any) damage was done
	function checkDamage(attack,defence){
		let attackRoll   = rollMultipleDice(attack)
		let defenceRoll  = rollMultipleDice(defence)
		if(attackRoll>defenceRoll){
			let rolledOver = attackRoll-defenceRoll
			if(rolledOver > 30) {
				return 3
			}
			if(rolledOver >20){
				return 2
			}
			return 1
		}
		return 0
	}
	//this function takes besides the names of the character also 4 ints and uses those for an attack.
	//We don't just read it out of the characters list to make it more reusable
	function doAttackRoll(attackerName,defenderName, accuracy,agility,attack,defence){
		let resultSTR= attackerName
		let damage=0
		if(checkEvaded(accuracy,agility)){
			damage=checkDamage(attack,defence)
			resultSTR +=" <b>landed</b> his attack and did "+damage+" damage to "+defenderName
		} else {
			resultSTR +=" <b>missed</b> his attack on "+defenderName
		}
		$("#outcomeText").empty().html(resultSTR)
		return damage
	}
	function doBuildInAttack(isAbility=false){
		let kind="physical"
		if(isAbility){
			kind="ability"
		}
		let attacker = getCharByCode($("#attackingCharacter").val()).character
		let defender = getCharByCode($("#defendingCharacter").val()).character
		let damage   = doAttackRoll(
			attacker.name,
			defender.name,
			attacker.stats[ROLE_TO_STAT_ID["evade_attack"]].total,
			defender.stats[ROLE_TO_STAT_ID["evade_defense"]].total,
			attacker.stats[ROLE_TO_STAT_ID[kind+"_attack"]].total,
			defender.stats[ROLE_TO_STAT_ID[kind+"_defense"]].total
		)
		updateSafeDamageButton(damage)
	}
	function updateSafeDamageButton(amount){
		let safeDamageBTN=$("#safeDamage")
		let state = false
		if(amount <=0){
			state = true
		}
		$(safeDamageBTN).prop("disabled",state)
		$(safeDamageBTN).val(amount)
	}
	$("#basicAttack").on("click",function(){
		doBuildInAttack(false)
	})
	$("#abilityAttack").on("click",function(){
		doBuildInAttack(true)
	})
	$("#customRoll").on("click",function(){
		let attacker       = getCharByCode($("#attackingCharacter").val()).character
		let defender       = getCharByCode($("#defendingCharacter").val()).character
		let attackUsedStat = $("#attackingCharacterStat").val()
		let defendUsedStat = $("#defendingCharacterStat").val()
		let outputSTR      = attacker.name + " rolled <b>"+ rollMultipleDice(attacker["stats"][attackUsedStat].total)+
			"</b><br>While "+defender.name +" rolled <b>"+rollMultipleDice(defender["stats"][defendUsedStat].total)+"</b>"
		$("#outcomeText").empty().html(outputSTR)
		//we have no idea how or even if the GM wants to turn the rolls into damage.
		//Thus we set the dealt damage to 0 which also nicely disables the button
		updateSafeDamageButton(0)
	})
	//this button saves the damage to the server allowing it to be stored. After that its time to update a good portion of the page
	$("#safeDamage").on("click",function(){
		let data={
			name		:	"Damage", //this is what we want to call it. Damage seems reasonable
			value		:	-$(this).val(), //the amount of damage we want. Needs to be inverted as it is a minus modifier to health
			countDown	:	-1, //we don't want it to automatically decay
			statId		:	ROLE_TO_STAT_ID["health"]//we want to change the health. As simple as that
		}
		let defenderCharCode = getCharByCode($("#defendingCharacter").val()).character.code
		$.ajax({
		url		:	"<?php echo base_url("index.php/ajax/modifiers/create")?>/"+defenderCharCode,
		method	:	"POST",
		data	:	data,
		dataType:	"json",
		success	:	function(returnData){
			//database is updated, lets update the page
			if(returnData.success){
				updatePage($("#attackingCharacter").val(),$("#defendingCharacter").val())
			}
		}
	})
	})
</script>
<script>
	
</script>

