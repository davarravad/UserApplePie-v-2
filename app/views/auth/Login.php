<?php 
use \Helpers\Form,
	\Core\Error; 	
?>

<div class='panel panel-info' style='max-width: 500px' align='center'>
	<div class='panel-heading'>
		<div class='panel-title'>Login</div>
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
			<?php echo Form::input(array('type' => 'submit', 'name' => 'submit', 'value' => 'Submit', 'class' => 'btn btn-success btn-sm')); ?>

		<?php echo Form::close(); ?>
		
	</div>
</div>