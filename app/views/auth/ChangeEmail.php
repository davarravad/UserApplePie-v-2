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
 $("#email").keyup(function()
 {  
  var name = $(this).val(); 
  
  if(name.length >= <?php echo MIN_EMAIL_LENGTH;?>)
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

<div class='col-lg-8 col-centered'>
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