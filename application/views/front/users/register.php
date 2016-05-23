
		<div class="col-md-6">
			<div class="login-box"   '><!-- height:768px; max-width:1024px;width:100%; -->
				<div class="login-box-body" style="background:none;">
					<p class="login-box-msg">Register</p>
					<form method="post">
						<div class="alert alert-danger" id="error" hidden></div>
						<div class="form-group has-feedback">
							<input type="text" id="username" class="form-control" placeholder="Username">
							<span class="fa fa-user form-control-feedback"></span>
						</div>
						<div class="form-group has-feedback">
							<input type="text" id="mail" class="form-control" placeholder="email" >
							<span class="fa fa-envelope form-control-feedback"></span>
						</div>
						<div class="form-group has-feedback">
							<input type="password" id="password" class="form-control" placeholder="Password" >
							<span class="glyphicon glyphicon-lock form-control-feedback"></span>
						</div>
						<div class="form-group has-feedback">
							<input type="password" id="passwordRepeat" class="form-control" placeholder="Password check" >
							<span class="glyphicon glyphicon-lock form-control-feedback"></span>
						</div>
						
						<div class="row">
							<div class="col-xs-4"></div><!-- /.col -->
							<div class="col-xs-4">
								<button type="post" id="register" class="btn btn-primary btn-block btn-flat">Sign In</button>
							</div><!-- /.col -->
						</div>
					</form>
				</div><!-- /.login-box-body -->
			</div><!-- /.login-box -->
		</div>
	
	<script>
		$(function () {
			$('input').iCheck({
				checkboxClass: 'icheckbox_square-blue',
				radioClass: 'iradio_square-blue',
				increaseArea: '20%' // optional
			});
			function error(message){
					$("#error").empty().html("<p>"+message+"</p>").show();
			}
			
			$("#register").on("click",function(event){
				event.preventDefault();
				var password		=	$("#password").val()
				var passwordCheck	=	$("#passwordRepeat").val()
				if(password!=passwordCheck){
					error("Passwords do not match.");
				} else {
					var username	=	$("#username").val()
					var mail		=	$("#mail").val()
					if( password && username){
						$.ajax({
							url		:	"<?php echo base_url("index.php/ajax/register") ?>",
							method	:	"POST",
							data	:	{password	:	password, 
									passwordCheck	:	passwordCheck,
									username		:	username,
									mail			:	mail
								},
							dataType:	"json",
							success	:	function(data){
								console.log(data)
								if(!data.success){
									error(data.error)
								} else {
									window.location = "<?php echo base_url("index.php/register/success") ?>"
								}
							}
						
						})
					}
				}
			})
		});
    </script>
