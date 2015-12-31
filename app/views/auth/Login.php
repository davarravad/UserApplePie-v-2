<?php 
use Helpers\Form,
	Core\Error,
	Core\Success,
	Core\Language;
	
?>
<div class='col-lg-8 col-centered'>
	<div class='panel panel-default'>
		<div class='panel-heading'>
			<h3 class='jumbotron-heading'><?php echo Language::show('title_login', 'Auth'); ?></h3>
		</div>
		<div class='panel-body'>
			<p><?php echo Language::show('welcome_login', 'Auth'); ?></p>
			<div align=center>
				<!-- Display Login Box -->
				<div class='panel panel-info' style='max-width: 500px' align='center'>
					<div class='panel-heading'>
						<div class='panel-title'><?php echo Language::show('title_login', 'Auth'); ?></div>
					</div>
					<div class='pannel-body' style='padding:10px' align='center'>
						<?php echo Error::display($error); ?>
						<?php echo Form::open(array('method' => 'post')); ?>
							<div class='input-group' style='width: 80%; margin-bottom: 25px'>
								<span class='input-group-addon'><i class='glyphicon glyphicon-user'></i></span>
								<?php echo Form::input(array('name' => 'username', 'class' => 'form-control', 'placeholder' => 'UserName or Email')); ?>
							</div>
							<div class='input-group' style='width: 80%; margin-bottom: 25px'>
								<span class='input-group-addon'><i class='glyphicon glyphicon-lock'></i></span>
								<?php echo Form::input(array('type' => 'password', 'name' => 'password', 'class' => 'form-control', 'placeholder' => 'Password')); ?>
							</div>
							<!-- CSRF Token -->
							<input type="hidden" name="csrf_token" value="<?php echo $data['csrf_token']; ?>" />
							<button class="btn btn-md btn-success" name="submit" type="submit">
								<?php echo Language::show('login', 'Auth'); ?>
							</button>
						<?php echo Form::close(); ?>
					</div>
				</div>
				<!-- End Display Login Box -->
				<a class="btn btn-primary btn-sm" name="" href="<?php echo DIR;?>Register">
					<?php echo Language::show('register', 'Auth'); ?>
				</a>
				<a class="btn btn-primary btn-sm" name="" href="<?php echo DIR;?>ForgotPassword">
					<?php echo Language::show('forgot_password', 'Auth'); ?>
				</a>
				<a class="btn btn-primary btn-sm" name="" href="<?php echo DIR;?>ResendActivationEmail">
					<?php echo Language::show('resend_activation', 'Auth'); ?>
				</a>
			</div>
		</div>
	</div>
</div>