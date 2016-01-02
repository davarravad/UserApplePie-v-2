<?php 
use Helpers\Form,
	Core\Error,
	Core\Success,
	Core\Language; 	
?>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

<script type="text/javascript">
 $(document).ready(function() {
 
$('#passwordInput, #confirmPasswordInput').on('keyup', function(e) {
 
if($('#passwordInput').val() != '' && $('#confirmPasswordInput').val() != '' && $('#passwordInput').val() != $('#confirmPasswordInput').val())
{
$('#passwordStrength').html('<div class="alert alert-danger" role="alert">Passwords do not match!</div>');
$('#password01').html("<i class='glyphicon glyphicon-remove text-danger'></i>");
$('#password02').html("<i class='glyphicon glyphicon-remove text-danger'></i>");
 
return false;
}
 
// Must have capital letter, numbers and lowercase letters
var strongRegex = new RegExp("^(?=.{8,})(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*\\W).*$", "g");
 
// Must have either capitals and lowercase letters or lowercase and numbers
var mediumRegex = new RegExp("^(?=.{7,})(((?=.*[A-Z])(?=.*[a-z]))|((?=.*[A-Z])(?=.*[0-9]))|((?=.*[a-z])(?=.*[0-9]))).*$", "g");
 
// Must be at least 8 characters long
var okRegex = new RegExp("(?=.{<?php echo MIN_PASSWORD_LENGTH; ?>,}).*", "g");
 
if (okRegex.test($(this).val()) === false) {
// If ok regex doesn't match the password
$('#passwordStrength').html("<div class='alert alert-danger alert-dismissible' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>Password must be at least <?php echo MIN_PASSWORD_LENGTH;?> characters long.</div>");
$('#password01').html("<i class='glyphicon glyphicon-remove text-danger'></i>");
	if($('#confirmPasswordInput').val()){
		$('#password02').html("<i class='glyphicon glyphicon-remove text-danger'></i>");
	}
} else if (strongRegex.test($(this).val())) {
// If reg ex matches strong password
$('#passwordStrength').html("");
$('#password01').html("<i class='glyphicon glyphicon-thumbs-up text-success'></i>");
	if($('#confirmPasswordInput').val()){
		$('#password02').html("<i class='glyphicon glyphicon-thumbs-up text-success'></i>");
	}
} else if (mediumRegex.test($(this).val())) {
// If medium password matches the reg ex
$('#passwordStrength').html("<div class='alert alert-info alert-dismissible' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>Good Password!</div>");
$('#password01').html("<i class='glyphicon glyphicon-ok text-info'></i>");
	if($('#confirmPasswordInput').val()){
		$('#password02').html("<i class='glyphicon glyphicon-ok text-info'></i>");
	}
} else {
// If password is ok
$('#passwordStrength').html("<div class='alert alert-warning alert-dismissible' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>Weak Password!</div>");
$('#password01').html("<i class='glyphicon glyphicon-remove text-warning'></i>");
	if($('#confirmPasswordInput').val()){
		$('#password02').html("<i class='glyphicon glyphicon-remove text-warning'></i>");
	}
}
return true;
});
 
});
</script>

<div class='col-lg-8 col-centered'>
	<div class='panel panel-default'>
		<div class='panel-heading'>
			<h3 class='jumbotron-heading'><?php echo Language::show('title_change_password', 'Auth'); ?></h3>
		</div>
		<div class='panel-body'>
			<p><?php echo Language::show('welcome_change_password', 'Auth'); ?></p>
			<div align=center>
				<!-- Display Login Box -->
				<div class='panel panel-info' style='max-width: 500px' align='center'>
					<div class='panel-heading'>
						<div class='panel-title'><?php echo Language::show('title_change_password', 'Auth'); ?></div>
					</div>
					<div class='pannel-body' style='padding:10px' align='center'>
						<?php echo Error::display($error); ?>
						<?php echo Success::display($success); ?>
						<?php echo Form::open(array('method' => 'post')); ?>
							<!-- Current Password -->
							<div class='input-group' style='width: 80%; margin-bottom: 25px'>
								<span class='input-group-addon'><i class='glyphicon glyphicon-lock'></i></span>
								<?php echo Form::input(array('type' => 'password', 'name' => 'currpassword', 'class' => 'form-control', 'placeholder' => 'Current Password')); ?>
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

							<!-- Display Live Password Status -->
							<span class='label' id='passwordStrength'></span>
							
							<!-- CSRF Token -->
							<input type="hidden" name="csrf_token" value="<?php echo $data['csrf_token']; ?>" />
							<button class="btn btn-md btn-success" name="submit" type="submit">
								<?php echo Language::show('change_password', 'Auth'); ?>
							</button>
						<?php echo Form::close(); ?>
					</div>
				</div>
				<!-- End Display Login Box -->
			</div>
		</div>
	</div>
</div>