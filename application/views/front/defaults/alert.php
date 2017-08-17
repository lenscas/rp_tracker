<?php
	$class="col-md-8";
	if($hasRPHeader){
		$class="col-md-12";
	}
?>
<div class="<?php echo $class ?>" style="height:100%">
	<div class="row" style="height:72px">
			<div class="alert alert-danger" id="basicAlert" style="display:none">
				<p>You are not supposed to see this</p>
			</div>
	</div>
	<div class="row" style="height:calc(100% - 72px)">
		<div class="col-md-12" style="height:100%; overflow:100%;">
