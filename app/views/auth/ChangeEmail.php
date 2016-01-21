<?php
use Helpers\Form,
	Core\Error,
	Core\Success,
	Core\Language;
?>


<div class='col-lg-8 col-md-8'>
	<div class='panel panel-default'>
		<div class='panel-heading'>
			<h3 class='jumbotron-heading'><?php echo Language::show('title_change_email', 'Auth'); ?></h3>
		</div>
		<div class='panel-body'>
			<p><?php echo Language::show('welcome_change_email', 'Auth'); ?></p>
			<div align=center>
				<!-- Display Login Box -->
				<div class='panel panel-info' style='max-width: 500px' align='center'>
					<div class='panel-heading'>
						<div class='panel-title'><?php echo Language::show('title_change_email', 'Auth'); ?></div>
					</div>
					<div class='pannel-body' style='padding:10px' align='center'>
						<?php echo Error::display($error); ?>
						<?php echo Success::display($success); ?>
						<?php echo Form::open(array('method' => 'post')); ?>

							<!-- Current Password -->
							<div class='input-group' style='width: 80%; margin-bottom: 25px'>
								<span class='input-group-addon'><i class='glyphicon glyphicon-lock'></i></span>
								<?php echo Form::input(array('type' => 'password', 'name' => 'passwordemail', 'class' => 'form-control', 'placeholder' => 'Current Password')); ?>
							</div>

							<!-- Email -->
							<div class='input-group' style='width: 80%; margin-bottom: 25px'>
								<span class='input-group-addon'><i class='glyphicon glyphicon-envelope'></i></span>
								<?php echo Form::input(array('id' => 'email', 'type' => 'text', 'name' => 'newemail', 'class' => 'form-control', 'placeholder' => $data['email'])); ?>
								<span id='resultemail' class='input-group-addon'></span>
							</div>

							<!-- Error Message Display -->
							<span id='resultemail2' class='label'></span>

							<!-- CSRF Token -->
							<input type="hidden" name="csrf_token" value="<?php echo $data['csrf_token']; ?>" />
							<button class="btn btn-md btn-success" name="submit" type="submit">
								<?php echo Language::show('change_email', 'Auth'); ?>
							</button>
						<?php echo Form::close(); ?>
					</div>
				</div>
				<!-- End Display Login Box -->
			</div>
		</div>
	</div>
</div>
