		<div class="col-md-8">
			<div class="hold-transition login-page" style="background:none;">
				<div class="row">
					<div class="login-logo" style="padding-top:50px">
						<p><b>RP</b> Tracker</p>
					</div>
				</div>
			</div>
			<div class="login-box"   '><!-- height:768px; max-width:1024px;width:100%; -->
				<div class="login-box-body" style="background:none;">
					<p class="login-box-msg">Log in</p>
					<form method="post">
						<div class="alert alert-danger" id="error" hidden></div>
						<div class="form-group has-feedback">
							<input type="text" name="Username" id="username" class="form-control" placeholder="Gebruikers naam">
							<span class="glyphicon glyphicon-envelope form-control-feedback"></span>
						</div>
						<div class="form-group has-feedback">
							<input type="password" name="Password" id="password" class="form-control" placeholder="Wachtwoord" >
							<span class="glyphicon glyphicon-lock form-control-feedback"></span>
						</div>
						<div class="row">
							<div class="col-xs-4"></div><!-- /.col -->
							<div class="col-xs-4">
								<button type="post" id="signIn" class="btn btn-primary btn-block btn-flat">Sign In</button>
							</div><!-- /.col -->
						</div>
					</form>
				</div><!-- /.login-box-body -->
			</div><!-- /.login-box -->
		</div>

	<!-- jQuery 2.1.4 -->
	<script src="<?php echo base_url("third_party/jquery-1.11.3.min.js") ?>"></script>
	<!-- Bootstrap 3.3.5 -->
	<script src="<?php echo base_url("third_party/bootstrap-3.3.5-dist/js/bootstrap.min.js") ?>"></script>
	<!-- iCheck -->
	<script src="<?php echo base_url("third_party/AdminLTE-2.3.0/plugins/iCheck/icheck.min.js") ?>"></script>
	<script>
		$(function () {
			$('input').iCheck({
				checkboxClass: 'icheckbox_square-blue',
				radioClass: 'iradio_square-blue',
				increaseArea: '20%' // optional
			});
			$("#signIn").on("click",function(event){
				event.preventDefault();
				var password	=	$("#password").val()
				var username	=	$("#username").val()
				if( password && username){
					$.ajax({
						url		:	"<?php echo base_url("index.php/ajax/login") ?>",
						method	:	"POST",
						data	:	{password : password, username : username},
						dataType:	"json",
						success	:	function(data){
							console.log(data)
							if(!data.loggedIn){
								$("#error").empty().html("<p>"+data.error+"</p>").show();
							} else {
								window.location = "<?php echo base_url("index.php/profile") ?>"
							}
						}
						
					})
				}
			})
		});
    </script>
