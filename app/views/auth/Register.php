<?php
use Helpers\Form,
	Core\Error,
	Core\Success,
	Core\Language;
?>

<div class='col-lg-12'>
	<div class='panel panel-default'>
		<div class='panel-heading'>
			<h3 class='jumbotron-heading'><?php echo Language::show('title_register', 'Auth'); ?></h3>
		</div>
		<div class='panel-body'>
			<p><?php echo Language::show('welcome_register', 'Auth'); ?></p>
			<div align=center>
				<!-- Display Login Box -->
				<div class='panel panel-info' style='max-width: 500px' align='center'>
					<div class='panel-heading'>
						<div class='panel-title'><?php echo Language::show('title_register', 'Auth'); ?></div>
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

							<!-- Password 1 -->
							<div class='input-group' style='width: 80%; margin-bottom: 25px'>
								<span class='input-group-addon'><i class='glyphicon glyphicon-lock'></i></span>
								<?php echo Form::input(array('id' => 'passwordInput', 'type' => 'password', 'name' => 'password', 'class' => 'form-control', 'placeholder' => 'Password')); ?>
								<span id='password01' class='input-group-addon'></span>
							</div>

							<!-- Password 2 -->
							<div class='input-group' style='width: 80%; margin-bottom: 25px'>
								<span class='input-group-addon'><i class='glyphicon glyphicon-lock'></i></span>
								<?php echo Form::input(array('id' => 'confirmPasswordInput', 'type' => 'password', 'name' => 'passwordc', 'class' => 'form-control', 'placeholder' => 'Confirm Password')); ?>
								<span id='password02' class='input-group-addon'></span>
							</div>

							<!-- Email -->
							<div class='input-group' style='width: 80%; margin-bottom: 25px'>
								<span class='input-group-addon'><i class='glyphicon glyphicon-envelope'></i></span>
								<?php echo Form::input(array('id' => 'email', 'type' => 'text', 'name' => 'email', 'class' => 'form-control', 'placeholder' => 'E-Mail')); ?>
								<span id='resultemail' class='input-group-addon'></span>
							</div>

							<!-- reCAPTCHA -->
							<?php if(RECAP_PUBLIC_KEY && RECAP_PRIVATE_KEY){ ?>
							<script type='text/javascript'>var RecaptchaOptions = {theme : 'clean'};</script>
							<div class="g-recaptcha" data-sitekey="<?php echo RECAP_PUBLIC_KEY;?>"></div>
							<script type="text/javascript" src="https://www.google.com/recaptcha/api.js?hl=en">
							</script>
							<?php } ?>

							<!-- CSRF Token -->
							<input type="hidden" name="csrf_token" value="<?php echo $data['csrf_token']; ?>" />

							<!-- Error Msg Display -->
							<span id='resultun2' class='label'></span>
							<span class='label' id='passwordStrength'></span>
							<span id='resultemail2' class='label'></span>

							<button class="btn btn-md btn-success" name="submit" type="submit">
								<?php echo Language::show('register', 'Auth'); ?>
							</button>
						<?php echo Form::close(); ?>
					</div>
				</div>
				<!-- End Display Login Box -->
			</div>
		</div>
	</div>
</div>
