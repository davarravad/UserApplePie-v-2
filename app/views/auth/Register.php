<?php 
use Helpers\Form,
	Core\Error,
	Core\Success; 	
?>
<div class='col-lg-8 col-centered'>
	<div class='panel panel-default'>
		<div class='panel-heading'>
			<h3 class='jumbotron-heading'><?php echo $data['title'] ?></h3>
		</div>
		<div class='panel-body'>
			<p><?php echo $data['welcome_message'] ?></p>
			<div align=center>
				<!-- Display Login Box -->
				<div class='panel panel-info' style='max-width: 500px' align='center'>
					<div class='panel-heading'>
						<div class='panel-title'>Register</div>
					</div>
					<div class='pannel-body' style='padding:10px' align='center'>
						<?php echo Error::display($error); ?>
						<?php echo Success::display($success); ?>
						<?php echo Form::open(array('method' => 'post')); ?>
						
							<!-- Username -->
							<div class='input-group' style='width: 80%; margin-bottom: 25px'>
								<span class='input-group-addon'><i class='glyphicon glyphicon-user'></i></span>
								<?php echo Form::input(array('id' => 'username', 'name' => 'username', 'class' => 'form-control', 'placeholder' => 'UserName')); ?>
								<span id='resultun' class='input-group-addon'></span>
							</div>							
							<div class='form-group' style='width: 75%; margin-bottom: 5px'>
								<font size=1>Pick a username you will remember.</font>
							</div>
							
							<!-- Password 1 -->
							<div class='input-group' style='width: 80%; margin-bottom: 25px'>
								<span class='input-group-addon'><i class='glyphicon glyphicon-lock'></i></span>
								<?php echo Form::input(array('id' => 'passwordInput', 'type' => 'password', 'name' => 'password', 'class' => 'form-control', 'placeholder' => 'Password')); ?>
								<span id='password01' class='input-group-addon'></span>
							</div>
							<div class='form-group' style='width: 75%; margin-bottom: 5px'>
								<font size=1>Pick a password you will remember.</font>
							</div>
							
							<!-- Password 2 -->
							<div class='input-group' style='width: 80%; margin-bottom: 25px'>
								<span class='input-group-addon'><i class='glyphicon glyphicon-lock'></i></span>
								<?php echo Form::input(array('id' => 'confirmPasswordInput', 'type' => 'password', 'name' => 'passwordc', 'class' => 'form-control', 'placeholder' => 'Confirm Password')); ?>
								<span id='password02' class='input-group-addon'></span>
							</div>
							<div class='form-group' style='width: 75%; margin-bottom: 5px'>
								<font size=1>Confirm Your Password.</font>
							</div>
							
							<!-- Email -->
							<div class='input-group' style='width: 80%; margin-bottom: 25px'>
								<span class='input-group-addon'><i class='glyphicon glyphicon-envelope'></i></span>
								<?php echo Form::input(array('id' => 'email', 'type' => 'text', 'name' => 'email', 'class' => 'form-control', 'placeholder' => 'E-Mail')); ?>
								<span id='password02' class='input-group-addon'></span>
							</div>
							<div class='form-group' style='width: 75%; margin-bottom: 5px'>
								<font size=1>Please use Current Working Email so you can activate your UserApplePie account.  Check your Email and click the link provided.</font>
							</div>
							
							<!-- reCAPTCHA -->
							
							<!-- CSRF Token -->
							<input type="hidden" name="csrf_token" value="<?php echo $data['csrf_token']; ?>" />
							
							<?php echo Form::input(array('type' => 'submit', 'name' => 'submit', 'value' => 'Register', 'class' => 'btn btn-success btn-sm')); ?>
						<?php echo Form::close(); ?>
					</div>
				</div>
				<!-- End Display Login Box -->
			</div>
		</div>
	</div>
</div>