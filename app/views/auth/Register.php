<?php 
use Helpers\Form,
	Core\Error,
	Core\Success,
	Core\Language;
?>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>


<script type="text/javascript">
$(document).ready(function()
{    
 $("#username").keyup(function()
 {  
  var name = $(this).val(); 
  
  if(name.length > 4)
  {  
   $("#resultun").html('');
   
   /*$.post("username-check.php", $("#reg-form").serialize())
    .done(function(data){
    $("#result").html(data);
   });*/
   
   $.ajax({
    
    type : 'POST',
    url  : 'LiveCheckUserName',
    data : $(this).serialize(),
    success : function(data)
        {
              /*$("#resultun").html(data);*/
			if(data == 'OK')
			{
			   $("#resultun").html("<i class='glyphicon glyphicon-ok text-success'></i>");
			   $("#resultun2").html("");	
			}
			if(data == 'CHAR')
			{
			   $("#resultun").html("<i class='glyphicon glyphicon-remove text-danger'></i>");
			   $("#resultun2").html("<div class='alert alert-danger alert-dismissible' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>User Name Invalid!</div>");	
			}
			if(data == 'INUSE')
			{
			   $("#resultun").html("<i class='glyphicon glyphicon-remove text-danger'></i>");
			   $("#resultun2").html("<div class='alert alert-danger alert-dismissible' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>User Name is already in use.</div>");	
			}
        }
    });
    return false;
   
  }
  else
  {
   $("#resultun").html("<i class='glyphicon glyphicon-remove text-danger'></i>");
   $("#resultun2").html("<div class='alert alert-danger alert-dismissible' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>User Name must be at least <strong>5</strong> characters.</div>");
  }
 });
 
});
</script>

<script type="text/javascript">
$(document).ready(function()
{    
 $("#email").keyup(function()
 {  
  var name = $(this).val(); 
  
  if(name.length > 4)
  {  
   $("#resultemail").html('');
   
   $.ajax({
    
    type : 'POST',
    url  : 'LiveCheckEmail',
    data : $(this).serialize(),
    success : function(data)
        {
              /*$("#resultun").html(data);*/
			if(data == 'OK')
			{
			   $("#resultemail").html("<i class='glyphicon glyphicon-ok text-success'></i>");
			   $("#resultemail2").html("");	
			}
			if(data == 'BAD')
			{
			   $("#resultemail").html("<i class='glyphicon glyphicon-remove text-danger'></i>");
			   $("#resultemail2").html("<div class='alert alert-danger alert-dismissible' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>Email Address Invalid!</div>");	
			}
			if(data == 'INUSE')
			{
			   $("#resultemail").html("<i class='glyphicon glyphicon-remove text-danger'></i>");
			   $("#resultemail2").html("<div class='alert alert-danger alert-dismissible' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>Email is already in use.</div>");	
			}
        }
    });
    return false;
   
  }
  else
  {
   $("#resultemail").html("<i class='glyphicon glyphicon-remove text-danger'></i>");
   $("#resultemail2").html("<div class='alert alert-danger alert-dismissible' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>Email must be at least <strong>5</strong> characters.</div>");
  }
 });
 
});
</script>

<script src="app/templates/default/js/jq-pw-strength.js"></script>



<div class='col-lg-8 col-centered'>
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
								<span id='resultemail' class='input-group-addon'></span>
							</div>
							<div class='form-group' style='width: 75%; margin-bottom: 5px'>
								<font size=1>Please use Current Working Email so you can activate your UserApplePie account.  Check your Email and click the link provided.</font>
							</div>
							
							<!-- reCAPTCHA -->
							
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
