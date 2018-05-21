
if not returnDeltas then
	function returnDeltas(deltas)
		io.write("[")
		local firstDelta = true
		for dkey,delta in ipairs(deltas) do
			firstDelta = (not firstDelta ) and (io.write(",") and false)
			io.write("{")
			local firstV = true
			for fkey,fvalue in pairs(delta) do
				firstV = ((not firstV ) and (io.write(",") and false))
				io.write('"'..fkey..'":"'..tostring(fvalue)..'"')
			end
			io.write("}")
		end
		io.write("]\n")
	end
end
function Battle(battleData,systemConfig)
	local deltas = {}
	local fun = {}
	local charset = {}

	local function printTable(ptable,tabs,printStr)
		tabs = tabs or 0
		printStr = printStr or ""
		for key,value in pairs(ptable) do
			local str = ""
			for i=0, tabs do
				str = str .. "\t"
			end
			if type(value) == "table" then
				printStr = printStr..str..key.. "\tTable\n"
				printStr = printTable(value,tabs+1,printStr)
			else
				printStr = printStr..str..key .. "\t"..value.."\n"
			end
		end
		return printStr
	end
	local function getDelta(kind,code)
		if code == nil then
			code = kind
			kind = nil
		end
		for key,delta in ipairs(deltas) do
			if not kind then
				if delta.code == code then
					return delta
				end
			elseif delta.what == kind and delta.code == code then
				return delta
			end
		end
	end
	local function removeDelta(deltaData)
		getDelta(deltaData.what,deltaData.code).isInActive = true
	end
	local function checkIfDeltaCodeIsUnique(what,code)
		return not getDelta(what,code)
	end
	-- qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890
	for i = 48,  57 do table.insert(charset, string.char(i)) end
	for i = 65,  90 do table.insert(charset, string.char(i)) end
	for i = 97, 122 do table.insert(charset, string.char(i)) end
	local function createTempCode(checkIfExists)
		while true do
			local codeTable = {}
			for i=1,10 do
				table.insert(codeTable,charset[math.random(1,#charset)])
			end
			local code = "lua_temp_"..table.concat(codeTable,"")
			if (not checkIfExists(code)) and checkIfDeltaCodeIsUnique(code) then
				return code
			end
		end
	end

	local function searchMod(character,modId)
		local workWith = battleData.characters[character.code].modifiers
		for stat,mods in pairs(workWith) do
			if tonumber(modId) then
				for key,mod in pairs(mods) do
					if mod.modifiersId and tonumber(mod.modifiersId) == tonumber(modId) then
						return {
							mod      = mod,
							statName = stat,
							modKey   = key,
							modId    = mod.modifiersId,
						}
					end
				end
			else
				if mods[modId] then
					return {
						["mod"] = mods[modId],
						statName = stat,
						modKey = modId,
						modId  = modId,
						isNew = true
					}
				end
			end
		end
	end
	function fun.print(...)
		local arg = {...}
		if #arg == 0 then
			arg[#arg+1] = tostring(nil)
		end
		for key,value in ipairs(arg) do
			arg[key] = tostring(value)
		end
		table.insert(deltas,{
			mode    = modes.NOTHING,
			what    = kinds.OUTPUT,
			lol     = "WTF?",
			message = table.concat(arg,"\t")
		})
	end
	local function copyOver(copyTo,copyValues)
		for key,value in pairs(copyValues) do
			copyTo[key] = value
		end
	end
	function fun:insertModifier(modifierData)
		local workWith  = battleData.characters[modifierData.character.code].modifiers[modifierData.type]
		local sanerData = {
			mode      = modes.INSERT,
			what      = kinds.MODIFIER,
			amount    = modifierData.amount or error("No amount set for modifier."),
			character = modifierData.character.code or error("No target set for modifier."),
			countDown = modifierData.countDown or error("No countdown set for modifier."),
			type      = modifierData.type or error("No type set for modifier."),
			name      = modifierData.name or error("No name set for modifier."),
			code      = createTempCode(
				function(code)
					return workWith[code]
				end
			)
		}
		table.insert(deltas,sanerData)
		workWith[sanerData.code] = {}
		copyOver(workWith[sanerData.code],sanerData)
		return sanerData.code
	end
	function fun:deleteModifier(character,modId)
		modifier = searchMod(character,modId)
		if modifier.isNew then
			removeDelta({what = kinds.MODIFIER,code = modifier.modId})
		end
		table.insert(deltas,{
				mode       = modes.DELETE,
				what       = kinds.MODIFIER,
				code       = createTempCode(function(code)return false end),
				modId      = modifier.modId,
				isInActive = modifier.isNew,
				name       = modifier.mod.name
			}
		)
		battleData.characters[character.code].modifiers[modifier.statName][modifier.modKey] = nil
	end

	function fun:updateModifier(character,modId,newData)
		local modifier = searchMod(character,modId)
		local sanerData = {
			mode      = modes.UPDATE,
			what      = kinds.MODIFIER,
			code      = createTempCode(function(code) return false end),
			amount    = newData.amount,
			countDown = newData.countDown,
			modId     = modifier.modId,
			name      = modifier.mod.name
		}
		table.insert(deltas,sanerData)
		local toInsert = {
			amount = sanerData.amount,
			countDown = sanerData.countDown
		}
		copyOver(modifier.mod, toInsert)
	end
	function fun:removeTurnFromMods(character)
		local allMods = battleData.characters[character.code].modifiers
		for statName,mods in pairs(allMods) do
			for k, mod in pairs(mods) do
				if tonumber(mod.isBase) == 0 then
					if tonumber(mod.countDown) > -1 then
						if tonumber(mod.countDown) == 0 then
							fun:deleteModifier(character,mod.modifiersId)
						else
							mod.countDown = mod.countDown - 1
							fun:updateModifier(character,mod.modifiersId,mod)
						end
					end
				end
			end
		end
	end
	function fun:insertCharacter(newData)
		local sanerData = {
			what = kinds.CHARACTER,
			mode = modes.INSERT,
			code = createTempCode(function(code) return battleData.characters[code] end),
			name = newData.name,
			age  = newData.age,
			appearance  = newData.appearance,
			backstory   = newData.backstory,
			personality = newData.personality,
			turnOrder   = newData.turnOrder,
		}
		table.insert(deltas,sanerData)
		battleData.characters[sanerData.code] = {
			code = sanerData.code,
			name = newData.name,
			age  = newData.age,
			appearance = newData.appearance,
			backstory  = newData.backstory,
			personality = newData.personality,
			turnOrder  = newData.turnOrder,
			modifiers  = {}
		}
		for key,value in ipairs(systemConfig.statNames) do
			battleData.characters[sanerData.code].modifiers[value] = {}
		end
		local char =  {code = sanerData.code}
		for key,value in pairs(newData.modifiers) do
			value.character = char
			self:insertModifier(value)
		end
		return char
	end
	function fun:getCharacterByCode(charCode)
		return battleData.characters[charCode] and {code = charCode}
	end
	function fun:getTotalStatsOnChar(char)
		local calcStats = {}
		local stats  = battleData.characters[char.code].modifiers
		for statName, mods in pairs(stats) do
			calcStats[statName] = 0
			for modKey,mod in pairs(mods) do
				calcStats[statName] = mod.value + calcStats[statName]
			end
		end
		return calcStats
	end
	function fun:getNextCharacter(character)
		local first = nil
		local nextPot = nil
		for k,potChar in pairs(battleData.characters) do
			if tonumber(potChar.turnOrder) == 0 then
				first = potChar
			elseif tonumber(potChar.turnOrder) > tonumber(character.turnOrder) then
				if nextPot then
					if tonumber(nextPot.turnOrder) > tonumber(potChar.turnOrder) then
						nextPot = potChar
					end
				else
					nextPot = potChar
				end
			end
		end
		--fun.potChar(nextPot.code, first.code)
		return nextPot or first
	end
	function fun:getCurrentCharacter()
		for k,char in pairs(battleData.characters) do
			if tonumber(char.isTurn) == 1 then
				return char
			end
		end
		local key,char = next(battleData.characters)
		error("no char found")
	end
	function fun:setTurnTo(character)
		local sanerData = {
			what    = kinds.NEXT_TURN,
			mode    = modes.UPDATE,
			code    = createTempCode(function(code) return false end),
			nextTurn = character.code
		}
		table.insert(deltas,sanerData)
	end
	function fun:printBattle()
		print("battle:")
		fun.print(printTable(battleData))
		print("delta's")
		fun.print(printTable(deltas))
	end
	return fun,deltas
end
function roll(amount,sides)
	sides = sides or 10
	local rolls = {}
	local total = 0
	for i=1,amount do
		local rolled = math.random(sides)
		table.insert(rolls,rolled)
		total = total + rolled
	end
	return total,rolls
end
function rolld10(amount) return roll(amount,10) end
function rolld20(amount) return roll(amount,20) end
function rolld6(amount)  return roll(amount,6)  end

local battle,deltas = Battle(battleEnv,config)
local actionRun,message = load(actionScript,"Action" ,"bt",{
	table    = table,
	math     = math,
	string   = string,
	pairs    = pairs,
	next     = next,
	pcall    = pcall,
	select   = select,
	tonumber = tonumber,
	tostring = tostring,
	type     = type,
	table    = table,
	ipairs   = ipairs,
	print    = battle.print,
	error    = error,
	assert   = assert,
	battle   = battle,
	roll     = roll,
	rolld6   = rolld6,
	rolld20  = rolld20,
	rolld10  = rolld10
})


local success = nil
if actionRun then
	success,message = pcall(actionRun)
else
	success = false
	--message = "Not a valid script/contains syntax errors."
end

if not success then
	local newDeltas = {}
	for key,value in pairs(deltas) do
		if value.what==kinds.OUTPUT then
			table.insert(newDeltas,value)
		end
	end
	table.insert(newDeltas,{
		mode = modes.NOTHING,
		what = kinds.ERROR,
		message = message}
	)
	table.insert(newDeltas,{
		mode = modes.NOTHING,
		what = kinds.ERROR,
		message = actionScript
	})
	deltas = newDeltas
end
returnDeltas(deltas)
