<!DOCTYPE html>
<html lang="en" style="height:100%">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    

    <title>RP tracker</title>

    <!-- Bootstrap Core CSS -->
    <link href="<?php echo base_url("third_party/bootstrap-3.3.5-dist/css/bootstrap.min.css")?>" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="<?php echo base_url("third_party/startbootsrtap/css/heroic-features.css")?>" rel="stylesheet">
    <!-- Font Awesome because Awesome> !-->
    <link rel="stylesheet" href="<?php echo base_url("third_party/font-awesome-4.4.0/css/font-awesome.min.css")?>">
	<link rel="stylesheet" href="<?php echo base_url("third_party/jquery-ui-1.11.4/jquery-ui.min.css") ?>">
	<link rel="stylesheet" href="<?php echo base_url("third_party/jquery-ui-1.11.4/jquery-ui.theme.css") ?>">  
	<script src="<?php echo base_url("third_party/jquery-1.11.3.min.js")?>" type="text/javascript"></script>
	<script src="<?php echo base_url("third_party/jquery-ui-1.11.4/jquery-ui.js")?>" type="text/javascript"></script>
	<script src="<?php echo base_url("third_party/bootstrap-3.3.5-dist/js/bootstrap.min.js")?>" type="text/javascript"></script>
	<script src="<?php echo base_url("third_party/tinymce/js/tinymce/tinymce.min.js")?>"></script>
	
	 <link rel="stylesheet" href="<?php echo base_url("third_party/AdminLTE-2.3.0/dist/css/AdminLTE.min.css")?>">
    <!-- iCheck -->
    <link rel="stylesheet" href="<?php echo base_url("third_party/AdminLTE-2.3.0/plugins/iCheck/square/blue.css")?>">
    
	<!-- Datatable -->
    <script src="<?php echo base_url("third_party/DataTables-1.10.9/media/js/jquery.dataTables.min.js")?>" type="text/javascript"></script>
     <link rel="stylesheet" href="<?php echo base_url("third_party/DataTables-1.10.9/media/css/jquery.dataTables.min.css")?>">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>
<body style="padding-top:0px; height:100%; overflow:hidden" >
	<nav class=" navbar navbar-default" style="margin-bottom:0px; height:5%;">
		<div class="container-fluid" style="height:100%">
			<div class="navbar-header" style="height:100%">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-examample-navbar-collapse-1" area-expanded="false">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand">RP tracker</a>
			</div>
			
			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1" style="height:100%">
				<?php 
					if($loggedIn){
				?>
					<ul class="nav navbar-nav">
						<li><a href="<?php echo base_url("index.php/rp/create") ?>">Create RP</a></li>
						<li><a href="<?php echo base_url("index.php/rp/list") ?>">Join RP</a></li>
						<li><a href="<?php echo base_url("index.php/game/worldmap") ?>">World map</a></li>
					</ul>
					<ul class="nav navbar-nav navbar-right">
						<li><a href="<?php echo base_url("index.php/profile") ?>">Profile</a></li>
						<li><a href="<?php echo base_url("index.php/logout") ?>">Logout</a></li>
					</ul>
				
				<?php	
					} else {
				?>
					<ul class="nav navbar-nav navbar-right">
						<li class=""><a href="<?php echo base_url("index.php/login") ?>">Login</a></li>
						<li class=""><a href="<?php echo base_url("index.php/register") ?>">Register</a></li>
					</ul>
						
				<?php	
					}
				?>
				</ul>
			</div>
		</div>
	</nav>
	<div class="container-fluid" style="height:95%; padding-left:0;padding-right:0;">
		<div class="col-md-12" style="height:100%; padding:0;margin:0;">
